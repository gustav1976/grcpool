<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Poll_Question_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_title = '';
	private $_question = '';
	private $_expire = 0;
	private $_type = '';
	private $_bestAnswer = '';
	private $_totalShares = 0;
	private $_totalVotes = 0;
	private $_timeUpdated = 0;
	private $_moreInfo = '';
	private $_moneySupply = 0.00000000;
	private $_closed = 0;
	private $_totalPoolShares = 0.00000000;
	private $_poolCalcShares = 0.00000000;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setTitle(string $string) {$this->_title=$string;}
	public function getTitle():string {return $this->_title;}
	public function setQuestion(string $string) {$this->_question=$string;}
	public function getQuestion():string {return $this->_question;}
	public function setExpire(int $int) {$this->_expire=$int;}
	public function getExpire():int {return $this->_expire;}
	public function setType(string $string) {$this->_type=$string;}
	public function getType():string {return $this->_type;}
	public function setBestAnswer(string $string) {$this->_bestAnswer=$string;}
	public function getBestAnswer():string {return $this->_bestAnswer;}
	public function setTotalShares(float $float) {$this->_totalShares=$float;}
	public function getTotalShares():float {return $this->_totalShares;}
	public function setTotalVotes(int $int) {$this->_totalVotes=$int;}
	public function getTotalVotes():int {return $this->_totalVotes;}
	public function setTimeUpdated(int $int) {$this->_timeUpdated=$int;}
	public function getTimeUpdated():int {return $this->_timeUpdated;}
	public function setMoreInfo(string $string) {$this->_moreInfo=$string;}
	public function getMoreInfo():string {return $this->_moreInfo;}
	public function setMoneySupply(float $float) {$this->_moneySupply=$float;}
	public function getMoneySupply():float {return $this->_moneySupply;}
	public function setClosed(int $int) {$this->_closed=$int;}
	public function getClosed():int {return $this->_closed;}
	public function setTotalPoolShares(float $float) {$this->_totalPoolShares=$float;}
	public function getTotalPoolShares():float {return $this->_totalPoolShares;}
	public function setPoolCalcShares(float $float) {$this->_poolCalcShares=$float;}
	public function getPoolCalcShares():float {return $this->_poolCalcShares;}
}

abstract class GrcPool_Poll_Question_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'poll_question';
	protected $_model = 'GrcPool_Poll_Question_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'title' => array('type'=>'STRING','dbType'=>'varchar(500)'),
		'question' => array('type'=>'STRING','dbType'=>'varchar(2000)'),
		'expire' => array('type'=>'INT','dbType'=>'int(11)'),
		'type' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'bestAnswer' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'totalShares' => array('type'=>'FLOAT','dbType'=>'decimal(20,8)'),
		'totalVotes' => array('type'=>'INT','dbType'=>'int(11)'),
		'timeUpdated' => array('type'=>'INT','dbType'=>'int(11)'),
		'moreInfo' => array('type'=>'STRING','dbType'=>'varchar(1000)'),
		'moneySupply' => array('type'=>'FLOAT','dbType'=>'decimal(20,8)'),
		'closed' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'totalPoolShares' => array('type'=>'FLOAT','dbType'=>'decimal(20,8)'),
		'poolCalcShares' => array('type'=>'FLOAT','dbType'=>'decimal(20,8)'),
	);
}