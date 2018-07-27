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
	private $attachedProjectsUrl;

	public function getRawName() {
		$pos1 = strpos($this->rawXml,'<name>');
		$pos2 = strpos($this->rawXml,'</name>');
		$rawName = substr($this->rawXml,$pos1+6,$pos2-$pos1-6);
		if (strlen($rawName) > 50) {
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
		$this->attachedProjectsUrl = array();
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
	 						$this->logIt($error."\n\n".'----------------------------'.$xml.'-------------------------------','error.in',true);
	 					}
 					}
 				}
			}
		} catch (Throwable $t) {
			$this->error = 'BOINC may be submitting invalid data. [2]';
			if ($log) {
				$this->logIt($xml,'error.in');
			}
		}
		if ($log) {
			$this->logIt($xml,'in');
		}
	}
	
	private function logIt($message,$req = 'in') {
		try {
			if (trim($this->rawName) == '') {
				return;
			}
			$subDir = substr($this->rawName,0,1);
			if (!is_dir(Constants::BOINC_XML_LOG_DIR.'/'.$subDir)) {
				mkdir(Constants::BOINC_XML_LOG_DIR.'/'.$subDir);
			}
			$dir = 	Constants::BOINC_XML_LOG_DIR.'/'.$subDir.'/'.$this->rawName.'.'.time().'.'.$this->rawDomainName.'.'.$req.'.xml';
			file_put_contents($dir,$message);
		} catch (Throwable $t) {
			error_log($t);
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
		$urlDao = new GrcPool_Boinc_Account_Url_DAO();
		$creditDao = new GrcPool_Member_Host_Credit_DAO();
		$keyDao = new GrcPool_Boinc_Account_Key_DAO();
		if (trim((String)$this->xml->name) != '') {
			$this->member = $dao->initWithUsername((String)$this->xml->name);
		}
		$this->memberValid = $this->member==null?false:$this->member->getPasswordHash()==(String)$this->xml->password_hash;
		
		if ($this->memberValid) {
			
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
					$urlObj = $urlDao->initWithUrl((String)$this->xml->project->url);
					if ($urlObj) {
						$testProj = $projectDao->getWithMemberIdAndDbidAndAccountId($this->member->getId(),(String)$this->xml->project->hostid,$urlObj->getAccountId());
						if ($testProj != null) {
							$this->host = $hostDao->initWithKey($testProj->getHostId());
						}
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
 				$urlObj = $urlDao->initWithUrl((String)$project->url);
 				$account = null;
 				if ($urlObj) {
 					$account = $accountDao->initWithKey($urlObj->getAccountId());
 				}
 				$weakKey = '';
 				if ($account) {
 					$keyObj = $keyDao->getWithAccountAndPool($account->getId(), $this->member->getPoolId());
 					if ($keyObj) {
 						$weakKey = $keyObj->getWeak();
 					}
 				}
 				if ($account != null && $account->getId() != 0 && (String)$project->account_key != "" && (String)$project->account_key == $weakKey) { // key indicates if using pool account
	 				$dbid = (String)$project->hostid;
	 				$cpid = (String)$this->xml->host_cpid;
	 				$obj = $hostProjectDao->getWithMemberIdAndDbidAndAccountId($this->member->getId(),$dbid,$account->getId());
	 				if ($obj == null) {
	 					$obj = $hostProjectDao->getWithMemberIdAndCpidAndAccountIdAndPoolId($this->member->getId(),$cpid,$account->getId(),$this->member->getPoolId());
	 					if ($obj == null) {
	 						//$obj = new GrcPool_Member_Host_Project_OBJ();
	 						//$obj->setAttached(1);
	 						//$obj->setHostCpid((String)$this->xml->host_cpid);
	 						//$obj->setResourceShare(100);
	 						// I would have liked to add the project if they sere using the weak key here, but could open up security problems if any dbid was accpeted
	 						// so lets restrict projects being added only from account pages
	 						$this->error .= $account->getName().' is not in your pool account, but was found in your client.';
	 						continue;
	 					} else {
	 						if ($dbid == 0) {
	 							$this->error .= $account->getName().' does not have a valid ID from the project site. Please try updating the project in your BOINC client.';
	 							continue;
	 						}
	 						if ($obj->getHostDbid() != 0) {
	 							$testDbid = $hostProjectDao->getWithHostDbIdAndAccountId($dbid,$account->getId());
	 							if ($testDbid && count($testDbid) > 0) {
	 								$this->error .= $account->getName().'  appears to already have a host in the pool. Usually this is a MAC address problem. You may need to contact support '.Constants::ADMIN_EMAIL_ADDRESS.' for help.';
	 							} else {
	 								$this->error .= $account->getName().'  You may have a new ID from the project. You can try deleting the project from the pool and adding it back in. Contact support '.Constants::ADMIN_EMAIL_ADDRESS.' for more help.';
	 							}
	 							continue;	
	 						}
	 					}
	 				} else {
	 					$this->attachedProjects[$account->getId()] = 1;
	 					$this->attachedProjectsUrl[$account->getId()] = $urlObj;
	 				}
	 				if ($obj->getHostDbid() != $dbid) {
	 					// dbid changing, so lets validate it is not duplicate
	 					$testObj = $hostProjectDao->getWithHostDbIdAndAccountId($dbid, $account->getId());
	 					if (count($testObj)) {
	 						$this->error .= $account->getName().' host already exists in the pool. You may need to contact support '.Constants::ADMIN_EMAIL_ADDRESS.' for help.';
	 						continue;
	 					}
	 					$blackListDao = new GrcPool_Boinc_Host_Blacklist_DAO();
	 					$blackList = $blackListDao->initWithAccountIdAndDbid($account->getId(),$dbid);
	 					if ($blackList) {
	 						// this will cause the project dbid to stay zero, so warning message will persist on site
	 						$blackList->setBlockMemberId($this->member->getId());
	 						$blackListDao->save($blackList);
	 						$this->error .= $account->getName().' host has been blacklisted, please contact '.Constants::ADMIN_EMAIL_ADDRESS.' for support.';
	 						continue;
	 					}
	 					// if changing a non zero hostdbid, the prevoius dbid should be blacklisted
	 					if ($obj->getHostDbid() != 0) {
	 						// lets black list this id
	 						$creditObj = $creditDao->initWithAccountIdAndDbid($account->getId(),$obj->getHostDbid());
	 						$blackListObj = new GrcPool_Boinc_Host_Blacklist_OBJ();
	 						$blackListObj->setAccountId($account->getId());
	 						$blackListObj->setHostDbid($obj->getHostDbid());
	 						$blackListObj->setMemberId($this->member->getId());
	 						$blackListObj->setThetime(time());
	 						if ($creditObj) {
	 							$blackListObj->setOwed($creditObj->getOwed());
	 						}
	 						$blackListDao->save($blackListObj);
	 					}
	 				}
	 				$obj->setHostDbid((String)$project->hostid);
	 				//$obj->setHostId($this->host->getId());
	 				//$obj->setMemberId($this->member->getId());
	 				//$obj->setProjectUrl((String)$project->url);
	 				$hostProjectDao->save($obj);
 				} else if ($account != null && $account->getId() != 0) {
 					// attached using a personal account
 					$this->attachedProjects[$account->getId()] = 0;
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
			<signing_key>'.Constants::URL_SIGNING_KEY.'</signing_key>
			<global_preferences></global_preferences>
			<message>'.$msg.'</message><error>'.$msg.'</error>
			</acct_mgr_reply>';
		if ($this->log) {
			$this->logIt($xml,'error.out');
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
			<signing_key>'.Constants::URL_SIGNING_KEY.'</signing_key>
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
			$keyDao = new GrcPool_Boinc_Account_Key_DAO();
			$urlDao = new GrcPool_Boinc_Account_Url_DAO();
			$hostProjects = $hostProjectDao->getWithMemberIdAndHostCpid($this->member->getId(),$this->xml->host_cpid);
			foreach ($hostProjects as $hostProject) {
				if ($hostProject->getPoolId() != $this->member->getPoolId()) {continue;} // don't send it because it is on wrong pool
				if ($hostProject->getAttached() == 2) {continue;} // special case, orphaned project
				$account = $accountsDao->initWithKey($hostProject->getAccountId());
				if ($account) {
					$weakKey = '';
					$key = $keyDao->getWithAccountAndPool($account->getId(),$this->member->getPoolId());
					if ($key) {
						$weakKey = $key->getWeak();
					}
					$attachedFlag = isset($this->attachedProjects[$hostProject->getAccountId()])?$this->attachedProjects[$hostProject->getAccountId()]:null;
					
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
					
					$urlObj = null;
					if (isset($this->attachedProjectsUrl[$account->getId()])) {
						$urlObj = $this->attachedProjectsUrl[$account->getId()]; // use what is already in client
					} else {
						$urlObj = $urlDao->initWithKey($account->getUrlId());
					}
					$acct->setUrl($urlObj->getUrl());
					$acct->setNo_ati($hostProject->getNoAtiGpu()|null);
					$acct->setNo_cpu($hostProject->getNoCpu()|null);
					$acct->setNo_cuda($hostProject->getNoNvidiaGpu()|null);
					$acct->setNo_intel($hostProject->getNoIntelGpu()|null);
					$acct->setResource_share($hostProject->getResourceShare());
					$acct->setUrl_signature($urlObj->getSignature());
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
		if ($this->log) {
			$this->logIt($xml,'out');
		}
		return $xml;
	}
}