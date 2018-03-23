<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Poll_Answer_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_questionId = 0;
	private $_answer = '';
	private $_votes = 0;
	private $_share = 0.00000000;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setQuestionId(int $int) {$this->_questionId=$int;}
	public function getQuestionId():int {return $this->_questionId;}
	public function setAnswer(string $string) {$this->_answer=$string;}
	public function getAnswer():string {return $this->_answer;}
	public function setVotes(int $int) {$this->_votes=$int;}
	public function getVotes():int {return $this->_votes;}
	public function setShare(float $float) {$this->_share=$float;}
	public function getShare():float {return $this->_share;}
}

abstract class GrcPool_Poll_Answer_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'poll_answer';
	protected $_model = 'GrcPool_Poll_Answer_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'questionId' => array('type'=>'INT','dbType'=>'int(11)'),
		'answer' => array('type'=>'STRING','dbType'=>'varchar(2000)'),
		'votes' => array('type'=>'INT','dbType'=>'int(11)'),
		'share' => array('type'=>'FLOAT','dbType'=>'decimal(20,8)'),
	);
}