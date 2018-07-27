<?php
class GrcPool_Controller_Logout extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction() {
		setcookie(Constants::SESSION_COOKIE_NAME,"",time()-36000,"/",Constants::SESSION_COOKIE_DOMAIN,true,true);
		if (isset($_COOKIE[Constants::SESSION_COOKIE_NAME])) {$_COOKIE[Constants::SESSION_COOKIE_NAME] = "";}
		global $USER;
		$dao = new GrcPool_Session_DAO();
		$dao->disableWithUserId($USER->getId());
		$USER = new GrcPool_Member_OBJ();
	}
	
}