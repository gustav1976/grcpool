<?php
class GrcPool_Boinc_Host_Blacklist_OBJ extends GrcPool_Boinc_Host_Blacklist_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Boinc_Host_Blacklist_DAO extends GrcPool_Boinc_Host_Blacklist_MODELDAO {

	/**
	 * 
	 * @param int $accountId
	 * @param int $dbid
	 * @return NULL|GrcPool_Boinc_Host_Blacklist_OBJ
	 */
	public function initWithAccountIdAndDbid(int $accountId,int $dbid) {
		return $this->fetch(array($this->where('accountId',$accountId),$this->where('hostDbid',$dbid)));
	}
	
}