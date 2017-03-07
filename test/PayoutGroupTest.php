<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PayoutTest extends TestCase {
	
	public function testNoGrcAddress() {
		$payout = new GrcPool_Payout();
		$group = new GrcPool_PayoutGroup();
		$obj = new GrcPool_View_Member_Host_Project_Credit_OBJ();
		$obj->setGrcAddress('');
		$payout->setMinOwePayout(1);
		$group->add($obj);
		$result = $payout->process($group);
		$this->assertEquals(Constants::PAYOUT_ERROR_NO_GRC_ADDR,$result->error);
	}

	public function testMinAmountOwed() {
		$payout = new GrcPool_Payout();
		$group = new GrcPool_PayoutGroup();
		$obj = new GrcPool_View_Member_Host_Project_Credit_OBJ();
		$obj->setGrcAddress('S123456789');
		$obj->setOwed(.9);
		$group->add($obj);
		
		$result = $payout->process($group);
		$this->assertEquals(Constants::PAYOUT_ERROR_NO_MIN_AMOUNT,$result->error);

		$payout->setMinOwePayout(1);
		$result = $payout->process($group);
		$this->assertEquals(Constants::PAYOUT_ERROR_MIN_NOT_MET,$result->error);
	}
	
	public function testPayout() {
		$payout = new GrcPool_Payout();
		$payout->setMinOwePayout(1);
		$payout->setPayoutFee(.005);
 		$group = new GrcPool_PayoutGroup();
 		$obj = new GrcPool_View_Member_Host_Project_Credit_OBJ();
 		$obj->setGrcAddress('S123456789');
 		$obj->setOwed(10.12345678);
 		$obj->setDonation(10);
 		$group->add($obj);
 		$result = $payout->process($group);
 		$this->assertEquals($result->sum(),$obj->getOwed());
	}
	
	public function testPayout100Donation() {
		$payout = new GrcPool_Payout();
		$payout->setMinOwePayout(1);
		$payout->setPayoutFee(.005);
		$group = new GrcPool_PayoutGroup();
		$obj = new GrcPool_View_Member_Host_Project_Credit_OBJ();
		$obj->setGrcAddress('S123456789');
		$obj->setOwed(10.12345678);
		$obj->setDonation(100);
		$group->add($obj);
		$result = $payout->process($group);
		$this->assertEquals($result->sum(),$obj->getOwed());
	}
	
	public function testPayout99Donation() {
		$payout = new GrcPool_Payout();
		$payout->setMinOwePayout(1);
		$payout->setPayoutFee(.005);
		$group = new GrcPool_PayoutGroup();
		$obj = new GrcPool_View_Member_Host_Project_Credit_OBJ();
		$obj->setGrcAddress('S123456789');
		$obj->setOwed(10.12345678);
		$obj->setDonation(99);
		$group->add($obj);
		$result = $payout->process($group);
		$this->assertEquals($result->sum(),$obj->getOwed());
	}
	
	public function testPayout99Dot99Donation() {
		$payout = new GrcPool_Payout();
		$payout->setMinOwePayout(1);
		$payout->setPayoutFee(.005);
		$group = new GrcPool_PayoutGroup();
		$obj = new GrcPool_View_Member_Host_Project_Credit_OBJ();
		$obj->setGrcAddress('S123456789');
		$obj->setOwed(10.12345678);
		$obj->setDonation(99.99);
		$group->add($obj);
		$result = $payout->process($group);
		$this->assertEquals(Constants::PAYOUT_ERROR_MIN_CALC_AMOUNT_NOT_MET,$result->error);
		$this->assertEquals($result->sum(),$obj->getOwed());
	}
	

}