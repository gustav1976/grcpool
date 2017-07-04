<?php
/*
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
THIS SEEMED LIKE A GOOD IDEA... TRY TO SYNC DBIDS TO THE CLIENTS - HOWEVER THIS IS BAD!!!! 
EVEN THOUGH CPID SEEMS LIKE IT SHOULD BE UNIOQUE ENOUGH, IT ISN"T, MULTIPLE BOINC CLIENTS 
WILL HAVE THE SAME CPIDS...
*/

exit;


if (!isset($argv[1])) {
	exit;
}
require_once(dirname(__FILE__).'/../bootstrap.php');
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ UPDATEDBIDS START ".date("Y.m.d H.i.s")."\n";
set_time_limit(60);

$projDao = new GrcPool_Member_Host_Project_DAO();
$creditDao = new GrcPool_Member_Host_Credit_DAO();
$projs = $projDao->fetchAll(array($projDao->where('hostDbId',0)));

foreach ($projs as $proj) {
	echo '~~~~~~~~~~~~~~~~~~~~~~~~~~~~'."\n";
	echo 'Pool: '.$proj->getPoolId()."\n";
	$hash = '';
	if ($proj->getPoolId() == 1) {
		$hash = md5($proj->getHostCpid().'admin@grcpool.com');
	} else if ($proj->getPoolId() == 2) {
		$hash = md5($proj->getHostCpid().'admin2@grcpool.com');
	} else {
	 	continue;
	}
	$credit = $creditDao->getWithCpidAndProjectUrlAndPoolId($hash,$proj->getProjectUrl(),$proj->getPoolId());
	if ($credit) {
		echo 'HASH: '.$hash."\n";
		print_r($proj);
		print_r($credit);
		if (count($credit) > 1) {
			echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\nTOO MANY !!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
		} else {
			$credit = array_pop($credit);
			$check = $projDao->getWithHostDbIdAndProjectUrl($credit->getHostDbid(),$proj->getProjectUrl());
			print_r($check);
			if ($check) {
				echo "\n\n!!!!!!!!!! DUPLICATE\n";
				echo 'HASH: '.$hash."\n";
			} else {
				echo "NO DUPLICATE\n";
				echo 'HASH: '.$hash."\n";
				$proj->setHostDbid($credit->getHostDbid());
				print_r($proj);exit;
				$projDao->save($proj);
			}
		}
	}
}

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ UPDATEDBIDS END ".date("Y.m.d H.i.s")."\n";
