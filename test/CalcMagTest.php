<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class MagCalcTest extends TestCase {

	public function testCalcMag() {
		$this->assertEquals(.02,GrcPool_Utils::calculateMag(2.804906, 451293.88287126, 28, 2));
		$this->assertEquals(.00,GrcPool_Utils::calculateMag(1,792540.90226594,28,2));
	}
	
	public function testMinRacWithMag() {
 		$this->assertEquals(243.08,GrcPool_Utils::getMinRac(99837000,28));
  		$this->assertEquals(24.30,GrcPool_Utils::getMinRac(9983700,28));
   		$this->assertEquals(2.43,GrcPool_Utils::getMinRac(998370,28));
   		$this->assertEquals(.24,GrcPool_Utils::getMinRac(99837,28));
   		$this->assertEquals(.02,GrcPool_Utils::getMinRac(9983,28));
  		$this->assertEquals(0,GrcPool_Utils::getMinRac(983,28));
  		$this->assertEquals(0,GrcPool_Utils::getMinRac(98,28));
  		$this->assertEquals(0,GrcPool_Utils::getMinRac(8,28));
	}
	
	public function testTruncate() {
		$this->assertEquals('a11.00','a'.Utils::truncate(11,2));
		$this->assertEquals('a11.00','a'.Utils::truncate(11.0,2));
		$this->assertEquals('a11.00','a'.Utils::truncate(11.00,2));
		$this->assertEquals('a11.00','a'.Utils::truncate(11.0000,2));
		$this->assertEquals(11.54,Utils::truncate(11.5468,2));
		$this->assertEquals(58469871.546,Utils::truncate(58469871.5468698746,3));
		$this->assertEquals(58469871.5468,Utils::truncate(58469871.5468698746,4));
		$this->assertEquals(58469871.54686,Utils::truncate(58469871.5468698746,5));
		$this->assertEquals(58469871.546869,Utils::truncate(58469871.5468698746,6));
		$this->assertEquals(58469871.5468698,Utils::truncate(58469871.5468698746,7));
		$this->assertEquals(58469871.54686987,Utils::truncate(58469871.5468698746,8));
	}
	

}