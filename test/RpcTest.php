<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RpcTest extends TestCase {

	public function setUp() {

	}

	public function testRequest1() {
		$rpc = new BoincApi_Rpc(file_get_contents(dirname(__FILE__).'/data/1.xml'));
		$this->assertEquals('bryhardt-101',$rpc->getRawName());
		$this->assertEquals('bryhardt-101',$rpc->getName());
	}
	
	public function testRequest2() {
		//$rpc = new BoincApi_Rpc(file_get_contents(dirname(__FILE__).'/data/2.xml'));
	}

	public function testRequest3() {
		//$rpc = new BoincApi_Rpc(file_get_contents(dirname(__FILE__).'/data/3.xml'));
	}

	public function testRequest4() {
		//$rpc = new BoincApi_Rpc(file_get_contents(dirname(__FILE__).'/data/4.xml'));
	}
	

}