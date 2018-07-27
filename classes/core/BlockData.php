<?php
class BlockData {
	
	public $block; // current highest block
	public $blocks; // curren highest block per pool
	public $lastBlocks; // last blocks per pool
	
	public function __construct($json = null) {
		$this->blocks = array();
		$this->lastBlocks = array();
		if ($json) {
			$data = json_decode($json,true);
			$this->block = $data['block'];
			$this->blocks = $data['blocks']??array();
			$this->lastBlocks = $data['lastBlocks']??array();
		}
	}
	
	public function toArray() {
		$json = array();
		$json['block'] = $this->block;
		$json['blocks'] = $this->blocks;
		$json['lastBlocks'] = $this->lastBlocks;
		return $json;
	}
	
	public function toJson() {
		return json_encode($this->toArray());
	}
	
}