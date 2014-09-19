<?php
$pagetitle="BBB conference";

require_once('config.php');
require_once('database.php');
require_once('utility.php');


$login="spoinsar";
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

if (!isset($_GET['role']) || $_GET['role']!='admin') {
	$role='user';
} else {
	$role='admin';
}

if (!isset($_GET['hash'])) {
	showerror("Le lien de la conférence ne semble pas valide. Vérifiez qu'il ai bien été copié en entier. [hash manquant]");
}
$hash=$_GET['hash'];


$db=new Db;
if (!$db->confexists($confcreator, $confname)) {
	showerror("la conférence $confname de l'utilisateur $confcreator a été supprimée ou désactivée. Recontactez l'utilisateur pour lui demander de recréer la conférence ou vous donner un autre lien.");
}

// ### TODO : validate checksum !!!


include('header.php');

if (!isset($_GET['guestname'])) {
	echo "<h2>Comment vous appelez-vous ?</h2>
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
	}
	showbar();
}

function showbar() {
	echo "<div id=\"bar\">";
	echo "<a href=\"".SITE_URL."\">H<img alt=\"Créez et gérrez vos conférences (utilisateurs UTC)\" src=\"\"></a>";
	echo "<a id=\"btn_fs\" href=\"#\" onClick=\"toggleFullScreen();\">F<img alt=\"Plein écran\" src=\"\"></a>";
	echo "</div>";
}

echo "<script src=\"fullscreen.js\"></script>";

include('footer.php');
?>