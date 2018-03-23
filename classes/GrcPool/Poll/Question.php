<?php
class GrcPool_Poll_Question_OBJ extends GrcPool_Poll_Question_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Poll_Question_DAO extends GrcPool_Poll_Question_MODELDAO {
	public function initWithTitle($title) {
		return $this->fetch(array($this->where('title',$title)));
	}
	
	public function getActivePolls() {
		$time = time();
		$time = $time + 5 * 60 * 60;
		return $this->fetchAll(array($this->where('expire',$time,'>')),array('expire'=>'asc'));
	}
	
	public function getCompletedPolls() {
		$time = time();
		$time = $time + 5 * 60 * 60;
		return $this->fetchAll(array($this->where('expire',$time,'<=')),array('expire'=>'desc'));
	}
	
	public function getPollsNeededToClose() {
		$time = time();
		$time = $time + 5 * 60 * 60;
		return $this->fetchAll(array($this->where('expire',$time,'<='),$this->where('closed',0)),array('expire'=>'asc'));
	}
	

}