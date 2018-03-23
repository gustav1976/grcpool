<?php
class GrcPool_Json {
	public static function getHostSettings(GrcPool_Member_OBJ $user,GrcPool_Member_Host_OBJ $host) {
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$keyDao = new GrcPool_Boinc_Account_Key_DAO();
		$hostProjectsDao = new GrcPool_Member_Host_Project_DAO();
		
		$accounts = $accountDao->fetchAll(array(),array('name'=>'asc'));
		$keys = $keyDao->getForPoolId($user->getPoolId());
		$accountKeys = array();
		foreach ($keys as $key) {
			$accountKeys[$key->getAccountId()]['weak'] = $key->getWeak();
			$accountKeys[$key->getAccountId()]['attachable'] = $key->getAttachable();
		}
		
		$hostProjects = $hostProjectsDao->getWithMemberIdAndHostId($user->getId(),$host->getId());
		$projects = array();
		foreach ($accounts as $idx => $account) {
			$project = array();
			$project['id'] = $account->getId();
			$project['name'] = $account->getName();
			if (isset($accountKeys[$account->getId()]) && $accountKeys[$account->getId()]['weak'] != '' && ($user->getId() == Constants::ADMIN_USER_ID || ($account->getWhiteList() && $account->getAttachable() && $accountKeys[$account->getId()]['attachable']))) {
				$project['attachable'] = true;
			} else {
				$project['attachable'] = false;
			}
			$project['inClient'] = false;
			foreach ($hostProjects as $proj) {
				if ($account->getId() == $proj->getAccountId() && $proj->getPoolId() == $user->getPoolId() && $proj->getAttached() != 2) {
					$project['projectId'] = $proj->getId(); 
					$project['inClient'] = true;
					$project['attached'] = $proj->getAttached()==1?true:false;
					$project['noCpu'] = $proj->getNoCpu()?true:false;
					$project['noNvidiaGpu'] = $proj->getNoNvidiaGpu()?true:false;
					$project['noAtiGpu'] = $proj->getNoAtiGpu()?true:false;
					$project['noIntelGpu'] = $proj->getNoIntelGpu()?true:false;
					$project['resourceShare'] = $proj->getResourceShare();
					$project['hostDbid'] = $proj->getHostDbId();
					$project['message'] = '';
					if ($proj->getHostDbId()==0) {
						$project['warning'] = '<a href="/help/topics/1">This project may not be attached correctly or needs to be synchronized from your client.</a>';
					} else {
						if ($account->getGrcName() != Constants::GRCNAME_WORLD_COMMUNITY_GRID) {
 							$project['message'] = '<a target="_blank" href="'.$account->getBaseUrl().'show_host_detail.php?hostid='.$proj->getHostDbid().'">host &amp; task details</a> <i class="fa fa-external-link"></i>';
						}
					}
				}
			}
			array_push($projects,$project);
		}
		return json_encode($projects);		
	}
}