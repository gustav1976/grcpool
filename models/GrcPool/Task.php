<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Task_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_name = '';
	private $_message = '';
	private $_success = 0;
	private $_theTime = 0;
	private $_timeStarted = 0.0000;
	private $_timeCompleted = 0.0000;
	private $_info = '';
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setName(string $string) {$this->_name=$string;}
	public function getName():string {return $this->_name;}
	public function setMessage(string $string) {$this->_message=$string;}
	public function getMessage():string {return $this->_message;}
	public function setSuccess(int $int) {$this->_success=$int;}
	public function getSuccess():int {return $this->_success;}
	public function setTheTime(int $int) {$this->_theTime=$int;}
	public function getTheTime():int {return $this->_theTime;}
	public function setTimeStarted(float $float) {$this->_timeStarted=$float;}
	public function getTimeStarted():float {return $this->_timeStarted;}
	public function setTimeCompleted(float $float) {$this->_timeCompleted=$float;}
	public function getTimeCompleted():float {return $this->_timeCompleted;}
	public function setInfo(string $string) {$this->_info=$string;}
	public function getInfo():string {return $this->_info;}
}

abstract class GrcPool_Task_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'task';
	protected $_model = 'GrcPool_Task_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'mediumint(5)'),
		'name' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'message' => array('type'=>'STRING','dbType'=>'varchar(500)'),
		'success' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'theTime' => array('type'=>'INT','dbType'=>'int(11)'),
		'timeStarted' => array('type'=>'FLOAT','dbType'=>'decimal(15,4)'),
		'timeCompleted' => array('type'=>'FLOAT','dbType'=>'decimal(15,4)'),
		'info' => array('type'=>'STRING','dbType'=>'varchar(1000)'),
	);
}