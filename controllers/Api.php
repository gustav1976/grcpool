<?php
class GrcPool_Controller_Api extends GrcPool_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function memberAction() {
		$body = file_get_contents('php://input');
		$json = json_decode($body,true);
		$output = array();
		if (Property::getValueFor('environment') == 'dev') {
			if ($this->get('apiKey')) {
				$json['apiKey'] = $this->get('apiKey');
			}
		}
		if (isset($json['apiKey']) && $json['apiKey'] != '') {
			$usrDao = new GrcPool_Member_DAO();
			$usr = $usrDao->getWithApiKey($json['apiKey']);
			if ($usr == null || $usr->getId() == 0) {
				$output['error'] = 'unknown api key';
			} else {
				if ($this->args(0) == 'stats') {
					$numberOfHosts = 0;
					$hostDao = new GrcPool_Member_Host_DAO();
					$hosts = $hostDao->getWithMemberId($usr->getId());
					$output['numberOfHosts'] = count($hosts);
					
					$creditDao = new GrcPool_View_Member_Host_Project_Credit_DAO();
					$credits = $creditDao->getWithMemberId($usr->getId());
					
					$output['mag'] = 0;
					$output['grcOwed'] = 0;
					$output['sparcOwed'] = 0;
					foreach ($credits as $credit) {
						$output['mag'] += $credit->getMag();
						$output['grcOwed'] += $credit->getOwed();
						$output['sparcOwed'] += $credit->getSparc();
					}
					
					$payoutDao = new GrcPool_Member_Payout_DAO();
					$output['grcEarned'] = number_format($payoutDao->getTotalForMemberId($usr->getId(),Constants::CURRENCY_GRC),3);
					$output['sparcEarned'] = number_format($payoutDao->getTotalForMemberId($usr->getId(),Constants::CURRENCY_SPARC),3);
					
					$orphanDao = new GrcPool_View_All_Orphans_DAO();
					$orphans = $orphanDao->getOrphansForMember($usr->getId());
					$output['grcOrphansOwed'] = 0;
					$output['sparcOrphansOwed'] = 0;
					foreach ($orphans as $orphan) {
						$output['grcOrphansOwed'] += $orphan->getOwed();
						$output['sparcOrphansOwed'] += $orphan->getSparc();
					}
					
					$cache = new Cache();
					$superblockData = new SuperBlockData($cache->get(Constants::CACHE_SUPERBLOCK_DATA));
					$magUnit = $superblockData->magUnit;
					$output['estimatedDailyGrc'] = number_format($magUnit*$output['mag'],2,'.','');
				}
			}
		}
		header('Content-Type: application/json');
		echo json_encode($output);
		exit;
	}
	
	public function loginNotificationAction() {
		header('Content-Type: application/json');
		if ($this->getUser()->getId()) {
			$change = $this->args(0,Controller::VALIDATION_NUMBER);
			if ($change !== null) {
				$this->getUser()->setLoginEmail($change?1:0);
				$dao = new GrcPool_Member_DAO();
				$usr = $this->getUser();
				$dao->save($usr);
			}
			$json= array();
			$json['loginEmail'] = $this->getUser()->getLoginEmail();
			echo json_encode($json);
		}
		exit;
	}
	
	public function hostNameAction() {
		$hostDao = new GrcPool_Member_Host_DAO();
		$host = $hostDao->initWithKey($this->args(0));
		if ($host->getMemberId() == $this->getUser()->getId()) {
			$data = json_decode(file_get_contents('php://input'),true);
			if (isset($data['customName'])) {
				$host->setCustomName(htmlspecialchars($data['customName']));
				$hostDao->save($host);
				$host = $hostDao->initWithKey($this->args(0));
				$json = array('customName' => $host->getCustomName()!=''?$host->getCustomName():$host->getHostName());
				echo json_encode($json);
			}
		}
		exit;
	}
	
	public function hostProjectDeleteAction() {
		$hostProjectsDao = new GrcPool_Member_Host_Project_DAO();
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$hostDao = new GrcPool_Member_Host_DAO();
		$host = $hostDao->initWithKey($this->args(0),Controller::VALIDATION_NUMBER);
		if ($host->getMemberId() != $this->getUser()->getId()) {
			exit;
		}	
		$proj = $hostProjectsDao->initWithKey($this->args(1,Controller::VALIDATION_NUMBER));
		if ($proj && $proj->getHostId() == $host->getId() && $proj->getMemberId() == $this->getUser()->getId()) {
			$hostProjectsDao->delete($proj);
		}
		header('Content-Type: application/json');
		echo GrcPool_Json::getHostSettings($this->getUser(),$host);
		exit;
	}
	
	public function hostSettingsAction() {
		$hostProjectsDao = new GrcPool_Member_Host_Project_DAO();
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$keyDao = new GrcPool_Boinc_Account_Key_DAO();
		$hostDao = new GrcPool_Member_Host_DAO();
		$host = $hostDao->initWithKey($this->args(0),Controller::VALIDATION_NUMBER);
		if ($host->getMemberId() != $this->getUser()->getId()) {
			exit;
		}		
		if ($this->post('ids')) {
			foreach ($this->post('ids') as $id) {
				$hostProject = $hostProjectsDao->getActiveProjectForHost($host->getId(),$id,$this->getUser()->getPoolId());
				if ($hostProject == null) {
					$hostProject = new GrcPool_Member_Host_Project_OBJ();
				} else {
					if ($hostProject->getMemberId() != $this->getUser()->getId()) {
						exit;
					}
				}
				$hostProject->setHostId($host->getId());
				$hostProject->setMemberId($this->getUser()->getId());
				$hostProject->setAccountId($id);
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
		}
		header('Content-Type: application/json');
		echo GrcPool_Json::getHostSettings($this->getUser(),$host);
		exit;
	}
	
	public function isMemberNameAvailableAction() {
		header('Content-Type: application/json');
		$name = $this->get('name');
		$result = array();
		if ($name) {
			$dao = new GrcPool_Member_DAO();
			$obj = $dao->initWIthUsername($name);
			$result['result'] = $obj == null;
		} else {
			$result['result'] = false;
		}
		echo json_encode($result);
		exit;
	}

	public function blockHeightAction() {
		$numberOfPools = Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS);
		$blocks = array();
		$lowestHeight = 0;
		$checkBlocks = array();
		if ($this->args(0)) {
			$daemon = GrcPool_Utils::getDaemonForPool();
			$blockHeight = $this->args(0,Controller::VALIDATION_NUMBER);
			if ($blockHeight) {
				$blocks['height'] = $this->args(0);
				$blocks['hash'] = $daemon->getBlockHash($blocks['height']);
				$blocks['version'] = trim($daemon->getVersion());
				echo json_encode($blocks,JSON_PRETTY_PRINT);
				exit;
			}
		}
		for ($i = 1; $i <= $numberOfPools; $i++) {
			$blocks[$i] = array();
			$daemon = GrcPool_Utils::getDaemonForPool($i);
			$blocks[$i]['height'] = trim($daemon->getBlockHeight());
			$checkBlocks[$blocks[$i]['height']] = 1;
			$blocks[$i]['hash'] = trim($daemon->getBlockHash($blocks[$i]['height']));
			$blocks[$i]['version'] = trim($daemon->getVersion());
			if ($blocks[$i]['height'] < $lowestHeight || $lowestHeight == 0) {
				$lowestHeight = $blocks[$i]['height'];
			}
		}
		if (count($checkBlocks) > 1) {
			$blocks[$lowestHeight] = array();
			for ($i = 1; $i <= $numberOfPools; $i++) {
				$daemon = GrcPool_Utils::getDaemonForPool($i);
				$blocks[$lowestHeight][$i] = trim($daemon->getBlockHash($lowestHeight));
			}
		}
		echo json_encode($blocks,JSON_PRETTY_PRINT);
		exit;
	}
	
	public function superBlockAgeAction() {
		header('Content-Type: application/json');
		$cache = new Cache();
		echo $cache->get(Constants::CACHE_SUPERBLOCK_DATA);		
		exit;
	}
	
	public function projectsAction() {
		$acctDao = new GrcPool_Boinc_Account_DAO();
		$accounts = $acctDao->fetchAll(array(),array('name'=>'asc'));
		$apiObj = new GrcPool_ApiJson();
		$arr = array();
		foreach ($accounts as $account) {
			array_push($arr,[
				'name' => $account->getName(),
				'url' => $account->getUrl(),
				'key' => $account->getWeakKey(),
				'whiteList' => $account->getWhiteList(),
				'rac' => $account->getRac(),
				'attachable' => $account->getAttachable(),
				'minRac' => $account->getMinRac(),
			]);	
		}
		$apiObj->setData($arr);
		echo $apiObj->toJson();
		exit;
	}

}

