<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';

$runOnHours = array('3','9','15','21');
if (!$FORCE && array_search(date('G'),$runOnHours) === false) {
	echo "NOT TIME TO RUN ".date('G')."\n";
	exit;
}

require_once(dirname(__FILE__).'/../../bootstrap.php');

$idArg = 1;
if ($FORCE) {$idArg++;}

echo "############## GETHOSTDATA ".date("Y.m.d H.i.s")."\n";

$settingsDao = new GrcPool_Settings_DAO();

if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE\n\n";
	exit;
}

$lockFile = 'payout.lock';
$fp = fopen(dirname(__FILE__).'/../'.$lockFile,"w");
if (!flock($fp, LOCK_EX | LOCK_NB)) {
	echo('!!!!!!!!!! LOCKED !!!!!!!!!!!!!');
	exit;
}

$id = 0;
$poolArg = 0;
if (isset($argv[$idArg])) {
	$id = $argv[$idArg];
}
if (isset($argv[$idArg+1])) {
	$poolArg = $argv[$idArg+1];
}

$projectDao = new GrcPool_Boinc_Account_DAO();
$hostProjectDao = new GrcPool_Member_Host_Project_DAO();
$hostDao = new GrcPool_Member_Host_Credit_DAO();
$keyDao = new GrcPool_Boinc_Account_Key_DAO();

$poolWhiteListCount = $settingsDao->getValueWithName(Constants::SETTINGS_POOL_WHITELIST_COUNT);
$projects = $projectDao->fetchAll();

foreach ($projects as $project) {
	echo '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ '.$project->getName().' '.$project->getBaseUrl()."\n";
	
	if ($id && $project->getId() != $id) {
		echo 'SKIPPING: '.$project->getBaseUrl()."\n";
		continue;
	}

	if ($project->getTeamId() == 0) {
		echo '!!!!!!!!!!! NO TEAMID: '.$project->getBaseUrl()."\n";
		continue;
	}

 	if ($project->getWhiteList() == 0) {
 		echo "!!!!!!!!!!! BLACK LISTED ".$project->getGrcName().' '.$project->getBaseUrl()."\n";
 		$hostDao->setMagToZeroForAccountId($project->getId()); // just in case of a manual override
 		continue;
 	}
		
	$domain = $project->getBaseUrl();
	if ($project->getSecure() && !strstr($domain,'https:')) {
		$domain = str_replace('http:','https:',$domain);
	}

	// get team stuff
	$file = 'team_lookup.php';
	$query = '?team_id='.$project->getTeamId();
	$url = $domain.$file.$query;
	//echo 'Team Lookup: '.$url."\n";
	$data = file_get_contents($url);
	$xml = simplexml_load_string($data);
	$rac = (String)$xml->expavg_credit;
	if ($rac == 0) {
		echo 'Team Lookup: '.$url."\n";
		echo '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! NO RAC DATA'."\n";
		continue;
	}
	echo 'RAC '.$rac."   \n";
	$project->setRac($rac);

	$project->setMinRac(GrcPool_Utils::getMinRac($rac,$poolWhiteListCount));
	$projectDao->save($project);
	$hostCount = 0;
	for ($poolId = 1; $poolId <= Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS); $poolId++) {
		
		if ($poolArg && $poolId != $poolArg) {
			echo 'SKIPPING POOL: '.$poolId."\n";
			continue;
		}
		
		echo "%%%%%%%%%%%%%%%%%%% HOSTS FOR POOL #".$poolId."\n";
		$key = $keyDao->getWithAccountAndPool($project->getId(),$poolId);
		if (!$key || $key->getStrong() == '') {
			echo '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! NO STRONG KEY'."\n";
			continue;
		}
		$hostCountPool = 0;
		// get host stuff
		$file = 'show_user.php';
		$query = '?auth='.$key->getStrong().'&format=xml';
		$url = $domain.$file.$query;
		//echo 'User Lookup: '.$url."\n";
		$data = file_get_contents($url);
		$xml = simplexml_load_string($data);
		
		if (!$xml->host) {
			echo 'User Lookup: '.$url."\n";
			echo '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! NO HOST DATA'."\n";
			continue;
		} else {
			$project->setLastSeen(time());
			$projectDao->save($project);
		}
		
		foreach ($xml->host as $host) {
			$hostCount++;
			$hostCountPool++;
			$obj = $hostDao->initWithAccountIdAndDbid($project->getId(),(String)$host->id);
			if ($obj == null) {
				$obj = new GrcPool_Member_Host_Credit_OBJ();
				$obj->setHostDbid((String)$host->id);
				$obj->setHostCpid((String)$host->host_cpid);
				$obj->setTotalCredit((String)$host->total_credit);
				$obj->setAvgCredit((String)$host->expavg_credit);
				$obj->setAccountId($project->getId());
				$obj->setLastSeen(time());
				$obj->setPoolId($poolId);
			} else {
				$obj->setAccountId($project->getId());
				$obj->setHostCpid((String)$host->host_cpid);
				$obj->setTotalCredit((String)$host->total_credit);
				$obj->setAvgCredit((String)$host->expavg_credit);
				$obj->setLastSeen(time());
			}
			if ((String)$host->total_credit != $obj->getMagTotalCredit()) {
				$obj->setMagTotalCredit((String)$host->total_credit);
			}
			if ($obj->getAvgCredit() < $project->getMinRac() || $project->getMinRac() == 0) {
				$mag = 0;
			} else {
				$mag = GrcPool_Utils::calculateMag($obj->getAvgCredit(),$project->getRac(),$poolWhiteListCount,2);
			}
			if ($obj->getMemberIdCredit() == 0) {
				$hostObjs = $hostProjectDao->getWithHostDbIdAndAccountId($obj->getHostDbid(),$obj->getAccountId());
				if ($hostObjs) {
					$hostObj = array_pop($hostObjs);
					$obj->setMemberIdCredit($hostObj->getMemberId());
				}
			}
			$obj->setMag($mag);
			$hostDao->save($obj);
		}
		echo 'Number of hosts for pool project: '.$hostCountPool."\n";
	}
	echo 'Number of hosts for project: '.$hostCount."\n";
}
