<?php
class GrcPool_Session_OBJ extends GrcPool_Session_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Session_DAO extends GrcPool_Session_MODELDAO {

	public function deleteWithUserId($memberId) {
		$sql = 'delete from '.$this->getFullTableName().' where userid = '.$memberId;
		$this->executeQuery($sql);
	}
	
	public function cleanup() {
		$thirtyDays = time()-(86400*30);
		$sql = 'delete from '.$this->getFullTableName().' where lastUsed < '.$thirtyDays.'';
		$this->executeQuery($sql);
	}
	
	public function getWithUserId($memberId) {
		return $this->fetch(array($this->where('userid',$memberId)));
	}

	public function getActiveSession($memberId) {
		return $this->fetch(array($this->where('userid',$memberId),$this->where('disable',0)));
	}
	
	
	public function disableWithUserId($userid) {
		$sql = 'update '.$this->getFullTableName().' set disable = 1 where userid = '.$userid;
		$this->executeQuery($sql);
	}
	
}