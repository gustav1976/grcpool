<?php
class GrcPool_Boinc_Account_OBJ extends GrcPool_Boinc_Account_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Boinc_Account_DAO extends GrcPool_Boinc_Account_MODELDAO {
	
	/**
	 * 
	 * @return GrcPool_Boinc_Account_OBJ[]
	 */
	public function getWhitelistedProjects() {
		return $this->fetchAll(array($this->where('whiteList',1)));
	}

	/**
	 * 
	 * @param string $name
	 * @return NULL|GrcPool_Boinc_Account_OBJ
	 */
	public function getWithGrcName(string $name) {
		return $this->fetch(array($this->where('grcname',$name)));
	}
	
}