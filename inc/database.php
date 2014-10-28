<?php

require_once('config.php');
require_once('utility.php');

class Db {
	var $dbh;
	var $listconf_stmt;
	var $confexists_stmt;
	var $deleteconf_stmt;
	var $insertconf_stmt;
	var $activateconf_stmt;
	
	function __construct() {
		try {
			$this->dbh = new PDO("mysql:host=localhost;dbname=".DB_NAME, DB_LOGIN, DB_PASSWORD);
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
}

?>