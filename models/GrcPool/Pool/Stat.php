<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Pool_Stat_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_name = '';
	private $_value = '';
	private $_theTime = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setName(string $string) {$this->_name=$string;}
	public function getName():string {return $this->_name;}
	public function setValue(string $string) {$this->_value=$string;}
	public function getValue():string {return $this->_value;}
	public function setTheTime(int $int) {$this->_theTime=$int;}
	public function getTheTime():int {return $this->_theTime;}
}

abstract class GrcPool_Pool_Stat_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'pool_stat';
	protected $_model = 'GrcPool_Pool_Stat_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'name' => array('type'=>'STRING','dbType'=>'varchar(25)'),
		'value' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'theTime' => array('type'=>'INT','dbType'=>'int(11)'),
	);
}