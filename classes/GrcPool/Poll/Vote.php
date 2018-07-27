<?php
class GrcPool_Poll_Vote_OBJ extends GrcPool_Poll_Vote_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Poll_Vote_DAO extends GrcPool_Poll_Vote_MODELDAO {

	public function deleteWithMemberId($memberId) {
		$sql = 'delete from '.$this->getFullTableName().' where memberId = '.$memberId;
		$this->executeQuery($sql);
	}
	
	public function getWithMemberIdAndQuestionId($memberId,$questionId) {
		return $this->fetchAll(array($this->where('memberId',$memberId),$this->where('questionId',$questionId)));
	}
	
	public function getWithQuestionId($questionId) {
		return $this->fetchAll(array($this->where('questionId',$questionId)));
	}
	
	public function getVotesForQuestionId($questionId) {
		$sql = 'select answerId,count(*) as howmany from '.$this->getFullTableName().' where questionId = '.$questionId.' group by answerId';
		$result = $this->query($sql);
		$data = array();
		foreach ($result as $r) {
			$data[$r['answerId']] = $r['howmany'];
		}
		return $data;
	}
	
	public function getWeightsForQuestionId($questionId) {
		$sql = 'select answerId,sum(weight) as weight,count(*) as howMany from '.$this->getFullTableName().' where questionId = '.$questionId.' group by answerId';
		$result = $this->query($sql);
		$data = array();
		foreach ($result as $r) {
			$data[$r['answerId']] = array('weight' => $r['weight'],'howMany' => $r['howMany']);
		}
		return $data;
	}
	
	public function getPollsVotedIn($memberId) {
		$sql = 'select distinct questionId from '.$this->getFullTableName().' where memberId = '.$memberId;
		$data = $this->query($sql);
		$result = array();
		foreach ($data as $d) {
			array_push($result,$d['questionId']);
		}
		
		return $result;
	}
	
	public function getZeroWeightForQuestionId($id) {
		return $this->fetchAll(array($this->where('questionId',$id),$this->where('weight',0)));
	}
	
}