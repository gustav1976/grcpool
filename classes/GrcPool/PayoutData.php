<?php
class GrcPool_PayoutData {
	
	public $error = '';
	public $donation = 0;
	public $amount = 0;
	public $fee = 0;
	public $owed = 0;
	
	public function sum() {
		return (
			$this->donation*COIN	+
			$this->amount*COIN +
			$this->fee*COIN
		)/COIN;
	}

}