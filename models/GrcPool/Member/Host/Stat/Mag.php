<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Member_Host_Stat_Mag_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_memberId = 0;
	private $_hostId = 0;
	private $_accountId = 0;
	private $_thetime = 0;
	private $_mag = 0;
	private $_avgCredit = 0.000000;
	private $_poolId = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setMemberId(int $int) {$this->_memberId=$int;}
	public function getMemberId():int {return $this->_memberId;}
	public function setHostId(int $int) {$this->_hostId=$int;}
	public function getHostId():int {return $this->_hostId;}
	public function setAccountId(int $int) {$this->_accountId=$int;}
	public function getAccountId():int {return $this->_accountId;}
	public function setThetime(int $int) {$this->_thetime=$int;}
	public function getThetime():int {return $this->_thetime;}
	public function setMag(float $float) {$this->_mag=$float;}
	public function getMag():float {return $this->_mag;}
	public function setAvgCredit(float $float) {$this->_avgCredit=$float;}
	public function getAvgCredit():float {return $this->_avgCredit;}
	public function setPoolId(int $int) {$this->_poolId=$int;}
	public function getPoolId():int {return $this->_poolId;}
}

abstract class GrcPool_Member_Host_Stat_Mag_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'member_host_stat_mag';
	protected $_model = 'GrcPool_Member_Host_Stat_Mag_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'memberId' => array('type'=>'INT','dbType'=>'int(11)'),
		'hostId' => array('type'=>'INT','dbType'=>'int(11)'),
		'accountId' => array('type'=>'INT','dbType'=>'smallint(5)'),
		'thetime' => array('type'=>'INT','dbType'=>'int(11)'),
		'mag' => array('type'=>'FLOAT','dbType'=>'decimal(9,2)'),
		'avgCredit' => array('type'=>'FLOAT','dbType'=>'decimal(22,6)'),
		'poolId' => array('type'=>'INT','dbType'=>'tinyint(2)'),
	);
}