<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

echo "############## GETHOSTDATA ".date("Y.m.d H.i.s")."\n";
$settingsDao = new GrcPool_Settings_DAO();
if ($settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE";
	exit;
}

$lockFile = 'payout.lock';
$fp = fopen(dirname(__FILE__).'/'.$lockFile,"w");
if (!flock($fp, LOCK_EX | LOCK_NB)) {
	echo('!!!!!!!!!! LOCKED !!!!!!!!!!!!!');
	exit;
}

set_time_limit(120);

$id = 0;
if (isset($argv[1])) {
	$id = $argv[1];
}

$daemon = GrcPool_Utils::getDaemonForEnvironment();

$whiteListed = $daemon->getWhitelistedProjects();

$numberOfProjects = count($whiteListed);

if ($numberOfProjects < 10) {
	echo 'WHITE LISTED PROJECT COUNT LOW: '.$numberOfProjects;
	exit;
}
 

$projectDao = new GrcPool_Boinc_Account_DAO();
$hostDao = new GrcPool_Member_Host_Credit_DAO();
$projects = $projectDao->fetchAll();

foreach ($projects as $project) {
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
	if (array_search($project->getGrcName(),$whiteListed) === false) {
		echo "!!!!!!!!!!! BLACK LISTED PROJECT ".$project->getGrcName().' '.$project->getUrl()."\n";
		$hostDao->setMagToZeroForProjectUrl($project->getUrl());
		$project->setWhitelist(0);
		$projectDao->save($project);
		continue;
	}
	if (!$project->getWhiteList()) {
		$project->setWhitelist(1);
		$projectDao->save($project);
	}
	echo '~~~~~~~~~ '.$project->getName().' '.$project->getBaseUrl()."\n";
		
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
	echo 'RAC '.$rac."   ";
	$project->setRac($rac);
	$project->setWhiteListCount($numberOfProjects);
	$project->setMinRac(GrcPool_Utils::getMinRac($rac,$numberOfProjects));
	$projectDao->save($project);
	
	// get host stuff
	$file = 'show_user.php';
	$query = '?auth='.$project->getStrongKey().'&format=xml';
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
	
	$hostCount = 0;
	foreach ($xml->host as $host) {
		$hostCount++;
		$obj = $hostDao->initWithProjectUrlDbid($project->getUrl(),(String)$host->id);
		
		if ($obj == null) {
			$obj = new GrcPool_Member_Host_Credit_OBJ();
			$obj->setHostDbid((String)$host->id);
			$obj->setHostCpid((String)$host->host_cpid);
			$obj->setTotalCredit((String)$host->total_credit);
			$obj->setAvgCredit((String)$host->expavg_credit);
			$obj->setProjectUrl($project->getUrl());
			$obj->setLastSeen(time());
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
			//$mag = Utils::truncate(Constants::GRC_MAG_MULTIPLIER*(($obj->getAvgCredit()/$project->getRac())/$numberOfProjects),2);
			$mag = GrcPool_Utils::calculateMag($obj->getAvgCredit(),$project->getRac(),$numberOfProjects,2);
		}
		$obj->setMag($mag);
		$hostDao->save($obj);
	}
	echo 'Number of hosts: '.$hostCount."\n";
}

// cleanup rows with a long owedCalc
$sql = 'update grcpool.member_host_credit set owedCalc = concat(\'+\',owed) where char_length(owedCalc) > 500';
$hostDao->executeQuery($sql);

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

