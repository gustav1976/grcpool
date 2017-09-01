<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RpcTest extends TestCase {

	private function baseData() {
		$dao = new GrcPool_Member_DAO();
		$sql = 'delete from grcpool.member where id = 1 or username like \'phptest%\'';$dao->executeQuery($sql);
		$sql = 'delete from grcpool.member_host where memberId = 1';$dao->executeQuery($sql);
		$sql = 'delete from grcpool.member_host_project where memberId = 1';$dao->executeQuery($sql);
		$sql = 'insert into grcpool.member (
			id,email,username,password,passwordHash,regtime,grcAddress,verifyKey,twoFactorKey,apiKey,apiSecret,sparcAddress
		) values (
			1,\'phptest@grcpool.com\',\'phptest\',\'\',\'THISISATESTHASH\','.time().',\'\',\'\',\'\',\'\',\'\',\'\'
		)';$dao->executeQuery($sql);
	}
	
	public function setUp() {
		global $DATABASE;
		$PROPERTY = new Property(Constants::PROPERTY_FILE);
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
		$urlDao = new GrcPool_Boinc_Account_Url_DAO();
		$projObj = new GrcPool_Member_Host_Project_OBJ();
		$projObj->setMemberId(1);
		$projObj->setHostId($hostId);
		$projObj->sethostCpid('f1fa920df893f4f1e4e7cfd248a5f522');
		$projObj->setHostDbid(0);
		$projObj->setAccountId(1);
		$projObj->setAttached(1);
		$projDao->save($projObj);
		$inXml = file_get_contents(dirname(__FILE__).'/data/2.in.xml');
		$inXml = str_replace('{{hostId}}',$hostId,$inXml);
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$urlObj = $urlDao->initWithUrl((String)$xml->account->url);
		$this->assertEquals($projObj->getAccountId(),$urlObj->getAccountId());
		// 3 PROJECT UPDATE
		$inXml = file_get_contents(dirname(__FILE__).'/data/3.in.xml');
		$inXml = str_replace('{{hostId}}',$host->getId(),$inXml);
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$projDao = new GrcPool_Member_Host_Project_DAO();
		$projObj = $projDao->getActiveProjectForHost($host->getId(),1,1);
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
		//$this->assertEquals((String)$xml->account->no_cpu,'1');
		//$this->assertEquals((String)$xml->account->no_cuda,'1');
		//$this->assertEquals((String)$xml->account->no_intel,'1');
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
 		$urlDao = new GrcPool_Boinc_Account_Url_DAO();
 		$projDao = new GrcPool_Member_Host_Project_DAO();
 		$projObj = new GrcPool_Member_Host_Project_OBJ();
 		$projObj->setMemberId(1);
 		$projObj->setHostId($hostId);
 		$projObj->sethostCpid('f1fa920df893f4f1e4e7cfd248a5f522');
 		$projObj->setHostDbid(0);
 		$projObj->setAccountId(1);
 		$projObj->setAttached(1);
 		$projDao->save($projObj);
 		$inXml = file_get_contents(dirname(__FILE__).'/data/2.in.xml');
 		$inXml = str_replace('<hostId>{{hostId}}</hostId>','',$inXml);
 		$rpc = new BoincApi_Rpc($inXml);
 		$rpc->process(false);
 		$xml = simplexml_load_string($rpc->getResponseXml());
 		$urlObj = $urlDao->initWithUrl((String)$xml->account->url);
 		$this->assertEquals($projObj->getAccountId(),$urlObj->getAccountId());
 		// 3 PROJECT UPDATE
 		$projObj->setHostDbid(123456789);
 		$projDao->save($projObj);
  		$inXml = file_get_contents(dirname(__FILE__).'/data/3.in.xml');
  		$inXml = str_replace('<hostId>{{hostId}}</hostId>','',$inXml);
  		$rpc = new BoincApi_Rpc($inXml);
  		$rpc->process(false);
  		$xml = simplexml_load_string($rpc->getResponseXml());
  		$projDao = new GrcPool_Member_Host_Project_DAO();
  		$projObj = $projDao->getActiveProjectForHost($host->getId(),1,1);
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
		$this->assertEquals('Seti@Home is currently not in your account, please login to the pool and add it first.',(String)$xml->message);
	}
	
	public function testProjectWrongAuthenticator() {
		$this->baseData();

		$rpc = new BoincApi_Rpc(file_get_contents(dirname(__FILE__).'/data/1.in.xml'));
		$rpc->process();
		$xml = simplexml_load_string($rpc->getResponseXml());
		$hostId = (int)$xml->opaque->hostId;
		$dao = new GrcPool_Member_Host_DAO();
		$host = $dao->initWithKey($hostId);
		$this->assertEquals($hostId,$host->getId());
		
		$projDao = new GrcPool_Member_Host_Project_DAO();
		$projObj = new GrcPool_Member_Host_Project_OBJ();
		$projObj->setMemberId(1);
		$projObj->setHostId($hostId);
		$projObj->sethostCpid('f1fa920df893f4f1e4e7cfd248a5f522');
		$projObj->setHostDbid(0);
		$projObj->setAccountId(1);
		$projObj->setAttached(1);
		$projDao->save($projObj);
		$inXml = file_get_contents(dirname(__FILE__).'/data/2.in.xml');
		$inXml = str_replace('<hostId>{{hostId}}</hostId>','',$inXml);
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		
		$projObj->setHostDbid(123456789);
		$projDao->save($projObj);
		$inXml = file_get_contents(dirname(__FILE__).'/data/3.in.xml');
		$inXml = str_replace('<hostId>{{hostId}}</hostId>','',$inXml);
		$inXml = str_replace('<account_key>10434153_b20d137e634496b8adac4de17581b91e</account_key>','<account_key>b20d137e634496b8adac4de17581b91e</account_key>',$inXml);		
		// PROJECT SHOULD NOT BE IN RETURN TO CLIENT - WRONG AUTHENTICATOR
		$rpc = new BoincApi_Rpc($inXml);
		$rpc->process(false);
		$xml = simplexml_load_string($rpc->getResponseXml());
		$this->assertEquals(null,$xml->account->url);		
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