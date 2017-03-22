<?php
class WebSocket {
	
	private $_protocol;
	private $_domain;
	private $_path;
	private $_port;
	
	public function __construct() {

	}
	
	private function post($cmd,$json) {
		$ch = curl_init();
		$url = 'http://127.0.0.1:3001/'.$cmd;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($json))
		);
		if(curl_exec($ch) === false) {
			//echo 'Curl error: ' . curl_error($ch);
		} else {
			//echo 'Operation completed without any errors';
		}
		curl_close($ch);
	}
	
	public function updateTicker($array) {
		$this->post('updateTicker',json_encode($array));
	}
	
	
}