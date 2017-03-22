<?php
abstract class GrcPool_Member_Host_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_memberId = 0;
	private $_cpId = '';
	private $_hostName = '';
	private $_clientVersion = '';
	private $_model = '';
	private $_osName = '';
	private $_osVersion = '';
	private $_virtualBoxVersion = '';
	private $_productName = '';
	private $_firstContact = 0;
	private $_lastContact = 0;
	private $_numberOfCpus = 0;
	private $_numberOfCudas = 0;
	private $_numberOfAmds = 0;
	private $_numberOfIntels = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setMemberId(int $int) {$this->_memberId=$int;}
	public function getMemberId():int {return $this->_memberId;}
	public function setCpId(string $string) {$this->_cpId=$string;}
	public function getCpId():string {return $this->_cpId;}
	public function setHostName(string $string) {$this->_hostName=$string;}
	public function getHostName():string {return $this->_hostName;}
	public function setClientVersion(string $string) {$this->_clientVersion=$string;}
	public function getClientVersion():string {return $this->_clientVersion;}
	public function setModel(string $string) {$this->_model=$string;}
	public function getModel():string {return $this->_model;}
	public function setOsName(string $string) {$this->_osName=$string;}
	public function getOsName():string {return $this->_osName;}
	public function setOsVersion(string $string) {$this->_osVersion=$string;}
	public function getOsVersion():string {return $this->_osVersion;}
	public function setVirtualBoxVersion(string $string) {$this->_virtualBoxVersion=$string;}
	public function getVirtualBoxVersion():string {return $this->_virtualBoxVersion;}
	public function setProductName(string $string) {$this->_productName=$string;}
	public function getProductName():string {return $this->_productName;}
	public function setFirstContact(int $int) {$this->_firstContact=$int;}
	public function getFirstContact():int {return $this->_firstContact;}
	public function setLastContact(int $int) {$this->_lastContact=$int;}
	public function getLastContact():int {return $this->_lastContact;}
	public function setNumberOfCpus(int $int) {$this->_numberOfCpus=$int;}
	public function getNumberOfCpus():int {return $this->_numberOfCpus;}
	public function setNumberOfCudas(int $int) {$this->_numberOfCudas=$int;}
	public function getNumberOfCudas():int {return $this->_numberOfCudas;}
	public function setNumberOfAmds(int $int) {$this->_numberOfAmds=$int;}
	public function getNumberOfAmds():int {return $this->_numberOfAmds;}
	public function setNumberOfIntels(int $int) {$this->_numberOfIntels=$int;}
	public function getNumberOfIntels():int {return $this->_numberOfIntels;}
}

abstract class GrcPool_Member_Host_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'member_host';
	protected $_model = 'GrcPool_Member_Host_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'memberId' => array('type'=>'INT','dbType'=>'int(11)'),
		'cpId' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'hostName' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'clientVersion' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'model' => array('type'=>'STRING','dbType'=>'varchar(200)'),
		'osName' => array('type'=>'STRING','dbType'=>'varchar(200)'),
		'osVersion' => array('type'=>'STRING','dbType'=>'varchar(200)'),
		'virtualBoxVersion' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'productName' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'firstContact' => array('type'=>'INT','dbType'=>'int(11)'),
		'lastContact' => array('type'=>'INT','dbType'=>'int(11)'),
		'numberOfCpus' => array('type'=>'INT','dbType'=>'mediumint(5)'),
		'numberOfCudas' => array('type'=>'INT','dbType'=>'mediumint(3)'),
		'numberOfAmds' => array('type'=>'INT','dbType'=>'mediumint(3)'),
		'numberOfIntels' => array('type'=>'INT','dbType'=>'mediumint(3)'),
	);
}