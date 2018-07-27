<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Sparc_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_accountId = 0;
	private $_name = '';
	private $_week = 0;
	private $_poolId = 0;
	private $_sparc = 0;
	private $_paid = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setAccountId(int $int) {$this->_accountId=$int;}
	public function getAccountId():int {return $this->_accountId;}
	public function setName(string $string) {$this->_name=$string;}
	public function getName():string {return $this->_name;}
	public function setWeek(int $int) {$this->_week=$int;}
	public function getWeek():int {return $this->_week;}
	public function setPoolId(int $int) {$this->_poolId=$int;}
	public function getPoolId():int {return $this->_poolId;}
	public function setSparc(float $float) {$this->_sparc=$float;}
	public function getSparc():float {return $this->_sparc;}
	public function setPaid(int $int) {$this->_paid=$int;}
	public function getPaid():int {return $this->_paid;}
}

abstract class GrcPool_Sparc_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'sparc';
	protected $_model = 'GrcPool_Sparc_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'accountId' => array('type'=>'INT','dbType'=>'mediumint(5)'),
		'name' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'week' => array('type'=>'INT','dbType'=>'smallint(2)'),
		'poolId' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'sparc' => array('type'=>'FLOAT','dbType'=>'decimal(16,8)'),
		'paid' => array('type'=>'INT','dbType'=>'tinyint(1)'),
	);
}