<?php
class Property {

	private $_propFile;
	
	public function __construct($propertyFile) {
		if (file_exists($propertyFile)) {
			$this->_propFile = json_decode(file_get_contents($propertyFile),true);
		}
	}
	
	public function get($name) {
		if (isset($this->_propFile[$name])) {
			return $this->_propFile[$name];
		}
	}

}