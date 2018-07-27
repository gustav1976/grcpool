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

	public function deleteWithMemberId($memberId) {
		$sql = 'delete from '.$this->getFullTableName().' where memberId = '.$memberId;
		$this->executeQuery($sql);
	}
	
	/**
	 * 
	 * @param unknown $memberId
	 * @param unknown $cpid
	 * @return GrcPool_Member_Host_Project_OBJ[]
	 */
	public function getWithMemberIdAndHostCpid($memberId,$cpid) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('hostCpid',$cpid)));
	}
	
	public function getWithMemberId($memberId) {
		return $this->fetchAll(array($this->where('memberId',$memberId)));
	}
	
 	public function getWithMemberIdAndDbidAndAccountId($memberId,$dbid,$accountId) {
 		return $this->fetch(array($this->where('memberId',$memberId),$this->where('hostDbid',$dbid),$this->where('accountId',$accountId)));
 	}
	
 	public function getWithMemberIdAndCpidAndAccountIdAndPoolId($memberId,$cpid,$accountId,$poolId) {
 		return $this->fetch(array($this->where('memberId',$memberId),$this->where('poolId',$poolId),$this->where('hostCpid',$cpid),$this->where('accountId',$accountId)));
 	}
	
	public function getHostIdsWithErrors($memberId) {
		$datas = $this->fetchAll(array($this->where('memberid',$memberId),$this->where('hostDbid',0)));
		$ids = array();
		foreach ($datas as $data) {
			$ids[$data->getHostId()] = 1;
		}
		return $ids;
	}
	
	public function getActiveProjectForHost($hostId,$accountId,$poolId) {
		return $this->fetch(array($this->where('poolId',$poolId),$this->where('attached',2,'!='),$this->where('hostId',$hostId),$this->where('accountId',$accountId)));
	}
	
	public function getWithMemberIdAndHostId($memberId,$hostId) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('hostId',$hostId)));
	}
	
	public function getWithMemberIdAndDbid($memberId,$dbId) {
		return $this->fetch(array($this->where('memberId',$memberId),$this->where('hostDbid',$dbId)));
	}

 	public function getWithHostDbIdAndAccountId($hostDbId,$accountId) {
 		return $this->fetchAll(array($this->where('hostDbid',$hostDbId),$this->where('accountId',$accountId)));
 	}
	
// 	public function getWithHostDbIdAndAccountId($hostDbId,$projectId) {
// 		return $this->fetchAll(array($this->where('hostDbid',$hostDbId),$this->where('projectUrl',$projectId)));
// 	}
	
	
	public function deleteWithMemberIdAndHostId($memberId,$hostId) {
		$sql = 'delete from '.$this->getFullTableName().' where hostId = '.addslashes($hostId).' and memberId = '.addslashes($memberId).'';
		$this->executeQuery($sql);
	}
}
