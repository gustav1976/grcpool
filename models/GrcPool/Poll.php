<?php
abstract class GrcPool_Poll_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_expires = 0;
	private $_type = '';
	private $_title = '';
	private $_question = '';
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setExpires(int $int) {$this->_expires=$int;}
	public function getExpires():int {return $this->_expires;}
	public function setType(string $string) {$this->_type=$string;}
	public function getType():string {return $this->_type;}
	public function setTitle(string $string) {$this->_title=$string;}
	public function getTitle():string {return $this->_title;}
	public function setQuestion(string $string) {$this->_question=$string;}
	public function getQuestion():string {return $this->_question;}
}

abstract class GrcPool_Poll_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'poll';
	protected $_model = 'GrcPool_Poll_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'expires' => array('type'=>'INT','dbType'=>'int(11)'),
		'type' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'title' => array('type'=>'STRING','dbType'=>'varchar(500)'),
		'question' => array('type'=>'STRING','dbType'=>'varchar(500)'),
	);
}