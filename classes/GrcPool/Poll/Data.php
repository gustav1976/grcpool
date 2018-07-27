<?php
class GrcPool_Poll_Data{
	
	private $pollAnswers;
	private $pollQuestion;
	private $pollVoteSummary;
	private $answerKeys;
	
	public function __construct($pollQuestionObj) {
		$this->pollQuestion = $pollQuestionObj;
		$pollAnswerDao = new GrcPool_Poll_Answer_DAO();
		$pollVoteDao = new GrcPool_Poll_Vote_DAO();
		$this->pollAnswers = $pollAnswerDao->getWithQuestionId($pollQuestionObj->getId());
		$this->answerKeys = array_keys($this->pollAnswers);
		$this->pollVoteSummary = $pollVoteDao->getWeightsForQuestionId($pollQuestionObj->getId());
	}
	
	public function getAnswerId($idx) {
		return $this->answerKeys[$idx];
	}
	
	public function getPrettyPollTitle() {
		return str_replace('_',' ',$this->pollQuestion->getQuestion());
	}
	
	public function getNumberOfAnswers() {
		return count($this->pollAnswers);
	}
	
	public function getPrettyAnswerText($idx) {
		return $this->_getAnswerWithIdx($idx)->getPrettyAnswer();
	}
	
	public function getAnswerText($idx) {
 		return $this->_getAnswerWithIdx($idx)->getAnswer();
	}
	
	public function getAnswerGrcVoteCount($idx) {
		return $this->_getAnswerWithIdx($idx)->getVotes();
	}
	
	public function getAnswerGrcShares($idx) {
		$shares = $this->_getAnswerWithIdx($idx)->getShare();
		return $shares;
	}
	
	public function getAnswerGrcSharesTotal() {
		$total = 0;
		foreach ($this->pollAnswers as $answer) {
			$total += $answer->getShare();
		}
		return $total;
	}
	
	public function getAnswerGrcPercent($idx) {
		$share = $this->getAnswerGrcShares($idx);
		$total = $this->getAnswerGrcSharesTotal();
		return $total==0?0:number_format(100*$share/$total,2);
	}
	
	public function getAnswerPoolVoteCount($idx) {
		$answer = $this->_getAnswerWithIdx($idx);
		return isset($this->pollVoteSummary[$answer->getId()])?$this->pollVoteSummary[$answer->getId()]['howMany']:0;
	}
	
	public function getAnswerPoolShares($idx) {
		$answer = $this->_getAnswerWithIdx($idx);
		return isset($this->pollVoteSummary[$answer->getId()])?$this->pollVoteSummary[$answer->getId()]['weight']:0;
	}
	
	public function getAnswerPoolSharesTotal() {
		$total = 0;
		foreach ($this->pollAnswers as $answer) {
			$weight = isset($this->pollVoteSummary[$answer->getId()])?$this->pollVoteSummary[$answer->getId()]['weight']:0;
			$total += $weight;
		}
		return $total;
	}
	
	public function getAnswerPoolAdjustedShare($idx) {
		$answer = $this->_getAnswerWithIdx($idx);
		$weight = isset($this->pollVoteSummary[$answer->getId()])?$this->pollVoteSummary[$answer->getId()]['weight']:0;
		return $this->pollQuestion->getTotalPoolShares()*$weight/$this->pollQuestion->getPoolCalcShares();
	}
	
	public function getAnswerPoolAdjustedSharesTotal() {
		$total = 0;
		foreach ($this->answerKeys as $idx => $key) {
			$total += $this->getAnswerPoolAdjustedShare($idx);
		}
		return $total;
	}
	
	public function getAnswerPoolAdjustedPercent($idx) {
		$poolWeight = $this->getAnswerPoolAdjustedShare($idx);
		$grcWeight = $this->getAnswerGrcShares($idx);
		$totalPoolWeight = $this->getAnswerPoolAdjustedSharesTotal();
		$totalGrcWeight = $this->getAnswerGrcSharesTotal();
		return number_format(100*($poolWeight+$grcWeight)/($totalPoolWeight+$totalGrcWeight),2);
	}
	
	public function getAnswerGrcVoteCountTotal() {
		$total = 0;
		foreach ($this->pollAnswers as $answer) {
			$total += $answer->getVotes();
		}
		return $total;
	}
	
	public function getAnswerPoolVoteCountTotal() {
		$total = 0;
		foreach ($this->pollAnswers as $answer) {
			$total += isset($this->pollVoteSummary[$answer->getId()])?$this->pollVoteSummary[$answer->getId()]['howMany']:0;
		}
		return $total;
	}
	
	/**
	 * 
	 * @param int $idx
	 * @return GrcPool_Poll_Answer_OBJ
	 */
	private function _getAnswerWithIdx($idx) {
		return $this->pollAnswers[$this->answerKeys[$idx]];
	}
	
	
	
}