<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Boinc_Account_Key_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_accountId = 0;
	private $_poolId = 0;
	private $_strong = '';
	private $_weak = '';
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setAccountId(int $int) {$this->_accountId=$int;}
	public function getAccountId():int {return $this->_accountId;}
	public function setPoolId(int $int) {$this->_poolId=$int;}
	public function getPoolId():int {return $this->_poolId;}
	public function setStrong(string $string) {$this->_strong=$string;}
	public function getStrong():string {return $this->_strong;}
	public function setWeak(string $string) {$this->_weak=$string;}
	public function getWeak():string {return $this->_weak;}
}

abstract class GrcPool_Boinc_Account_Key_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'boinc_account_key';
	protected $_model = 'GrcPool_Boinc_Account_Key_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'smallint(5)'),
		'accountId' => array('type'=>'INT','dbType'=>'smallint(5)'),
		'poolId' => array('type'=>'INT','dbType'=>'tinyint(2)'),
		'strong' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'weak' => array('type'=>'STRING','dbType'=>'varchar(100)'),
	);
}