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
		$this->view->captcha = false;
		if ($this->post('memberName')) {
			$memberDao = new GrcPool_Member_DAO();
			$member = $memberDao->initWithUsername($this->post('memberName'));
			if (	$member &&
					($member->getFailCount() < 2 || GrcPool_Utils::isHuman($this->post('g-recaptcha-response'))) && 
					UserHelper::passwordHashMatch($this->post('password'),$member->getPassword())
			) {
				$goOn = true;
				if ($member->getTwoFactor() && $member->get2faLogin()) {
					$google2fa = new Google2FA();
					$token = $this->post('token');
					$valid = false;
					try {
						$valid = $google2fa->verifyKey($member->getTwoFactorKey(),$token);
					} catch (Exception $e) {
	
					}
					if (!$valid) {
						$this->addErrorMsg('Your token was not correct.');
						if ($member->getFailCount() < 2) {
							$member->setFailCount($member->getFailCount()+1);
							$memberDao->save($member);
						}
						$goOn = false;
					}
				}
				if ($goOn) {
					if ($member->getLoginEmail()) {
						$email = new Email();
						$email->addFrom('admin@grcpool.com');
						$email->addTo($member->getEmail());
						$email->setSubject('grcpool.com Login Notification');
						$email->setMessage('
							A login to your grcpool.com account just occurred. If this was not you, you may want to change your password immediately and contact support.
							<br/><br/>
							'.$_SERVER['REMOTE_ADDR'].' @ '.gmdate("Y-m-d H:i:s").' GMT
							<br/><br/>
							You have the option to disable this alert from within your account page.
						');
						try {
							$email->send();
						} catch(Exception $e) {
						
						}
					}
					$member->setFailCount(0);
					$memberDao->save($member);
					$sessionDao = new GrcPool_Session_DAO();
					//$sessionDao->disableWithUserId($member->getId());
					$session = new GrcPool_Session_OBJ();
					$session->setUserId($member->getId());
					$session->setIp($_SERVER['REMOTE_ADDR']);
					$session->setLastUsed(time());
					$session->setUsername($member->getUsername());
					$session->setSession(md5(UserHelper::generateRandomString(40).microtime()));
					$session->setRemember($this->post('rememberMe')=='true'?1:0);
					$sessionDao->save($session);
					if ($this->post('rememberMe') =='true') {
						$expire = time()+86400*30;
					} else {
						$expire = 0;
					}
					setcookie(Constants::SESSION_COOKIE_NAME,$session->getSession(),$expire,"/",Constants::SESSION_COOKIE_DOMAIN,true,true);
					Server::go('/account/index');
				}
			} else {
				if ($member && $member->getId() > 0) {
					if ($member->getFailCount() < 2) {
						$this->addErrorMsg('Unable to authenticate, please try again. (1)');
					} else {
						$this->addErrorMsg('Unable to authenticate due to too many failed attempts. Please try again but identify yourself as a human below.');
					}
					$member->setFailCount($member->getFailCount()+1);
					$memberDao->save($member);
				} else {
					$this->addErrorMsg('Unable to authenticate, please try again.');
				}
			}
			$this->view->captcha = $member&&$member->getFailCount()>1?true:false;
		}
	}
}