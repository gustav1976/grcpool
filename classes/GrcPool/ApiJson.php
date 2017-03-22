<?php
class GrcPool_ApiJson {
	
	private $errors;
	private $data;
	
	public function setErrors($errors) {
		$this->errors = $errors;
	}
	public function setData($data) {
		$this->data = $data;
	}
	
	public function toJson() {
		$result = array();
		if ($this->errors) {
			$this->result['errors'] = $this->errors;
		} else {
			$this->result = $this->data;
		}
		return json_encode($this->result);
	}
	
	
}