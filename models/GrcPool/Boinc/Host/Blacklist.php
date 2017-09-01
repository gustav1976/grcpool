<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Boinc_Host_Blacklist_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_accountId = 0;
	private $_hostDbid = 0;
	private $_thetime = 0;
	private $_memberId = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setAccountId(int $int) {$this->_accountId=$int;}
	public function getAccountId():int {return $this->_accountId;}
	public function setHostDbid(int $int) {$this->_hostDbid=$int;}
	public function getHostDbid():int {return $this->_hostDbid;}
	public function setThetime(int $int) {$this->_thetime=$int;}
	public function getThetime():int {return $this->_thetime;}
	public function setMemberId(int $int) {$this->_memberId=$int;}
	public function getMemberId():int {return $this->_memberId;}
}

abstract class GrcPool_Boinc_Host_Blacklist_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'boinc_host_blacklist';
	protected $_model = 'GrcPool_Boinc_Host_Blacklist_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'accountId' => array('type'=>'INT','dbType'=>'mediumint(5)'),
		'hostDbid' => array('type'=>'INT','dbType'=>'int(11)'),
		'thetime' => array('type'=>'INT','dbType'=>'int(11)'),
		'memberId' => array('type'=>'INT','dbType'=>'int(11)'),
	);
}