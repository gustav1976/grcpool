<?php
class GrcPool_Payout {

	private $minOwePayout = 0;
	private $payoutFee = 0;
	
	public function setMinOwePayout($minOwePayout) {
		$this->minOwePayout = $minOwePayout;
	}
	public function setPayoutFee($fee) {
		$this->payoutFee = $fee;
	}
	
	
	/**
	 * 
	 * @param GrcPool_PayoutGroup $group
	 * @return GrcPool_PayoutData
	 */
	public function process(GrcPool_PayoutGroup $group) {
		$payoutData = new GrcPool_PayoutData();
		
		if ($this->minOwePayout <= 0) {
			$payoutData->error = Constants::PAYOUT_ERROR_NO_MIN_AMOUNT;
			return $payoutData;
		}
		
		if (trim($group->getGrcAddress()) == '') {
			$payoutData->error = Constants::PAYOUT_ERROR_NO_GRC_ADDR;
			return $payoutData;
		}
		
		if ($group->getOwed() < $this->minOwePayout) {
			$payoutData->error = Constants::PAYOUT_ERROR_MIN_NOT_MET;
			return $payoutData;
		}
		
		$payoutData->owed = $group->getOwed();
		$amount = $group->getOwed()*COIN;
		$donation = 0;
		$fee = $this->payoutFee*COIN;
		
		if ($group->getDonation() > 0) {
			if ($group->getDonation() >= 100) {
				$donation = $amount;
				$fee = 0;
			} else {
				$donation = floor($amount*($group->getDonation()/100));
			}
		}

		$payoutData->amount = Utils::truncate(($amount-$donation-$fee)/COIN,8);
		$payoutData->donation = Utils::truncate($donation/COIN,8);
		$payoutData->fee = $fee/COIN;
		
		if ($payoutData->amount <> 0 && $payoutData->amount < $this->payoutFee * 2) {
			$payoutData->error = Constants::PAYOUT_ERROR_MIN_CALC_AMOUNT_NOT_MET;
		}
		
		return $payoutData;
		
	}
	
}