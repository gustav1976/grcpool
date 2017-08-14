<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../bootstrap.php');

set_time_limit(300);

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ WCGTASKS START ".date("Y.m.d H.i.s")."\n";

$settingsDao = new GrcPool_Settings_DAO();
$taskDao = new GrcPool_Wcg_Tasks_DAO();

for ($poolId = 1; $poolId <= Constants::NUMBER_OF_POOLS; $poolId++) {
	echo '############ POOL '.$poolId."\n";
	$poolName = 'grcpool.com'.($poolId==1?'':'-'.$poolId);
	$poolCode = $settingsDao->getValueWithName(Constants::SETTINGS_WCG_CODE.($poolId==1?'':$poolId));

	$modTime = $taskDao->getMaxModTimeWithPoolId($poolId);
	echo "MOD TIME: ".$modTime."\n";
	
	$haveMoreRecords = true;
	$offset = 0;
	$resultsAvailable = 0;
	$limit = 250;
	$saved = 0;
	while ($haveMoreRecords) {
		$url = 'https://www.worldcommunitygrid.org/api/members/'.$poolName.'/results?code='.$poolCode.'&Limit='.$limit.'&Offset='.$offset.'&ModTime='.$modTime;
		echo $url."\n";
		$body = file_get_contents($url);
		$json = json_decode($body,true);
		if (!$json) {
			echo "NO JSON :";print_r($json);
			break;
		}
		if ($offset ==0) {
			$resultsAvailable = $json['ResultsStatus']['ResultsAvailable'];
			echo 'Records: '.$resultsAvailable."\n";
		}
		if ($offset + $limit > $resultsAvailable) {
			$haveMoreRecords = false;
		}
		foreach ($json['ResultsStatus']['Results'] as $result) {
 			$obj = $taskDao->fetch(array(
 				$taskDao->where('workUnitId',$result['WorkunitId']),
 				$taskDao->where('deviceId',$result['DeviceId']),
 			));
			if (!$obj) {
				$obj = new GrcPool_Wcg_Tasks_OBJ();
			}
			$obj->setPoolId($poolId);
			$obj->setAppName($result['AppName']);
			$obj->setClaimedCredit($result['ClaimedCredit']);
			$obj->setCpuTime($result['CpuTime']);
			$obj->setElapsedTime($result['ElapsedTime']);
			$obj->setGrantedCredit($result['GrantedCredit']);
			$obj->setDeviceId($result['DeviceId']);
			$obj->setModTime($result['ModTime']);
			$obj->setWorkUnitId($result['WorkunitId']);
			$obj->setResultId($result['ResultId']);
			$obj->setName($result['Name']);
			$obj->setOutcome($result['Outcome']);
			$obj->setReportDeadline(strtotime($result['ReportDeadline']));
			$obj->setSentTime(strtotime($result['SentTime']));
			$obj->setServerState($result['ServerState']);
			$obj->setValidateState($result['ValidateState']);
			$obj->setFileDeleteState($result['FileDeleteState']);
			$taskDao->save($obj);
			$saved++;
		}
		$offset+=$limit;
	}
	echo 'Saved: '.$saved;
	
}


echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ WCGTASKS END ".date("Y.m.d H.i.s")."\n";
