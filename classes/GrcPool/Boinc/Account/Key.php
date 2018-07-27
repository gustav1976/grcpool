<?php
class GrcPool_Boinc_Account_Key_OBJ extends GrcPool_Boinc_Account_Key_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Boinc_Account_Key_DAO extends GrcPool_Boinc_Account_Key_MODELDAO {

	public function getForPoolId($id) {
		return $this->fetchAll(array($this->where('poolId',$id)));
	}
	
	public function getWithAccountAndPool($id,$poolId) {
		return $this->fetch(array($this->where('accountId',$id),$this->where('poolId',$poolId)));
	}
	
}