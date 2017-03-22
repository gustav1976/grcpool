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
		$result['pending'] = $data['Pending Superblock Height'];
		$result['block'] = $data['Superblock Block Number'];
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
		$interest = $json[1]['CPID Lifetime Interest Paid'];
		return $interest;
	}
	
	public function getTotalBalance() {
		$data = $this->executeDaemon('getinfo');
		$json = json_decode($data,true);
		$balance = $json['balance']+$json['stake'];
		return trim($balance);
	}
	
	public function getMagnitude() {
		$data = GridcoinDaemon::executeDaemon('list mymagnitude');
		$json = json_decode($data,true);
		$mag = $json[1]['Magnitude (Last Superblock)'];
		return trim($mag);
	}
	
	public function getWhitelistedProjects() {
		$data = GridcoinDaemon::executeDaemon('list projects');
		$json = json_decode($data,true);
		$projects = array();
		foreach ($json as $project) {
			if (isset($project['Project']) && $project['Project'] != '') {
				array_push($projects,$project['Project']);
			}
		}
		return $projects;
	}
	
	public function getNumberOfProjects() {
		$data = GridcoinDaemon::executeDaemon('list projects');
		$json = json_decode($data,true);
		$numberOfProjects = 0;
		foreach ($json as $project) {
			if (isset($project['Project']) && $project['Project'] != '') {
				$numberOfProjects++;
			}
		}
		return $numberOfProjects;
	}	
	
	
}
