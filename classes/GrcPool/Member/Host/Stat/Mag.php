<?php
class GrcPool_Member_Host_Stat_Mag_OBJ extends GrcPool_Member_Host_Stat_Mag_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Member_Host_Stat_Mag_DAO extends GrcPool_Member_Host_Stat_Mag_MODELDAO {

	/**
	 * 
	 * @param int $memberId
	 */
	public function deleteWithMemberId(int $memberId) {
		$sql = 'delete from '.$this->getFullTableName().' where memberId = '.$memberId;
		$this->executeQuery($sql);
	}
	
	/**
	 * 
	 * @param int $memberId
	 * @param int $since
	 * @return GrcPool_Member_Host_Stat_Mag_OBJ[]
	 */
	public function getWithMemberId(int $memberId,int $since = 0) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('thetime',$since,'>=')),array('accountId'=>'asc','thetime'=>'asc'));
	}
	
	/**
	 * 
	 * @param int $memberId
	 * @param int $since
	 * @return GrcPool_Member_Host_Stat_Mag_OBJ[]
	 */
	public function getMagDataWithMemberId(int $memberId,int $since = 0) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('thetime',$since,'>='),$this->where('mag',0,'>')),array('accountId'=>'asc','thetime'=>'asc'));
	}
	
	public function getMagStatsWithMemberId($grouping,$memberId,$since) {
		$group = 'date';
		switch ($grouping) {
			case 'week' : $group = 'yearweek';break;
		}
		$sql = '
			select 	sum(mag) as mag,
					'.$group.'(FROM_UNIXTIME(theTime)) as theTime
			from	'.$this->getFullTableName().'
			where	memberId = \''.$memberId.'\'
			and		theTime > '.$since.'
			GROUP BY '.$group.'(FROM_UNIXTIME(theTIme))
			order by theTime
		';
		return $this->query($sql);
	}
}