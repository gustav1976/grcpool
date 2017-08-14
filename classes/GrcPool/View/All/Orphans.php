<?php
class GrcPool_View_All_Orphans_OBJ extends GrcPool_View_All_Orphans_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_View_All_Orphans_DAO extends GrcPool_View_All_Orphans_MODELDAO {

	public function getAvailableOrphanPayoutsForPool($poolId,$minAmount) {
		$sql = 'select * from '.$this->getFullTableName().' where poolId = '.$poolId.' and (mag = 0.00 or owed >= '.$minAmount.') and (memberIdPayout != 0 or memberIdCredit != 0) order by owed desc';
		return $this->queryObjects($sql);
	}
	
	public function getOrphansForMember($memberId) {
		return $this->fetchAll(array($this->where('memberIdPayout',$memberId),$this->where('mag',0,'>')));
	}
	
}
