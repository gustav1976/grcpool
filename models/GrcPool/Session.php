<?php
abstract class GrcPool_Session_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_userid = 0;
	private $_session = '';
	private $_created = NULL;
	private $_ip = '';
	private $_username = '';
	private $_lastUsed = 0;
	private $_remember = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setUserid(int $int) {$this->_userid=$int;}
	public function getUserid():int {return $this->_userid;}
	public function setSession(string $string) {$this->_session=$string;}
	public function getSession():string {return $this->_session;}
	public function getCreated():string {return $this->_created;}
	public function setIp(string $string) {$this->_ip=$string;}
	public function getIp():string {return $this->_ip;}
	public function setUsername(string $string) {$this->_username=$string;}
	public function getUsername():string {return $this->_username;}
	public function setLastUsed(int $int) {$this->_lastUsed=$int;}
	public function getLastUsed():int {return $this->_lastUsed;}
	public function setRemember(int $int) {$this->_remember=$int;}
	public function getRemember():int {return $this->_remember;}
}

abstract class GrcPool_Session_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'session';
	protected $_model = 'GrcPool_Session_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11) unsigned'),
		'userid' => array('type'=>'INT','dbType'=>'int(8)'),
		'session' => array('type'=>'STRING','dbType'=>'varchar(40)'),
		'ip' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'username' => array('type'=>'STRING','dbType'=>'varchar(255)'),
		'lastUsed' => array('type'=>'INT','dbType'=>'int(11)'),
		'remember' => array('type'=>'INT','dbType'=>'tinyint(1)'),
	);
}