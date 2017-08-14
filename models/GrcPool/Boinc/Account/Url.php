<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Boinc_Account_Url_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_accountId = 0;
	private $_url = '';
	private $_signature = '';
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setAccountId(int $int) {$this->_accountId=$int;}
	public function getAccountId():int {return $this->_accountId;}
	public function setUrl(string $string) {$this->_url=$string;}
	public function getUrl():string {return $this->_url;}
	public function setSignature(string $string) {$this->_signature=$string;}
	public function getSignature():string {return $this->_signature;}
}

abstract class GrcPool_Boinc_Account_Url_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'boinc_account_url';
	protected $_model = 'GrcPool_Boinc_Account_Url_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'smallint(5)'),
		'accountId' => array('type'=>'INT','dbType'=>'smallint(5)'),
		'url' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'signature' => array('type'=>'STRING','dbType'=>'varchar(500)'),
	);
}