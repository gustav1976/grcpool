<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Status_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_name = '';
	private $_block = 0;
	private $_hash = '';
	private $_diff = 0;
	private $_thetime = 0;
	private $_balance = 0;
	private $_connections = 0;
	private $_version = '';
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setName(string $string) {$this->_name=$string;}
	public function getName():string {return $this->_name;}
	public function setBlock(int $int) {$this->_block=$int;}
	public function getBlock():int {return $this->_block;}
	public function setHash(string $string) {$this->_hash=$string;}
	public function getHash():string {return $this->_hash;}
	public function setDiff(float $float) {$this->_diff=$float;}
	public function getDiff():float {return $this->_diff;}
	public function setThetime(int $int) {$this->_thetime=$int;}
	public function getThetime():int {return $this->_thetime;}
	public function setBalance(float $float) {$this->_balance=$float;}
	public function getBalance():float {return $this->_balance;}
	public function setConnections(int $int) {$this->_connections=$int;}
	public function getConnections():int {return $this->_connections;}
	public function setVersion(string $string) {$this->_version=$string;}
	public function getVersion():string {return $this->_version;}
}

abstract class GrcPool_Status_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'status';
	protected $_model = 'GrcPool_Status_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(5)'),
		'name' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'block' => array('type'=>'INT','dbType'=>'int(11)'),
		'hash' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'diff' => array('type'=>'FLOAT','dbType'=>'decimal(12,8)'),
		'thetime' => array('type'=>'INT','dbType'=>'int(11)'),
		'balance' => array('type'=>'FLOAT','dbType'=>'decimal(16,8)'),
		'connections' => array('type'=>'INT','dbType'=>'int(4)'),
		'version' => array('type'=>'STRING','dbType'=>'varchar(50)'),
	);
}