<?php
class GrcPool_Poll_Answer_OBJ extends GrcPool_Poll_Answer_MODEL {
	public function __construct() {
		parent::__construct();
	}
	
	public function getPrettyAnswer() {
		return str_replace('_',' ',$this->getAnswer());
	}
}

class GrcPool_Poll_Answer_DAO extends GrcPool_Poll_Answer_MODELDAO {
	
	public function getWithQuestionIdAndAnswer($id,$answer) {
		return $this->fetch(array($this->where('questionId',$id),$this->where('answer',$answer)),array('id'=>'asc'));
	}
	
	public function getWithQuestionId($id) {
		return $this->fetchAll(array($this->where('questionId',$id)),array('id'=>'asc'));
	}
	
}