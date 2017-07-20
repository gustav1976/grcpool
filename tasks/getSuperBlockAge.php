<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';

$settingsDao = new GrcPool_Settings_DAO();
if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE";
	exit;
}


$cache = new Cache();
$daemon = GrcPool_Utils::getDaemonForEnvironment();

$superblockData = new SuperBlockData($cache->get(Constants::CACHE_SUPERBLOCK_DATA));

$result = $daemon->getSuperBlockAge();
$superblockData->timestamp = $result['timestamp'];
$superblockData->age = $result['age'];
$superblockData->pending = $result['pending'];
$superblockData->block = $result['block'];
$superblockData->ageText = $result['ageText'];

if ($FORCE || ($superblockData->pending == 0 && $superblockData->lastBlock != $superblockData->block)) {
	
	echo "NEW SUPERBLOCK DATA NEEDED";
	
	$settingsDao = new GrcPool_Settings_DAO();
	$superblockData->paidOut = array();
	$superblockData->paidOut[0] = $settingsDao->getValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT);
	$superblockData->paidOut[1] = $settingsDao->getValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT.'2');
	
	$hostCreditDao = new GrcPool_Member_Host_Credit_DAO();
	$totalOwed = array();
	$totalOwed[0] = $hostCreditDao->getTotalOwedForPool(1);
	$totalOwed[1] = $hostCreditDao->getTotalOwedForPool(2);
	$superblockData->owed = $totalOwed;
	
	$superblockData->lastBlock = $superblockData->block;
	
	$version = $daemon->getVersion();
	$superblockData->version = $version;

	$wl = $daemon->getWhitelistedProjects();
	$superblockData->whiteListCount = count($wl);
	$superblockData->projects = $wl;
	
	////////////////////

	$superblockData->mag = array();
	$superblockData->basis = array();
	$superblockData->expectedDailyEarnings = array();
	$superblockData->fulfillment = array();
	$superblockData->interest = array();
	$superblockData->research = array();
	$superblockData->txCount = array();
	$superblockData->magUnit = array();
	$superblockData->balance = array();

	// POOL 1

	$mag = $daemon->getMagnitude();
	$superblockData->mag[0] = $mag;
	
	$basisDao = new GrcPool_Wallet_Basis_DAO();
	$basisObj = $basisDao->initWithKey(1);
	$superblockData->basis[0] = $basisObj->getBasis();
	
	$rsaData = $daemon->getRsa();
	$superblockData->expectedDailyEarnings[0] = $rsaData[1]['Expected Earnings (Daily)'];
	$superblockData->fulfillment[0] = $rsaData[1]['Fulfillment %'];
	$superblockData->interest[0] = $rsaData[1]['CPID Lifetime Interest Paid'];
	$superblockData->research[0] = $rsaData[1]['CPID Lifetime Research Paid'];
	$superblockData->txCount[0] = $rsaData[1]['Tx Count'];
	$superblockData->magUnit = $rsaData[2]['Magnitude Unit (GRC payment per Magnitude per day)'];
	
	//$stakingInfo = $daemon->getStakingInfo();
	//$superblockData->netWeight = $stakingInfo['weight']/COIN;
	
	$balance = $daemon->getTotalBalance();
	$superblockData->balance[0] = $balance;
	
	// POOL 2
	
	echo "GETTING POOL 2 DATA\n";
	
	$daemon2 = GrcPool_Utils::getDaemonForEnvironment(Constants::DAEMON_POOL_2_PATH,Constants::DAEMON_POOL_2_DATADIR);
	$basisObj = $basisDao->initWithKey(2);
	$superblockData->basis[1] = $basisObj->getBasis();

	$rsaData = $daemon2->getRsa();
	$superblockData->expectedDailyEarnings[1] = $rsaData[1]['Expected Earnings (Daily)'];
	$superblockData->fulfillment[1] = $rsaData[1]['Fulfillment %'];
	$superblockData->interest[1] = $rsaData[1]['CPID Lifetime Interest Paid'];
	$superblockData->research[1] = $rsaData[1]['CPID Lifetime Research Paid'];
	$superblockData->txCount[1] = $rsaData[1]['Tx Count'];
	
	$balance = $daemon2->getTotalBalance();
	$superblockData->balance[1] = $balance;

	$mag = $daemon2->getMagnitude();
	$superblockData->mag[1] = $mag;
	
	
	////////////////
	

	
	$creditViewDao = new GrcPool_View_Member_Host_Project_Credit_DAO();
	$noAddrs = $creditViewDao->getOwedWithNowAddress();
	$totalNoAddrs = array();
	$totalNoAddrs[0] = 0;
	$totalNoAddrs[1] = 0;
	foreach ($noAddrs as $no) {
		$totalNoAddrs[$no->getPoolId()-1] += $no->getOwed();
	}
	$superblockData->grcNoAddress = $totalNoAddrs;
	
	$objs = $hostCreditDao->getOwedWithNoOwner();
	$total = array();
	$total[0] = 0;
	$total[1] = 0;
	foreach ($objs as $obj) {
		$total[$obj->getPoolId()-1] += $obj->getOwed();
	}
	$superblockData->grcOwnerUnknown = $total;

}

$cache->set($superblockData->toJson(),Constants::CACHE_SUPERBLOCK_DATA);
echo $superblockData->toJson();