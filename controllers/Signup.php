<?php
class GrcPool_Controller_Signup extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction() {

		$this->view->email = $this->post('memberEmail');
		$this->view->username = $this->post('memberName');
		$settingsDao = new GrcPool_Settings_DAO();
		$this->view->poolSignup = $settingsDao->getValueWithName(Constants::SETTINGS_POOL_SIGN_UP);
		
		if ($this->view->email != '' || $this->view->username != '') {
			$dao = new GrcPool_Member_DAO();
			$obj = new GrcPool_Member_OBJ();
			$errors = '';
			if (strlen($this->post('memberName')) < 6) {
				$errors = 'Please provide a longer user name, 6 characters minimum.<br/>';
			} else {
				$test = $dao->initWithUsername($this->post('memberName'));
				if ($test != null) {
					$errors .= 'This username is already taken.<br/>';
				}
			}
			if (!Utils::isValidEmail($this->post('memberEmail'))) {
				$errors .= 'Your email appears to be improperly formatted.<br/>';
			} else {
				$test = $dao->initWithEmail($this->post('memberEmail'));
				if ($test != null) {
					$errors .= 'This email is already registered.<br/>';
				}
			}
			if (strlen($this->post('password')) < 8) {
				$errors .= 'Please provide a longer password, 8 characters minimum.<br/>';
			} else {
				if ($this->post('password') != $this->post('confirmPassword')) {
					$errors .= 'Your passwords did not match.<br/>';
				}
			}
			if (!$errors) {
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
			}
			if ($errors) {
				$this->addErrorMsg($errors);
			} else {
				$obj->setPoolId($settingsDao->getValueWithName(Constants::SETTINGS_POOL_SIGN_UP));
				$obj->setEmail($this->post('memberEmail'));
				$obj->setRegTime(time());
				$obj->setUsername($this->post('memberName'));
				$obj->setPassword(UserHelper::encodePassword($this->post('password')));
				$obj->setPasswordHash(md5($this->post('password').strtolower($this->post('memberName'))));
				$dao->save($obj);
				Server::go('/login/index/register');
			}
		}
		
	}
	
}