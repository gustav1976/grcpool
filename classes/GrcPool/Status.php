<?php
class GrcPool_Status_OBJ extends GrcPool_Status_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Status_DAO extends GrcPool_Status_MODELDAO {

	public function getWithName($name) {
		return $this->fetch(array($this->where('name',$name)));
	}
	
	
	public function isInSync() {
		$objs = $this->fetchAll();
		$highest = 0;
		foreach ($objs as $obj) {
			if ($obj->getBlock() > $highest) {
				$highest = $obj->getBlock();
			}
		}
		$inSync = true;		
		foreach ($objs as $obj) {
			if ($obj->getBlock() != $highest || $obj->getDiff() < .35) {
				$inSync = false;
			}
		}			
		return $inSync;
	}
	
	
}