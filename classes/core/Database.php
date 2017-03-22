<?php
class Database {
	private $_db;
	private $_conn;
	private $_type;
	private $_path;
	private $_host;
	private $_user;
	private $_pass;
	private $_name;
	private $_isCon = false;	
	private $_utf8 = true;
	
	public function __construct($user,$pass,$name,$host) {
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_name = $name;
		$this->_host = $host;
	    $this->_conn = "mysql:host=".$this->_host.";dbname=".$this->_name;
	}
	
	public function setUtf8($b) {
		$this->_utf8 = $b;	
	}
	
	public function execute($sql) {
    	$statement = $this->_db->prepare($sql);
    	$statement->execute();
    	//return $statement->fetchAll(PDO::FETCH_ASSOC);	
	}
	
	public function getDb() {
		return $this->_db;	
	}
	
	public function connect() {
 		if (!$this->_isCon) {
 			try {
 				if ($this->_utf8) {
 				    $this->_db = new PDO($this->_conn,$this->_user,$this->_pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
 				} else {
 					$this->_db = new PDO($this->_conn,$this->_user,$this->_pass);
 				}
 			    $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 			    $this->_isCon = true;
 			    return true;
 		    } catch (PDOException $e) {
 			    return false;
 			}
 		} else {
 			return true;
 		}
	}
	
	public function disconnect() {
	    if($this->isCon) {
	        unset($this->_db);
	        $this->_isCon = false;
	        return true;
    	}
	}
}