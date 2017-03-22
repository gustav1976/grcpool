<?php
class GrcPool_Controller_Project extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction() {
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$accounts = $accountDao->fetchAll(array(),array('name'=>'asc'));

		$this->view->accounts = $accounts;
	
		$hostCreditDao = new GrcPool_Member_Host_Credit_DAO();
		$totalMag = $hostCreditDao->getTotalMag();
		$this->view->totalMag = $totalMag;
	
		$projStats = $hostCreditDao->getProjectStats();
		$this->view->projStats = $projStats;
	
	}
	
}
