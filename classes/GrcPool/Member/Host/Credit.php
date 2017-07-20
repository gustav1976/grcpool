<?php
class GrcPool_Member_Host_Credit_OBJ extends GrcPool_Member_Host_Credit_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Member_Host_Credit_DAO extends GrcPool_Member_Host_Credit_MODELDAO {
	/**
	 *
	 * @param string $projectUrl
	 * @param int $dbid
	 * @return NULL|GrcPool_MemberHostCredit_OBJ
	 */
	public function initWithProjectUrlDbid($projectUrl,$dbid) {
		return $this->fetch(array($this->where('projectUrl',$projectUrl),$this->where('hostDbid',$dbid)));
	}
	
	public function getWithCpidAndProjectUrlAndPoolId($hash,$projectUrl,$poolId) {
		return $this->fetchAll(array($this->where('projectUrl',$projectUrl),$this->where('poolId',$poolId),$this->where('hostCpid',$hash)));
	}
	
	public function getActiveStatsProjectUrl($url) {
		$sql = 'select sum(totalCredit) as totalCredit, sum(mag) as totalMag, count(*) as numberOfHosts from '.$this->getFullTableName().' where avgCredit > 0 and projectUrl = \''.$url.'\'';
		$result = $this->query($sql);
		return $result[0];
	}
	
	public function getNumberOfActiveHostsForPool($poolId) {
		$sql = 'select count(*) as howMany from '.$this->getFullTableName().' where avgCredit > 0 and poolId = '.$poolId;
		$result = $this->query($sql);
		return $result[0]['howMany'];
	}
	
	public function getNumberOfActiveHosts() {
		$sql = 'select count(*) as howMany from '.$this->getFullTableName().' where avgCredit > 0';
		$result = $this->query($sql);
		return $result[0]['howMany'];
	}
	
	public function setMagToZeroForProjectUrl($projectUrl) {
		$sql = "update ".$this->getFullTableName()." set mag = 0 where projectUrl = '".addslashes($projectUrl)."'";
		$this->executeQuery($sql);
	}
	
	/**
	 * @return int
	 */
	public function getTotalMag() {
		$sql = 'select sum(mag) as totalMag from '.$this->getFullTableName();
		$result = $this->query($sql);
		return $result[0]['totalMag'];
	}
	
	public function getTotalMagForPool($id) {
		$sql = 'select sum(mag) as totalMag from '.$this->getFullTableName() .' where poolId = '.$id;
		$result = $this->query($sql);
		return $result[0]['totalMag'];
	}
	
    public function getTotalOwed() {
        $sql = 'select sum(owed) as totalOwed from '.$this->getFullTableName();
        $result = $this->query($sql);
        return $result[0]['totalOwed'];
	}
	
	public function getTotalOwedForPool($poolId) {
		$sql = 'select sum(owed) as totalOwed from '.$this->getFullTableName().' where poolId = '.$poolId;
		$result = $this->query($sql);
		return $result[0]['totalOwed'];
	}

	public function getProjectStats($limit = 0) {
		$sql = 'select projectUrl,poolId,sum(mag) as totalMag,count(*) as howMany from '.$this->getFullTableName().' where mag > 0 group by projectUrl,poolId order by totalMag desc '.($limit?'limit '.$limit:'');
		$results = $this->query($sql);
		$projects = array();
		foreach ($results as $result) {
			if (!isset($projects[$result['projectUrl']])) {
				$projects[$result['projectUrl']] = array();
				$projects[$result['projectUrl']]['mag'] = 0;
				$projects[$result['projectUrl']]['hostCount'] = 0;
			}
			$projects[$result['projectUrl']]['mag_'.$result['poolId']] = $result['totalMag'];
			$projects[$result['projectUrl']]['hostCount_'.$result['poolId']] = $result['howMany'];
			$projects[$result['projectUrl']]['mag'] += $result['totalMag'];
			$projects[$result['projectUrl']]['hostCount'] += $result['howMany'];
		}
		return $projects;
	}
	
	public function getOrphanedOwed() {
		$sql = 'select * from '.$this->getFullTableName().' where (hostDbid,projectUrl) not in (select hostDbid,projectUrl from grcpool.member_host_project) and owed > 0 and memberId = memberIdCredit and memberId != 0';
		return $this->queryObjects($sql);
	}
	
	public function getOrphanedOwedForMemberId($memberId) {
		$sql = 'select * from '.$this->getFullTableName().' where (hostDbid,projectUrl) not in (select hostDbid,projectUrl from grcpool.member_host_project) and owed > 0 and memberId = memberIdCredit and memberId = '.$memberId;
		return $this->queryObjects($sql);
	}
	
	public function getOwedWithNoOwner() {
		$sql = 'select * from '.$this->getFullTableName().' where (hostDbid,projectUrl) not in (select hostDbid,projectUrl from grcpool.member_host_project) and owed > 0';
		return $this->queryObjects($sql);
	}
}
