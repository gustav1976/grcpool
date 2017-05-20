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
	
}