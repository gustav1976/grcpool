<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_View_Vote_Member_MODEL {

	public function __construct() { }

	private $_username = '';
	private $_id = 0;
	private $_questionId = 0;
	private $_answerId = 0;
	private $_time = 0;
	private $_memberId = 0;
	private $_rank = 0;
	private $_mag = 0.00;
	private $_balance = 0.00000000;
	private $_weight = 0.00000000;
	public function setUsername(string $string) {$this->_username=$string;}
	public function getUsername():string {return $this->_username;}
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setQuestionId(int $int) {$this->_questionId=$int;}
	public function getQuestionId():int {return $this->_questionId;}
	public function setAnswerId(int $int) {$this->_answerId=$int;}
	public function getAnswerId():int {return $this->_answerId;}
	public function setTime(int $int) {$this->_time=$int;}
	public function getTime():int {return $this->_time;}
	public function setMemberId(int $int) {$this->_memberId=$int;}
	public function getMemberId():int {return $this->_memberId;}
	public function setRank(int $int) {$this->_rank=$int;}
	public function getRank():int {return $this->_rank;}
	public function setMag(float $float) {$this->_mag=$float;}
	public function getMag():float {return $this->_mag;}
	public function setBalance(float $float) {$this->_balance=$float;}
	public function getBalance():float {return $this->_balance;}
	public function setWeight(float $float) {$this->_weight=$float;}
	public function getWeight():float {return $this->_weight;}
}

abstract class GrcPool_View_Vote_Member_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'view_vote_member';
	protected $_model = 'GrcPool_View_Vote_Member_OBJ';
	protected $_primaryKey = '';
	protected $_fields = array(
		'username' => array('type'=>'STRING','dbType'=>'varchar(25)'),
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'questionId' => array('type'=>'INT','dbType'=>'int(11)'),
		'answerId' => array('type'=>'INT','dbType'=>'int(11)'),
		'time' => array('type'=>'INT','dbType'=>'int(11)'),
		'memberId' => array('type'=>'INT','dbType'=>'int(11)'),
		'rank' => array('type'=>'INT','dbType'=>'smallint(3)'),
		'mag' => array('type'=>'FLOAT','dbType'=>'decimal(8,2)'),
		'balance' => array('type'=>'FLOAT','dbType'=>'decimal(16,8)'),
		'weight' => array('type'=>'FLOAT','dbType'=>'decimal(22,8)'),
	);
}