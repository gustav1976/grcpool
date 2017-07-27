<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';

$idArg = 1;
if ($FORCE) {$idArg++;}

$neededProjectCount = 15;
$validSuper = true;

echo "############## GETHOSTDATA ".date("Y.m.d H.i.s")."\n";
$settingsDao = new GrcPool_Settings_DAO();
if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE\n\n";
	exit;
}

$lockFile = 'payout.lock';
$fp = fopen(dirname(__FILE__).'/'.$lockFile,"w");
if (!flock($fp, LOCK_EX | LOCK_NB)) {
	echo('!!!!!!!!!! LOCKED !!!!!!!!!!!!!');
	exit;
}

set_time_limit(240);

$id = 0;
if (isset($argv[$idArg])) {
	$id = $argv[$idArg];
}
//$id = 29; // !!!!!!!!!!!!!!!!!!!!!!!!! REMOVE THIS

$cache = new Cache();
$superblockData = new SuperBlockData($cache->get(Constants::CACHE_SUPERBLOCK_DATA));
$whiteListed = $superblockData->projects;
if ($whiteListed == null) {
	$whiteListed = array();
}
$numberOfProjects = $superblockData->whiteListCount;

if ($numberOfProjects < $neededProjectCount) {
	$validSuper = false;
	$PROPERTY = new Property(Constants::PROPERTY_FILE);
	if (!$PROPERTY->get('test')) {
		echo 'WHITE LISTED PROJECT COUNT LOW: '.$numberOfProjects."\n\n";
		//exit;
	}
}
 

$projectDao = new GrcPool_Boinc_Account_DAO();
$hostProjectDao = new GrcPool_Member_Host_Project_DAO();
$hostDao = new GrcPool_Member_Host_Credit_DAO();
$projects = $projectDao->fetchAll();

foreach ($projects as $project) {
	echo '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ '.$project->getName().' '.$project->getBaseUrl()."\n";
	
	if ($id && $project->getId() != $id) {
		echo 'SKIPPING: '.$project->getUrl()."\n";
		continue;
	}
	if ($project->getStrongKey() == '') {
		echo '!!!!!!!!!!! NO STRONG KEY: '.$project->getUrl()."\n";
		continue;
	}
	if ($project->getTeamId() == 0) {
		echo '!!!!!!!!!!! NO TEAMID: '.$project->getUrl()."\n";
		continue;
	}
	
	if ($validSuper) {
		if (array_search($project->getGrcName(),$whiteListed) === false) {
			$PROPERTY = new Property(Constants::PROPERTY_FILE);
			if (!$PROPERTY->get('test')) {
				echo "!!!!!!!!!!! BLACK LISTED PROJECT BY NETWORK ".$project->getGrcName().' '.$project->getUrl()."\n";
				$hostDao->setMagToZeroForProjectUrl($project->getUrl());
				$project->setWhitelist(0);
				$projectDao->save($project);
				continue;
			}
		}
		if (!$project->getWhiteList()) {
			$project->setWhitelist(1);
			$projectDao->save($project);
		}
	} else {
		if ($project->getWhiteList() == 0) {
			echo "!!!!!!!!!!! BLACK LISTED PROJECT BY TABLE ".$project->getGrcName().' '.$project->getUrl()."\n";
			continue;
		}
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
	if ($validSuper) {
		$project->setWhiteListCount($numberOfProjects);
	}
	$project->setMinRac(GrcPool_Utils::getMinRac($rac,$project->getWhiteListCount()));
	$projectDao->save($project);
	$hostCount = 0;
	for ($poolId = 1; $poolId <= Constants::NUMBER_OF_POOLS; $poolId++) {
		echo "%%%%%%%%%%%%%%%%%%% HOSTS FOR POOL #".$poolId."\n";
		if ($project->getStrongKeyForPoolId($poolId) == '') {
			echo '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! NO STRONG KEY'."\n";
			continue;
		}
		$hostCountPool = 0;
		// get host stuff
		$file = 'show_user.php';
		$query = '?auth='.$project->getStrongKeyForPoolId($poolId).'&format=xml';
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
			$obj = $hostDao->initWithProjectUrlDbid($project->getUrl(),(String)$host->id);
			
			if ($obj == null) {
				$obj = new GrcPool_Member_Host_Credit_OBJ();
				$obj->setHostDbid((String)$host->id);
				$obj->setHostCpid((String)$host->host_cpid);
				$obj->setTotalCredit((String)$host->total_credit);
				$obj->setAvgCredit((String)$host->expavg_credit);
				$obj->setProjectUrl($project->getUrl());
				$obj->setLastSeen(time());
				$obj->setPoolId($poolId);
			} else {
				$obj->setProjectUrl($project->getUrl());
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
				$mag = GrcPool_Utils::calculateMag($obj->getAvgCredit(),$project->getRac(),$project->getWhiteListCount(),2);
			}
			if ($obj->getMemberIdCredit() == 0) {
				$hostObjs = $hostProjectDao->getWithHostDbIdAndProjectUrl($obj->getHostDbid(),$obj->getProjectUrl());
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


/////////////////// SYNCH CPID UPDATES
/*
$sql = 'UPDATE grcpool.member_host_project
INNER JOIN grcpool.member_host_credit ON grcpool.member_host_project.hostDbid = grcpool.member_host_credit.hostDbid and grcpool.member_host_project.projectUrl = grcpool.member_host_credit.projectUrl
SET grcpool.member_host_project.hostCpid = grcpool.member_host_credit.hostCpid';
$hostDao->executeQuery($sql);

$sql = 'UPDATE grcpool.member_host
INNER JOIN grcpool.member_host_project ON grcpool.member_host.id = grcpool.member_host_project.hostid 
		SET grcpool.member_host.cpid = grcpool.member_host_project.hostCpid';
$hostDao->executeQuery($sql);
*/

