<?php
class GrcPool_Sparc_OBJ extends GrcPool_Sparc_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Sparc_DAO extends GrcPool_Sparc_MODELDAO {

	public function getNeededToPay() {
		return $this->fetchAll(array($this->where('paid',0),$this->where('sparc',0,'>')));
	}
	
	public function getTotalPerProject() {
		$sql = '
			select accountId,name,sum(sparc) as sparc
			from '.$this->getFullTableName().'
			group by accountId,name order by name
		';
		$results = $this->query($sql);
		return $results;
	}
	
}