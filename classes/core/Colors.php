<?php 
class Colors {

	private $_name = 'custom';
	
	public function __constructor($name = 'custom') {
		$this->_name = $name;			
	}
	
// 	public static function decToHex($val) {
// 		$hex = '';
// 		$parts = explode(",",$val);
// 		$hex .= dechex($parts[0]);
// 		$hex .= dechex($parts[1]);
// 		$hex .= dechex($parts[2]);
// 		return '#'.$hex;
// 	}
	
	public function getAsArray() {
		return json_decode(file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'colors.json'),true);
	}
	
}