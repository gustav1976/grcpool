<?php
class GridcoinDaemon {
	
	private $testnet = false;
	private $datadir = '';
	private $path = '';

	private function executeDaemon($cmd) {
		$command = $this->path.' '.($this->testnet?'-testnet':'').' -datadir='.$this->datadir.' '.$cmd;
		$stdout = $stderr = $status = null;
		$descriptorspec = array(
				1 => array('pipe', 'w'),
				2 => array('pipe', 'w')
		);
		$process = proc_open($command, $descriptorspec, $pipes);
		if (is_resource($process)) {
			$stdout = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			$stderr = stream_get_contents($pipes[2]);
			// TODO log error
			fclose($pipes[2]);
			$status = proc_close($process);
		}
		return $stdout;
	}
	
	private function safe_json_encode($value){
		$encoded = json_encode($value);
		switch (json_last_error()) {
			case JSON_ERROR_NONE:
				return $encoded;
			case JSON_ERROR_DEPTH:
				throw new Exception();
			case JSON_ERROR_STATE_MISMATCH:
				throw new Exception();
			case JSON_ERROR_CTRL_CHAR:
				throw new Exception();
			case JSON_ERROR_SYNTAX:
				throw new Exception();
			case JSON_ERROR_UTF8:
				$clean = $this->utf8ize($value);
				return $this->safe_json_encode($clean);
			default:
				throw new Exception();
		}
	}
	
	private function utf8ize($mixed) {
		if (is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$mixed[$key] = $this->utf8ize($value);
			}
		} else if (is_string ($mixed)) {
			return utf8_encode($mixed);
		}
		return $mixed;
	}
	
	public function setDataDir($s) {
		$this->datadir = $s;
	}
	public function setPath($s) {
		$this->path = $s;
	}
	public function isTestNet() {
		return $this->testnet;
	}
	public function setTestnet($b) {
		$this->testnet = $b;
	}
	
	public function getBlockHeight() {
		return $this->executeDaemon('getblockcount');
	}
	
	public function getRsa() {
		$data = $this->executeDaemon('list rsa');
		$json = json_decode($data,true);
		return $json;
	}
	
	public function isAddress($address) {
		$data = $this->executeDaemon('validateaddress '.$address);
		$data = json_decode($data,true);
		if (isset($data['isvalid'])) {
			return $data['isvalid'];
		} else {
			return false;
		}
	}
	
	public function send($toAddress,$amount) {
		$data = $this->executeDaemon('sendtoaddress '.$toAddress.' '.$amount);
		return trim($data);
	}
	
	public function getSuperBlockAge() {
		$data = $this->executeDaemon('execute superblockage');
		$data = json_decode($data,true);
		$data = $data[1];
		$result = array();
		$result['timestamp'] = str_replace('-','/',$data['Superblock Timestamp']);
		$result['age'] = $data['Superblock Age'];
		$result['pending'] = floor($data['Pending Superblock Height']);
		$result['block'] = floor($data['Superblock Block Number']);
		$result['ageText'] = Utils::getTimeAgo(time()-$data['Superblock Age']);
		return $result;
	}

	public function getAvailableBalance() {
		$data = $this->executeDaemon('getbalance');
		return trim($data);
	}
	
	public function getTotalInterest() {
		$data = $this->executeDaemon('list rsa');
		$json = json_decode($data,true);
		$interest = isset($json[1]['CPID Lifetime Interest Paid'])?$json[1]['CPID Lifetime Interest Paid']:0;
		return $interest;
	}
	
	public function getTotalBalance() {
		$data = $this->executeDaemon('getinfo');
		$json = json_decode($data,true);
		$balance = $json['balance']+$json['stake'];
		return trim($balance);
	}
	
	public function getVersion() {
		$data = $this->executeDaemon('getinfo');
		$json = json_decode($data,true);
		$version = $json['version'];
		return trim($version);
	}
	
//	public function getStakingInfo() {
//		$data = $this->executeDaemon('getstakinginfo');
//		$json = json_decode($data,true);
//		return $json;
//	}
	
	public function getMagnitude() {
		$data = $this->executeDaemon('list mymagnitude');
		$json = json_decode($data,true);
		$mag = 0;
		if (isset($json[1]) && isset($json[1]['Magnitude (Last Superblock)'])) {
			$mag = $json[1]['Magnitude (Last Superblock)'];
		}
		return trim($mag);
	}
	
	public function sendMany($many) {
		//sendmany <fromaccount> {address:amount,...} [minconf=1] [comment]
		//sendmany "test" "{\"mrAgidiJ5TwxGsQNnADRxY6cyWRnm4x2Xd\":25.01,\"mhoAu3qvv81BdZWeaxZH2qmPFc6Et8LD4r\":35.000001}"
		$string = '';
		foreach ($many as $m) {
			 
		}
	}
	
	public function getWhitelistedProjects($block = '') {
		if ($block == '') {
			$data = $this->getSuperBlockAge();
			$block = $data['block'];
		}
		if ($block == '') return;
		$blockHash = $this->executeDaemon('getblockhash '.$block);
		if ($blockHash == '') return;
		$blockData = json_decode($this->executeDaemon('getblock '.$blockHash),true);
		if ($blockData['IsSuperBlock'] == '') return;
		$txHash = $blockData['tx'][0];
		$txJson = $this->executeDaemon('gettransaction '.$txHash);
		$txJson = $this->utf8ize($txJson);
		$tx = json_decode($txJson,true);
		$hashBoinc = $tx['hashboinc'];
		$thisMatchArray = null;
		preg_match_all("/<(?'Key'[a-zA-Z].*?)>(?'Value'.*?)<\/.*?>/m", $hashBoinc, $thisMatch);
		for($c=0;$c < count($thisMatch['Key']);$c++) {
			if($thisMatch['Key'][$c] == "|") {
				$thisMatchArray[$thisMatch['Key'][$c]][] = addslashes($thisMatch['Value'][$c]);
			} else {
				$thisMatchArray[$thisMatch['Key'][$c]] = addslashes($thisMatch['Value'][$c]);
			}
		}
		preg_match_all("/(?'Project'.*?),(?'TotalRAC'.*?);/m", $thisMatchArray['AVERAGES'], $thisMatchProjects);
		$projects = $thisMatchProjects['Project'];
		array_pop($projects);
		return $projects;
		

	}
	
	
	
}
