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
	
}