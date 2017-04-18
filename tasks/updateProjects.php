<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

/*$projects = array(
	'http://moowrap.net/',
	'http://asteroidsathome.net/boinc/',
	'http://atlasathome.cern.ch/',
	'http://www.cosmologyathome.org/',
	'http://einstein.phys.uwm.edu/',
	'http://www.enigmaathome.net/',
	'http://www.gpugrid.net/',
	'https://lhcathome.cern.ch/lhcathome/',
	'http://milkyway.cs.rpi.edu/milkyway/',
	'http://boinc.bakerlab.org/rosetta/',
	'http://setiathome.berkeley.edu/',
);*/

$exe = new BoincCmd();

$accountDao = new GrcPool_Boinc_Account_DAO();
$accounts = $accountDao->fetchAll();
foreach ($accounts as $account) {
	//if ($account->getAttachable() == 0) {
		echo $account->getUrl()."\n";
		//$exe->updateProject($account->getUrl());
		sleep(3);
	//}
}
