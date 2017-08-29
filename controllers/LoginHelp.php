<?php
class GrcPool_Controller_LoginHelp extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function passwordAction() {
		$dao = new GrcPool_Member_DAO();
		$this->view->showForm = true;
		$id = $this->args(0,Controller::VALIDATION_NUMBER);
		if (!$id) {
			Server::goHome();
		}
		$time = $this->args(1,Controller::VALIDATION_NUMBER);
		if ($time < time()-86400) {
			Server::goHome();
		}
		$member = $dao->initWithKey($id);
		if (!$member) {
			Server::goHome();
		}
		$hash = $this->args(2,Controller::VALIDATION_ALPHANUM);
		if (md5($time.$member->getPassword()) != $hash) {
			Server::goHome();
		}
		$this->view->twoFactor = $member->getTwoFactor();
		if ($this->post('password') != '') {
			if (strlen($this->post('password')) < 8 || $this->post('password') !== $this->post('confirmPassword')) {
				$this->addErrorMsg('Your passwords did not match or was not long enough.');
			} else if ($this->view->twoFactor && !UserHelper::authenticate($member,$this->post('authorization'))) {
				$this->addErrorMsg('Your authorization failed.');
			} else {
				$member->setPassword(UserHelper::encodePassword($this->post('password')));
				if ($this->post('boinc') == 1) {
					$member->setPasswordHash(md5($this->post('password').strtolower($member->getUsername())));
				}
				$dao->save($member);
				$this->addSuccessMsg('Your password has been updated. You may try logging in now.');
				$this->view->showForm = false;
			}
		}
	}
	
	public function indexAction() {
		$dao = new GrcPool_Member_DAO();
		if ($this->post('username')) {
			$errors = '';
			if ($this->post('g-recaptcha-response') != "") {
				$property = new Property(Constants::PROPERTY_FILE);				
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify?secret='.$property->get('googleCaptchaPrivate').'&response='.$this->post('g-recaptcha-response'),
				));
				$gResult = json_decode(curl_exec($curl),true);
				if (!$gResult['success']) {
					$errors .= 'You appear to be a robot, try again.<br/>';
				}
			} else {
				$errors .= 'Please verify you are not a robot.<br/>';
			}
			if ($errors) {
				$this->addErrorMsg($errors);
			} else {
				$member = $dao->initWithUsername($this->post('username'));
				if ($member) {
					$time = time();
					$hash = md5($time.$member->getPassword());
					$link = '/loginHelp/password/'.$member->getId().'/'.$time.'/'.$hash;
					$email = new Email();
					$email->addFrom(Constants::ADMIN_EMAIL_ADDRESS);
					$email->addTo($member->getEmail());
					$email->setSubject(Constants::BOINC_POOL_NAME.' password reset');
					$email->setMessage(Email::getPasswordResetEmail($member->getUsername(),$link));
					$email->send();
					$this->addSuccessMsg('An email has been sent to the email address on file.');
				} else {
					$this->addErrorMsg('There was a problem trying to find the username.');
				}
			}
		}
		
	}
}