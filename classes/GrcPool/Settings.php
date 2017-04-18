<?php
class GrcPool_Settings_OBJ extends GrcPool_Settings_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Settings_DAO extends GrcPool_Settings_MODELDAO {

	public function getValueWithName($name) {
		$obj = $this->fetch(array($this->where('theName',$name)));
		if ($obj != null) {
			return $obj->getTheValue();
		} else {
			return null;
		}
	}
	
	public function setValueWithName($name,$value) {
		$obj = $this->fetch(array($this->where('theName',$name)));
		if ($obj == null) {
			$obj = new GrcPool_Settings_OBJ();
		}
		$obj->setTheName($name);
		$obj->setTheValue($value);
		$this->save($obj);
	}
	
}