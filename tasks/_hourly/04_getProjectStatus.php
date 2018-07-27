<?php

set_time_limit(240);

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../bootstrap.php');

$id = 0;
if (isset($argv[1])) {
	$id = $argv[1];
}

$projectDao = new GrcPool_Boinc_Account_DAO();
$projects = $projectDao->fetchAll();
$statDao = new GrcPool_Boinc_Account_Stats_DAO();

foreach ($projects as $project) {
	if ($id && $project->getId() != $id) {
		continue;
	}
	
	echo '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ '.$project->getName().' '.$project->getBaseUrl()."\n";
		
	$domain = $project->getBaseUrl();
	if ($project->getSecure() && !strstr($domain,'https:')) {
		$domain = str_replace('http:','https:',$domain);
	}

	
	
	// GET SERVER STATUS
	if ($project->getGrcName() == Constants::GRCNAME_SETI) {
		$file = 'sah_status.xml';
	} else {
		$file = 'server_status.php?xml=1';
	}
	$url = $domain.$file;
	echo ' -- server status: '.$url."\n";
	$data = file_get_contents($url);
	try {
		$xml = simplexml_load_string($data);
		if ($xml) {
			$dbStates = $xml->database_file_states;
			$numberOfTasks = 0;
			$numberOfProgress = 0;
			$numberValidating = 0;
			foreach ($dbStates->children() as $dbState) {
				$eleName = (String)$dbState->getName();
				if (strstr($eleName,'results_ready_to_send')) {
					$numberOfTasks += (int)$dbState;
				} else if (strstr($eleName,'results_in_progress')) {
					$numberOfProgress += (int)$dbState;
				} else if (strstr($eleName,'results_awaiting_validation') || strstr($eleName,'waiting_for_validation')) {
					$numberValidating += (int)$dbState;
				}
			}
			$stat = $statDao->fetch(array($statDao->where('accountId',$project->getId()),$statDao->where('name','READY_TO_SEND')));
			if ($stat == null) {$stat = new GrcPool_Boinc_Account_Stats_OBJ();}
			$stat->setAccountId($project->getId());$stat->setModTime(time());$stat->setName('READY_TO_SEND');$stat->setValue($numberOfTasks);$statDao->save($stat);
			$stat = $statDao->fetch(array($statDao->where('accountId',$project->getId()),$statDao->where('name','IN_PROGRESS')));
			if ($stat == null) {$stat = new GrcPool_Boinc_Account_Stats_OBJ();}
			$stat->setAccountId($project->getId());$stat->setModTime(time());$stat->setName('IN_PROGRESS');$stat->setValue($numberOfProgress);$statDao->save($stat);
			$stat = $statDao->fetch(array($statDao->where('accountId',$project->getId()),$statDao->where('name','IN_VALIDATION')));
			if ($stat == null) {$stat = new GrcPool_Boinc_Account_Stats_OBJ();}
			$stat->setAccountId($project->getId());$stat->setModTime(time());$stat->setName('IN_VALIDATION');$stat->setValue($numberValidating);$statDao->save($stat);
			echo 'READY: '.$numberOfTasks.'  PROGRESS: '.$numberOfProgress.'  VALIDATIN: '.$numberValidating."\n";
		}
	} catch (Exception $e) {
		
	}
}
