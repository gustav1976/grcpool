<?php
class GrcPool_Member_Payout_OBJ extends GrcPool_Member_Payout_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Member_Payout_DAO extends GrcPool_Member_Payout_MODELDAO {
	
	public function getTotalForMemberId($memberId,$currency = Constants::CURRENCY_GRC) {
		$sql = 'select sum(amount) as total from '.$this->getFullTableName().' where memberId = '.$memberId.' and currency = \''.$currency.'\'';
		$results = $this->query($sql);
		if (isset($results[0]) && isset($results[0]['total'])) {
			return $results[0]['total'];
		} else {
			return 0;
		}
	}
	
	public function getCurrencyCount($currency) {
		$result = $this->query('select count(*) as howmany from '.$this->getFullTableName().' where currency = \''.$currency.'\'');
		return $result[0]['howmany'];
	}
	
	public function getEarningsStats($grouping,$memberId,$since,$currency = Constants::CURRENCY_GRC) {
		$group = 'date';
		switch ($grouping) {
			case 'week' : $group = 'yearweek';break;
		}
		$sql = '
			select 	sum(amount) as amount,
					'.$group.'(FROM_UNIXTIME(theTime)) as theTime
			from	'.$this->getFullTableName().'
			where	memberId = \''.$memberId.'\'
			and		theTime > '.$since.'
			GROUP BY '.$group.'(FROM_UNIXTIME(theTIme))
			order by theTime
		';
		return $this->query($sql);
	}
	
	public function getWithMemberIdSince($memberId,$since,$currency = Constants::CURRENCY_GRC) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('thetime',$since,'>='),$this->where('currency',$currency)),array('thetime'=>'asc'));
	}
	
	public function getTopEarners($limit,$currency = Constants::CURRENCY_GRC) {
		$sql = 'select memberId,username,sum(amount) as totalAmount from '.$this->getFullTableName().' where currency = \''.$currency.'\' group by memberId,username order by totalAmount desc limit '.$limit;
		return $this->query($sql);
	}
	
	public function getTotalAmountForPool($poolId,$currency = Constants::CURRENCY_GRC) {
		$sql = 'select sum(amount) as totalAmount from '.$this->getFullTableName().' where currency = \''.$currency.'\' and poolId = '.addslashes($poolId);
		$result = $this->query($sql);
		return $result[0]['totalAmount'];
	}
	
	public function getTotalAmount($currency = Constants::CURRENCY_GRC) {
		$sql = 'select sum(amount) as totalAmount from '.$this->getFullTableName().' where currency = \''.$currency.'\'';
		$result = $this->query($sql);
		return $result[0]['totalAmount'];
	}
	
	public function getTopDonators($limit) {
		$sql = 'select memberId,username,sum(donation) as totalAmount from '.$this->getFullTableName().' group by memberId,username order by totalAmount desc limit '.$limit;
		return $this->query($sql);
	}
	
	public function getLatest($limit = array(),$currency = Constants::CURRENCY_GRC) {
		return $this->fetchAll(array($this->where('currency',$currency)),array('thetime' => 'desc'),$limit);
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