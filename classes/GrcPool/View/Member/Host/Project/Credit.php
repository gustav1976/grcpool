<?php
class GrcPool_View_Member_Host_Project_Credit_OBJ extends GrcPool_View_Member_Host_Project_Credit_MODEL {
	public function __construct() {
		parent::__construct();
	}
	
	public function getId() {
		return $this->getMemberId();
	}
}

class GrcPool_View_Member_Host_Project_Credit_DAO extends GrcPool_View_Member_Host_Project_Credit_MODELDAO {
	
	public function getWithMemberIdAndHostId($memberId,$hostId) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('hostId',$hostId)));
	}
	
	public function getOwed($min = 0) {
		return $this->fetchAll(array($this->where('owed',$min,'>')));
	}
	
	public function getOwedForPool($poolId,$min = 0) {
		return $this->fetchAll(array($this->where('projectPoolId',$poolId),$this->where('owed',$min,'>')));
	}
	
	public function getWithMemberId($id) {
		return $this->fetchAll(array($this->where('memberId',$id)),array('hostName'=>'asc'));
	}
	
	public function getOwedForMember($id) {
		$sql = 'select sum(owed) as owed from '.$this->getFullTableName().' where memberId = '.addslashes($id).'';
		$result = $this->query($sql);
		if (isset($result[0]['owed'])) {
			return $result[0]['owed'];
		} else {
			return 0;
		}
		
	}
	
	public function getTopAccounts($limit) {
		$sql = 'select memberId,username,sum(mag) as magTotal from '.$this->getFullTableName().' group by memberId,username order by magTotal desc limit '.$limit;
		return $this->query($sql);
	}
	
	public function getTopHosts($limit) {
		$sql = 'select memberId,poolId,username,hostId,sum(mag) as magTotal from '.$this->getFullTableName().' group by memberId,poolId,username,hostId order by magTotal desc limit '.$limit;
		return $this->query($sql);
	}
	
	public function getOwedWithNowAddress() {
		return $this->fetchAll(array($this->where('owed','0','>'),$this->where('grcAddress','')));
	}
	

	
}