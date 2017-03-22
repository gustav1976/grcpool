<?php
class GrcPool_Boinc_Account_OBJ extends GrcPool_Boinc_Account_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Boinc_Account_DAO extends GrcPool_Boinc_Account_MODELDAO {
	
	public function getWhitelistedProjects() {
		return $this->fetchAll(array($this->where('whiteList',1)));
	}
	
	public function initWithUrl($url) {
		return $this->fetch(array($this->where('url',$url)));
	}

}