<?php
require_once(dirname(__FILE__).'/../bootstrap.php');
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ UPDATEDBIDS START ".date("Y.m.d H.i.s")."\n";
set_time_limit(60);

$projDao = new GrcPool_Member_Host_Project_DAO();
$creditDao = new GrcPool_Member_Host_Credit_DAO();
$projs = $projDao->fetchAll(array($projDao->where('hostDbId',0)));

foreach ($projs as $proj) {
	echo '~~~~~~~~~~~~~~~~~~~~~~~~~~~~'."\n";
	$hash = md5($proj->getHostCpid().'admin@grcpool.com');
	$credit = $creditDao->getWithCpidAndProjectUrl($hash,$proj->getProjectUrl());
	if ($credit) {
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
				$projDao->save($proj);
			}
		}
	}
}

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ UPDATEDBIDS END ".date("Y.m.d H.i.s")."\n";
