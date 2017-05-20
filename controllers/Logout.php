<?php
class GrcPool_Controller_Logout extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction() {
		setcookie(SESSION_COOKIE_NAME,"",time()-36000,"/",SESSION_COOKIE_DOMAIN,true,true);
		if (isset($_COOKIE[SESSION_COOKIE_NAME])) {$_COOKIE[SESSION_COOKIE_NAME] = "";}
		global $USER;
		$USER = new GrcPool_Member_OBJ();
	}
	
}