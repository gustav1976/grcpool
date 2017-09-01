<?php
class GrcPool_Pool_Stat_OBJ extends GrcPool_Pool_Stat_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Pool_Stat_DAO extends GrcPool_Pool_Stat_MODELDAO {

	public function getWithName($name) {
		return $this->fetchAll(array($this->where('name',$name)),array('theTime'=>'asc'));
	}
	
	public function getDailyStats($name,$since = 0) {
		$sql = '
			select 	count(*) as howmany, 
					sum(value) as value, 
					date(FROM_UNIXTIME(theTime)) as theDay
			from	'.$this->getFullTableName().'
			where	name = \''.$name.'\'
			GROUP BY DATE(FROM_UNIXTIME(theTIme))
			order by theDay
		';
		return $this->query($sql);
	}
	
}