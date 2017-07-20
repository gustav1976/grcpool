<?php
class GrcPool_Boinc_Account_User_OBJ extends GrcPool_Boinc_Account_User_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Boinc_Account_User_DAO extends GrcPool_Boinc_Account_User_MODELDAO {
	
	public function deleteWithAccountId($accountId) {
		$this->executeQuery('delete from '.$this->getFullTableName().' where accountId = '.$accountId);
	}
	
	public function getWithAccountId($accountId) {
		return $this->fetchAll(array($this->where('accountId',$accountId)),array('avgCredit'=>'desc'));
	}

}