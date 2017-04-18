<?php
class GrcPool_Member_Host_Project_OBJ extends GrcPool_Member_Host_Project_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Member_Host_Project_DAO extends GrcPool_Member_Host_Project_MODELDAO {

	// NOT CORRECT RETURNS MULTPLES OF HOSTS
// 	public function getWithMemberIdAndProjectUrl($memberId,$projUrl) {
// 		return $this->fetch(array($this->where('memberId',$memberId),$this->where('projectUrl',$projUrl)));
// 	}

	/**
	 * 
	 * @param unknown $memberId
	 * @param unknown $cpid
	 * @return GrcPool_Member_Host_Project_OBJ[]
	 */
	public function getWithMemberIdAndHostCpid($memberId,$cpid) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('hostCpid',$cpid)));
	}
	
	public function getWithMemberIdAndDbidAndProjectUrl($memberId,$dbid,$url) {
		return $this->fetch(array($this->where('memberId',$memberId),$this->where('hostDbid',$dbid),$this->where('projectUrl',$url)));
	}
	
	public function getWithMemberIdAndCpidAndProjectUrl($memberId,$cpid,$url) {
		return $this->fetch(array($this->where('memberId',$memberId),$this->where('hostCpid',$cpid),$this->where('projectUrl',$url)));
	}
	
	public function getWithHostIdAndProjectUrl($hostId,$url) {
		return $this->fetch(array($this->where('hostId',$hostId),$this->where('projectUrl',$url)));
	}
	
	public function getWithMemberIdAndHostId($memberId,$hostId) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('hostId',$hostId)));
	}
	
	public function getWithMemberIdAndDbid($memberId,$dbId) {
		return $this->fetch(array($this->where('memberId',$memberId),$this->where('hostDbid',$dbId)));
	}

	public function getWithHostDbIdAndProjectUrl($hostDbId,$url) {
		return $this->fetchAll(array($this->where('hostDbid',$hostDbId),$this->where('projectUrl',$url)));
	}
	
	public function deleteWithMemberIdAndHostId($memberId,$hostId) {
		$sql = 'delete from '.$this->getFullTableName().' where hostId = '.addslashes($hostId).' and memberId = '.addslashes($memberId).'';
		$this->executeQuery($sql);
	}
}
