<?php
abstract class DAO {

	private $_db;
	private $_inTransaction;
	private $_tranError;
	private $_error;
	
    public function __construct() {
    	global $DATABASE;
		$this->_tranError = false;
		$this->_inTransaction = false;
    	$this->_db = $DATABASE->getDb();
    }
    
    public function getDb() {
    	return $this->_db;
    }
    public function getError() {
    	return $this->_error;
    }
    public function beginTransaction() {
    	if (!$this->_inTransaction) {
	    	$this->_db->beginTransaction();	    		
    		$this->_inTransaction = true;
    		$this->_tranError = false;	
    		$this->_error = '';
    	}
    }
    public function isTransactionError() {
    	return $this->_tranError;
    }
    public function getLastInsertId() {
	    return $this->getDb()->lastInsertId();
    }
    public function endTransaction() {
    	$this->_inTransaction = false;
    	if ($this->_tranError) {
    		$this->rollback();	
    		return false;
    	} else {
    		$this->commit();
    		return true;
    	}
    }
    private function commit() {
    	$this->_db->commit();	
    	$this->_inTransaction = false;
    }
    public function rollback() {
    	$this->_db->rollBack();	
    	$this->_inTransaction = false;
    }
    public function isError() {
    	return !$this->_error=='';	
    }
    public function execute(&$statement) {
    	if (!$this->_inTransaction) {
    		$this->_error = '';
    	}
    	try {
    		$statement->execute();
    	} catch (Exception $e) {
    		error_log($e);
    		if ($this->_inTransaction) {
    			$this->_tranError = true;	
    		}
    		$this->_error = $e;
    	}
    }
    public function executeQuery($sql) {
    	$statement = $this->_db->prepare($sql);
		$this->execute($statement);
    }
    public function query($sql) {
    	$statement = $this->_db->prepare($sql);
		$this->execute($statement);
    	return $statement->fetchAll(PDO::FETCH_ASSOC);	
    }    
    public function isDuplicateError() {
    	if (strstr($this->getError(),'Integrity constraint violation') && strstr($this->getError(),'Duplicate entry')) {
    		return true;
    	} else {
    		return false;
    	}
    }


}