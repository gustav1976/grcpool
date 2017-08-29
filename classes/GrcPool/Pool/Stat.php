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
	
}