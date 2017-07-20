<?php
class GrcPool_Boinc_Account_Stats_OBJ extends GrcPool_Boinc_Account_Stats_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Boinc_Account_Stats_DAO extends GrcPool_Boinc_Account_Stats_MODELDAO {
	
	public function getWithName($name) {
		return $this->fetchAll(array($this->where('name',$name)));
	}

	public function getWithAccountId($id) {
		return $this->fetchAll(array($this->where('accountId',$id)));
	}
	
}