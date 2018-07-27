<?php
class GrcPool_Task_Helper {
	
	public static function runCreditUpdateTasks() {
		self::reportHostMag();
		self::magProjectHostAction();
		self::magAccount();
	}
	
	public static function runPayoutTasks() {
		self::earnTop();
		self::earnDonation();
	}
	
	public static function earnDonation() {
		$dao = new GrcPool_Member_Payout_DAO();
		$members = $dao->getTopDonators(100);
		$cache = new Cache();
		$cache->set($members,Constants::CACHE_REPORT_EARNDONATE);
	}
	
	public static function earnTop() {
		$dao = new GrcPool_Member_Payout_DAO();
		$members = $dao->getTopEarners(100,Constants::CURRENCY_GRC);
		$totalGrc = $dao->getTotalAmount(Constants::CURRENCY_GRC);
		$data = array();
		$data['members'] = $members;
		$data['totalGrc'] = $totalGrc;
		$cache = new Cache();
		$cache->set($data,Constants::CACHE_REPORT_EARNTOP);
	}
	
	public static function magAccount() {
		$dao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$hosts = $dao->getTopAccounts(100);
		$cache = new Cache();
		$cache->set($hosts,Constants::CACHE_REPORT_MAGACCOUNT);
	}
	
	public static function reportHostMag() {
		$dao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$hosts = $dao->getTopHosts(100);
		$hostDao = new GrcPool_Member_Host_DAO();
		$keys = array();
		$projects = array();
		foreach ($hosts as $host) {
			$keys[$host['hostId']] = $host;
		}
		$hostDetails = $hostDao->initWithKeys(array_keys($keys));
		foreach ($hosts as $idx => $host) {
			$hosts[$idx]['detail'] = $hostDetails[$host['hostId']];
		}
		$cache = new Cache();
		$cache->set($hosts,Constants::CACHE_REPORT_HOSTMAG);
	}
	
	
	public static function magProjectHostAction() {
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$accounts = $accountDao->fetchAll();
		$dao = new GrcPool_View_Member_Host_Project_Credit_DAO();
		$hosts = $dao->fetchAll(array(),array('mag'=>'desc'),100);
		$data = array();
		foreach ($hosts as $host) {
			$d = array();
			$d['host'] = $host;
			$d['account'] = $accounts[$host->getAccountId()];
			array_push($data,$d);
		}
		$cache = new Cache();
		$cache->set($data,Constants::CACHE_REPORT_PROJECTHOST);
	}
	
}