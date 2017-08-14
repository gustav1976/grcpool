<?php
class GrcPool_Member_Host_Stat_Mag_OBJ extends GrcPool_Member_Host_Stat_Mag_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Member_Host_Stat_Mag_DAO extends GrcPool_Member_Host_Stat_Mag_MODELDAO {

	public function deleteWithMemberId($memberId) {
		$sql = 'delete from '.$this->getFullTableName().' where memberId = '.$memberId;
		$this->executeQuery($sql);
	}
	
	public function getWithMemberId($memberId,$since = 0) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('thetime',$since,'>=')),array('accountId'=>'asc','thetime'=>'asc'));
	}
	
	public function getMagDataWithMemberId($memberId,$since = 0) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('thetime',$since,'>='),$this->where('mag',0,'>')),array('accountId'=>'asc','thetime'=>'asc'));
	}
}