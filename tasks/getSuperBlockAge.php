<?php
require_once(dirname(__FILE__).'/../bootstrap.php');
$settingsDao = new GrcPool_Settings_DAO();
if ($settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE";
	exit;
}
$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';

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
	$superblockData->paidOut = $settingsDao->getValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT);
	
	$hostCreditDao = new GrcPool_Member_Host_Credit_DAO();
	$totalOwed = $hostCreditDao->getTotalOwed();
	$superblockData->owed = $totalOwed;
	
	$basisDao = new GrcPool_Wallet_Basis_DAO();
	$superblockData->basis = $basisDao->getBasis();
	
	$superblockData->lastBlock = $superblockData->block;
	
	$version = $daemon->getVersion();
	$superblockData->version = $version;
	
	$mag = $daemon->getMagnitude();
	$superblockData->mag = $mag;
	
	$wl = $daemon->getNumberOfProjects();
	$superblockData->whiteListCount = $wl;
	
	$rsaData = $daemon->getRsa();
	$superblockData->expectedDailyEarnings = $rsaData[1]['Expected Earnings (Daily)'];
	$superblockData->fulfillment = $rsaData[1]['Fulfillment %'];
	$superblockData->interest = $rsaData[1]['CPID Lifetime Interest Paid'];
	$superblockData->research = $rsaData[1]['CPID Lifetime Research Paid'];
	$superblockData->txCount = $rsaData[1]['Tx Count'];
	$superblockData->magUnit = $rsaData[2]['Magnitude Unit (GRC payment per Magnitude per day)'];
	
	$balance = $daemon->getTotalBalance();
	$superblockData->balance = $balance;
	
	$creditViewDao = new GrcPool_View_Member_Host_Project_Credit_DAO();
	$noAddrs = $creditViewDao->getOwedWithNowAddress();
	$totalNoAddrs = 0;
	foreach ($noAddrs as $no) {
		$totalNoAddrs += $no->getOwed();
	}
	$superblockData->grcNoAddress = $totalNoAddrs;
	
	$objs = $hostCreditDao->getOwedWithNoOwner();
	$total = 0;
	foreach ($objs as $obj) {
		$total += $obj->getOwed();
	}
	$superblockData->grcOwnerUnknown = $total;

}

$cache->set($superblockData->toJson(),Constants::CACHE_SUPERBLOCK_DATA);
echo $superblockData->toJson();