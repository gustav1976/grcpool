<?php
class GrcPool_Controller_EmailVerification extends GrcPool_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function indexAction() {
		$usrDao = new GrcPool_Member_DAO();
		$userid = $this->args(0,Controller::VALIDATION_NUMBER);
		$key = $this->args(1,Controller::VALIDATION_ALPHANUM);
		if ($userid && $key) {
			$usr = $usrDao->initWithKey($userid);
			if ($usr) {
				$parts = explode("_",$usr->getVerifyKey());
				if ($parts[0] + 3600*24 < time()) {
					$this->view->message = 'This verification has expired.';
					$usr->setVerifyKey('');
				} else {
					if ($parts[1] == $key) {
						$this->view->message = 'Your email is now verified, thanks!';
						$usr->setVerifyKey('');
						$usr->setVerified(1);
					} else {
						$this->view->message = 'Email verification failed, you may want to try again.';
						$usr->setVerifyKey('');
					}
				}
				$usrDao->save($usr);
			} else {
				$this->view->message = 'There was no email verification found for this member.';
			}
		} else {
			$this->view->message = 'There was no email verification found for this request.';
		}
	}
}