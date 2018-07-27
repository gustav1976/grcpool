<?php
class GrcPool_Controller_Home extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction() {
		
		$hostCreditDao = new GrcPool_Member_Host_Credit_DAO();
		
		$cache = new Cache();
		$superblockData = new SuperBlockData($cache->get(Constants::CACHE_SUPERBLOCK_DATA));
		
		$this->view->mags = array();
		$this->view->activeHosts = array();
		for ($i = 1; $i <= Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS); $i++) {
			$this->view->mags[$i] = $hostCreditDao->getTotalMagForPool($i);
			if ($this->view->mags[$i] == '') {
				$this->view->mags[$i] = 0;
			}
			$this->view->activeHosts[$i] = $hostCreditDao->getNumberOfActiveHostsForPool($i);
		}
		
		$settingsDao = new GrcPool_Settings_DAO();
		$this->view->poolWhiteListCount = $settingsDao->getValueWithName(Constants::SETTINGS_POOL_WHITELIST_COUNT);
		$this->view->txFee = $settingsDao->getValueWithName(Constants::SETTINGS_PAYOUT_FEE);
		$this->view->minPayout = $settingsDao->getValueWithName(Constants::SETTINGS_MIN_OWE_PAYOUT);
		$this->view->minStake = $settingsDao->getValueWithName(Constants::SETTINGS_MIN_STAKE_BALANCE);
		$this->view->totalPaidOut = 0;
		$this->view->cpids = array();
		for ($i = 1; $i <= Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS); $i++) {
			array_push($this->view->cpids,$settingsDao->getValueWithName(Constants::SETTINGS_CPID.($i==1?'':$i)));
			$this->view->totalPaidOut += $settingsDao->getValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT.($i==1?'':$i));
		}
		$this->view->online = $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE);
		$this->view->onlineMessage = '';
		if (!$this->view->online) {
			$this->view->onlineMessage = $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_MESSAGE);
		}
		$this->view->superblockData = $superblockData;
		
	}
	
}