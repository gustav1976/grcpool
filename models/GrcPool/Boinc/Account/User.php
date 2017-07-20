<?php
abstract class GrcPool_Boinc_Account_User_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_accountId = 0;
	private $_name = '';
	private $_totalCredit = 0;
	private $_avgCredit = 0;
	private $_cpid = '';
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setAccountId(int $int) {$this->_accountId=$int;}
	public function getAccountId():int {return $this->_accountId;}
	public function setName(string $string) {$this->_name=$string;}
	public function getName():string {return $this->_name;}
	public function setTotalCredit(float $float) {$this->_totalCredit=$float;}
	public function getTotalCredit():float {return $this->_totalCredit;}
	public function setAvgCredit(float $float) {$this->_avgCredit=$float;}
	public function getAvgCredit():float {return $this->_avgCredit;}
	public function setCpid(string $string) {$this->_cpid=$string;}
	public function getCpid():string {return $this->_cpid;}
}

abstract class GrcPool_Boinc_Account_User_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'boinc_account_user';
	protected $_model = 'GrcPool_Boinc_Account_User_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'accountId' => array('type'=>'INT','dbType'=>'mediumint(4)'),
		'name' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'totalCredit' => array('type'=>'FLOAT','dbType'=>'decimal(22,6)'),
		'avgCredit' => array('type'=>'FLOAT','dbType'=>'decimal(22,6)'),
		'cpid' => array('type'=>'STRING','dbType'=>'varchar(50)'),
	);
}