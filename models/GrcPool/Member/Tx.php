<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Member_Tx_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_memberId = 0;
	private $_type = '';
	private $_amount = 0;
	private $_thetime = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setMemberId(int $int) {$this->_memberId=$int;}
	public function getMemberId():int {return $this->_memberId;}
	public function setType(string $string) {$this->_type=$string;}
	public function getType():string {return $this->_type;}
	public function setAmount(int $int) {$this->_amount=$int;}
	public function getAmount():int {return $this->_amount;}
	public function setThetime(int $int) {$this->_thetime=$int;}
	public function getThetime():int {return $this->_thetime;}
}

abstract class GrcPool_Member_Tx_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'member_tx';
	protected $_model = 'GrcPool_Member_Tx_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'memberId' => array('type'=>'INT','dbType'=>'int(11)'),
		'type' => array('type'=>'STRING','dbType'=>'varchar(20)'),
		'amount' => array('type'=>'INT','dbType'=>'bigint(20)'),
		'thetime' => array('type'=>'INT','dbType'=>'int(11)'),
	);
}