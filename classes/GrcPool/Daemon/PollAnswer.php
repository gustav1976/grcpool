<?php
class GrcPool_Daemon_PollAnswer {

	private $votes = 0;
	private $answer = '';
	private $share = 0;
	
	public function getVotes() {return $this->votes;}
	public function setVotes($s) {$this->votes = $s;}
	public function getAnswer() {return $this->answer;}
	public function setAnswer($s) {$this->answer = $s;}
	public function getShare() {return $this->share;}
	public function setShare($s) {$this->share = $s;}
	
}