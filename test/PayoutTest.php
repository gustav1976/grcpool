<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PayoutGroupTest extends TestCase {

	private $group;
	
	public function setUp() {
		$this->group = new GrcPool_PayoutGroup();
		$obj = new GrcPool_View_Member_Host_Project_Credit_OBJ();
		$obj->setMemberId(1);
		$obj->setCreditId(1);
		$obj->setGrcAddress('S1234567890');
		$obj->setOwed(10.12345678);
		$obj->setOwedCalc('+ 123*123');
		$obj->setDonation(10.5);
		$obj->setUsername('bryhardt');
		$this->group->add($obj);
		
		$obj = new GrcPool_View_Member_Host_Project_Credit_OBJ();
		$obj->setMemberId(1);
		$obj->setCreditId(2);
		$obj->setGrcAddress('S1234567890');
		$obj->setOwed(10.12345678);
		$obj->setOwedCalc('+ 456*456');
		$obj->setDonation(10.5);
		$obj->setUsername('bryhardt');
		$this->group->add($obj);
	}
	
	
	public function testGetOwed() {
		$this->assertEquals(20.24691356,$this->group->getOwed());
	}
	
	public function testGetCreditIds() {
		$this->assertEquals('1,2',implode(',',$this->group->getCreditIds()));
	}
	
	public function testGetGrcAddress() {
		$this->assertEquals('S1234567890',$this->group->getGrcAddress());
	}
	
	public function testGetOwedCalc() {
		$this->assertEquals('+ 123*123+ 456*456',$this->group->getOwedCalc());
	}
	
	public function testGetUsername() {
		$this->assertEquals('bryhardt',$this->group->getUsername());
	}
	
	public function testGetDonation() {
		$this->assertEquals(10.5,$this->group->getDonation());
	}
	
	public function testGetId() {
		$this->assertEquals(1,$this->group->getId());
	}
	
}