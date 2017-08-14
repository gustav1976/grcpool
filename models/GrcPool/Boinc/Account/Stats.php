<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Boinc_Account_Stats_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_accountId = 0;
	private $_name = '';
	private $_value = '';
	private $_modTime = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setAccountId(int $int) {$this->_accountId=$int;}
	public function getAccountId():int {return $this->_accountId;}
	public function setName(string $string) {$this->_name=$string;}
	public function getName():string {return $this->_name;}
	public function setValue(string $string) {$this->_value=$string;}
	public function getValue():string {return $this->_value;}
	public function setModTime(int $int) {$this->_modTime=$int;}
	public function getModTime():int {return $this->_modTime;}
}

abstract class GrcPool_Boinc_Account_Stats_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'boinc_account_stats';
	protected $_model = 'GrcPool_Boinc_Account_Stats_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'accountId' => array('type'=>'INT','dbType'=>'mediumint(4)'),
		'name' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'value' => array('type'=>'STRING','dbType'=>'varchar(2000)'),
		'modTime' => array('type'=>'INT','dbType'=>'int(11)'),
	);
}