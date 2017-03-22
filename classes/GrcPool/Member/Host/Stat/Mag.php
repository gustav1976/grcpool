<?php
class GrcPool_Member_Host_Stat_Mag_OBJ extends GrcPool_Member_Host_Stat_Mag_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Member_Host_Stat_Mag_DAO extends GrcPool_Member_Host_Stat_Mag_MODELDAO {

	public function getWithMemberId($memberId,$since = 0) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('thetime',$since,'>=')),array('projectUrl'=>'asc','thetime'=>'asc'));
	}
}