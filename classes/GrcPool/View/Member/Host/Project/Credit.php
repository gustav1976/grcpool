<?php
class GrcPool_View_Member_Host_Project_Credit_OBJ extends GrcPool_View_Member_Host_Project_Credit_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_View_Member_Host_Project_Credit_DAO extends GrcPool_View_Member_Host_Project_Credit_MODELDAO {

	public function getOwed($min = 0) {
		return $this->fetchAll(array($this->where('owed',$min,'>')));
	}
	
	public function getWithMemberId($id) {
		return $this->fetchAll(array($this->where('id',$id)),array('hostName'=>'asc'));
	}
	
	public function getOwedForMember($id) {
		$sql = 'select sum(owed) as owed from '.$this->getFullTableName().' where id = '.addslashes($id).'';
		$result = $this->query($sql);
		if (isset($result[0]['owed'])) {
			return $result[0]['owed'];
		} else {
			return 0;
		}
		
	}
	
	public function getTopAccounts($limit) {
		$sql = 'select username,sum(mag) as magTotal from '.$this->getFullTableName().' group by username order by magTotal desc limit '.$limit;
		return $this->query($sql);
	}
	
	public function getTopHosts($limit) {
		$sql = 'select username,hostId,sum(mag) as magTotal from '.$this->getFullTableName().' group by username,hostId order by magTotal desc limit '.$limit;
		return $this->query($sql);
	}
	
	public function getOwedWithNowAddress() {
		return $this->fetchAll(array($this->where('owed','0','>'),$this->where('grcAddress','')));
	}
	

	
}