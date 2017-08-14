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
		$daemon = GrcPool_Utils::getDaemonForEnvironment();
		echo $daemon->getBlockHeight();
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

