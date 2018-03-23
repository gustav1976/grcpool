<?php
class GrcPool_Task_OBJ extends GrcPool_Task_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Task_DAO extends GrcPool_Task_MODELDAO {

	public function getLatestTasksByName() {
		$sql = 'select id,name,message,success,timeStarted,timeCompleted,info,max(theTime) as theTime from '.$this->getFullTableName().' group by name';
		return $this->queryObjects($sql);		
	}
	
	public function getWithName($name) {
		return $this->fetchAll(array($this->where('name',$name)),array('theTime'=>'desc'));
	}
	
}