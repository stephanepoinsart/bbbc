<?php
require_once('inc/config.php');
require_once('inc/database.php');
require_once('inc/utility.php');


if (!isset($login)) {
	$login='';
}

if (!isset($_GET['confcreator'])) {
	showerror("Le lien de la conférence ne semble pas valide. Vérifiez qu'il ai bien été copié en entier. [confcreator manquant]");
}
$confcreator=$_GET['confcreator'];

if (!isset($_GET['confname'])) {
	showerror("Le lien de la conférence ne semble pas valide. Vérifiez qu'il ai bien été copié en entier. [confname manquant]");
}
$confname=$_GET['confname'];
$pagetitle="BBB - $confname";

if (!isset($_GET['role']) || $_GET['role']!='admin') {
	$role='user';
	$strrole='utilisateur';
} else {
	$role='admin';
	$strrole='administrateur';
}

if (!isset($_GET['hash'])) {
	showerror("Le lien de la conférence ne semble pas valide. Vérifiez qu'il ai bien été copié en entier. [code de contrôle manquant]");
}
$hash=$_GET['hash'];
$urlhash=gensitehash($confcreator, $confname, $role);
if ($hash!=$urlhash) {
	showerror("Le lien de la conférence ne semble pas valide. Vérifiez qu'il ai bien été copié en entier. [les codes de contrôles ne correspondent pas]");
}


$db=new Db;
if (!$db->confexists($confcreator, $confname)) {
	showerror("la conférence $confname de l'utilisateur $confcreator a été supprimée ou désactivée. Recontactez l'utilisateur pour lui demander de recréer la conférence ou vous donner un autre lien.");
}

require_once('inc/header.php');

showbuffered();

if (!isset($_GET['guestname'])) {
	echo "<header>
			<h1>Se connecter à une webconférence</h1>
	</header>
	<main>
		<div id=\"confinfo\">Conférence \"$confname\" crée par \"$confcreator\", accès $strrole</div>
		<h2>Lien à communiquer aux participants...</h2>
			<p>Voici le lien vers cette page (lien $strrole), que vous pouvez copier-coller et transmettre aux autres participants pour qu'ils puissent se connecter :</p>
			<div class=\"conflink\">";
				// based on : http://stackoverflow.com/questions/5216172/getting-current-url
				$currenturl = 'http';
				if (isset($_SERVER['HTTPS']) && filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
					$currenturl .= "s";
				}
				$currenturl .= "://";
				if ($_SERVER["SERVER_PORT"] != "80") {
					$currenturl .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				} else {
					$currenturl .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				}
			echo "<a href=\"$currenturl\" title=\"Lien vers cette page\">$currenturl</a></div>
			<p>N'oubliez pas de demander à toutes les personnes que vous invitez de bien vérifier à l'avance si la conférence fonctionne correctement sur leur ordinateur.</p>  
		<h2>Test de configuration...</h2>
			<div class=\"diagitem\">Flash player : <span id=\"flashmessage\">détection en cours...</span></div>
			<div class=\"diagitem\">Réseau : <span id=\"networkmessage\">&nbsp;</span>
				<ul>
					<li class=\"diagdetail\">Download : <span id=\"downloadspeed\">détection en cours...</span></li>
					<li class=\"diagdetail\">Upload : <span id=\"uploadspeed\">détection en cours...</span></li>
					<li class=\"diagdetail\">Max ping : <span id=\"pingdiag\">détection en cours...</span></li>
				</ul>
			</div>
			<div class=\"diagitem\">Navigateur : <span id=\"browsermessage\">détection en cours...</span></div>
		<h2>Se connecter à la conférence...</h2>
			<form method=\"GET\" action=\"".SITE_URL."redirect.php\">
			<label for=\"guestname\">Votre nom :</label>
			<input type=\"text\" name=\"guestname\" id=\"guestname\" value=\"$login\"/>
			<input type=\"hidden\" name=\"confcreator\" value=\"$confcreator\"/>
			<input type=\"hidden\" name=\"confname\" value=\"$confname\"/>
			<input type=\"hidden\" name=\"role\" value=\"$role\"/>
			<input type=\"hidden\" name=\"hash\" value=\"$hash\"/>
			<input type=\"submit\" name=\"connect\" value=\"Se connecter\"/>
			</form>";
} else {
	$guestname=$_GET['guestname'];
	$bbbcreateurl=genbbbcreateurl(urlencode($confcreator), urlencode($confname), $role);

	if (!file_get_contents($bbbcreateurl)) {
		showerror("Une erreur interne a été générée par BBB lors de la demande de création de conférence.");
	} else {
		$bbbjoinurl=genbbbjoinurl(urlencode($confcreator), urlencode($confname), $role, urlencode($guestname));
		echo "<iframe src=\"$bbbjoinurl\"><a href=\"$bbbjoinurl\">Rejoindre la conférence</a></iframe>";
		$db->logguest($confcreator, $confname, $guestname);
	}
}

function showbar() {
	echo "<div id=\"bar\">
		<div id=\"pingtool\" title=\"qualité de votre connexion : rouge ou supérieur à 300 = problème\"><div id=\"pinggraph\"></div><span id=\"pingnumeric\"></span></div> |
		<a href=\"".SITE_URL."\" target=\"_blank\"><img class=\"icon\" alt=\"Retourner à l'écran de gestion des confs (utilisateurs UTC)\" title=\"Créez et gérrez vos conférences (utilisateurs UTC)\" src=\"static/img/server_go.png\"/></a> |
		<a id=\"btn_fs\" href=\"#\" onClick=\"toggleFullScreen();\"><img class=\"icon\" alt=\"Plein écran\" title=\"Plein écran\" src=\"static/img/arrow_out.png\"/></a> |
		<a id=\"btn_help\" href=\"http://ics.utc.fr/doc-cap/co/1-creer.html\" target=\"_blank\"><img class=\"icon\" src=\"static/img/help.png\"/> <span class=\"text\">documentation</span></a>
		</div>";
}

echo "<script type=\"text/javascript\" src=\"3rdparty/jquery-2.1.1.js\"></script><script type=\"text/javascript\" src=\"3rdparty/ua-parser.js\"></script>";

showbar();

echo "<script src=\"static/js/fullscreen.js\"></script>";
if (!isset($guestname)) {
	echo "<script src=\"3rdparty/flash_detect.js\"></script>";
	echo "<script>window.setTimeout(display_flash_version(),500); window.setTimeout(display_browser_version(),800); window.setTimeout(checkspeed(), 2500);</script>";
}
if (!isset($_GET['guestname'])) {
	echo "</main>";
}
require_once('inc/footer.php');
?>