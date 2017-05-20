<?php
class GrcPool_Member_Host_Xml_OBJ extends GrcPool_Member_Host_Xml_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Member_Host_Xml_DAO extends GrcPool_Member_Host_Xml_MODELDAO {

	public function deleteWithMemberId($memberId) {
		$sql = 'delete from '.$this->getFullTableName().' where memberId = '.$memberId;
		$this->executeQuery($sql);
	}
	
	public function getWithMemberIdAndHostId($memberId,$hostId) {
		return $this->fetch(array($this->where('memberId',$memberId),$this->where('hostId',$hostId)));
	}
	
}