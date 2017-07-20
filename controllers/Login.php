<?php
use PragmaRX\Google2FA\Google2FA;
class GrcPool_Controller_Login extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction() {
		
		if ($this->args(0) == 'register') {
			$this->addSuccessMsg('You have successfully registered. Please login below.');
		}
		
		if ($this->post('memberName')) {
			$memberDao = new GrcPool_Member_DAO();
			$member = $memberDao->initWithUsername($this->post('memberName'));
			if ($member) {
				if (UserHelper::passwordHashMatch($this->post('password'),$member->getPassword())) {
					$goOn = true;
					if ($member->getTwoFactor()) {
						$google2fa = new Google2FA();
						$token = $this->post('token');
						$valid = false;
						try {
							$valid = $google2fa->verifyKey($member->getTwoFactorKey(),$token);
						} catch (Exception $e) {

						}
						if (!$valid) {
							$this->addErrorMsg('Your token was not correct.');
							$goOn = false;
						}
					}
					if ($goOn) {
						$sessionDao = new GrcPool_Session_DAO();
						$session = new GrcPool_Session_OBJ();
						$session->setUserId($member->getId());
						$session->setIp($_SERVER['REMOTE_ADDR']);
						$session->setLastUsed(time());
						$session->setUsername($member->getUsername());
						$session->setSession(md5(UserHelper::generateRandomString(40).microtime()));
						$session->setRemember($this->post('rememberMe')=='true'?1:0);
						$sessionDao->save($session);
						if ($this->post('remember')=='true') {
							$expire = time()+60*60*24*30*5;
						} else {
							$expire = 0;
						}
						setcookie(Constants::SESSION_COOKIE_NAME,$session->getSession(),$expire,"/",Constants::SESSION_COOKIE_DOMAIN,true,true);
						Server::go('/account/index');
					}
				} else {
					$this->addErrorMsg('Sorry, your username or password did not match any records');
				}
			} else {
				$this->addErrorMsg('Sorry, your username or password did not match any records');
			}
		}
		
	}
	
}