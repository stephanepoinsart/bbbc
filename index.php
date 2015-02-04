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
			<h1>Gérez vos webconférences</h1>
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
<p>Guide utilisateur : <a id="btn_help" href="http://ics.utc.fr/doc-cap/co/1-creer.html" target="_blank"><img class="icon" src="static/img/help.png"/> <span class="text">Documentation</span></a></p>
<p>Contact : <a href="mailto:cap@utc.fr">Cellule d'Appui Pédagogique &lt;cap@utc.fr&gt;</a></p>

<h2>A savoir si vous utilisez...</h2>
<ul>
<li>Fonction "Partage de documents" : convertissez toujours les fichiers à partager en PDF. Si vous envoyez directement des fichiers Word/Powerpoint/Excel ou OpenDocument, ceux-ci risquent de ne pas s'afficher de manière optimale.</li>
<li>Fonction "Partage d'affichage écran" : la personne qui montre son écran doit installer java, activer les applets java sur votre navigateur, et les autoriser pour le site bbb.utc.fr. Vérifiez ici que vous avez bien la version 1.7.0_72 ou supérieur. Ce n'est pas évident, donc n'hésitez pas à nous prévenir en cas de problème.</li>
</ul>


<?php 
echo "<h2>Créer une nouvelle conférence</h2>
	<form method=\"POST\" action=\"".SITE_URL."\">
		<label for=\"newconfname\">Nom de conférence :</label>
		<input type=\"text\" name=\"newconfname\" id=\"newconfname\"/>
		<input type=\"submit\" name=\"create\" value=\"Créer\"/>
	</form>
	<h2>Liste des conférences</h2>";

display_conflist($conflist);

?>

</main>

<?php
require_once('inc/footer.php');
?>