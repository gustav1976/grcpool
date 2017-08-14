<?php
class GrcPool_Boinc_Account_OBJ extends GrcPool_Boinc_Account_MODEL {
	public function __construct() {
		parent::__construct();
	}
	
// 	public function getStrongKeyForPoolId($poolId) {
// 		switch ($poolId) {
// 			case 2 : return $this->getStrongKey2();
// 			default : return $this->getStrongKey();
// 		}
// 	}
	
// 	public function getWeakKeyForPoolId($poolId) {
// 		switch ($poolId) {
// 			case 2 : return $this->getWeakKey2();
// 			default : return $this->getWeakKey();
// 		}
// 	}
}

class GrcPool_Boinc_Account_DAO extends GrcPool_Boinc_Account_MODELDAO {
	
	public function getWhitelistedProjects() {
		return $this->fetchAll(array($this->where('whiteList',1)));
	}
	
// 	public function initWithUrl($url) {
// 		return $this->fetch(array($this->where('url',$url)));
// 	}

	public function getWithGrcName($name) {
		return $this->fetch(array($this->where('grcname',$name)));
	}
	
}