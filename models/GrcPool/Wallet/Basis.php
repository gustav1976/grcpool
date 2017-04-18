<?php
abstract class GrcPool_Wallet_Basis_MODEL {

	public function __construct() { }

	private $_basis = 0;
	public function setBasis(int $int) {$this->_basis=$int;}
	public function getBasis():int {return $this->_basis;}
}

abstract class GrcPool_Wallet_Basis_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'wallet_basis';
	protected $_model = 'GrcPool_Wallet_Basis_OBJ';
	protected $_primaryKey = 'basis';
	protected $_fields = array(
		'basis' => array('type'=>'INT','dbType'=>'bigint(20)'),
	);
}