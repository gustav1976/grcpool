<?php
// this is a little bit speghetti, needs refactoring
class BoincApi_Rpc {

	private $message;
	private $memberValid = false;
	private $xml;
	private $rawXml;
	private $member;
	private $host;
	private $error;
	private $rawName;
	private $log;

	public function getRawName() {
		$pos1 = strpos($this->rawXml,'<name>');
		$pos2 = strpos($this->rawXml,'</name>');
		$rawName = substr($this->rawXml,$pos1+6,$pos2-$pos1-6);
		if (strlen($rawName > 50)) {
			return 'unknown';
		}
		return $rawName;
	}
		
	public function getName() {
		if ($this->xml) {
			return (String)$this->xml->name;
		}
	}

	public function __construct($xml,$log = false) {
		$this->rawXml = $xml;
		$this->log = $log;
		$this->error = '';
		$this->rawName = $this->getRawName();
		try {
			libxml_use_internal_errors(true);	
			$this->xml = simplexml_load_string($xml);
			if ($this->xml === false) {
				// lets try UTF8 Escaping, although this could be a problem for real data
				// $utf8Xml = utf8_encode($xml);
				// lets try and remove global preferences since it seems to be a big culprit
				$newXml = '';
				$pos = strpos($xml,'</working_global_preferences>');
				$pos = strpos($xml,'<global_preferences>',$pos);
				$endPos = strpos($xml,'</global_preferences>',$pos);
				$newXml = substr($xml,0,$pos);
				$newXml .= substr($xml,$endPos+21);
 				$this->xml = simplexml_load_string($newXml);
 				if ($this->xml === false) {
 					$newXml = utf8_encode($xml);
 					$this->xml = simplexml_load_string($newXml);
 					if ($this->xml === false) {
	 					$error = '';
	 					foreach (libxml_get_errors() as $err) {
	 						$error .= $err->message."\n";
	 					}
	 					$this->error = 'BOINC may be submitting invalid data. [1]';
	 					if ($log) {
	 	    				file_put_contents('/backup/poolLogs/'.$this->rawName.'.'.time().'.error.in.xml',$error."\n\n".'----------------------------'.$xml.'-------------------------------');
	 					}
 					}
 				}
			}
		} catch (Throwable $t) {
			$this->error = 'BOINC may be submitting invalid data. [2]';
			if ($log) {
				file_put_contents('/backup/poolLogs/'.$this->rawName.'.'.time().'.error.in.xml',$xml);
			}
		}
		if ($log) {
			file_put_contents('/backup/poolLogs/'.$this->rawName.'.'.time().'.in.xml',$xml);
		}
	}
	
	public function process($echo = false) {
		if ($echo) echo "\n~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\nPROCESS START\n";
		if ($this->error != '') {
			return;
		}
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$dao = new GrcPool_Member_DAO();
		$hostProjectDao = new GrcPool_Member_Host_Project_DAO();
		$this->member = $dao->initWithUsername((String)$this->xml->name);
		$this->memberValid = $this->member==null?false:$this->member->getPasswordHash()==(String)$this->xml->password_hash;
		
		if ($this->memberValid) {
			
			// log last XML request in Database
			if ((String)$this->xml->opaque->hostId) {
				// cannot trust host actually in log
				$xmlDao = new GrcPool_Member_Host_Xml_DAO();
				$xmlObj = $xmlDao->getWithMemberIdAndHostId($this->member->getId(),(String)$this->xml->opaque->hostId);
				if ($xmlObj == null) {
					$xmlObj = new GrcPool_Member_Host_Xml_OBJ();
				}
				$xmlObj->setMemberId($this->member->getId());
				$xmlObj->setHostId((String)$this->xml->opaque->hostId);
				$xmlObj->setThetime(time());
				$xmlObj->setXml(gzcompress($this->rawXml));
				$xmlDao->save($xmlObj);
				if ($echo) echo "LOGGED XML IN DATABASE\n";
			}
			
			$hostDao = new GrcPool_Member_Host_DAO();

			$this->host = null;
			// try to match host with hostId
			if ((String)$this->xml->opaque->hostId) {
				$testHost = $hostDao->initWithKey((String)$this->xml->opaque->hostId);
				if ($testHost != null && $testHost->getMemberId() == $this->member->getId()) {
					if ($echo) echo "FOUND HOST WITH OPAQUE HOSTID\n";
					$this->host = $testHost;
				} else {
					if ($echo) echo "UNABLE TO MATCH HOST ID\n";
				}
			} else {
				if ($echo) echo "NO HOST ID\n";
			}

			// try to match host with project dbid
			if ($this->host == null) {
				if ($this->xml->project->hostid) {
					$projectDao = new GrcPool_Member_Host_Project_DAO();
					$testProj = $projectDao->getWithMemberIdAndDbidAndProjectUrl($this->member->getId(),(String)$this->xml->project->hostid,(String)$this->xml->project->url);
					if ($testProj != null) {
						if ($echo) echo "FOUND HOST WITH PROJECT DBID AND URL\n";
						$this->host = $hostDao->initWithKey($testProj->getHostId());
					}
				} else {
					if ($echo) echo "NO HOST DBID\n";
				}
			}
			
			// try to match host with CPID
			if ($this->host == null) {
				$this->host = $hostDao->initWithMemberIdAndCpId($this->member->getId(),(String)$this->xml->host_cpid);
				// lets check previous cpid
				if ($this->host == null && $this->xml->previous_host_cpid) {
					if ($echo) echo "LETS TRY THE PREVIOUS CPID\n";
					$this->host = $hostDao->initWithMemberIdAndCpId($this->member->getId(),(String)$this->xml->previous_host_cpid);
				} else {
					if ($echo) echo "HAVE HOST WITH CPID\n";
				}
			}
			
			if ($this->host== null) {
				if ($echo) echo "NO HOST CREATE NEW ONE\n";
				$this->host = new GrcPool_Member_Host_OBJ();
				$this->host->setFirstContact(time());
			}
			
			$numberOfCpus = 0;
			$numberOfCudas = 0;
			$numberOfAmds = 0;
			$numberOfIntel = 0;
			if ($this->xml->host_info->p_ncpus) {
				$numberOfCpus = (String)$this->xml->host_info->p_ncpus;
			}
			if ($this->xml->host_info->coprocs->coproc_intel_gpu) {
				$numberOfIntel = (String)$this->xml->host_info->coprocs->coproc_intel_gpu->count;
			}
			if ($this->xml->host_info->coprocs->coproc_cuda) {
				$numberOfCudas = (String)$this->xml->host_info->coprocs->coproc_cuda->count;
			}
			if ($this->xml->host_info->coprocs->coproc_ati) {
				$numberOfAmds = (String)$this->xml->host_info->coprocs->coproc_ati->count;
			}

			$this->host->setNumberOfCpus($numberOfCpus);
			$this->host->setNumberOfCudas($numberOfCudas);
			$this->host->setNumberOfAmds($numberOfAmds);
			$this->host->setNumberOfIntels($numberOfIntel);

			$this->host->setLastContact(time());
			$this->host->setClientVersion((String)$this->xml->client_version);
			$this->host->setCpId(((String)$this->xml->host_cpid));
			$this->host->setHostName((String)$this->xml->domain_name);
			$this->host->setMemberId($this->member->getId());
			$this->host->setModel((String)$this->xml->host_info->p_model);
			$this->host->setOsName((String)$this->xml->host_info->os_name);
			$this->host->setOsVersion((String)$this->xml->host_info->os_version);
			$this->host->setProductName((String)$this->xml->host_info->product_name);
			$this->host->setVirtualBoxVersion((String)$this->xml->host_info->virtualbox_version);
			$hostDao->save($this->host);
			
 			foreach ($this->xml->project as $project) {
 				$account = $accountDao->initWithUrl((String)$project->url);
 				if ($account != null && $account->getId() != 0 && (String)$project->account_key == $account->getWeakKey()) { // key indicates if using pool account
	 				$dbid = (String)$project->hostid;
	 				$cpid = (String)$this->xml->host_cpid;
	 				$projectUrl = (String)$project->url;
	 				$obj = $hostProjectDao->getWithMemberIdAndDbidAndProjectUrl($this->member->getId(),$dbid,$projectUrl);
	 				if ($obj == null) {
	 					$obj = $hostProjectDao->getWithMemberIdAndCpidAndProjectUrl($this->member->getId(),$cpid,$projectUrl);
	 					if ($obj == null) {
	 						//$obj = new GrcPool_Member_Host_Project_OBJ();
	 						//$obj->setAttached(1);
	 						//$obj->setHostCpid((String)$this->xml->host_cpid);
	 						//$obj->setResourceShare(100);
	 						// I would have liked to add the project if they sere using the weak key here, but could open up security problems if any dbid was accpeted
	 						// so lets restrict projects being added only from account pages
	 						$this->error .= $projectUrl.' is currently not in your account, please login to the pool and add it first.';
	 						continue;
	 						if ($echo) echo "CREATED NEW PROJECT FOR HOST\n";
	 					} else {
	 						if ($echo) echo "FOUND PROJECT WITH CPID\n";
	 					}
	 				} else {
	 					if ($echo) echo "FOUND PROJECT WITH DBID\n";
	 				}
	 				if ($obj->getHostDbid() != $dbid) {
	 					// dbid changing, so lets validate it is not duplicate
	 					$testObj = $hostProjectDao->getWithHostDbIdAndProjectUrl($dbid, $projectUrl);
	 					if (count($testObj)) {
	 						$this->error .= $projectUrl.' appears to already be in the pool. Please remove the prior project before trying to add with this host.';
	 						continue;
	 					}
	 				}
	 				$obj->setHostDbid((String)$project->hostid);
	 				//$obj->setHostId($this->host->getId());
	 				//$obj->setMemberId($this->member->getId());
	 				//$obj->setProjectUrl((String)$project->url);
	 				$hostProjectDao->save($obj);
 				}
 			}
		}
	}
	
	private function memberHasProject($url) {
		foreach ($this->xml->project as $project) {
			if ((String)$project->url == $url) {
				return true;
			}
		}
		return false;
	}
	
	private function getErrorXml($msg) {
		$config = new BoincApi_ProjectConfig();
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml .= '<acct_mgr_reply>
			<name>'.$config->getName().'</name>
			<signing_key>'.URL_SIGNING_KEY.'</signing_key>
			<global_preferences></global_preferences>
			<message>'.$msg.'</message><error>'.$msg.'</error>
			</acct_mgr_reply>';
		if ($this->log) {
			file_put_contents('/backup/poolLogs/'.$this->rawName.'.'.time().'.error.out.xml',$xml);
		}
		return $xml;
	}
	
	public function getResponseXml() {
		
		if ($this->error != '') {
			return $this->getErrorXml($this->error);
		}
		
		$config = new BoincApi_ProjectConfig();
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml .= '<acct_mgr_reply>
			<name>'.$config->getName().'</name>
			<signing_key>'.URL_SIGNING_KEY.'</signing_key>
			<global_preferences></global_preferences>
		';
		if ($this->host) {
			$xml .= '<opaque><hostId>'.$this->host->getId().'</hostId></opaque>';
		}
		if ($this->memberValid) {
			$accountsDao = new GrcPool_Boinc_Account_DAO();
			$hostProjectDao = new GrcPool_Member_Host_Project_DAO();
			$hostProjects = $hostProjectDao->getWithMemberIdAndHostCpid($this->member->getId(),$this->xml->host_cpid);
			foreach ($hostProjects as $hostProject) {
				$account = $accountsDao->initWithUrl($hostProject->getProjectUrl());
				$acct = new BoincApi_Account();
				$acct->setUrl($hostProject->getProjectUrl());
				$acct->setNo_ati($hostProject->getNoAtiGpu()|null);
				$acct->setNo_cpu($hostProject->getNoCpu()|null);
				$acct->setNo_cuda($hostProject->getNoNvidiaGpu()|null);
				$acct->setNo_intel($hostProject->getNoIntelGpu()|null);
				$acct->setResource_share($hostProject->getResourceShare());
				$acct->setUrl_signature($account->getSignature());
				$acct->setAuthenticator($account->getWeakKey());
				if ($hostProject->getAttached()==0) {
					$acct->setDetach(1);
				}
				$xml .= $acct->toXml();
			}
		} else {
			$xml .= '<message>Authorization Failed</message><error>Authorization Failed</error>';			
		}
		$xml .= '</acct_mgr_reply>';
		if ($this->log) {
			file_put_contents('/backup/poolLogs/'.$this->rawName.'.'.time().'.out.xml',$xml);
		}
		return $xml;
	}
}