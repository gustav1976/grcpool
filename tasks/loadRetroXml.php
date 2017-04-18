<?php
exit;
require_once(dirname(__FILE__).'/../bootstrap.php');

$d = dir('/backup/poolLogs');

$files = array();

while (false !== ($entry = $d->read())) {
	if (strstr($entry,'.in.xml')) {
		$parts = explode(".",$entry);
		if (!isset($files[$parts[0]])) {
			$xml = simplexml_load_string(file_get_contents('/backup/poolLogs/'.$entry));
			if ($xml->opaque->hostId) {
				//$files[$parts[0]] = $entry;
				array_push($files,$entry);
			}
		}
	}
}

$hostDao = new GrcPool_Member_Host_DAO();
$memberDao = new GrcPool_Member_DAO();
$xmlDao = new GrcPool_Member_Host_Xml_DAO();

foreach ($files as $file) {
	echo $file."\n";
	echo "~~~~~~~~~~~~~~~~~~~~\n";
	$xml = simplexml_load_string(file_get_contents('/backup/poolLogs/'.$file));
	
	$memberName = (String)$xml->name;
	if ($memberName != 'bryhardt-pi-01') {
		continue;
	}
	$member = $memberDao->initWithUsername($memberName);
	
	$hostId = (String)$xml->opaque->hostId;
	$host = $hostDao->initWithKey($hostId);
	if ($member && $host->getMemberId() == $member->getId()) {

		$numberOfCpus = 0;
		$numberOfCudas = 0;
		$numberOfAmds = 0;
		$numberOfIntel = 0;
		$numberOfCpus = (String)$xml->host_info->p_ncpus;
		$coprocs = $xml->host_info->coprocs;
		//foreach ($coprocs->children() as $name => $child) {
		//	echo $name."\n";
		//}
		if ($coprocs->coproc_intel_gpu) {
			$numberOfIntel = (String)$coprocs->coproc_intel_gpu->count;
		}
		if ($coprocs->coproc_cuda) {
			$numberOfCudas = (String)$coprocs->coproc_cuda->count;
		}
		if ($coprocs->coproc_ati) {
			$numberOfAmds = (String)$coprocs->coproc_ati->count;
		}
		$host->setNumberOfCpus($numberOfCpus);
		$host->setNumberOfCudas($numberOfCudas);
		$host->setNumberOfAmds($numberOfAmds);
		$host->setNumberOfIntels($numberOfIntel);
		$hostDao->save($host);

		$xmlObj = $xmlDao->getWithMemberIdAndHostId($member->getId(),$host->getId());
		if ($xmlObj == null) {
			$xmlObj = new GrcPool_Member_Host_Xml_OBJ();
		}
		$xmlObj->setMemberId($member->getId());
		$xmlObj->setHostId($host->getId());
		$xmlObj->setThetime(time());
		$xmlObj->setXml(gzcompress(file_get_contents('/backup/poolLogs/'.$file)));
		$xmlDao->save($xmlObj);
		
	} else {
		echo "UNKNOWN HOST";
	}
	
}
