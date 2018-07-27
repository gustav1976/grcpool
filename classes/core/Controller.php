<?php
abstract class Controller {
	
	public $view;
	public $input;
	private $_uri;
	private $_errors;
	private $_success;
	private $_renderView;
	private $_name;
		
	const VALIDATION_NUMBER = 1;
	const VALIDATION_ALPHANUM = 2;
	
	public function __construct() {
		$this->view = new stdClass();	
		$this->input = new stdClass();
		$this->_errors = array();
		$this->_success = array();
	}
	public function getControllerName() {
		return $this->_name;
	}
	public function getUser() {
		global $USER;
		return $USER;
	}
	public function setUser($user) {
		global $USER;
		$USER = $user;
	}	
	public function setName($s) {
		$this->_name = $s;
	}
	public function setRenderView($str) {
		$this->_renderView = $str;	
	}
	public function getRenderViewForDisplay() {
		return str_replace('indexIndex','index',$this->_renderView);	
	}
	public function getRenderview() {
		return $this->_renderView;
	}
	public function callAction($action,$arg=null) {
		$this->{$action}($arg);
		$this->render();
	}
	public function addParam($name,$value) {
		$this->input->{$name} = $value;
	}
	
	public function setUri($s) {$this->_uri = $s;}
	public function getUri() {return $this->_uri;}
	public function formUnserialize($salt,$name) {
		if (md5($_POST[$name].$salt) == $_POST[$name.'Hash']) {
			return unserialize(gzinflate(base64_decode($_POST[$name])));	
		} else {
			return null;
		}
	}
	public function formSerialize($data,$salt,$name) {
		$ser = base64_encode(gzdeflate(serialize($data)));
		$hash = md5($ser.$salt);
		return '<input type="hidden" name="'.$name.'" value="'.$ser.'"/><input type="hidden" name="'.$name.'Hash" value="'.$hash.'"/>';
	}
	public function formEscape($text) {
		return htmlspecialchars($text);
	}
	public function hasErrorMessages() {
		return $this->_errors;	
	}
	public function addErrorMsg($msg) {
		array_push($this->_errors,$msg);	
	}
	public function addSuccessMsg($msg) {
		array_push($this->_success,$msg);	
	}
	public function renderMessages() {
		$result = '';
		if ($this->_errors) {
			$result .= Bootstrap_Alert::error(implode('<br/>',$this->_errors));
		}
		if ($this->_success) {
			$result .= Bootstrap_Alert::success(implode('<br/>',$this->_success));
		}		
		return $result;
	}
	public function args($id,$validate = null) {
		if (isset($this->input->{'arg'.$id})) {
			if ($validate != null) {
				$str = $this->input->{'arg'.$id};
				switch ($validate) {
					case Controller::VALIDATION_NUMBER : return ctype_digit($str)?$str:null;
					case Controller::VALIDATION_ALPHANUM : return ctype_alnum($str)?$str:null; 
					default : return null;
				}	
			} else {
				return trim($this->input->{'arg'.$id});
			}
		} else {
			return null;
		}
	}
	public function forwardAction($action) {
		$array = get_object_vars($this->input);
		foreach ($array as $idx => $value) {
			if (strpos($idx,'arg') === 0) {
				continue;
			}
			unset($this->input->$idx);
		}
		$array = get_object_vars($this->view);		
		foreach ($array as $idx => $value) {unset($this->input->$idx);}		
		//echo '<pre>';print_r($this);exit;
		if (strstr($this->_renderView,'/')) {
			$parts = explode("/",$this->_renderView);
			$this->_renderView = $parts[0].'/'.strtolower($this->_name).ucfirst($action);
		} else {
			$this->_renderView = $action;
		}
		$this->callAction($action.'Action');					
		exit;
	}
	public function get($name,$trim = true) {
		if (isset($_GET[$name])) {
			if (is_array($_GET[$name])) {
				return $_GET[$name];
			} else {
				if ($trim) {
					return trim($_GET[$name]);
				} else {
					return $_GET[$name];
				}
			}
		} else {
			return null;
		}
	}	
	public function isPost() {
		return $_SERVER['REQUEST_METHOD']=='POST';
	}
	public function post($name,$trim = true) {
		if (isset($_POST[$name])) {
			if (is_array($_POST[$name])) {
				return $_POST[$name];
			} else {
				if ($trim) {
					return trim($_POST[$name]);
				} else {
					return $_POST[$name];
				}
			}
		}	
	}
}