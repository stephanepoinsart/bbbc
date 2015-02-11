<?php
/*
 * BBBC : BigBlueButton Control
* (c) Cellule d'Appui Pédagogique, Université de Technologie de Compiègne
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*/


/* Various functions to read / write the database */

require_once('config.php');
require_once('utility.php');

class Db {
	var $dbh;
	var $listconf_stmt;
	var $confexists_stmt;
	var $deleteconf_stmt;
	var $insertconf_stmt;
	var $activateconf_stmt;
	
	var $logguest_stmt;
	var $listguest_stmt;
	
	function __construct() {
		try {
			$this->dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_LOGIN, DB_PASSWORD);
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbh->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
		} catch (PDOException $e) {
			$this->dbh=null;
			showerror($e->getMessage());
			return;
		}
		try {
			$this->listconf_stmt=$this->dbh->prepare("SELECT id, confname, createtime FROM conf WHERE username=:username");
			$this->deleteconf_stmt=$this->dbh->prepare("DELETE FROM conf WHERE id=:id AND username=:username");
			$this->insertconf_stmt=$this->dbh->prepare("INSERT INTO conf (confname, username) VALUES (:confname, :username)");
			$this->confexists_stmt=$this->dbh->prepare("SELECT count(*) FROM conf WHERE confname=:confname AND username=:username");
			
			$this->logguest_stmt=$this->dbh->prepare("INSERT INTO lastlogin (confid, guestname) SELECT conf.id, :guestname FROM conf WHERE username=:username AND confname=:confname");
		} catch (PDOException $e) {
			$this->dbh=null;
			showerror($e->getMessage());
			return;
		}
	}

	function listconf($username) {
		try {
			$this->listconf_stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$this->listconf_stmt->execute();
			return $this->listconf_stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			showerror($e->getMessage());
			return null;
		}
	}
	
	function confexists($username, $confname) {
		try {
			$this->confexists_stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$this->confexists_stmt->bindValue(':confname', $confname, PDO::PARAM_STR);
			$this->confexists_stmt->execute();
			$item=$this->confexists_stmt->fetch(PDO::FETCH_NUM);
			return ($item[0]);
		} catch (PDOException $e) {
			showerror($e->getMessage());
			return null;
		}
	}

	/*
	 * return the conf id
	 */
	function insertconf($username, $confname) {
		try {
			$this->insertconf_stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$this->insertconf_stmt->bindValue(':confname', $confname, PDO::PARAM_STR);
			$this->dbh->beginTransaction();
			// strangly, for PDO lastInsertId is not reliable on mysql... 
			//$lastid=$this->dbh->lastInsertId();
			$this->insertconf_stmt->execute();
			$this->dbh->commit();
			/*if (!$lastid)
				showerror("Erreur de base de donnée. L'insertion de la conférence dans la base de donnée n'a pas fonctionnée");
			else
				return $lastid;
			*/
			return true;
		} catch (PDOException $e) {
			showerror("Impossible de créer la conférence. Existe-t'elle déjà ?<br>".$e->getMessage());
			return null;
		}
	}

	function deleteconf($username, $id) {
		try {
			$this->deleteconf_stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$this->deleteconf_stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$this->dbh->beginTransaction();
			$this->deleteconf_stmt->execute();
			if ($this->deleteconf_stmt->rowCount()==0) {
				showerror("La conférence pour laquelle vous avez demandé la suppression n'a pas pu être trouvée. Si la même demande de suppression a été envoyée plusieurs fois par accident, vous pouvez ignorer ce message.");
			}
			if ($this->deleteconf_stmt->rowCount()>1) {
				showerror("Une erreur de base de donnée s'est produite lors de la suppression de la conférence : suppression d'une conférence avec doublons.");
			}
			$this->dbh->commit();
			return true;
		} catch (PDOException $e) {
			showerror($e->getMessage());
			return false;
		}
	}
	
	function activateconf($username, $id, $active) {
		
	}
	
	/*
	 * return the conf id
	 */
	function logguest($username, $confname, $guestname) {
		try {
			$this->logguest_stmt->bindValue(':guestname', $guestname, PDO::PARAM_STR);
			$this->logguest_stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$this->logguest_stmt->bindValue(':confname', $confname, PDO::PARAM_STR);
			$this->dbh->beginTransaction();
			// strangly, for PDO lastInsertId is not reliable on mysql...
			//$lastid=$this->dbh->lastInsertId();
			$this->logguest_stmt->execute();
			$this->dbh->commit();
			return true;
		} catch (PDOException $e) {
			showerror("Impossible de conserver $guestname dans la liste des participants à la conférence $confname de $username<br>".$e->getMessage());
			return null;
		}
	}
	
}

?>