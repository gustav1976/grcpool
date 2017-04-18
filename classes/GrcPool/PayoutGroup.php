<?php
class GrcPool_PayoutGroup {

	/**
	 * 
	 * @var GrcPool_View_Member_Host_Project_Credit_OBJ[]
	 */
	private $objs;
	
	public function __construct() {
		$this->objs = array();
	}
	
	public function add(GrcPool_View_Member_Host_Project_Credit_OBJ $obj) {
		array_push($this->objs,$obj);
	}
	
	public function getOwed():float {
		$amount = 0;
		foreach ($this->objs as $obj) {
			$amount += $obj->getOwed()*Constants::COIN;
		}
		return $amount/Constants::COIN;
	}
	
	/**
	 * 
	 * @return int[]
	 */
	public function getCreditIds():array {
		$arr = array();
		foreach ($this->objs as $obj) {
			array_push($arr,$obj->getCreditId());
		}
		return $arr;		
	}
	
	public function getGrcAddress():string {
		return $this->objs[0]->getGrcAddress();
	}
	
	public function getOwedCalc():string {
		$str = '';
		foreach ($this->objs as $obj) {
			$str .= $obj->getOwedCalc();
		}
		return $str;
	}

	public function getId():int {
		return $this->objs[0]->getId();
	}
	
	public function getUsername():string {
		return $this->objs[0]->getUsername();
	}
	
	public function getDonation():float {
		return $this->objs[0]->getDonation();		
	}
}