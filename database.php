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
		} catch (PDOException $e) {
			echo $e->getMessage();
			$this->dbh=null;
			return;
		}
		try {
			$this->listconf_stmt=$this->dbh->prepare("SELECT id, confname, createtime FROM conf WHERE username=:username");
			$this->deleteconf_stmt=$this->dbh->prepare("DELETE FROM conf WHERE id=:id AND username=:username");
			$this->insertconf_stmt=$this->dbh->prepare("INSERT INTO conf (confname, username) VALUES (:confname, :username)");
			$this->confexists_stmt=$this->dbh->prepare("SELECT count(*) FROM conf WHERE confname=:confname AND username=:username");
		} catch (PDOException $e) {
			echo $e->getMessage();
			$this->dbh=null;
			return;
		}
	}

	function listconf($username) {
		try {
			$this->listconf_stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$this->listconf_stmt->execute();
			return $this->listconf_stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo $e->getMessage();
			return null;
		}
	}
	
	function confexists($username, $confname) {
		try {
			$this->confexists_stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$this->confexists_stmt->bindValue(':confname', $confname, PDO::PARAM_STR);
			$this->confexists_stmt->execute();
			return ($this->confexists_stmt->fetch(PDO::FETCH_NUM)[0]);
		} catch (PDOException $e) {
			echo $e->getMessage();
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
			$this->insertconf_stmt->execute();
			$this->dbh->commit();
			return $this->dbh->lastInsertId();
		} catch (PDOException $e) {
			echo $e->getMessage();
			return null;
		}
	}

	function deleteconf($username, $id) {
		try {
			$this->deleteconf_stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$this->deleteconf_stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$this->dbh->beginTransaction();
			$this->deleteconf_stmt->execute();
			$this->dbh->commit();
			return true;
		} catch (PDOException $e) {
			echo $e->getMessage();
			return false;
		}
	}
	
	function activateconf($username, $id, $active) {
		
	}
}

?>