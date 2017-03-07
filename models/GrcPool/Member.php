<?php
abstract class GrcPool_Member_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_email = '';
	private $_username = '';
	private $_password = '';
	private $_passwordHash = '';
	private $_regTime = 0;
	private $_verified = 0;
	private $_grcAddress = '';
	private $_donation = 0.00;
	private $_verifyKey = '';
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setEmail(string $string) {$this->_email=$string;}
	public function getEmail():string {return $this->_email;}
	public function setUsername(string $string) {$this->_username=$string;}
	public function getUsername():string {return $this->_username;}
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
}

abstract class GrcPool_Member_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'member';
	protected $_model = 'GrcPool_Member_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'email' => array('type'=>'STRING','dbType'=>'varchar(200)'),
		'username' => array('type'=>'STRING','dbType'=>'varchar(25)'),
		'password' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'passwordHash' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'regTime' => array('type'=>'INT','dbType'=>'int(11)'),
		'verified' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'grcAddress' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'donation' => array('type'=>'FLOAT','dbType'=>'decimal(5,2)'),
		'verifyKey' => array('type'=>'STRING','dbType'=>'varchar(100)'),
	);
}