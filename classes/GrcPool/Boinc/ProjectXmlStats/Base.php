<?php
class GrcPool_Boinc_ProjectXmlStats_Base {
	
	private $account = null;
	private $feedPath = null;
	private $tableXml = null;
	private $basePath = null;
	
	public function __construct(GrcPool_Boinc_Account_OBJ $account) {
		$this->account = $account;
		$this->basePath = dirname(__FILE__).'/../../../../tasks/feeds/';
		$this->feedPath = $this->basePath.preg_replace( '/[^a-z0-9]+/', '-', strtolower($this->account->getGrcName())).'_';
		if (file_exists($this->feedPath.'tables.xml')) {
			$this->tableXml = simplexml_load_file($this->feedPath.'tables.xml');
		}
	}
	
	private function getTeamData() {
		$data = '';
		if (file_exists($this->feedPath.'team.gz')) {
			$sfp = gzopen($this->feedPath.'team.gz', "rb");
			while ($string = gzread($sfp, 4096)) {
				$data .= $string;
			}
			gzclose($sfp);
		}
		return $data;
	}
	
	public function getNumberOfUsers() {
		return (int)$this->tableXml->nusers_total ?? 0;
	}
	
	public function getNumberOfTeams() {
		return (int)$this->tableXml->nteams_total ?? 0;
	}
	
	public function getNumberOfHosts() {
		return (int)$this->tableXml->nhosts_total ?? 0;
	}
	
	public function getTotalCredit() {
		return (float)$this->tableXml->total_credit ?? 0;
	}
	
	public function test() {
			$sfp = gzopen($this->feedPath.'user.gz', "rb");
			while ($string = gzread($sfp, 4096)) {
				$data .= $string;
			}
			gzclose($sfp);
			echo $data;
		
	}
	
	public function getTeamAvgCredit() {
		$xml = simplexml_load_string($this->getTeamData());
		$result = 0;
		if ($xml) {
			$teamXml = $xml->xpath('//team[id='.$this->account->getTeamId().']')[0];
			$result = (float)$teamXml->expavg_credit;
		}
		return $result;
	}
		
	public function getTeamUsers($accountId,$teamId) {
		$objects = array();
		if (file_exists($this->feedPath.'user.gz')) {
			$data = '';
			$tempFile = $this->basePath.'temp.xml';
			unlink($tempFile);

			$buffer_size = 4096;
			$out_file_name = $tempFile;
			$file = gzopen($this->feedPath.'user.gz', 'rb');
			$out_file = fopen($out_file_name, 'wb');
			while (!gzeof($file)) {
				fwrite($out_file, gzread($file, $buffer_size));
			}
			fclose($out_file);
			gzclose($file);
			
			$xml = simplexml_load_file($tempFile);
			foreach ($xml->user as $user) {
				if ((String)$user->teamid == $teamId) {
					$obj = new GrcPool_Boinc_Account_User_OBJ();
					$obj->setAvgCredit((float)$user->expavg_credit);
					$obj->setCpid((String)$user->cpid);
					$obj->setName(htmlspecialchars((String)$user->name));
					$obj->setAccountId($accountId);
					$obj->setTotalCredit((float)$user->total_credit);
					array_push($objects,$obj);
				}
			}
			unlink($tempFile);
		}
		return $objects;
	}
	
	public function getTeamTotalCredit() {
		$xml = simplexml_load_string($this->getTeamData());
		$result = 0;
		if ($xml) {
			$teamXml = $xml->xpath('//team[id='.$this->account->getTeamId().']')[0];
			$reuslt = (float)$teamXml->total_credit;
		}
		return $result;
	}
	
	public function getBadges() {
		$badges = array();
		$data = '';
		if (file_exists($this->feedPath.'badgeTeam.gz')) {
			$sfp = gzopen($this->feedPath.'badgeTeam.gz', "rb");
			while ($string = gzread($sfp, 4096)) {
				$data .= $string;
			}
			gzclose($sfp);
			$xml = simplexml_load_string($data);
			foreach ($xml->xpath('//badge_team[team_id='.$this->account->getTeamId().']') as $item) {
				$tableBadges = $this->tableXml->xpath('//badges/badge[id='.$item->badge_id.']')[0];
				//print_r($tableBadges);
				$tableBadge = $tableBadges[0];
				array_push($badges,
					array(
						'badgeId' => (String)$item->badge_id,
						'title' => (String)$tableBadge->title,
						'image' => (String)$tableBadge->image_url,
					)	
				);
			}
		}
		return json_encode($badges);
	}
	
}