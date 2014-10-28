<?php
$pagetitle="BBB control";

require_once('inc/config.php');
require_once('inc/database.php');
require_once('inc/utility.php');


require_once('inc/cas.php');

if (!$login) {
	showerror('Problème d\'authentification... Login / mot de passe incorrect ?');
}

$db=new Db;

if (isset($_POST['create'])) {
	createconf($_POST['newconfname']);
}

if (isset($_POST['delete'])) {
	deleteconf($_POST['id'], $_POST['confname']);
}

$conflist=$db->listconf($login);

require_once('inc/header.php');
if (!$login)
	showerror("Impossible de valider votre login, réessayez de vous connecter...");

function createconf($newconfname) {
	global $login, $db;
	if (!$newconfname) {
		showerror("Vous devez choisir un nom de conférence");
	}
	if (strlen($newconfname)>180) {
		showerror("Le nom de conférence est trop long");
	}
	if ($db->insertconf($login, $newconfname)) {
		showsuccess("La conférence $newconfname a bien été créée");
	}
}

function deleteconf($id, $confname) {
	global $login, $db;
	if (!isset($id)||$id<=0) {
		showerror("Erreur de chargement de la page : impossible d'identifier le numéro de conf à supprimer");
	}
	if ($db->deleteconf($login, $id)) {
		showsuccess("La conférence $confname a bien été supprimée");
	}
}


function display_conflist($conflist) {
	global $login;
	
	echo "<table><thead><tr><th>Nom</th><th>Lien admin</th><th>Lien utilisateur</th><th>Date de création</th><th>suppr.</th></tr></thead><tbody>";
	
	foreach ($conflist as $id => $data) {
		$adminurl=gensiteurl($login, $id, $data['confname'], 'admin');
		$userurl=gensiteurl($login, $id, $data['confname'], 'user');
		echo "<tr>
				<th>".$data['confname']."</th>
				<td><a href=\"$adminurl\">lien admin</a></td>
				<td><a href=\"$userurl\">lien utilisateur</a></td>
				<td>".$data['createtime']."</td>
				<td>
						<form method=\"POST\" action=\"".SITE_URL."\"/>
						<input type=\"hidden\" name=\"id\" value=\"".$id."\"/>
						<input type=\"hidden\" name=\"confname\" value=\"".$data['confname']."\"/>
						<input type=\"submit\" name=\"delete\" value=\"x\" alt=\"supprimer webconf ".$data['confname']."\" title=\"supprimer\"/>
						</form>
				</td>
			</tr>";
	}
	echo "</tbody></table>";
}

echo "<header>
			<h1>Gérrez vos webconférences</h1>
			<div id=\"loginblock\"><span id=\"logindisplay\">$login</span> <span id=\"logout\"><form method=\"POST\" action=\"?logout\"><input type=\"submit\" value=\"déconnexion\"></input></form></span></div>
	</header>
	<main>";

showbuffered();
?>
<h2>Comment organiser votre conférence ?</h2>
<ul>
<li>Créez au moins une conférence (ou plusieurs pour isoler les participants, plus sécurisé...)</li>
<li>Donnez le lien "admin" aux orateurs en qui vous avez confiance, et le lien "utilisateurs" aux autres participants (étudiants...)</li>
<li>Demandez à chaque participant de tester à l'avance pour éviter d'être surpris par des problèmes (microphone...)</li>
</ul>
<p>Contact : <a href="mailto:cap@utc.fr">Cellule d'Appui Pédagogique &lt;cap@utc.fr&gt;</a></p>

<?php 
echo "<h2>Créer une nouvelle conf</h2>
	<form method=\"POST\" action=\"".SITE_URL."\">
		<label for=\"newconfname\">Nom de conférence :</label>
		<input type=\"text\" name=\"newconfname\" id=\"newconfname\"/>
		<input type=\"submit\" name=\"create\" value=\"Créer\"/>
	</form>
	<h2>Liste des confs</h2>";

display_conflist($conflist);


echo "</main>";
require_once('inc/footer.php');
?>