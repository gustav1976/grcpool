<?php
class GrcPool_Member_Notice_OBJ extends GrcPool_Member_Notice_MODEL {
	
	const NOTICE_DELETE = 1;
	
	public function __construct() {
		parent::__construct();
	}


	
}

class GrcPool_Member_Notice_DAO extends GrcPool_Member_Notice_MODELDAO {

	public function isNoticeForMembeAndId($memberId,$noticeId) {
		 $obj = $this->fetch(array($this->where('noticeId',$noticeId),$this->where('memberId',$memberId)));
		 return $obj!=null?true:false;
	}
	
}