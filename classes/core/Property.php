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

	public static function getValueFor($name) {
		if (file_exists(Constants::PROPERTY_FILE)) {
			$props = json_decode(file_get_contents(Constants::PROPERTY_FILE),true);
			if (isset($props[$name])) {
				return $props[$name];
			}
		}
		return null;
	}

}