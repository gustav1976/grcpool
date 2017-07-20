<?php
class GrcPool_Member_Host_OBJ extends GrcPool_Member_Host_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Member_Host_DAO extends GrcPool_Member_Host_MODELDAO {

	public function deleteWithMemberId($memberId) {
		$sql = 'delete from '.$this->getFullTableName().' where memberId = '.$memberId;
		$this->executeQuery($sql);
	}
	
	public function initWithMemberIdAndCpId($memberId,$cpid) {
		return $this->fetch(array($this->where('memberId',$memberId),$this->where('cpId',$cpid)));
	}
	
	public function getWithMemberId($memberId) {
		return $this->fetchAll(array($this->where('memberId',$memberId)));
	}
	
	public static function sortByDisplayedHostName($a,$b) {
		return strnatcasecmp($a->getCustomName()?$a->getCustomName():$a->getHostName(),$b->getCustomName()?$b->getCustomName():$b->getHostName());
	}
	
}