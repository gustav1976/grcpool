<?php
abstract class GrcPool_View_Member_Payout_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_username = '';
	private $_amount = 0;
	private $_donation = 0.00000000;
	private $_fee = 0.00000000;
	private $_tx = '';
	private $_thetime = 0;
	private $_calculation = '';
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setUsername(string $string) {$this->_username=$string;}
	public function getUsername():string {return $this->_username;}
	public function setAmount(float $float) {$this->_amount=$float;}
	public function getAmount():float {return $this->_amount;}
	public function setDonation(float $float) {$this->_donation=$float;}
	public function getDonation():float {return $this->_donation;}
	public function setFee(float $float) {$this->_fee=$float;}
	public function getFee():float {return $this->_fee;}
	public function setTx(string $string) {$this->_tx=$string;}
	public function getTx():string {return $this->_tx;}
	public function setThetime(int $int) {$this->_thetime=$int;}
	public function getThetime():int {return $this->_thetime;}
	public function setCalculation(string $string) {$this->_calculation=$string;}
	public function getCalculation():string {return $this->_calculation;}
}

abstract class GrcPool_View_Member_Payout_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'view_member_payout';
	protected $_model = 'GrcPool_View_Member_Payout_OBJ';
	protected $_primaryKey = '';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'username' => array('type'=>'STRING','dbType'=>'varchar(25)'),
		'amount' => array('type'=>'FLOAT','dbType'=>'decimal(16,8)'),
		'donation' => array('type'=>'FLOAT','dbType'=>'decimal(16,8)'),
		'fee' => array('type'=>'FLOAT','dbType'=>'decimal(16,8)'),
		'tx' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'thetime' => array('type'=>'INT','dbType'=>'int(11)'),
		'calculation' => array('type'=>'STRING','dbType'=>'varchar(4000)'),
	);
}