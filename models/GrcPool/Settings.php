<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Settings_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_theName = '';
	private $_theValue = '';
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setTheName(string $string) {$this->_theName=$string;}
	public function getTheName():string {return $this->_theName;}
	public function setTheValue(string $string) {$this->_theValue=$string;}
	public function getTheValue():string {return $this->_theValue;}
}

abstract class GrcPool_Settings_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'settings';
	protected $_model = 'GrcPool_Settings_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(3)'),
		'theName' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'theValue' => array('type'=>'STRING','dbType'=>'varchar(200)'),
	);
}