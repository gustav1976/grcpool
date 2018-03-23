<?php
abstract class Router {

	protected $_controllerPath;

	private $_uri;
	private $_uriParts;
	private $_routes;
	private $_action;
	private $_requestedAction;
	private $_modules;
	
	public function __construct($uri) {
		$this->_modules = array();
		if (substr($uri,strlen($uri)-1,1) == '/') {
			$uri = substr($uri,0,strlen($uri)-1);
		}
		if (strstr($uri,'?')) {
			$uri = substr($uri,0,strpos($uri,'?'));	
		}
		$this->_uri = $uri;
		$this->_uriParts = explode("/",$uri);
	}
	public function setModules($modules) {
		$this->_modules = $modules;
	}
	public function setRoutes($routes) {
		$this->_routes = $routes;	
	}
	private function getRoute() {
		if ($this->_routes) {
			foreach ($this->_routes->route as $route) {
				if (strpos($this->_uri,str_replace('/?','',(String)$route->uri)) === 0) {
					//echo '0 strpos('.$this->_uri.','.str_replace('/?','',(String)$route->uri).') = '.strpos($this->_uri,str_replace('/?','',(String)$route->uri)).'<BR>';
					$routeParts = explode("/",(String)$route->uri);
					//echo '1 '.$route->uri.' URI Part Count: '.count($this->_uriParts).'<br>';					
					if (count($routeParts) >= count($this->_uriParts)) {
						//echo '2 '.$route->uri.'<br>';					
						return $route;
					}
				}
			}
		}
		if (count($this->_uriParts) > 1) {
			$startIdx = 1;
			$module = '';
			if (array_search($this->_uriParts[1],$this->_modules) !== false) {
				$startIdx = 2;
				$module = $this->_uriParts[1];
			}
			if (!isset($this->_uriParts[$startIdx])) {
				$this->_uriParts[$startIdx] = 'index';
			}
			if (count($this->_uriParts) == $startIdx+1) {
				$this->_uriParts[$startIdx+1] = 'index';	
			}
			$routeXml = '<routes><route><module>'.$module.'</module><controller>'.ucfirst(preg_replace("/[^A-Za-z0-9]/","",$this->_uriParts[$startIdx])).'</controller><action>'.ucfirst(preg_replace("/[^A-Za-z0-9]/","",$this->_uriParts[$startIdx+1])).'</action><params>';
			$arg = 0;
			for ($i = $startIdx+2; $i < count($this->_uriParts); $i++) {
				$routeXml .= '<param><name>arg'.($arg++).'</name><default>'.$this->_uriParts[$i].'</default><type>string</type></param>';
			}
			$routeXml .= '</params></route></routes>';		
			$xml = simplexml_load_string($routeXml);
			return $xml->route;
		}
		$routeXml = '<routes><route><controller>Home</controller><action>index</action></route></routes>';		
		$xml = simplexml_load_string($routeXml);
		return $xml->route;
	}
		
	public function dispatch() {
		$route = $this->getRoute();
		$action = (String)$route->action;
		$urlAction = $action;
		if (is_numeric(substr($action,0,1))) {
			$action = 'id'.$action;
		}
		$this->_action = $action.'Action';
		$className = $this->_controllerPath;
		$viewPath = '';
		if ((String)$route->module != "") {
			$className .= '_'.ucfirst((String)$route->module);
			$viewPath = (String)$route->module.'/';
		}
		$className .= '_'.(String)$route->controller;
		if (!class_exists($className)) {
			if (file_exists(getenv("DOCUMENT_ROOT").'/../views/'.$viewPath.(lcfirst((String)$route->controller).ucfirst((String)$route->action)).'.php')) {
				$controller = new  $this->_controllerPath;
				$controller->setRenderView($viewPath.lcfirst((String)$route->controller).ucfirst((String)$route->action));
			} else {
				header("HTTP/1.0 404 Not Found");				
				$className = $this->_controllerPath.'_Home';
				$this->_action = 'indexAction';
				$controller = new $className;
				$controller->setRenderView('homeIndex');
			}
		} else {
			$controller = new $className;
			$controller->setRenderView($viewPath.lcfirst((String)$route->controller).ucfirst((String)$route->action));
		}
		$controller->setName((String)$route->controller);
		if (!method_exists($controller,$this->_action)) {
			if (method_exists($controller,'defaultAction')) {
				$this->_requestedAction = $urlAction;
				$this->_action = 'defaultAction';
				$controller->setRenderView(lcfirst((String)$route->controller).'Index');
			} else {
				header("HTTP/1.0 404 Not Found");				
				$className = $this->_controllerPath.'_Home';
				$controller = new $className;
				$this->_action = 'indexAction';
				$controller->setRenderView('homeIndex');
			}
		}
		$controller->setUri($this->_uri);
		$paramIdx = 0;
		$parts = explode("/",(String)$route->uri);
		if ((String)$route->module) {
			$firstParamIdx = 4;
		} else {
			$firstParamIdx = 3;
		}
		foreach ($parts as $idx => $part) {
			if ($part == '?') {
				$firstParamIdx = $idx;	
				break;
			}
		}
		if ($route->params) {
			foreach ($route->params->param as $param) {
				$type = (String)$param->type;
				$name = (String)$param->name;
				$default = (String)$param->default;
				if ($default === "NULL") {
					$default = null;
				}
				$controller->addParam($name,$default);
				if (isset($this->_uriParts[$paramIdx+$firstParamIdx])) {
					$value = trim((String)$this->_uriParts[$paramIdx+$firstParamIdx]);						
					if ($param->filter) {
						$filter = explode('|',(String)$param->filter);
						if (!in_array($value,$filter)) {
							continue;
						}
					}
					if ($type == 'int') {
						if (!preg_match("/[^0-9]/",$value)) {
							$controller->addParam($name,$value);
						}
					} else if ($type == 'string') {
						if (!preg_match("/[^0-9a-zA-Z_-]/",$value)) {
							$controller->addParam($name,$value);								
						}
					} else {
						echo 'undefined type '.$type;exit;	
					}
				}
				$paramIdx++;
			}
		}
		$controller->callAction($this->_action,$this->_requestedAction);
	}

}