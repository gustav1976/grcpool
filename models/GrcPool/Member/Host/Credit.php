<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Member_Host_Credit_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_hostDbid = 0;
	private $_hostCpid = '';
	private $_totalCredit = 0;
	private $_avgCredit = 0;
	private $_accountId = 0;
	private $_lastSeen = 0;
	private $_mag = 0;
	private $_magTotalCredit = 0;
	private $_owed = 0.00000000;
	private $_owedCalc = '';
	private $_memberIdPayout = 0;
	private $_poolId = 1;
	private $_memberIdCredit = 0;
	private $_sparc = 0.00000000;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setHostDbid(int $int) {$this->_hostDbid=$int;}
	public function getHostDbid():int {return $this->_hostDbid;}
	public function setHostCpid(string $string) {$this->_hostCpid=$string;}
	public function getHostCpid():string {return $this->_hostCpid;}
	public function setTotalCredit(float $float) {$this->_totalCredit=$float;}
	public function getTotalCredit():float {return $this->_totalCredit;}
	public function setAvgCredit(float $float) {$this->_avgCredit=$float;}
	public function getAvgCredit():float {return $this->_avgCredit;}
	public function setAccountId(int $int) {$this->_accountId=$int;}
	public function getAccountId():int {return $this->_accountId;}
	public function setLastSeen(int $int) {$this->_lastSeen=$int;}
	public function getLastSeen():int {return $this->_lastSeen;}
	public function setMag(float $float) {$this->_mag=$float;}
	public function getMag():float {return $this->_mag;}
	public function setMagTotalCredit(float $float) {$this->_magTotalCredit=$float;}
	public function getMagTotalCredit():float {return $this->_magTotalCredit;}
	public function setOwed(float $float) {$this->_owed=$float;}
	public function getOwed():float {return $this->_owed;}
	public function setOwedCalc(string $string) {$this->_owedCalc=$string;}
	public function getOwedCalc():string {return $this->_owedCalc;}
	public function setMemberIdPayout(int $int) {$this->_memberIdPayout=$int;}
	public function getMemberIdPayout():int {return $this->_memberIdPayout;}
	public function setPoolId(int $int) {$this->_poolId=$int;}
	public function getPoolId():int {return $this->_poolId;}
	public function setMemberIdCredit(int $int) {$this->_memberIdCredit=$int;}
	public function getMemberIdCredit():int {return $this->_memberIdCredit;}
	public function setSparc(float $float) {$this->_sparc=$float;}
	public function getSparc():float {return $this->_sparc;}
}

abstract class GrcPool_Member_Host_Credit_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'member_host_credit';
	protected $_model = 'GrcPool_Member_Host_Credit_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'hostDbid' => array('type'=>'INT','dbType'=>'int(11)'),
		'hostCpid' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'totalCredit' => array('type'=>'FLOAT','dbType'=>'decimal(22,6)'),
		'avgCredit' => array('type'=>'FLOAT','dbType'=>'decimal(22,6)'),
		'accountId' => array('type'=>'INT','dbType'=>'smallint(5)'),
		'lastSeen' => array('type'=>'INT','dbType'=>'int(11)'),
		'mag' => array('type'=>'FLOAT','dbType'=>'decimal(9,2)'),
		'magTotalCredit' => array('type'=>'FLOAT','dbType'=>'decimal(22,6)'),
		'owed' => array('type'=>'FLOAT','dbType'=>'decimal(16,8)'),
		'owedCalc' => array('type'=>'STRING','dbType'=>'varchar(4000)'),
		'memberIdPayout' => array('type'=>'INT','dbType'=>'int(11)'),
		'poolId' => array('type'=>'INT','dbType'=>'smallint(2)'),
		'memberIdCredit' => array('type'=>'INT','dbType'=>'int(11)'),
		'sparc' => array('type'=>'FLOAT','dbType'=>'decimal(16,8)'),
	);
}