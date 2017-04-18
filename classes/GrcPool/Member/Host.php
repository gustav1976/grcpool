<?php
class GrcPool_Member_Host_OBJ extends GrcPool_Member_Host_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Member_Host_DAO extends GrcPool_Member_Host_MODELDAO {

	public function initWithMemberIdAndCpId($memberId,$cpid) {
		return $this->fetch(array($this->where('memberId',$memberId),$this->where('cpId',$cpid)));
	}
	
	public function getWithMemberId($memberId) {
		return $this->fetchAll(array($this->where('memberId',$memberId)));
	}
	
}