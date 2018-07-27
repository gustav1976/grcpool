<?php
class GrcPool_Wcg_Tasks_OBJ extends GrcPool_Wcg_Tasks_MODEL {
	public function __construct() {
		parent::__construct();
	}
	
}

class GrcPool_Wcg_Tasks_DAO extends GrcPool_Wcg_Tasks_MODELDAO {

	public function getUniqueAppNames() {
		$sql = 'select distinct appName from '.$this->getFullTableName().' order by appName';
		$result = $this->query($sql);
		$names = array();
		foreach ($result as $r) {
			array_push($names,$r['appName']);
		}
		return $names;
	}
	
	public function getMaxModTimeWithPoolId($poolId) {
		$sql = 'select max(modTime) as maxMod from '.$this->getFullTableName().' where poolId = '.$poolId;
		$result = $this->query($sql);
		if (isset($result[0])) {
			return $result[0]['maxMod'];			
		} else {
			return 0;
		}
	}
	
	public function getWithDeviceId($deviceId) {
		return $this->fetchAll(array($this->where('deviceId',$deviceId)));
	}
	
	public function getCountWithDeviceIds($deviceIds,$where=array()) {
		array_push($where,$this->where('deviceId',$deviceIds));
		return $this->getCount($where);
	}
	
	public function getWithDeviceIds($deviceIds,$where=array(),$orderby = array(),$limit = array()) {
		if (!$orderby) {
			$orderby = array('modTime'=>'desc');
		}
		array_push($where,$this->where('deviceId',$deviceIds));
		return $this->fetchAll($where,$orderby,$limit);
	}
	
	public function getStatusWithDeviceIds($deviceIds) {
		$in = '';
		foreach ($deviceIds as $d) {
			if ($in != '') {
				$in .= ',';
			}
			$in .= addslashes($d);
		}
		$sql = 'select outcome,serverState,validateState from '.$this->getFullTableName().' where deviceId in ('.$in.')';
		return $this->query($sql);
	}
	
	
}