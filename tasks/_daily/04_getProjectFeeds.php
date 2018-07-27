<?php

exit;

ini_set('display_errors',1);
error_reporting(E_ALL);

set_time_limit(500);

require_once(dirname(__FILE__).'/../../bootstrap.php');

$id = 0;
if (isset($argv[1])) {
	$id = $argv[1];
}

$projectDao = new GrcPool_Boinc_Account_DAO();
$userDao = new GrcPool_Boinc_Account_User_DAO();
$projects = $projectDao->fetchAll();
$statDao = new GrcPool_Boinc_Account_Stats_DAO();

$feeds = array(
	'badgeTeam' => 'stats/badge_team.gz',
	'team' => 'stats/team.gz',
	'teamWork' => 'stats/team_work.gz',
	'tables' => 'stats/tables.xml',
	'user' => 'stats/user.gz',
);

foreach ($projects as $project) {
	break;
	if ($id && $project->getId() != $id) {
		continue;
	}
	
	echo '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ '.$project->getName().' '.$project->getBaseUrl()."\n";
		
	$domain = $project->getBaseUrl();
	if ($project->getSecure() && !strstr($domain,'https:')) {
		$domain = str_replace('http:','https:',$domain);
	}

	foreach ($feeds as $feedId => $feed) {
		$file = $feed;
		$url = $domain.$file;
		echo ' -- '.$feedId.': '.$url."\n";
		$ext = 'gz';
		if (strstr($feed,'.xml')) {
			$ext = 'xml';	
		}
		$new_file = dirname(__FILE__).'/../feeds/'.preg_replace( '/[^a-z0-9]+/', '-', strtolower($project->getGrcName())).'_'.$feedId.'.'.$ext;	
		try {	
			if ($ext == 'gz') {
				$remote = gzopen($url, "rb");
				if ($remote !== false) {
					$home = fopen($new_file, "w");
					while ($string = gzread($remote, 4096)) {
						fwrite($home, $string, strlen($string));
					}
					fclose($home);
					gzclose($remote);
				}
			} else if ($ext == 'xml') {
				$data = file_get_contents($url);
				file_put_contents($new_file,$data);
			}
		} catch (Exception $e) {
			
		}
// 		$result = '';
// 		$sfp = gzopen('feeds/data.gz', "rb");
// 		while ($string = gzread($sfp, 4096)) {
// 			$result .= $string;
// 		}
// 		//gzclose($sfp);
// 		echo $result;
	}
	//exit;
}

echo "\n\n";
foreach ($projects as $project) {
	if ($id && $project->getId() != $id) {
		continue;
	}
	echo'~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ '.$project->getBaseUrl()."\n";
	$feed = null;
	$className = 'GrcPool_Boinc_ProjectXmlStats_'.ucwords(preg_replace( '/[^a-z0-9]+/', '', strtolower($project->getGrcName())));
	if (class_exists($className)) {
		$feed = new $className($project);
	} else {
		$feed = new GrcPool_Boinc_ProjectXmlStats_Base($project);
	}
	
	$objects = $feed->getTeamUsers($project->getId(),$project->getTeamId());
	$userDao->deleteWithAccountId($project->getId());
	foreach ($objects as $object) {
		$userDao->save($object);
	}
	
	$stat = $statDao->fetch(array($statDao->where('accountId',$project->getId()),$statDao->where('name','TOTAL_USERS')));
	if ($stat == null) {$stat = new GrcPool_Boinc_Account_Stats_OBJ();}
	$stat->setAccountId($project->getId());$stat->setModTime(time());$stat->setName('TOTAL_USERS');$stat->setValue($feed->getNumberOfUsers());$statDao->save($stat);

	$stat = $statDao->fetch(array($statDao->where('accountId',$project->getId()),$statDao->where('name','TOTAL_HOSTS')));
	if ($stat == null) {$stat = new GrcPool_Boinc_Account_Stats_OBJ();}
	$stat->setAccountId($project->getId());$stat->setModTime(time());$stat->setName('TOTAL_HOSTS');$stat->setValue($feed->getNumberOfHosts());$statDao->save($stat);

	$stat = $statDao->fetch(array($statDao->where('accountId',$project->getId()),$statDao->where('name','TOTAL_TEAMS')));
	if ($stat == null) {$stat = new GrcPool_Boinc_Account_Stats_OBJ();}
	$stat->setAccountId($project->getId());$stat->setModTime(time());$stat->setName('TOTAL_TEAMS');$stat->setValue($feed->getNumberOfTeams());$statDao->save($stat);
	
	$stat = $statDao->fetch(array($statDao->where('accountId',$project->getId()),$statDao->where('name','TOTAL_CREDIT')));
	if ($stat == null) {$stat = new GrcPool_Boinc_Account_Stats_OBJ();}
	$stat->setAccountId($project->getId());$stat->setModTime(time());$stat->setName('TOTAL_CREDIT');$stat->setValue($feed->getTotalCredit());$statDao->save($stat);
	
	$stat = $statDao->fetch(array($statDao->where('accountId',$project->getId()),$statDao->where('name','BADGES')));
	if ($stat == null) {$stat = new GrcPool_Boinc_Account_Stats_OBJ();}
	$stat->setAccountId($project->getId());$stat->setModTime(time());$stat->setName('BADGES');$stat->setValue($feed->getBadges());$statDao->save($stat);

	$stat = $statDao->fetch(array($statDao->where('accountId',$project->getId()),$statDao->where('name','TEAM_CREDIT')));
	if ($stat == null) {$stat = new GrcPool_Boinc_Account_Stats_OBJ();}
	$stat->setAccountId($project->getId());$stat->setModTime(time());$stat->setName('TEAM_CREDIT');$stat->setValue($feed->getTeamTotalCredit());$statDao->save($stat);

	$stat = $statDao->fetch(array($statDao->where('accountId',$project->getId()),$statDao->where('name','TEAM_AVGCREDIT')));
	if ($stat == null) {$stat = new GrcPool_Boinc_Account_Stats_OBJ();}
	$stat->setAccountId($project->getId());$stat->setModTime(time());$stat->setName('TEAM_AVGCREDIT');$stat->setValue($feed->getTeamAvgCredit());$statDao->save($stat);
	
}





