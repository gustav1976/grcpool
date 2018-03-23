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
		$pills->addPill('Pool Charts','/report/poolChart',strstr($_SERVER['REQUEST_URI'],'/report/poolChart'));
		$pills->addPill('Pool Financials','/report/poolBalance',strstr($_SERVER['REQUEST_URI'],'/report/poolBalance'));
		$pills->addPill('SPARC','/report/sparc',strstr($_SERVER['REQUEST_URI'],'/report/sparc'));
		$this->getWebPage()->setSecondaryNav('<div style="border:1px solid #ddd;padding-top:20px;border-radius:5px;background-color:#fafafa;">'.$pills->render().'</div>');
		
	}
	
	public function indexAction() {
		Server::go('/report/magProjectHost');
	}
	
	public function poolBalanceAction() {
		$cache = new Cache();
		$this->view->superblockData = new SuperBlockData($cache->get(Constants::CACHE_SUPERBLOCK_DATA));
		$settingsDao = new GrcPool_Settings_DAO();
		$this->view->walletMode = $settingsDao->getValueWithName(Constants::SETTINGS_WALLET_MODE)??'SINGLE';
		$seeds = array();
		$profits = array();
		for ($i = 1; $i <= Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS); $i++) {
			$seeds[$i] = $settingsDao->getValueWithName((Constants::SETTINGS_SEED).($i>1?$i:''));
			$profits[$i] = $settingsDao->getValueWithName((Constants::SETTINGS_PROFIT_WITHDRAWN).($i>1?$i:''));
		}
		$this->view->profits = $profits;
		$this->view->seeds = $seeds;
	}
	
	public function poolChartAction() {
		
	}
	
	public function earnDonationAction() {
		$cache = new Cache();
		$this->view->members = $cache->get(Constants::CACHE_REPORT_EARNDONATE);
	}
	
	public function earnTopAction() {
		$cache = new Cache();
		$data = $cache->get(Constants::CACHE_REPORT_EARNTOP);
		$this->view->members = $data['members'];
		$this->view->totalGrc = $data['totalGrc'];
	}
	
	public function magAccountAction() {
		$cache = new Cache();
		$this->view->hosts = $cache->get(Constants::CACHE_REPORT_MAGACCOUNT);
	}
	
	public function magProjectHostAction() {
		$cache = new Cache();
		$this->view->hosts = $cache->get(Constants::CACHE_REPORT_PROJECTHOST);
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
	
	public function sparcAction() {
		$creditDao = new GrcPool_Member_Host_Credit_DAO();
		$sparcOwed = array();
		for ($i = 1; $i <= Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS); $i++) {
			$sparcOwed[$i] = $creditDao->getTotalOwedForPool($i,Constants::CURRENCY_SPARC);
		}
		$this->view->sparcOwed = $sparcOwed;
		
	}
	
	public function magHostAction() {
		$cache = new Cache();
		$this->view->hosts = $cache->get(Constants::CACHE_REPORT_HOSTMAG);
	}
}
