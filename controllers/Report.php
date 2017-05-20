<?php
class GrcPool_Controller_Report extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
		$pills = new Bootstrap_Pills();
		$pills->addPill('Magnitude',array(
			'Top Project Host'=>'/report/magProjectHost',
			'Top Host'=>'/report/magHost',
			'Top Accounts'=>'/report/magAccount'
		),strstr($_SERVER['REQUEST_URI'],'/report/mag'));
		$pills->addPill('Earnings',array(
				'Top Earners'=>'/report/earnTop',
				'Top Donators'=>'/report/earnDonation'
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
	}
	
	public function earnDonationAction() {
		$dao = new GrcPool_View_Member_Payout_DAO();
		$this->view->members = $dao->getTopDonators(50);
	}
	
	public function earnTopAction() {
		$dao = new GrcPool_View_Member_Payout_DAO();
		$this->view->members = $dao->getTopEarners(50);
	}
	
	public function magAccountAction() {
		$dao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$this->view->hosts = $dao->getTopAccounts(50);
	}
	
	public function magProjectHostAction() {
		$dao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$this->view->hosts = $dao->fetchAll(array(),array('mag'=>'desc'),50);
	}
	
	public function magHostAction() {
		$projectDao = new GrcPool_Member_Host_Project_DAO();
		$dao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$this->view->hosts = $dao->getTopHosts(50);
		$hostDao = new GrcPool_Member_Host_DAO();
		$keys = array();
		$projects = array();
		foreach ($this->view->hosts as $host) {
			//$projs = $projectDao->getWithMemberIdAndHostId($host['id'],$host['hostId']);
			//$projects[$host['hostId']] = $projs;
			$keys[$host['hostId']] = 1;
		}
		$this->view->projects = $projects;
		$this->view->hostDetails = $hostDao->initWithKeys(array_keys($keys));
	}
	

}