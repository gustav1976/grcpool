<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Member_Por_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_accountId = 0;
	private $_hostDbid = 0;
	private $_avgCredit = 0;
	private $_memberMag = 0;
	private $_poolMag = 0.00;
	private $_totalPor = 0;
	private $_amount = 0;
	private $_thetime = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setAccountId(int $int) {$this->_accountId=$int;}
	public function getAccountId():int {return $this->_accountId;}
	public function setHostDbid(int $int) {$this->_hostDbid=$int;}
	public function getHostDbid():int {return $this->_hostDbid;}
	public function setAvgCredit(float $float) {$this->_avgCredit=$float;}
	public function getAvgCredit():float {return $this->_avgCredit;}
	public function setMemberMag(float $float) {$this->_memberMag=$float;}
	public function getMemberMag():float {return $this->_memberMag;}
	public function setPoolMag(float $float) {$this->_poolMag=$float;}
	public function getPoolMag():float {return $this->_poolMag;}
	public function setTotalPor(int $int) {$this->_totalPor=$int;}
	public function getTotalPor():int {return $this->_totalPor;}
	public function setAmount(int $int) {$this->_amount=$int;}
	public function getAmount():int {return $this->_amount;}
	public function setThetime(int $int) {$this->_thetime=$int;}
	public function getThetime():int {return $this->_thetime;}
}

abstract class GrcPool_Member_Por_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'member_por';
	protected $_model = 'GrcPool_Member_Por_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'accountId' => array('type'=>'INT','dbType'=>'int(11)'),
		'hostDbid' => array('type'=>'INT','dbType'=>'int(11)'),
		'avgCredit' => array('type'=>'FLOAT','dbType'=>'decimal(22,6)'),
		'memberMag' => array('type'=>'FLOAT','dbType'=>'decimal(9,2)'),
		'poolMag' => array('type'=>'FLOAT','dbType'=>'decimal(12,2)'),
		'totalPor' => array('type'=>'INT','dbType'=>'bigint(20)'),
		'amount' => array('type'=>'INT','dbType'=>'bigint(20)'),
		'thetime' => array('type'=>'INT','dbType'=>'int(11)'),
	);
}