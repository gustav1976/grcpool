<?php
use PragmaRX\Google2FA\Google2FA;
class GrcPool_Controller_Account extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
		if ($this->getUser()->getId() == 0) {
			Server::goHome();			
		}
	}
	
	public function deleteAction() {
		if ($this->post('cmd') == 'submit') {
			$authCheck = UserHelper::authenticate($this->getUser(),$this->post('password'));
			if (!$authCheck) {
				$this->addErrorMsg('Your authorization for this failed.');
			} else {
				$usr = $this->getUser();
				$dao = new GrcPool_Member_Host_DAO();
				$dao->deleteWithMemberId($usr->getId());
				$dao = new GrcPool_Member_Host_Project_DAO();
				$dao->deleteWithMemberId($usr->getId());
				$dao = new GrcPool_Member_Host_Stat_Mag_DAO();
				$dao->deleteWithMemberId($usr->getId());
				$dao = new GrcPool_Member_Host_Xml_DAO();
				$dao->deleteWithMemberId($usr->getId());
				$dao = new GrcPool_Member_Notice_DAO();
				$dao->deleteWithMemberId($usr->getId());
				$dao = new GrcPool_Session_DAO();
				$dao->deleteWithUserId($usr->getId());
				$dao = new GrcPool_Member_DAO();
				$dao->delete($usr);
				Server::goHome();
			}
		}
		$this->view->twoFactor = $this->getUser()->getTwoFactor();
	}
	
	public function hostsAction() {
		
		$noticeDao = new GrcPool_Member_Notice_DAO();
		$hostDao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$dao = new GrcPool_Member_Host_DAO();
		$projDao = new GrcPool_Member_Host_Project_DAO();
		if ($this->args(0) == 'enableDelete') {
			if (!$noticeDao->isNoticeForMembeAndId($this->getUser()->getId(),GrcPool_Member_Notice_OBJ::NOTICE_DELETE)) {
				$obj = new GrcPool_Member_Notice_OBJ();
				$obj->setMemberId($this->getUser()->getId());
				$obj->setNoticeId(GrcPool_Member_Notice_OBJ::NOTICE_DELETE);
				$obj->setThetime(time());
				$noticeDao->save($obj);
			}
		} else if ($this->args(0) == 'delete') {
			$obj = $dao->initWithKey($this->args(1,Controller::VALIDATION_NUMBER));
			if ($obj && $obj->getMemberId() == $this->getUser()->getId()) {
				$hDao = new GrcPool_Member_Host_Project_DAO();
				$hDao->deleteWithMemberIdAndHostId($this->getUser()->getId(),$obj->getId());
				$dao->delete($obj);
			}
		}

		$this->view->errorHosts = $projDao->getHostIdsWithErrors($this->getUser()->getId());
		
		$memHosts = $dao->getWithMemberId($this->getUser()->getId());
		$this->view->memHosts = $memHosts;
		
		$hosts = $hostDao->getWithMemberId($this->getUser()->getId());
		$this->view->hosts = $hosts;
		
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$accounts = $accountDao->fetchAll();
		$this->view->accounts = array();
		foreach ($accounts as $account) {
			$this->view->accounts[$account->getUrl()] = $account;
		}
		
		$cache = new Cache();
		$superblockData = new SuperBlockData($cache->get(Constants::CACHE_SUPERBLOCK_DATA));
		$this->view->magUnit = $superblockData->magUnit;
		
		$this->view->hasDeleteNotice = $noticeDao->isNoticeForMembeAndId($this->getUser()->getId(),GrcPool_Member_Notice_OBJ::NOTICE_DELETE);
		
	}
	
	public function payoutsAction() {
		$dao = new GrcPool_View_Member_Payout_DAO();
		$numberToShow = 25;		
		$start = 0;
		if (is_numeric($this->args(0))) {
			$start = $this->args(0,Controller::VALIDATION_NUMBER);
		}
		
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$accounts = $accountDao->fetchAll();
		$this->view->accounts = array();
		foreach ($accounts as $account) {
			$this->view->accounts[$account->getUrl()] = $account;
		}
		
		$numberOfPayouts = $dao->getCountForUser($this->getUser()->getId());
		$pagination = new Bootstrap_Pagination();
		$pagination->setGroup($numberToShow);
		$pagination->setHref('/account/payouts/?');
		$pagination->setHowMany($numberOfPayouts);
		$pagination->setArrows(false);
		$pagination->setAdjacents(2);
		$pagination->setStart($start);
		
		$this->view->pagination = $pagination->render();
		
		$payouts = $dao->getWithMemberId($this->getUser()->getId(),array($start*$numberToShow,$numberToShow));
		
		$this->view->payoutTotal = $dao->getPayoutTotalForUser($this->getUser()->getId());
		$this->view->numberOfPayouts = $numberOfPayouts;
		$this->view->payouts = $payouts;
		
		$creditDao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$owed = $creditDao->getWithMemberId($this->getUser()->getId());
		$this->view->owed = $owed;
	}
	
	public function verifyAction() {
		$usrDao = new GrcPool_Member_DAO();
		$key = '';
		if ($this->getUser()->getVerifyKey() != '') {
			$parts = explode("_",$this->getUser()->getVerifyKey());
			$when = $parts[0];
			if ($when + 3600*24 > time()) {
				// key still valid
				$key = $parts[1];
			}
		}
		if ($key == '') {
			$key = md5(UserHelper::generateRandomString(100).microtime());
		}
		$email = new Email();
		$email->addFrom('admin@grcpool.com');
		$email->addTo($this->getUser()->getEmail());
		$email->setSubject('grcpool.com verification email');
		$email->setMessage(Email::getVerificationMessage($key,$this->getUser()->getId()));
		$email->send();
		$key = time().'_'.$key;
		$this->getUser()->setVerifyKey($key);
		$usr = $this->getUser();
		$usrDao->save($usr);		
	}
	
	public function twoFactorAuthAction() {
		$google2fa = new Google2FA();
		$dao = new GrcPool_Member_DAO();
		$member = $this->getUser();
		if ($this->post('cmd') == 'disable') {
			$authCheck = UserHelper::passwordHashMatch($this->post('password'),$this->getUser()->getPassword());
			if (!$authCheck) {
				$this->addErrorMsg('Your password was not correct');
			} else {
				$token = $this->post('token');
				$valid = false;
				try {
					$valid = $google2fa->verifyKey($this->getUser()->getTwoFactorKey(),$token);
				} catch (Exception $e) {
					
				}
				if (!$valid) {
					$this->addErrorMsg('Your token was not correct.');
				} else {
					$member->setTwoFactor(0);
					$member->setTwoFactorKey('');
					$dao->save($member);
					$this->setUser($member);
				}
			}
		}
		if ($member->getTwoFactorKey() == '') {
			$key = $google2fa->generateSecretKey(16);
			$member->setTwoFactorKey($key);
			$dao->save($member);
			$this->setUser($member);
		}
		if ($this->post('cmd') == 'activate') {
			if ($this->post('backedUp') != '1') {
				$this->addErrorMsg('You did not indicate that you have backed up your key.');
			} else {
				$authCheck = UserHelper::passwordHashMatch($this->post('password'),$this->getUser()->getPassword());
				if (!$authCheck) {
					$this->addErrorMsg('Your password was not correct');
				} else {
					$token = $this->post('token');
					$valid = $google2fa->verifyKey($this->getUser()->getTwoFactorKey(),$token);
					if (!$valid) {
						$this->addErrorMsg('Your token was not correct.');
					} else {
						$member->setTwoFactor(1);
						$dao->save($member);
						$this->setUser($member);
					}
				}
			}
		}
		
		$this->view->qrCode = $google2fa->getQRCodeInline('grcpool.com',$member->getEmail(),$this->getUser()->getTwoFactorKey()); 
		$this->view->twoFactor = $this->getUser()->getTwoFactor()?true:false;
		$this->view->key = $this->getUser()->getTwoFactorKey();
	}
	
	public function payoutAddressAction() {
		$settingsDao = new GrcPool_Settings_DAO();
		$this->view->clientOn = $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE);
		if ($this->post('cmd') == 'grcAddress') {
			$newAddr = $this->post('grcAddress');
			$charCheck = $newAddr==''||ctype_alnum($newAddr)?$newAddr:null;
			$message = '';
			if ($charCheck === null) {
				$this->addErrorMsg('The GRC Address you entered has inappropriate characters.');
			} else {
				$authCheck = UserHelper::authenticate($this->getUser(),$this->post('password'));
				if (!$authCheck) {
					$this->addErrorMsg('Your authorization for this change failed');
				} else {
					$daemon = GrcPool_Utils::getDaemonForEnvironment();
					if ($newAddr != '' && !$daemon->isAddress($newAddr)) {
						$this->addErrorMsg('GRC Address was not formatted properly.');
					} else {
						$usrDao = new GrcPool_Member_DAO();
						$this->getUser()->setGrcAddress($newAddr);
						$usr = $this->getUser();
						$usrDao->save($usr);
						$this->addSuccessMsg('GRC Address is updated');
						$email = new Email();
						$email->addFrom('admin@grcpool.com');
						$email->addTo($this->getUser()->getEmail());
						$email->setSubject('grcpool.com GRC Payout Address Changed');
						$email->setMessage('The GRC payout address on your grcpool.com account was changed.<br/><br/>Sincerely, your friendly GRC pool...');
						try {
							$email->send();
						} catch(Exception $e) {
						
						}
					}
				}
			}
		} else if ($this->post('cmd') == 'minAmount') {
			$input = $this->post('minimumAmount');
			if (is_numeric($input) && $input >= 1 and $input <= 1000) {
				$usrDao = new GrcPool_Member_DAO();
				$this->getUser()->setMinPayout(floor($input));
				$usr = $this->getUser();
				$usrDao->save($usr);
				$this->addSuccessMsg('GRC Minimum Payout is Updated');
			} else {
				$this->addErrorMsg('The Minimum Payout update Failed, check your values and try again.');
			}
		} else if ($this->post('cmd') == 'donation') {
			if (is_numeric($this->post('donation')) && $this->post('donation') <= 100 && $this->post('donation') >= 0) {
				$usrDao = new GrcPool_Member_DAO();
				$this->getUser()->setDonation($this->post('donation'));
				$usr = $this->getUser();
				$usrDao->save($usr);
				if ($usr->getDonation() > 0) {
					$this->addSuccessMsg('Donation was updated, thank you very much!');
				} else {
					$this->addSuccessMsg('Donation was updated.');
				}
			} else {
				$this->addErrorMsg('There was a problem with the donation value.');
			}
		}
		
		$this->view->minAmount = $this->getUser()->getMinPayout();
		$this->view->donation = $this->getUser()->getDonation();
		$this->view->twoFactor = $this->getUser()->getTwoFactor();
		$this->view->grcAddress = $this->getUser()->getGrcAddress();
	}
	
	public function orphansAction() {
		$orphanDao = new GrcPool_View_All_Orphans_DAO();
		$orphans = $orphanDao->getOrphansForMember($this->getUser()->getId());
		$this->view->orphans = $orphans;
		$this->view->orphansOwed= 0;
		foreach ($this->view->orphans as $orphan) {
			$this->view->orphansOwed+= $orphan->getOwed();
		}
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$accounts = $accountDao->fetchAll();
		$this->view->accounts = array();
		foreach ($accounts as $account) {
			$this->view->accounts[$account->getUrl()] = $account;
		}
		$settingsDao = new GrcPool_Settings_DAO();
		$this->view->payoutNoMag = $settingsDao->getValueWithName(Constants::SETTINGS_MIN_ORPHAN_PAYOUT_ZERO_MAG);
		$this->view->payoutWithMag = $settingsDao->getValueWithName(Constants::SETTINGS_MIN_ORPHAN_PAYOUT_WITH_MAG);
	}
	
	public function indexAction() {
		$usrDao = new GrcPool_Member_DAO();
		
		$messages = array();
		if ($this->getUser()->getVerified() == 0) {
			array_push($messages,'You have not verified your email address. If you would like to get email notifications, please <a href="/account/verify">verify now</a>.');
		}
		if ($this->getUser()->getGrcAddress() == '') {
			array_push($messages,'You have not entered a GRC payout address. In order to receive your earned GRC, <a href="/account/payoutAddress">please input an address</a>.');
		}
		$projectDao = new GrcPool_Member_Host_Project_DAO();
		$projs = $projectDao->getWithMemberIdAndDbid($this->getUser()->getId(),0);
		if ($projs) {
			array_push($messages,'There appears to be projects that are not yet attached correctly, you may want to review your host projects.');
		}

		$numberOfHosts = 0;
		$hostDao = new GrcPool_Member_Host_DAO();
		$hosts = $hostDao->getWithMemberId($this->getUser()->getId());
		$numberOfHosts = count($hosts);
		
		$creditDao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$credits = $creditDao->getWithMemberId($this->getUser()->getId());
		$mag = 0;
		$owed = 0;
		foreach ($credits as $credit) {
			$mag += $credit->getMag();
			$owed += $credit->getOwed();
		}
		
		$payoutDao = new GrcPool_Member_Payout_DAO();
		$this->view->totalPaid = number_format($payoutDao->getTotalForMemberId($this->getUser()->getId()),3);

		$orphanDao = new GrcPool_View_All_Orphans_DAO();
		$orphans = $orphanDao->getOrphansForMember($this->getUser()->getId());
		$this->view->orphans = $orphans;
		$this->view->orphansOwed= 0;
		foreach ($this->view->orphans as $orphan) {
			$this->view->orphansOwed+= $orphan->getOwed();
		}
				
		$cache = new Cache();
		$superblockData = new SuperBlockData($cache->get(Constants::CACHE_SUPERBLOCK_DATA));
		$this->view->magUnit = $superblockData->magUnit;
		
		$this->view->totalMag = $mag;
		$this->view->owed = $owed;
		$this->view->numberOfHosts = $numberOfHosts;
		$this->view->messages = $messages;
		
	}
	
	public function hostAction() {
		$hostDao = new GrcPool_Member_Host_DAO();
		$projectDao = new GrcPool_Boinc_Account_DAO();
		$noticeDao = new GrcPool_Member_Notice_DAO();
		$hostProjectsDao = new GrcPool_Member_Host_Project_DAO();
	
		$host = $hostDao->initWithKey($this->args(0),Controller::VALIDATION_NUMBER);
		if ($host->getMemberId() != $this->getUser()->getId()) {
			Server::goHome();
		}
		if ($this->args(1) == 'delete') {
			$proj = $hostProjectsDao->initWithKey($this->args(2,Controller::VALIDATION_NUMBER));
			if ($proj && $proj->getHostId() == $host->getId() && $proj->getMemberId() == $this->getUser()->getId()) {
				$hostProjectsDao->delete($proj);
			}
		}
		$this->view->host = $host;
		
		$projects = $projectDao->fetchAll(array(),array('name'=>'asc'));
		$this->view->projects = array();
		foreach ($projects as $account) {
			$this->view->projects[$account->getUrl()] = $account;
		}
		
		if ($this->post('cmd') == 'saveSettings' && $this->post('ids')) {
			foreach ($this->post('ids') as $id) {
				$hostProject = null;

				$hostProject = $hostProjectsDao->getActiveProjectForHost($host->getId(),$projects[$id]->getUrl(),$this->getUser()->getPoolId());
				//$hostProject = $hostProjectsDao->getWithHostIdAndProjectUrlAndPoolId($host->getId(),$projects[$id]->getUrl(),$this->getUser()->getPoolId());
				
				if ($hostProject == null) {
					$hostProject = new GrcPool_Member_Host_Project_OBJ();
				}
				$hostProject->setHostId($host->getId());
				$hostProject->setMemberId($this->getUser()->getId());
				$hostProject->setProjectUrl($projects[$id]->getUrl());
				$hostProject->setHostCpid($host->getCpid());
				$share = $this->post('resourceShare_'.$id);
				if (is_numeric($share) && $share < 10000) {
					$hostProject->setResourceShare($share);
				} else {
					$hostProject->setResourceShare(100);
				}
				$hostProject->setPoolId($this->getUser()->getPoolId());
				$hostProject->setNoAtiGpu($this->post('noatigpu_'.$id)|0);
				$hostProject->setNoCpu($this->post('nocpu_'.$id)|0);
				$hostProject->setNoNvidiaGpu($this->post('nonvidiagpu_'.$id)|0);
				$hostProject->setNoIntelGpu($this->post('nointelgpu_'.$id)|0);
				$hostProject->setAttached($this->post('detach_'.$id)==1?0:1);
				$hostProjectsDao->save($hostProject);
			}
			$this->addSuccessMsg('Host settings should be updated.');
		}
		
		//$hostProjects = $hostProjectsDao->getWithMemberIdAndHostCpid($this->getUser()->getId(),$host->getCpid());
		$hostProjects = $hostProjectsDao->getWithMemberIdAndHostId($this->getUser()->getId(),$host->getId());
		$this->view->hostProjects = $hostProjects;
		
		$this->view->hasDeleteNotice = $noticeDao->isNoticeForMembeAndId($this->getUser()->getId(),GrcPool_Member_Notice_OBJ::NOTICE_DELETE);

	}
	
	public function passwordEmailAction() {
		
		$dao = new GrcPool_Member_DAO();
		$this->view->showForm = true;
		$member = $this->getUser();
		if ($this->post('cmd') == 'submit') {
			$authCheck = UserHelper::authenticate($this->getUser(),$this->post('authorization'));
			if (!$authCheck) {
				$this->addErrorMsg('Your authorization for this change failed');
			} else {
				$passwordSave = false;
				$passwordError = false;
				$emailSave = false;
				$emailError = false;
				if (strlen($this->post('password')) > 0) {
					$passwordSave = true;
		 			if ((strlen($this->post('password')) < 8 || $this->post('password') !== $this->post('confirmPassword'))) {
						$this->addErrorMsg('Your passwords did not match or were not long enough.');
						$passwordError = true;
					} else {
						$member->setPassword(UserHelper::encodePassword($this->post('password')));
						if ($this->post('boinc') == 1) {
							$member->setPasswordHash(md5($this->post('password').strtolower($this->getUser()->getUsername())));
						}
					}
				}
				if (strlen($this->post('emailAddress')) > 0 && $this->post('emailAddress') != $this->getUser()->getEmail()) {
					$emailSave = true;
					if (!Utils::isValidEmail($this->post('emailAddress'))) {
						$this->addErrorMsg('Your email appears to be improperly formatted.');
						$emailError = true;
					} else {
						$test = $dao->initWithEmail($this->post('emailAddress'));
						if ($test != null) {
							$emailError = true;
							$this->addErrorMsg('This email is already registered.');
						} else {
							$member->setEmail($this->post('emailAddress'));
							$member->setVerified(0);
						}
					}
				}
				if ($emailSave || $passwordSave) {
					if ($emailError || $passwordError) {
							
					} else {
						$dao->save($member);
						$this->setUser($member);
						$this->addSuccessMsg('Your settings have been updated.');
					}
				}
			}
		}
		
		
		$this->view->twoFactor = $this->getUser()->getTwoFactor()?true:false;
		$this->view->emailAddress = $this->getUser()->getEmail();
	}
}