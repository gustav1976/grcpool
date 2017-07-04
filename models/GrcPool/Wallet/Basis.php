<?php
abstract class GrcPool_Wallet_Basis_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_basis = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setBasis($int) {$this->_basis=$int;}
	public function getBasis() {return $this->_basis;}
}

abstract class GrcPool_Wallet_Basis_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'wallet_basis';
	protected $_model = 'GrcPool_Wallet_Basis_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'smallint(2)'),
		'basis' => array('type'=>'INT','dbType'=>'bigint(20)'),
	);
}