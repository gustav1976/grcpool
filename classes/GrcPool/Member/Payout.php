<?php
class GrcPool_Member_Payout_OBJ extends GrcPool_Member_Payout_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Member_Payout_DAO extends GrcPool_Member_Payout_MODELDAO {
	
	public function getTotalForMemberId($memberId) {
		$sql = 'select sum(amount) as total from '.$this->getFullTableName().' where memberId = '.$memberId;
		$results = $this->query($sql);
		if (isset($results[0]) && isset($results[0]['total'])) {
			return $results[0]['total'];
		} else {
			return 0;
		}
	}
	
	public function getWithMemberIdSince($memberId,$since) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('thetime',$since,'>=')),array('thetime'=>'asc'));
	}
	
	public function getTopEarners($limit) {
		$sql = 'select memberId,username,sum(amount) as totalAmount from '.$this->getFullTableName().' group by memberId,username order by totalAmount desc limit '.$limit;
		return $this->query($sql);
	}
	
	public function getTotalAmountForPool($poolId) {
		$sql = 'select sum(amount) as totalAmount from '.$this->getFullTableName().' where poolId = '.addslashes($poolId);
		$result = $this->query($sql);
		return $result[0]['totalAmount'];
	}
	
	public function getTotalAmount() {
		$sql = 'select sum(amount) as totalAmount from '.$this->getFullTableName();
		$result = $this->query($sql);
		return $result[0]['totalAmount'];
	}
	
	public function getTopDonators($limit) {
		$sql = 'select memberId,username,sum(donation) as totalAmount from '.$this->getFullTableName().' group by memberId,username order by totalAmount desc limit '.$limit;
		return $this->query($sql);
	}
	
	public function getLatest($limit = array()) {
		return $this->fetchAll(array(),array('thetime' => 'desc'),$limit);
	}
	
	public function getCountForUser($id) {
		return $this->getCount(array($this->where('memberId',$id)));
	}
	
	public function getWithMemberId($id,$limit = array()) {
		return $this->fetchAll(array($this->where('memberId',$id)),array('thetime' => 'desc'),$limit);
	}
	
	public function getPayoutTotalForUser($id) {
		$sql = 'select sum(amount) as total from '.$this->getFullTableName().' where memberId = '.$id;
		$result = $this->query($sql);
		return $result[0]['total'];
	}
	
}