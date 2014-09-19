<?php
$pagetitle="BBB control";

require_once('config.php');
require_once('database.php');
require_once('utility.php');


$login="spoinsar";

$db=new Db;

if (isset($_POST['create'])) {
	createconf($_POST['newconfname']);
}

if (isset($_POST['delete'])) {
	deleteconf($_POST['id'], $_POST['confname']);
}

$conflist=$db->listconf($login);

include('header.php');





function createconf($newconfname) {
	global $login, $db;
	if (!$newconfname) {
		showerror("Vous devez choisir un nom de conférence");
	}
	if (strlen($newconfname)>180) {
		showerror("Le nom de conférence est trop long");
	}
	if ($db->insertconf($login, $newconfname)) {
		showsuccess("La conférence $s a bien été créée");
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
	
	echo "<table><tr><th>Nom</th><th>Lien admin</th><th>Lien utilisateur</th><th>Date de création</th><th>suppr.</th></tr>";
	
	foreach ($conflist as $id => $data) {
		$adminurl=gensiteurl($login, $id, $data['confname'], 'admin');
		$userurl=gensiteurl($login, $id, $data['confname'], 'user');
		echo "<tr>
				<td>".$data['confname']."</td>
				<td><a href=\"$adminurl\">lien admin</a></td>
				<td><a href=\"$userurl\">lien participant</a></td>
				<td>".$data['createtime']."</td>
				<td>
						<form method=\"POST\" action=\"".SITE_URL."\"/>
						<input type=\"hidden\" name=\"id\" value=\"".$id."\"/>
						<input type=\"hidden\" name=\"confname\" value=\"".$data['confname']."\"/>
						<input type=\"submit\" name=\"delete\" value=\"x\" title=\"supprimer\"/>
						</form>
				</td>
			</tr>";
	}
	echo "</table>";
}



echo "<h2>Créer une nouvelle conf</h2>
	<form method=\"POST\" action=\"".SITE_URL."\">
		<label for=\"newconfname\">Nom de conférence :</label>
		<input type=\"text\" name=\"newconfname\" id=\"newconfname\"/>
		<input type=\"submit\" name=\"create\" value=\"créer\"/>
	</form>
	<h2>Liste des confs</h2>";

display_conflist($conflist);

echo "</form>";
?>


<?php
include('footer.php');
?>