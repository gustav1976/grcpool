<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_View_Member_Host_Project_Credit_MODEL {

	public function __construct() { }

	private $_memberId = 0;
	private $_email = '';
	private $_username = '';
	private $_poolId = 1;
	private $_verified = 0;
	private $_grcAddress = '';
	private $_donation = 0.00;
	private $_hostDbid = 0;
	private $_projectPoolId = 1;
	private $_accountID = 0;
	private $_hostId = 0;
	private $_creditId = 0;
	private $_totalCredit = 0;
	private $_avgCredit = 0;
	private $_mag = 0;
	private $_owed = 0.00000000;
	private $_owedCalc = '';
	private $_hostName = '';
	private $_sparc = 0.00000000;
	public function setMemberId(int $int) {$this->_memberId=$int;}
	public function getMemberId():int {return $this->_memberId;}
	public function setEmail(string $string) {$this->_email=$string;}
	public function getEmail():string {return $this->_email;}
	public function setUsername(string $string) {$this->_username=$string;}
	public function getUsername():string {return $this->_username;}
	public function setPoolId(int $int) {$this->_poolId=$int;}
	public function getPoolId():int {return $this->_poolId;}
	public function setVerified(int $int) {$this->_verified=$int;}
	public function getVerified():int {return $this->_verified;}
	public function setGrcAddress(string $string) {$this->_grcAddress=$string;}
	public function getGrcAddress():string {return $this->_grcAddress;}
	public function setDonation(float $float) {$this->_donation=$float;}
	public function getDonation():float {return $this->_donation;}
	public function setHostDbid(int $int) {$this->_hostDbid=$int;}
	public function getHostDbid():int {return $this->_hostDbid;}
	public function setProjectPoolId(int $int) {$this->_projectPoolId=$int;}
	public function getProjectPoolId():int {return $this->_projectPoolId;}
	public function setAccountID(int $int) {$this->_accountID=$int;}
	public function getAccountID():int {return $this->_accountID;}
	public function setHostId(int $int) {$this->_hostId=$int;}
	public function getHostId():int {return $this->_hostId;}
	public function setCreditId(int $int) {$this->_creditId=$int;}
	public function getCreditId():int {return $this->_creditId;}
	public function setTotalCredit(float $float) {$this->_totalCredit=$float;}
	public function getTotalCredit():float {return $this->_totalCredit;}
	public function setAvgCredit(float $float) {$this->_avgCredit=$float;}
	public function getAvgCredit():float {return $this->_avgCredit;}
	public function setMag(float $float) {$this->_mag=$float;}
	public function getMag():float {return $this->_mag;}
	public function setOwed(float $float) {$this->_owed=$float;}
	public function getOwed():float {return $this->_owed;}
	public function setOwedCalc(string $string) {$this->_owedCalc=$string;}
	public function getOwedCalc():string {return $this->_owedCalc;}
	public function setHostName(string $string) {$this->_hostName=$string;}
	public function getHostName():string {return $this->_hostName;}
	public function setSparc(float $float) {$this->_sparc=$float;}
	public function getSparc():float {return $this->_sparc;}
}

abstract class GrcPool_View_Member_Host_Project_Credit_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'view_member_host_project_credit';
	protected $_model = 'GrcPool_View_Member_Host_Project_Credit_OBJ';
	protected $_primaryKey = '';
	protected $_fields = array(
		'memberId' => array('type'=>'INT','dbType'=>'int(11)'),
		'email' => array('type'=>'STRING','dbType'=>'varchar(200)'),
		'username' => array('type'=>'STRING','dbType'=>'varchar(25)'),
		'poolId' => array('type'=>'INT','dbType'=>'smallint(2)'),
		'verified' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'grcAddress' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'donation' => array('type'=>'FLOAT','dbType'=>'decimal(5,2)'),
		'hostDbid' => array('type'=>'INT','dbType'=>'int(11)'),
		'projectPoolId' => array('type'=>'INT','dbType'=>'smallint(3)'),
		'accountID' => array('type'=>'INT','dbType'=>'smallint(5)'),
		'hostId' => array('type'=>'INT','dbType'=>'int(11)'),
		'creditId' => array('type'=>'INT','dbType'=>'int(11)'),
		'totalCredit' => array('type'=>'FLOAT','dbType'=>'decimal(22,6)'),
		'avgCredit' => array('type'=>'FLOAT','dbType'=>'decimal(22,6)'),
		'mag' => array('type'=>'FLOAT','dbType'=>'decimal(9,2)'),
		'owed' => array('type'=>'FLOAT','dbType'=>'decimal(16,8)'),
		'owedCalc' => array('type'=>'STRING','dbType'=>'varchar(4000)'),
		'hostName' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'sparc' => array('type'=>'FLOAT','dbType'=>'decimal(16,8)'),
	);
}