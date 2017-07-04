<?php
class GrcPool_View_Member_Payout_OBJ extends GrcPool_View_Member_Payout_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_View_Member_Payout_DAO extends GrcPool_View_Member_Payout_MODELDAO {

	public function getLatest($limit = array()) {
		return $this->fetchAll(array(),array('thetime' => 'desc'),$limit);
	}
	
	public function getWithMemberId($id,$limit = array()) {
		return $this->fetchAll(array($this->where('id',$id)),array('thetime' => 'desc'),$limit);
	}
	
	public function getTopDonators($limit) {
		$sql = 'select id,username,sum(donation) as totalAmount from '.$this->getFullTableName().' group by id,username order by totalAmount desc limit '.$limit;
		return $this->query($sql);
	}
	
	public function getTopEarners($limit) {
		$sql = 'select id,username,sum(amount) as totalAmount from '.$this->getFullTableName().' group by id,username order by totalAmount desc limit '.$limit;
		return $this->query($sql);
	}
	
	public function getTotalAmount() {
		$sql = 'select sum(amount) as totalAmount from '.$this->getFullTableName();
		$result = $this->query($sql);
		return $result[0]['totalAmount'];
	}
	
	public function getTotalAmountForPool($poolId) {
		$sql = 'select sum(amount) as totalAmount from '.$this->getFullTableName().' where poolId = '.$poolId;
		$result = $this->query($sql);
		return $result[0]['totalAmount'];
	}
	
	
	public function getCountForUser($id) {
		return $this->getCount(array($this->where('id',$id)));
	}
	
	public function getPayoutTotalForUser($id) {
		$sql = 'select sum(amount) as total from '.$this->getFullTableName().' where id = '.$id;
		$result = $this->query($sql);
		return $result[0]['total'];
	}
	
}

