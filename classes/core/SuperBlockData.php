<?php
class SuperBlockData {
	
	public $whiteListCount;
	public $timestamp;
	public $age;
	public $pending;
	public $block;
	public $mag;
	public $ageText;
	public $lastBlock;	
	public $expectedDailyEarnings;
	public $interest;
	public $research;
	public $txCount;
	public $balance;
	public $basis;
	public $owed;
	public $paidOut;
	public $fulfillment;
	public $grcNoAddress;
	public $grcOwnerUnknown;
	public $magUnit;
	public $version;
	public $projects;
	//public $netWeight;
	
	public function __construct($json = null) {
		if ($json) {
			$data = json_decode($json,true);
			$this->version = $data['version'];
			//$this->netWeight = $data['netWeight'];
			$this->grcNoAddress = $data['grcNoAddress'];
			$this->whiteListCount = $data['whiteListCount'];
			$this->timestamp = $data['timestamp'];
			$this->age = $data['age'];
			$this->pending = $data['pending'];
			$this->block = $data['block'];
			$this->mag = $data['mag'];
			$this->ageText = $data['ageText'];
			$this->lastBlock = $data['lastBlock'];
			$this->expectedDailyEarnings = $data['expectedDailyEarnings'];
			$this->interest = $data['interest'];
			$this->research = $data['research'];
			$this->txCount = $data['txCount'];
			$this->balance = $data['balance'];
			$this->basis = $data['basis'];
			$this->owed = $data['owed'];
			$this->paidOut = $data['paidOut'];
			$this->fulfillment = $data['fulfillment'];
			$this->grcOwnerUnknown = $data['grcOwnerUnknown'];
			$this->magUnit = $data['magUnit'];
			$this->projects = $data['projects'];
		}
	}
	
	public function toJson() {
		$json = array();
		$json['version'] = $this->version;
		//$json['netWeight'] = $this->netWeight;
		$json['grcNoAddress'] = $this->grcNoAddress;
		$json['timestamp'] = $this->timestamp;
		$json['age'] = $this->age;
		$json['pending'] = $this->pending;
		$json['block'] = $this->block;
		$json['ageText'] = $this->ageText;
		$json['whiteListCount'] = $this->whiteListCount;
		$json['mag'] = $this->mag;
		$json['lastBlock'] = $this->lastBlock;
		$json['expectedDailyEarnings'] = $this->expectedDailyEarnings;
		$json['interest'] = $this->interest;
		$json['research'] = $this->research;
		$json['txCount'] = $this->txCount;
		$json['balance'] = $this->balance;
		$json['basis'] = $this->basis;
		$json['owed'] = $this->owed;
		$json['paidOut'] = $this->paidOut;
		$json['fulfillment'] = $this->fulfillment;
		$json['grcOwnerUnknown'] = $this->grcOwnerUnknown;
		$json['magUnit'] = $this->magUnit;
		$json['projects'] = $this->projects;
		return json_encode($json);
	}
	
}