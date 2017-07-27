<?php
class GrcPool_Controller_Api extends GrcPool_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	
	public function hostNameAction() {
		$hostDao = new GrcPool_Member_Host_DAO();
		$host = $hostDao->initWithKey($this->args(0));
		if ($host->getMemberId() == $this->getUser()->getId()) {
			$data = json_decode(file_get_contents('php://input'),true);
			if (isset($data['customName'])) {
				$host->setCustomName(htmlspecialchars($data['customName']));
				$hostDao->save($host);
				$host = $hostDao->initWithKey($this->args(0));
				$json = array('customName' => $host->getCustomName()!=''?$host->getCustomName():$host->getHostName());
				echo json_encode($json);
			}
		}
		exit;
	}
	
	public function memberEarningsChartAction() {
		require(dirname(__FILE__).'/../classes/SVGGraph/SVGGraph.php');
		$memberId = $this->args(0);
		$dao = new GrcPool_Member_Payout_DAO();
		$lookBack = 180;
		$data = $dao->getWithMemberIdSince($memberId,time()-86400*$lookBack);
		$dataPoints = array();
		$projData = array();
		$settings = array(
				'back_colour'       => '#ffffff',    'stroke_colour'      => '#000',
				'back_stroke_width' => 0,         'back_stroke_colour' => '#eee',
				'axis_font'         => 'Georgia', 'axis_font_size'     => 10,
				'pad_right'         => 20,        'pad_left'           => 20,
				'marker_size'       => 3,
				'label_h' 			=> 'days ago',
				'label_v'			=> 'grc',
				'graph_title'		=> 'earnings per day',
				'force_assoc' 		=> true,
		);
		if (!$data) {
			$projData = array('' => array(0 => 0,1 => 0));
			$settings['axis_max_v'] = 1;
		}
		for ($i = 0; $i <= $lookBack; $i++) {
			$time = strtotime(date('m/d/Y',time()-86400*$i));
			$dataPoints[$time] = 0;
		}
		foreach ($data as $d) {
			$time = strtotime(date('m/d/Y',$d->getTheTime()));
			$dataPoints[$time]+= $d->getAmount();
		}
		ksort($dataPoints);
		$keep = false;
		$runningTotal = 0;
		foreach ($dataPoints as $time => $dp) {
			if ($dp == 0 && !$keep) {continue;}
			$keep = true;
			$projData[floor((time()-$time)/86400)] = number_format($dp+$runningTotal,2,'.','');
			$runningTotal += $dp;
		}
		
		$this->view->taskGraph= new SVGGraph($this->args(1,Controller::VALIDATION_NUMBER)??1000,$this->args(2,Controller::VALIDATION_NUMBER)??500,$settings);
		$this->view->taskGraph->auto_fit = true;
		$this->view->taskGraph->Values($projData);
		header('Content-type: image/svg+xml');
		$this->view->taskGraph->Render('LineGraph');
		exit;
		
	}
	
	public function memberRegistrationAction() {
		$memberDao = new GrcPool_Member_DAO();
		$members = $memberDao->fetchAll(array(),array('regTime'=>'asc'));
		$dates = array();
		foreach ($members as $member) {
			$date = date('Y-m-d',$member->getRegTime());
			if (!isset($dates[$date])) {
				$dates[$date] = 0;
			}
			$dates[$date]++;
		}
		$sums = array();
		$sums[0] = 0;
		foreach ($dates as $date) {
			array_push($sums,$date + $sums[count($sums)-1] );
		}
		require(dirname(__FILE__).'/../classes/SVGGraph/SVGGraph.php');
		$settings = array(
				'back_colour'       => '#fff',    'stroke_colour'      => '#000',
				'back_stroke_width' => 0,         'back_stroke_colour' => '#eee',
				'axis_font'         => 'Georgia', 'axis_font_size'     => 10,
				'pad_right'         => 20,        'pad_left'           => 20,
				'fill_under'        => array(true, false),
				'marker_size'       => 3,
				'marker_type'       => array('circle', 'square'),
				'marker_colour'     => array('blue', 'red'),
				'label_h' 			=> 'day',
				'label_v'			=> 'members',
				'graph_title'		=> 'grcpool.com membership'
		);
		$this->view->taskGraph= new SVGGraph($this->args(0,Controller::VALIDATION_NUMBER)??1000,$this->args(1,Controller::VALIDATION_NUMBER)??500,$settings);
		$this->view->taskGraph->auto_fit = true;
		$this->view->taskGraph->colours = array('#ffaaaa','#aaffaa','#aaaaff','#fffa67','#67ffef');
		$this->view->taskGraph->Values($sums);
		$this->view->taskGraph->Render('LineGraph');
		exit;
	}
	
	public function memberRacChartAction() {
		require(dirname(__FILE__).'/../classes/SVGGraph/SVGGraph.php');
		
		$memberId = $this->args(0);
		$dao = new GrcPool_Member_Host_Stat_Mag_DAO();
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$accounts = $accountDao->fetchAll(array(),array('name'=>'asc'));
		$data = $dao->getWithMemberId($memberId,time()-86400*45);
		$allProjects = array();
		$dataPoints = array();
		$projData = array();
		$settings = array(
				'back_colour'       => '#fff',    'stroke_colour'      => '#000',
				'back_stroke_width' => 0,         'back_stroke_colour' => '#eee',
				'axis_font'         => 'Georgia', 'axis_font_size'     => 10,
				'pad_right'         => 20,        'pad_left'           => 20,
				'marker_size'       => 3,
				'label_h' 			=> 'days ago',
				'label_v'			=> 'rac',
				'graph_title'		=> 'rac per project',
				'legend_position'   => 'top left 3 -3',
				'force_assoc' 		=> true,
				
		);
		if (!$data) {
			// setup data for blank graph
			$projData = array('' => array(0 => 0,1 => 0));
			$settings['axis_max_v'] = 1;
		}
		
		
		foreach ($data as $d) {
			if ($d->getAvgCredit() == 0) {continue;}
			if (!isset($allProjects[$d->getProjectUrl()])) {
				$allProjects[$d->getProjectUrl()] = 1;
			}
			$time = strtotime(date('m/d/Y',$d->getTheTime()));
			if (!isset($dataPoints[$time])) {
				$dataPoints[$time] = array();
			}
			if (!isset($dataPoints[$time][$d->getProjectUrl()])) {
				$dataPoints[$time][$d->getProjectUrl()]['avgCredit'] = 0;
				$dataPoints[$time][$d->getProjectUrl()]['count'] = 0;
			}
			$dataPoints[$time][$d->getProjectUrl()]['count']++;
			$dataPoints[$time][$d->getProjectUrl()]['avgCredit'] += $d->getAvgCredit();
		}
		ksort($dataPoints);
		$names = array();
		$ids = array();
		foreach ($accounts as $a) {
			$names[$a->getUrl()] = $a->getName();
			$ids[$a->getName()] = $a->getId();
		}
		if ($dataPoints) {
			foreach ($allProjects as $url=>  $p) {
				$projData[$names[$url]] = array();
			}
			foreach ($dataPoints as $time => $dp) {
				//array_push($labels,floor((time()-$time)/86400));
				foreach ($allProjects as $url=>  $p) {
					if (isset($dp[$url])) {
						$projData[$names[$url]][floor((time()-$time)/86400)] = floor($dp[$url]['avgCredit']);
					} else {
						$projData[$names[$url]][floor((time()-$time)/86400)] = NULL;
					}
				}
			}
		}
		$colors = new Colors();
		$pallette = $colors->getAsArray();
		$colours = array();
		foreach ($projData as $name => $data) {
			array_push($colours,$pallette[$ids[$name]-1]);
		}
		$settings['legend_entries'] = array_keys($projData);
		$this->view->taskGraph= new SVGGraph($this->args(1,Controller::VALIDATION_NUMBER)??1000,$this->args(2,Controller::VALIDATION_NUMBER)??500,$settings);
		$this->view->taskGraph->Colours($colours);
		$this->view->taskGraph->auto_fit = true;
		$this->view->taskGraph->Values($projData);
		header('Content-type: image/svg+xml');
		$this->view->taskGraph->Render('MultiLineGraph');
		exit;
	}
	
	
	public function memberTotalMagChartAction() {
		require(dirname(__FILE__).'/../classes/SVGGraph/SVGGraph.php');
		$memberId = $this->args(0);
		$dao = new GrcPool_Member_Host_Stat_Mag_DAO();
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$accounts = $accountDao->fetchAll(array(),array('name'=>'asc'));
		$data = $dao->getWithMemberId($memberId,time()-86400*45);
		$dataPoints = array();
		$projData = array();
		$settings = array(
				'back_colour'       => '#ffffff',    'stroke_colour'      => '#000',
				'back_stroke_width' => 0,         'back_stroke_colour' => '#eee',
				'axis_font'         => 'Georgia', 'axis_font_size'     => 10,
				'pad_right'         => 20,        'pad_left'           => 20,
				'marker_size'       => 3,
				'label_h' 			=> 'days ago',
				'label_v'			=> 'magnitude',
				'graph_title'		=> 'total magnitude',
				'force_assoc' 		=> true,
		);
		if (!$data) {
			$projData = array('' => array(0 => 0,1 => 0));
			$settings['axis_max_v'] = 1;
		}
		foreach ($data as $d) {
			if ($d->getMag() == 0) {continue;}
			$time = strtotime(date('m/d/Y',$d->getTheTime()));
			if (!isset($dataPoints[$time])) {
				$dataPoints[$time] = array();
				$dataPoints[$time]['mag'] = 0;
			}
			$dataPoints[$time]['mag'] += $d->getMag();
		}
		ksort($dataPoints);
		foreach ($dataPoints as $time => $dp) {
			if (isset($dp['mag'])) {
				$projData['mag'][floor((time()-$time)/86400)] = number_format($dp['mag'],2);
			} else {
				$projData['mag'][floor((time()-$time)/86400)] = NULL;
			}
		}
		$this->view->taskGraph= new SVGGraph($this->args(1,Controller::VALIDATION_NUMBER)??1000,$this->args(2,Controller::VALIDATION_NUMBER)??500,$settings);
		$this->view->taskGraph->auto_fit = true;
		$this->view->taskGraph->Values($projData);
		header('Content-type: image/svg+xml');
		$this->view->taskGraph->Render('LineGraph');
		exit;
	}
	
	public function memberMagChartAction() {
		require(dirname(__FILE__).'/../classes/SVGGraph/SVGGraph.php');
		$memberId = $this->args(0);
		$dao = new GrcPool_Member_Host_Stat_Mag_DAO();
		$accountDao = new GrcPool_Boinc_Account_DAO();
		$accounts = $accountDao->fetchAll(array(),array('name'=>'asc'));
		$data = $dao->getWithMemberId($memberId,time()-86400*45);
		$allProjects = array();		
		$dataPoints = array();
		$projData = array();
		$settings = array(
				'back_colour'       => '#ffffff',    'stroke_colour'      => '#000',
				'back_stroke_width' => 0,         'back_stroke_colour' => '#eee',
				'axis_font'         => 'Georgia', 'axis_font_size'     => 10,
				'pad_right'         => 20,        'pad_left'           => 20,
				'marker_size'       => 3,
				'label_h' 			=> 'days ago',
				'label_v'			=> 'magnitude',
				'graph_title'		=> 'magnitude per project',
				'legend_position'   => 'top left 3 -3',
				'force_assoc' 		=> true,
		);
		if (!$data) {
			$projData = array('' => array(0 => 0,1 => 0));
			$settings['axis_max_v'] = 1;
		}
		foreach ($data as $d) {
			if ($d->getMag() == 0) {continue;}
			if (!isset($allProjects[$d->getProjectUrl()])) {
				$allProjects[$d->getProjectUrl()] = 1;
			}
			$time = strtotime(date('m/d/Y',$d->getTheTime()));
			if (!isset($dataPoints[$time])) {
				$dataPoints[$time] = array();
			}
			if (!isset($dataPoints[$time][$d->getProjectUrl()])) {
				$dataPoints[$time][$d->getProjectUrl()]['mag'] = 0;
				$dataPoints[$time][$d->getProjectUrl()]['count'] = 0;
			}
			$dataPoints[$time][$d->getProjectUrl()]['count']++;
			//$dataPoints[$time][$d->getProjectUrl()]['mag'] = ($dataPoints[$time][$d->getProjectUrl()]['mag']+$d->getMag())/$dataPoints[$time][$d->getProjectUrl()]['count'];
			$dataPoints[$time][$d->getProjectUrl()]['mag'] += $d->getMag();
		}
		ksort($dataPoints);
		$names = array();
		$ids = array();
		foreach ($accounts as $a) {
			$names[$a->getUrl()] = $a->getName();
			$ids[$a->getName()] = $a->getId();
		}
		foreach ($allProjects as $url=>  $p) {
			$projData[$names[$url]] = array();
		}
		foreach ($dataPoints as $time => $dp) {
			foreach ($allProjects as $url=>  $p) {
				if (isset($dp[$url])) {
					$projData[$names[$url]][floor((time()-$time)/86400)] = number_format($dp[$url]['mag'],2);
				} else {
					$projData[$names[$url]][floor((time()-$time)/86400)] = NULL;
				}
			}
		}
		$colors = new Colors();
		$pallette = $colors->getAsArray();
		$colours = array();
		foreach ($projData as $name => $data) {
			array_push($colours,$pallette[$ids[$name]-1]);
		}
		$settings['legend_entries'] = array_keys($projData);
		$this->view->taskGraph= new SVGGraph($this->args(1,Controller::VALIDATION_NUMBER)??1000,$this->args(2,Controller::VALIDATION_NUMBER)??500,$settings);
		$this->view->taskGraph->Colours($colours);
		$this->view->taskGraph->auto_fit = true;
		$this->view->taskGraph->Values($projData);
		header('Content-type: image/svg+xml');
		$this->view->taskGraph->Render('MultiLineGraph');
		exit;
	}
	
	public function isMemberNameAvailableAction() {
		header('Content-Type: application/json');
		$name = $this->get('name');
		$result = array();
		if ($name) {
			$dao = new GrcPool_Member_DAO();
			$obj = $dao->initWIthUsername($name);
			$result['result'] = $obj == null;
		} else {
			$result['result'] = false;
		}
		echo json_encode($result);
		exit;
	}

	public function blockHeightAction() {
		$daemon = GrcPool_Utils::getDaemonForEnvironment();
		echo $daemon->getBlockHeight();
		exit;
	}
	
	public function superBlockAgeAction() {
		header('Content-Type: application/json');
		$cache = new Cache();
		echo $cache->get(Constants::CACHE_SUPERBLOCK_DATA);		
		exit;
	}
	
	public function projectsAction() {
		$acctDao = new GrcPool_Boinc_Account_DAO();
		$accounts = $acctDao->fetchAll(array(),array('name'=>'asc'));
		$apiObj = new GrcPool_ApiJson();
		$arr = array();
		foreach ($accounts as $account) {
			array_push($arr,[
				'name' => $account->getName(),
				'url' => $account->getUrl(),
				'key' => $account->getWeakKey(),
				'whiteList' => $account->getWhiteList(),
				'rac' => $account->getRac(),
				'attachable' => $account->getAttachable(),
				'minRac' => $account->getMinRac(),
			]);	
		}
		$apiObj->setData($arr);
		echo $apiObj->toJson();
		exit;
	}

}

