<?php
class GrcPool_Wallet_Basis_OBJ extends GrcPool_Wallet_Basis_MODEL {
	public function __construct() {
		parent::__construct();
	}
}

class GrcPool_Wallet_Basis_DAO extends GrcPool_Wallet_Basis_MODELDAO {

		public function getBasis() {
			$sql = 'select basis from '.$this->getFullTableName();
			$result = $this->query($sql);
			return $result[0]['basis']/COIN;
		}
		
		public function incrBasis($amount) {
			$sql = 'update '.$this->getFullTableName().' set basis = basis + '.($amount*COIN);
			$this->executeQuery($sql);
			return $this->getBasis();
		}
	
}