<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../../bootstrap.php');

$hostDao = new GrcPool_Member_Host_Credit_DAO();

$sql = '
  	insert into '.Constants::DATABASE_NAME.'.member_host_stat_mag (memberId,hostId,accountId,thetime,mag,avgCredit,poolId) 
  	select memberId,hostDbid,accountId,UNIX_TIMESTAMP(NOW()),mag,avgCredit,projectPoolId
  	from grcpool.view_member_host_project_credit 
  	where avgCredit > 0 or mag > 0
';
$hostDao->executeQuery($sql);

$sql = '
	insert into '.Constants::DATABASE_NAME.'.pool_stat (name,value,theTime) 
	select \'ACTIVE_MEMBERS\',count(distinct memberId) as howmany,UNIX_TIMESTAMP(NOW()) FROM `view_member_host_project_credit` where avgCredit > 0
';
$hostDao->executeQuery($sql);

for ($poolId = 1; $poolId <= Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS); $poolId++) {
	foreach (array('totalCredit','avgCredit','mag','owed') as $column) {
		$sql = '
			insert into '.Constants::DATABASE_NAME.'.pool_stat (name,value,theTime) 
			select \''.strtoupper($column).'_'.$poolId.'\',sum('.$column.'),UNIX_TIMESTAMP(NOW())
			from '.Constants::DATABASE_NAME.'.member_host_credit
			where '.Constants::DATABASE_NAME.'.member_host_credit.poolId = '.$poolId.'
		';
		$hostDao->executeQuery($sql);
	}
}


/*
for ($startTime = 1486000000-20000; $startTime < time()-86400; $startTime += 86400) {
	$sql = 'select count(distinct memberId) as howmany from grcpool.member_host_stat_mag where (avgCredit > 0 or mag > 0) and thetime > '.$startTime.' and thetime < '.($startTime+86400);
	$result = $hostDao->query($sql);
	$count = $result[0]['howmany'];
	if ($count) {
		echo $count."\n";
		$sql = '
			insert into grcpool.pool_stat (name,value,theTime)
			select \'ACTIVE_MEMBERS\','.$count.','.$startTime.'
		';
		$hostDao->executeQuery($sql);
	}	
}
*/