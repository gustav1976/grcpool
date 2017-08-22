<?php
class GrcPool_Controller_Api extends GrcPool_Controller {
	public function __construct() {
		parent::__construct();
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
		for ($i = 1; $i <= $numberOfPools; $i++) {
			$daemon = GrcPool_Utils::getDaemonForPool($i);
			$blocks[$i] = trim($daemon->getBlockHeight());
		}
		echo json_encode($blocks);
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

