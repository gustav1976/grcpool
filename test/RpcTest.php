<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RpcTest extends TestCase {

	private function baseData() {
		$dao = new GrcPool_Member_DAO();
		$sql = 'delete from grcpool.member_host where memberId = 1';$dao->executeQuery($sql);
		$sql = 'delete from grcpool.member_host_project where memberId = 1';$dao->executeQuery($sql);
		$member = new GrcPool_Member_OBJ();
		$member->setId(1);
		$member->setEmail('phptest@grcpool.com');
		$member->setUsername('phptest');
		$member->setPassword('');
		$member->setpasswordHash('THISISATESTHASH');
		$member->setGrcAddress('');
		$dao->save($member);
	}
	
	public function setUp() {
		global $DATABASE;
		$PROPERTY = new Property(dirname(__FILE__).'/../../properties/grcpool.props.json');
		$DATABASE = new Database($PROPERTY->get('databaseUser'),$PROPERTY->get('databasePassword'),$PROPERTY->get('databaseName'),$PROPERTY->get('databaseServer'));
		$DATABASE->connect();
	}
	
	public function testRequestAuthFail1() {
		$this->baseData();
		$rpc = new BoincApi_Rpc(file_get_contents(dirname(__FILE__).'/data/1.in.authfail.xml'));
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$this->assertEquals('Authorization Failed',(String)$xml->message);
	}
	
	public function testFullSequence() {
		$this->baseData();
		// 1 first request from host
		$rpc = new BoincApi_Rpc(file_get_contents(dirname(__FILE__).'/data/1.in.xml'));
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$hostId = (int)$xml->opaque->hostId;
		$dao = new GrcPool_Member_Host_DAO();
		$host = $dao->initWithKey($hostId);
		$this->assertEquals($hostId,$host->getId());
		// 2 request from host, return projects
		$projDao = new GrcPool_Member_Host_Project_DAO();
		$projObj = new GrcPool_Member_Host_Project_OBJ();
		$projObj->setMemberId(1);
		$projObj->setHostId($hostId);
		$projObj->sethostCpid('f1fa920df893f4f1e4e7cfd248a5f522');
		$projObj->setHostDbid(0);
		$projObj->setProjectUrl('http://setiathome.berkeley.edu/');
		$projObj->setAttached(1);
		$projDao->save($projObj);
		$inXml = file_get_contents(dirname(__FILE__).'/data/2.in.xml');
		$inXml = str_replace('{{hostId}}',$hostId,$inXml);
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$this->assertEquals($projObj->getProjectUrl(),(String)$xml->account->url);
		// 3 PROJECT UPDATE
		$inXml = file_get_contents(dirname(__FILE__).'/data/3.in.xml');
		$inXml = str_replace('{{hostId}}',$host->getId(),$inXml);
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$projDao = new GrcPool_Member_Host_Project_DAO();
		$projObj = $projDao->getWithHostIdAndProjectUrl($host->getId(),'http://setiathome.berkeley.edu/');
		$this->assertEquals('123456789',$projObj->getHostDbId());
		// 4 CHANGE SETTINGS
		$projObj->setNoCpu(1);
		$projObj->setNoNvidiaGpu(1);
		$projObj->setNoIntelGpu(1);
		$projObj->setNoAtiGpu(1);
		$projObj->setResourceShare(50);
		$projObj->setAttached(0);
		$projDao->save($projObj);
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$this->assertEquals((String)$xml->account->no_cpu,'1');
		$this->assertEquals((String)$xml->account->no_cuda,'1');
		$this->assertEquals((String)$xml->account->no_intel,'1');
		$this->assertEquals((String)$xml->account->detach,'1');
		$this->assertEquals((String)$xml->account->resource_share,'50');
		
		$projObj->setResourceShare(0);
		$projDao->save($projObj);
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$this->assertEquals((String)$xml->account->resource_share,'0');
	}
	
	public function testSequenceNoHostId() {
		$this->baseData();
		// 1 first request from host
		$rpc = new BoincApi_Rpc(file_get_contents(dirname(__FILE__).'/data/1.in.xml'));
		$rpc->process();
 		$xml = simplexml_load_string($rpc->getResponseXml());
 		$hostId = (int)$xml->opaque->hostId;
 		$dao = new GrcPool_Member_Host_DAO();
 		$host = $dao->initWithKey($hostId);
 		$this->assertEquals($hostId,$host->getId());
 		// 2 request from host, return projects
 		$projDao = new GrcPool_Member_Host_Project_DAO();
 		$projObj = new GrcPool_Member_Host_Project_OBJ();
 		$projObj->setMemberId(1);
 		$projObj->setHostId($hostId);
 		$projObj->sethostCpid('f1fa920df893f4f1e4e7cfd248a5f522');
 		$projObj->setHostDbid(0);
 		$projObj->setProjectUrl('http://setiathome.berkeley.edu/');
 		$projObj->setAttached(1);
 		$projDao->save($projObj);
 		$inXml = file_get_contents(dirname(__FILE__).'/data/2.in.xml');
 		$inXml = str_replace('<hostId>{{hostId}}</hostId>','',$inXml);
 		$rpc = new BoincApi_Rpc($inXml);
 		$rpc->process(false);
 		$xml = simplexml_load_string($rpc->getResponseXml());
 		$this->assertEquals($projObj->getProjectUrl(),(String)$xml->account->url);
 		// 3 PROJECT UPDATE
 		$projObj->setHostDbid(123456789);
 		$projDao->save($projObj);
  		$inXml = file_get_contents(dirname(__FILE__).'/data/3.in.xml');
  		$inXml = str_replace('<hostId>{{hostId}}</hostId>','',$inXml);
  		$rpc = new BoincApi_Rpc($inXml);
  		$rpc->process(false);
  		$xml = simplexml_load_string($rpc->getResponseXml());
  		$projDao = new GrcPool_Member_Host_Project_DAO();
  		$projObj = $projDao->getWithHostIdAndProjectUrl($host->getId(),'http://setiathome.berkeley.edu/');
  		$this->assertEquals('123456789',$projObj->getHostDbId());
	}
	
	public function testSequenceNoProject() {
		$this->baseData();
		// 1 first request from host
		$rpc = new BoincApi_Rpc(file_get_contents(dirname(__FILE__).'/data/1.in.xml'));
		$rpc->process();
		$xml = simplexml_load_string($rpc->getResponseXml());
		$hostId = (int)$xml->opaque->hostId;
		$dao = new GrcPool_Member_Host_DAO();
		$host = $dao->initWithKey($hostId);
		$this->assertEquals($hostId,$host->getId());
		// 3 request from host, return projects
		$inXml = file_get_contents(dirname(__FILE__).'/data/3.in.xml');
		$inXml = str_replace('<hostId>{{hostId}}</hostId>',$hostId,$inXml);
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$this->assertEquals('http://setiathome.berkeley.edu/ is currently not in your account, please login to the pool and add it first.',(String)$xml->message);
	}
	
	public function testInvalidXml() {
		$this->baseData();
		$inXml = file_get_contents(dirname(__FILE__).'/data/badGlobalPrefs_01.xml');
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$this->assertEquals('grcpool.com',(String)$xml->name);
		$this->assertEquals('',(String)$xml->message);
	}
	
	public function testUtf8Xml() {
		$this->baseData();
		$inXml = file_get_contents(dirname(__FILE__).'/data/utf8.xml');
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$this->assertEquals('grcpool.com',(String)$xml->name);
		$this->assertEquals('',(String)$xml->message);
	}
	
	
}