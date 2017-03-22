<?php
class GrcPool_Controller_Home extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction() {
		
		$hostCreditDao = new GrcPool_Member_Host_Credit_DAO();
		$totalMag = $hostCreditDao->getTotalMag();
		$this->view->totalMag = $totalMag;
		
		$activeHosts = $hostCreditDao->getNumberOfActiveHosts();
		$this->view->numberOfActiveHosts = $activeHosts;
		
		$settingsDao = new GrcPool_Settings_DAO();
		$this->view->txFee = $settingsDao->getValueWithName(SETTINGS_PAYOUT_FEE);
		$this->view->minPayout = $settingsDao->getValueWithName(SETTINGS_MIN_OWE_PAYOUT);
		$this->view->minStake = $settingsDao->getValueWithName(SETTINGS_MIN_STAKE_BALANCE);
		$this->view->totalPaidOut = $settingsDao->getValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT);
		$this->view->cpid = $settingsDao->getValueWithName(Constants::SETTINGS_CPID);
		
	}
	
}