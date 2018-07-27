<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Member_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_email = '';
	private $_username = '';
	private $_poolId = 1;
	private $_password = '';
	private $_passwordHash = '';
	private $_regTime = 0;
	private $_verified = 0;
	private $_grcAddress = '';
	private $_donation = 0.00;
	private $_verifyKey = '';
	private $_twoFactor = 0;
	private $_twoFactorKey = '';
	private $_apiKey = '';
	private $_apiSecret = '';
	private $_minPayout = 1;
	private $_sparcAddress = '';
	private $_minSparcPayout = 500;
	private $_failCount = 0;
	private $_loginEmail = 1;
	private $_2faLogin = 1;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setEmail(string $string) {$this->_email=$string;}
	public function getEmail():string {return $this->_email;}
	public function setUsername(string $string) {$this->_username=$string;}
	public function getUsername():string {return $this->_username;}
	public function setPoolId(int $int) {$this->_poolId=$int;}
	public function getPoolId():int {return $this->_poolId;}
	public function setPassword(string $string) {$this->_password=$string;}
	public function getPassword():string {return $this->_password;}
	public function setPasswordHash(string $string) {$this->_passwordHash=$string;}
	public function getPasswordHash():string {return $this->_passwordHash;}
	public function setRegTime(int $int) {$this->_regTime=$int;}
	public function getRegTime():int {return $this->_regTime;}
	public function setVerified(int $int) {$this->_verified=$int;}
	public function getVerified():int {return $this->_verified;}
	public function setGrcAddress(string $string) {$this->_grcAddress=$string;}
	public function getGrcAddress():string {return $this->_grcAddress;}
	public function setDonation(float $float) {$this->_donation=$float;}
	public function getDonation():float {return $this->_donation;}
	public function setVerifyKey(string $string) {$this->_verifyKey=$string;}
	public function getVerifyKey():string {return $this->_verifyKey;}
	public function setTwoFactor(int $int) {$this->_twoFactor=$int;}
	public function getTwoFactor():int {return $this->_twoFactor;}
	public function setTwoFactorKey(string $string) {$this->_twoFactorKey=$string;}
	public function getTwoFactorKey():string {return $this->_twoFactorKey;}
	public function setApiKey(string $string) {$this->_apiKey=$string;}
	public function getApiKey():string {return $this->_apiKey;}
	public function setApiSecret(string $string) {$this->_apiSecret=$string;}
	public function getApiSecret():string {return $this->_apiSecret;}
	public function setMinPayout(int $int) {$this->_minPayout=$int;}
	public function getMinPayout():int {return $this->_minPayout;}
	public function setSparcAddress(string $string) {$this->_sparcAddress=$string;}
	public function getSparcAddress():string {return $this->_sparcAddress;}
	public function setMinSparcPayout(int $int) {$this->_minSparcPayout=$int;}
	public function getMinSparcPayout():int {return $this->_minSparcPayout;}
	public function setFailCount(int $int) {$this->_failCount=$int;}
	public function getFailCount():int {return $this->_failCount;}
	public function setLoginEmail(int $int) {$this->_loginEmail=$int;}
	public function getLoginEmail():int {return $this->_loginEmail;}
	public function set2faLogin(int $int) {$this->_2faLogin=$int;}
	public function get2faLogin():int {return $this->_2faLogin;}
}

abstract class GrcPool_Member_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'member';
	protected $_model = 'GrcPool_Member_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'email' => array('type'=>'STRING','dbType'=>'varchar(200)'),
		'username' => array('type'=>'STRING','dbType'=>'varchar(25)'),
		'poolId' => array('type'=>'INT','dbType'=>'smallint(2)'),
		'password' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'passwordHash' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'regTime' => array('type'=>'INT','dbType'=>'int(11)'),
		'verified' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'grcAddress' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'donation' => array('type'=>'FLOAT','dbType'=>'decimal(5,2)'),
		'verifyKey' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'twoFactor' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'twoFactorKey' => array('type'=>'STRING','dbType'=>'varchar(40)'),
		'apiKey' => array('type'=>'STRING','dbType'=>'varchar(32)'),
		'apiSecret' => array('type'=>'STRING','dbType'=>'varchar(88)'),
		'minPayout' => array('type'=>'INT','dbType'=>'smallint(5)'),
		'sparcAddress' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'minSparcPayout' => array('type'=>'INT','dbType'=>'int(5)'),
		'failCount' => array('type'=>'INT','dbType'=>'smallint(1)'),
		'loginEmail' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'2faLogin' => array('type'=>'INT','dbType'=>'tinyint(1)'),
	);
}