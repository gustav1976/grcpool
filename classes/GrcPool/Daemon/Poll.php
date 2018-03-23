<?php
class GrcPool_Daemon_Poll {
	
	private $title = '';
	private $expire = 0;
	private $type = '';
	private $question = '';
	private $answers = array();
	private $bestAnswer = '';
	private $totalShares = 0;
	private $totalVotes = 0;
	
	public function getTitle() {return $this->title;}
	public function setTitle($s) {$this->title = $s;}
	public function getExpire() {return $this->expire;}
	public function setExpire($i) {$this->expire = $i;}
	public function getType() {return $this->type;}
	public function setType($s) {$this->type = $s;}
	public function getQuestion() {return $this->question;}
	public function setQuestion($s) {$this->question = $s;}
	public function getAnswers() {return $this->answers;}
	public function setAnswers($s) {$this->answers = $s;}
	public function addAnswer($o) {array_push($this->answers,$o);}
	public function getBestAnswer() {return $this->bestAnswer;}
	public function setBestAnswer($s) {$this->bestAnswer = $s;}
	public function getTotalShares() {return $this->totalShares;}
	public function setTotalShares($s) {$this->totalShares = $s;}
	public function getTotalVotes() {return $this->totalVotes;}
	public function setTotalVotes($s) {$this->totalVotes = $s;}
	
	
}