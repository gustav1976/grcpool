<?php 
class Cache {
	public function __construct() {
		
	}
	public function get($name,$expire = -1) {
		$d = Property::getValueFor('cacheDir').'/'.$name;
		if (file_exists($d) && ($expire == -1 || date('U') < filemtime($d)+$expire)) {
			return unserialize(file_get_contents($d));
		} else {
			return null;
		}
	}
	public function set($data,$name) {
		file_put_contents(Property::getValueFor('cacheDir').'/'.$name,serialize($data));
	}
}