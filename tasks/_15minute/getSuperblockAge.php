<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../bootstrap.php');

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';

$settingsDao = new GrcPool_Settings_DAO();
if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE";
	exit;
}

$numberOfPools = Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS);

$cache = new Cache();
$daemon = GrcPool_Utils::getDaemonForPool(1);
$updateHostData = array();

$superblockData = new SuperBlockData($cache->get(Constants::CACHE_SUPERBLOCK_DATA));
$hostDao = new GrcPool_Member_Host_Credit_DAO();

$result = $daemon->getSuperBlockAge();
if (!isset($result['timestamp']) || $result['timestamp'] == '') {
        echo "WALLET DOWN";
        exit;
}
$superblockData->timestamp = $result['timestamp'];
$superblockData->age = $result['age'];
$superblockData->pending = $result['pending'];
$superblockData->block = $result['block'];
$superblockData->ageText = $result['ageText'];

if ($FORCE || ($superblockData->pending == 0 && $superblockData->lastBlock != $superblockData->block)) {
	
	echo "\nNEW SUPERBLOCK DATA NEEDED\n\n";
	
	$settingsDao = new GrcPool_Settings_DAO();
	$poolWhiteListCount = $settingsDao->getValueWithName(Constants::SETTINGS_POOL_WHITELIST_COUNT);
	
	$superblockData->paidOut = array();
	
	$hostCreditDao = new GrcPool_Member_Host_Credit_DAO();
	$totalOwed = array();
	for ($poolId = 1; $poolId <= $numberOfPools; $poolId++) {
		$superblockData->paidOut[$poolId-1] = $settingsDao->getValueWithName((Constants::SETTINGS_TOTAL_PAID_OUT).($poolId>1?$poolId:''));
		$totalOwed[$poolId-1] = $hostCreditDao->getTotalOwedForPool($poolId);
	}

	$superblockData->owed = $totalOwed;
	
	$superblockData->lastBlock = $superblockData->block;
	
	$version = $daemon->getVersion();
	$superblockData->version = $version;

	$wl = $daemon->getWhitelistedProjects();
	if ($wl) {
		$superblockData->whiteListCount = count($wl);
		$superblockData->projects = $wl;
	}	
	////////////////////
	// WHITE LIST STUFF
	$projectDao = new GrcPool_Boinc_Account_DAO();
	$projects = $projectDao->fetchAll();
	$checkNumberOfProjects = $superblockData->whiteListCount;
	foreach ($projects as $project) {
		if ($project->getAuto()) {
			if (array_search($project->getGrcName(),$superblockData->projects) === false) {
				if ($project->getWhiteList()) {
					echo 'CHANGING '.$project->getName().' WHITELIST OFF'."\n";
					$hostDao->setMagToZeroForAccountId($project->getId());
					$project->setWhitelist(0);
					$projectDao->save($project);
				}
			} else if (!$project->getWhiteList()) {
				echo 'CHANGING '.$project->getName().' WHITELIST ON'."\n";
				array_push($updateHostData,$project->getId());
				$project->setWhitelist(1);
				$projectDao->save($project);
			}
		} else {
			if (array_search($project->getGrcName(),$superblockData->projects) === false) {
				if ($project->getWhiteList()) {
					// missing from network, override for pool
					echo "OVERRIDING ".$project->getName()."\n";
					$checkNumberOfProjects++;
				}
			}
		}
	}
	echo "PROJECT WHITE LIST COUNT FOR POOL IS: ".$checkNumberOfProjects."\n";
	if ($checkNumberOfProjects != $poolWhiteListCount) {
		echo 'CHANGING WHITE LIST COUNT FROM '.$poolWhiteListCount.' TO '.$checkNumberOfProjects."\n";
		$settingsDao->setValueWithName(Constants::SETTINGS_POOL_WHITELIST_COUNT,$checkNumberOfProjects);
	}

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

	$mag = $daemon->getMagnitude($settingsDao->getValueWithName((Constants::SETTINGS_CPID)));
	$superblockData->mag[0] = $mag;
	
	$basisDao = new GrcPool_Wallet_Basis_DAO();
	$basisObj = $basisDao->initWithKey(1);
	$superblockData->basis[0] = $basisObj->getBasis();
	
	$rsaData = $daemon->getRsa($settingsDao->getValueWithName((Constants::SETTINGS_CPID)));
	$superblockData->expectedDailyEarnings[0] = $rsaData[1]['Expected Earnings (Daily)']??0;
	$superblockData->fulfillment[0] = $rsaData[1]['Fulfillment %']??0;
	$superblockData->interest[0] = $rsaData[1]['CPID Lifetime Interest Paid']??0;
	$superblockData->research[0] = $rsaData[1]['CPID Lifetime Research Paid']??0;
	$superblockData->txCount[0] = $rsaData[1]['Tx Count']??0;
	$superblockData->magUnit = $rsaData[2]['Magnitude Unit (GRC payment per Magnitude per day)']??0;
	
	//$stakingInfo = $daemon->getStakingInfo();
	//$superblockData->netWeight = $stakingInfo['weight']/COIN;
	
	$balance = $daemon->getTotalBalance();
	$superblockData->balance[0] = $balance;
	
	// POOL 2
	
	echo "GETTING POOL > 1 DATA\n";
	
	for ($poolId = 2; $poolId <= $numberOfPools; $poolId++) {
		$daemon2 = GrcPool_Utils::getDaemonForPool($poolId);
		$basisObj = $basisDao->initWithKey($poolId);
		$superblockData->basis[$poolId-1] = $basisObj->getBasis();
	
		$cpid = $settingsDao->getValueWithName((Constants::SETTINGS_CPID).$poolId);
		$rsaData = $daemon2->getRsa($cpid);
		$superblockData->expectedDailyEarnings[$poolId-1] = $rsaData[1]['Expected Earnings (Daily)']??0;
		$superblockData->fulfillment[$poolId-1] = $rsaData[1]['Fulfillment %']??0;
		$superblockData->interest[$poolId-1] = $rsaData[1]['CPID Lifetime Interest Paid']??0;
		$superblockData->research[$poolId-1] = $rsaData[1]['CPID Lifetime Research Paid']??0;
		$superblockData->txCount[$poolId-1] = $rsaData[1]['Tx Count']??0;
		
		$balance = $daemon2->getTotalBalance();
		$superblockData->balance[$poolId-1] = $balance;
	
		$mag = $daemon2->getMagnitude($settingsDao->getValueWithName((Constants::SETTINGS_CPID).$poolId));
		$superblockData->mag[$poolId-1] = $mag;
	}
	
	////////////////
	

	
	$creditViewDao = new GrcPool_View_Member_Host_Project_Credit_DAO();
	$noAddrs = $creditViewDao->getOwedWithNowAddress();
	$totalNoAddrs = array();
	$total = array();
	for ($poolId = 1; $poolId <= $numberOfPools; $poolId++) {
		$totalNoAddrs[$poolId-1] = 0;
		$total[$poolId-1] = 0;
	}
	foreach ($noAddrs as $no) {
		$totalNoAddrs[$no->getPoolId()-1] += $no->getOwed();
	}
	$superblockData->grcNoAddress = $totalNoAddrs;
	
	$objs = $hostCreditDao->getOwedWithNoOwner();
	foreach ($objs as $obj) {
		$total[$obj->getPoolId()-1] += $obj->getOwed();
	}
	$superblockData->grcOwnerUnknown = $total;

}

$cache->set($superblockData->toJson(),Constants::CACHE_SUPERBLOCK_DATA);
echo $superblockData->toJson();
