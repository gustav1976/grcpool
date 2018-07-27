<?php
class GrcPool_Controller_Polls extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction() {
		$pollDao = new GrcPool_Poll_Question_DAO();
		$pollAnswerDao = new GrcPool_Poll_Answer_DAO();
		$voteDao = new GrcPool_Poll_Vote_DAO();
		$this->view->loggedIn = $this->getUser() && $this->getUser()->getId();
		if ($this->post('cmd') && $this->view->loggedIn) {
			$parts = explode("_",$this->post('cmd'));
			$question = $pollDao->initWithKey($parts[1]);
			if ($question != null && $question->getExpire() > time()) {
				$creditDao = new GrcPool_Member_Host_Credit_DAO();
				$totalMag = $creditDao->getTotalMagForMemberId($this->getUser()->getId());
				$totalOwed = $creditDao->getTotalOwedForMemberId($this->getUser()->getId());
				$votes = $voteDao->getWithMemberIdAndQuestionId($this->getUser()->getId(),$parts[1]);
				foreach ($votes as $vote) {
					$voteDao->delete($vote);
				}
				if ($parts[0] == 'vote') {
					$vote = new GrcPool_Poll_Vote_OBJ();
					$vote->setAnswerId($this->post('poll_'.$question->getId()));
					$vote->setMemberId($this->getUser()->getId());
					$vote->setQuestionId($question->getId());
					$vote->setTime(time());
					$vote->setRank(1);
					$vote->setMag($totalMag);
					$vote->setBalance($totalOwed);
					$vote->setWeight(GrcPool_Utils::getVoteWeight($totalMag,$totalOwed,$question->getMoneySupply()));
					$voteDao->save($vote);
				}
			}
		}
		
		//if ($this->getUser() && $this->getUser()->getId() == 573) {
			$polls = $pollDao->getActivePolls();
		//} else {
		//	$polls = array();
		//}
		
		$answerIds = array();
		foreach ($polls as $poll) {
			if ($this->view->loggedIn) {
				$votes = $voteDao->getWithMemberIdAndQuestionId($this->getUser()->getId(),$poll->getId());
				foreach ($votes as $vote) {
					array_push($answerIds,$vote->getAnswerId());
				}				
			}
		}
			
		$this->view->answerIds = $answerIds;
		$this->view->polls = $polls;
	}
	
	public function resultsAction() {
		if (!$this->args(0)) {
			Server::goHome();
		}
		$pollDao = new GrcPool_Poll_Question_DAO();
		$pollAnswerDao = new GrcPool_Poll_Answer_DAO();
		$voteDao = new GrcPool_View_Vote_Member_DAO();
		
		$poll = $pollDao->initWithKey($this->args(0));
		if (!$poll) {
			Server::goHome();
		}
		
		$voters = $voteDao->fetchAll(array($voteDao->where('questionId',$poll->getId())),array('weight'=>'desc'));
		$this->view->poll = $poll;
		$this->view->voters = $voters;
		$answers = $pollAnswerDao->getWithQuestionId($poll->getId());
		$this->view->answers = $answers;
		
	}
	
	public function completeAction() {
		$pollDao = new GrcPool_Poll_Question_DAO();
		$pollAnswerDao = new GrcPool_Poll_Answer_DAO();
		$voteDao = new GrcPool_Poll_Vote_DAO();
		
		$polls = $pollDao->getCompletedPolls();
		
		foreach ($polls as $poll) {
			$answers = $pollAnswerDao->getWithQuestionId($poll->getId());
			$poll->answers = $answers;
			$poll->weights = $voteDao->getWeightsForQuestionId($poll->getId());				
		}
			
		$this->view->polls = $polls;
	}
	
}