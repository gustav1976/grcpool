<?php
class GrcPool_Controller_Report extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
		$pills = new Bootstrap_Pills();
		$pills->addPill('Magnitude',array(
			(strstr($_SERVER['REQUEST_URI'],'/report/magProjectHost')?'<i class="fa fa-check"></i> ':'').'Top Mags for Project'=>'/report/magProjectHost',
			(strstr($_SERVER['REQUEST_URI'],'/report/magHost')?'<i class="fa fa-check"></i> ':'').'Top Mags for Host'=>'/report/magHost',
				(strstr($_SERVER['REQUEST_URI'],'/report/magAccount')?'<i class="fa fa-check"></i> ':'').'Top Mag for Account'=>'/report/magAccount'
		),strstr($_SERVER['REQUEST_URI'],'/report/mag'));
		$pills->addPill('Earnings',array(
				(strstr($_SERVER['REQUEST_URI'],'/report/earnTop')?'<i class="fa fa-check"></i> ':'').'Top Earners'=>'/report/earnTop',
				(strstr($_SERVER['REQUEST_URI'],'/report/earnDonation')?'<i class="fa fa-check"></i> ':'').'Top Donators'=>'/report/earnDonation'
		),strstr($_SERVER['REQUEST_URI'],'/report/earn'));
		$pills->addPill('Pool Financials','/report/poolBalance',strstr($_SERVER['REQUEST_URI'],'/report/poolBalance'));
		$this->getWebPage()->setSecondaryNav($pills->render());
		
	}
	
	public function indexAction() {
		Server::go('/report/magProjectHost');
	}
	
	public function poolBalanceAction() {
		$cache = new Cache();
		$this->view->superblockData = new SuperBlockData($cache->get(Constants::CACHE_SUPERBLOCK_DATA));
		$settingsDao = new GrcPool_Settings_DAO();
		$this->view->seed = $settingsDao->getValueWithName(Constants::SETTINGS_SEED);
		$this->view->seed2 = $settingsDao->getValueWithName(Constants::SETTINGS_SEED.'2');
	}
	
	public function earnDonationAction() {
		$dao = new GrcPool_Member_Payout_DAO();
		$this->view->members = $dao->getTopDonators(100);
	}
	
	public function earnTopAction() {
		$dao = new GrcPool_Member_Payout_DAO();
		$this->view->members = $dao->getTopEarners(100);
		$this->view->totalGrc = $dao->getTotalAmount();
	}
	
	public function magAccountAction() {
		$dao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$this->view->hosts = $dao->getTopAccounts(100);
	}
	
	public function magProjectHostAction() {
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$this->view->accounts = $accountDao->fetchAll();
		$dao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$this->view->hosts = $dao->fetchAll(array(),array('mag'=>'desc'),100);
	}
	
	public function researcherAction() {
		$memberDao = new GrcPool_Member_DAO();
		$this->view->member = $memberDao->initWithKey($this->args(0,Controller::VALIDATION_NUMBER));
		if (!$this->view->member || !$this->view->member->getId()) {Server::goHome();}
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$this->view->accounts = $accountDao->fetchAll();
		$this->view->hosts = null;
		$this->view->host = null;
		$hostDao = new GrcPool_Member_Host_DAO();
		$creditDao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		if ($this->args(1,Controller::VALIDATION_NUMBER)) {
			$this->view->host = $hostDao->initWithKey($this->args(1,Controller::VALIDATION_NUMBER));
			$this->view->credits = $creditDao->getWithMemberIdAndHostId($this->view->member->getId(),$this->view->host->getId());
		} else {
			$this->view->credits = $creditDao->getWithMemberId($this->view->member->getId());
			$hostIds = array();
			foreach ($this->view->credits as $credit) {
				$hostIds[$credit->getHostId()] = 1;
			}
			$this->view->hosts = $hostDao->initWithKeys(array_keys($hostIds));
		}
	}
	
	public function magHostAction() {
		$dao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$this->view->hosts = $dao->getTopHosts(100);
		$hostDao = new GrcPool_Member_Host_DAO();
		$keys = array();
		$projects = array();
		foreach ($this->view->hosts as $host) {
			$keys[$host['hostId']] = $host;
		}
		$hostDetails = $hostDao->initWithKeys(array_keys($keys));
		foreach ($this->view->hosts as $idx => $host) {
			$this->view->hosts[$idx]['detail'] = $hostDetails[$host['hostId']];
		}
	}
}
