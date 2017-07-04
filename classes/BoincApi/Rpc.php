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
	private $rawName = '';
	private $rawDomainName = '';
	private $log;
	private $attachedProjects;

	public function getRawName() {
		$pos1 = strpos($this->rawXml,'<name>');
		$pos2 = strpos($this->rawXml,'</name>');
		$rawName = substr($this->rawXml,$pos1+6,$pos2-$pos1-6);
		if (strlen($rawName > 50)) {
			return 'unknown';
		}
		return preg_replace( '/[^a-z0-9]+/', '-', strtolower( $rawName ) );
	}
	public function getRawDomainName() {
		$pos1 = strpos($this->rawXml,'<domain_name>');
		$pos2 = strpos($this->rawXml,'</domain_name>');
		$rawDomainName = substr($this->rawXml,$pos1+13,$pos2-$pos1-13);
		if (strlen($rawDomainName) > 50) {
			$rawDomainName = substr($rawDomainName,0,50);
		}
		return preg_replace( '/[^a-z0-9]+/', '-', strtolower( $rawDomainName ) );
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
		$this->rawDomainName = $this->getRawDomainName();
		$this->attachedProjects = array();
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
	 	    				file_put_contents('/backup/poolLogs/'.$this->rawName.'.'.time().'.'.$this->rawDomainName.'.error.in.xml',$error."\n\n".'----------------------------'.$xml.'-------------------------------');
	 					}
 					}
 				}
			}
		} catch (Throwable $t) {
			$this->error = 'BOINC may be submitting invalid data. [2]';
			if ($log) {
				file_put_contents('/backup/poolLogs/'.$this->rawName.'.'.time().'.'.$this->rawDomainName.'.error.in.xml',$xml);
			}
		}
		if ($log) {
			file_put_contents('/backup/poolLogs/'.$this->rawName.'.'.time().'.'.$this->rawDomainName.'.in.xml',$xml);
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
				$xmlObj->setHostId((int)$this->xml->opaque->hostId);
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
			if (isset($this->xml->host_info->p_ncpus)) {
				$numberOfCpus = (String)$this->xml->host_info->p_ncpus;
			}
			if (isset($this->xml->host_info->coprocs->coproc_intel_gpu)) {
				$numberOfIntel = (String)$this->xml->host_info->coprocs->coproc_intel_gpu->count;
			}
			if (isset($this->xml->host_info->coprocs->coproc_cuda)) {
				$numberOfCudas = (String)$this->xml->host_info->coprocs->coproc_cuda->count;
			}
			if (isset($this->xml->host_info->coprocs->coproc_ati)) {
				$numberOfAmds = (String)$this->xml->host_info->coprocs->coproc_ati->count;
			}

			$this->host->setNumberOfCpus(htmlspecialchars($numberOfCpus));
			$this->host->setNumberOfCudas(htmlspecialchars($numberOfCudas));
			$this->host->setNumberOfAmds(htmlspecialchars($numberOfAmds));
			$this->host->setNumberOfIntels(htmlspecialchars($numberOfIntel));

			$this->host->setLastContact(time());
			$this->host->setClientVersion(htmlspecialchars((String)$this->xml->client_version));
			$this->host->setCpId(htmlspecialchars((String)$this->xml->host_cpid));
			$this->host->setHostName(htmlspecialchars((String)$this->xml->domain_name));
			$this->host->setMemberId($this->member->getId());
			$this->host->setModel(htmlspecialchars((String)$this->xml->host_info->p_model));
			$this->host->setOsName(htmlspecialchars((String)$this->xml->host_info->os_name));
			$this->host->setOsVersion(htmlspecialchars((String)$this->xml->host_info->os_version));
			$this->host->setProductName(htmlspecialchars((String)$this->xml->host_info->product_name));
			$this->host->setVirtualBoxVersion(htmlspecialchars((String)$this->xml->host_info->virtualbox_version));
			$hostDao->save($this->host);
			
 			foreach ($this->xml->project as $project) {
 				$account = $accountDao->initWithUrl((String)$project->url);
 				$weakKey = '';
 				if ($account != null && $account->getId() != 0) {
 					switch ($this->member->getPoolId()) {
	 					case 1 : $weakKey = $account->getWeakKey();break;
 						case 2 : $weakKey = $account->getWeakKey2();break;
 					}
 				}
 				if ($account != null && $account->getId() != 0 && (String)$project->account_key == $weakKey) { // key indicates if using pool account
	 				$dbid = (String)$project->hostid;
	 				$cpid = (String)$this->xml->host_cpid;
	 				$projectUrl = (String)$project->url;
	 				$obj = $hostProjectDao->getWithMemberIdAndDbidAndProjectUrl($this->member->getId(),$dbid,$projectUrl);
	 				if ($obj == null) {
	 					$obj = $hostProjectDao->getWithMemberIdAndCpidAndProjectUrlAndPoolId($this->member->getId(),$cpid,$projectUrl,$this->member->getPoolId());
	 					if ($obj == null) {
	 						//$obj = new GrcPool_Member_Host_Project_OBJ();
	 						//$obj->setAttached(1);
	 						//$obj->setHostCpid((String)$this->xml->host_cpid);
	 						//$obj->setResourceShare(100);
	 						// I would have liked to add the project if they sere using the weak key here, but could open up security problems if any dbid was accpeted
	 						// so lets restrict projects being added only from account pages
	 						$this->error .= $projectUrl.' is currently not in your account, please login to the pool and add it first.';
	 						if ($echo) echo "CREATED NEW PROJECT FOR HOST\n";
	 						continue;
	 					} else {
	 						if ($echo) echo "FOUND PROJECT WITH CPID\n";
	 					}
	 				} else {
	 					$this->attachedProjects[$projectUrl] = 1;
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
 				} else if ($account != null && $account->getId() != 0) {
 					// attached using a personal account
 					$this->attachedProjects[(String)$project->url] = 0;
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
			file_put_contents('/backup/poolLogs/'.$this->rawName.'.'.time().'.'.$this->rawDomainName.'.error.out.xml',$xml);
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
			<global_preferences>
				<mod_time>0.000000</mod_time>
			</global_preferences>
		';
		if ($this->host) {
			$xml .= '<opaque><hostId>'.$this->host->getId().'</hostId></opaque>';
		}
		if ($this->memberValid) {
			$accountsDao = new GrcPool_Boinc_Account_DAO();
			$hostProjectDao = new GrcPool_Member_Host_Project_DAO();
			$hostProjects = $hostProjectDao->getWithMemberIdAndHostCpid($this->member->getId(),$this->xml->host_cpid);
			foreach ($hostProjects as $hostProject) {
				if ($hostProject->getPoolId() != $this->member->getPoolId()) {continue;} // don't send it because it is on wrong pool
				if ($hostProject->getAttached() == 2) {continue;} // special case, orphaned project
				$account = $accountsDao->initWithUrl($hostProject->getProjectUrl());
				if ($account) {
					$weakKey = '';
					switch ($this->member->getPoolId()) {
						case 1 : $weakKey = $account->getWeakKey();break;
						case 2 : $weakKey = $account->getWeakKey2();break;
					}
					$attachedFlag = isset($this->attachedProjects[$hostProject->getProjectUrl()])?$this->attachedProjects[$hostProject->getProjectUrl()]:null;
					
					if ( // writing this out for legibility
						($hostProject->getAttached() == 0 && $attachedFlag === null) || // pool detach, not in client 
						($hostProject->getAttached() == 0 && $attachedFlag === 0) || // pool detach, in client incorrectly
						($hostProject->getAttached() == 1 && $attachedFlag === 0) // pool attach, in client incorrectly
					) { continue; }

// 					if ($hostProject->getAttached()==0 && !isset($this->attachedProjects[$hostProject->getProjectUrl()])) {
// 						// if project detached in pool but isnt in boinc, skip it, might cause BIONC crashes
// 						continue;
// 					}
					
					
					$acct = new BoincApi_Account();
					$acct->setUrl($hostProject->getProjectUrl());
					$acct->setNo_ati($hostProject->getNoAtiGpu()|null);
					$acct->setNo_cpu($hostProject->getNoCpu()|null);
					$acct->setNo_cuda($hostProject->getNoNvidiaGpu()|null);
					$acct->setNo_intel($hostProject->getNoIntelGpu()|null);
					$acct->setResource_share($hostProject->getResourceShare());
					$acct->setUrl_signature($account->getSignature());
					$acct->setAuthenticator($weakKey);
					if ($hostProject->getAttached()==0) {
						$acct->setDetach(1);
					}
					$xml .= $acct->toXml();
				}
			}
		} else {
			$xml .= '<message>Authorization Failed</message><error>Authorization Failed</error>';			
		}
		$xml .= '</acct_mgr_reply>';
		
		//$xml = file_get_contents(dirname(__FILE__).'/../../test/data/Exikutioner.1493218818.out.xml');
		
		if ($this->log) {
			file_put_contents('/backup/poolLogs/'.$this->rawName.'.'.time().'.'.$this->rawDomainName.'.out.xml',$xml);
		}
		return $xml;
	}
}