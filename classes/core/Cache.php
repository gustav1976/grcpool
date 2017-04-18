<?php 
class Cache {
	
	public function __construct() {
		
	}
	
	private function getCacheDir() {
		$dir = dirname(__FILE__).'/../../cache';
		return $dir;
	}
	public function get($name,$expire = -1) {
		//if (!CACHE) return null;			
		$d = $this->getCacheDir().'/'.$name;
		if (file_exists($d) && ($expire == -1 || date('U') < filemtime($d)+$expire)) {
			return unserialize(file_get_contents($d));
		} else {
			return null;
		}
	}
	public function set($data,$name) {
		$d = $this->getCacheDir();
		file_put_contents($d.'/'.$name,serialize($data));
	}

}
