<?php
class GrcPool_Controller_Chart extends GrcPool_Controller {
	public function __construct() {
		parent::__construct();
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
		$poolStatDao = new GrcPool_Pool_Stat_DAO();
		
		$activeMembers = $poolStatDao->getWithName('ACTIVE_MEMBERS');
		$members = $memberDao->fetchAll(array(),array('regTime'=>'asc'));

		$allDates = array();
		$dates = array();
		foreach ($members as $member) {
			if ($member->getRegTime() < strtotime('2017-01-01')) continue;
			$date = strtotime(date('Y-m-d',$member->getRegTime()));
			if (!isset($dates[$date])) {
				$dates[$date] = 0;
				$allDates[$date] = 0;
			}
			$dates[$date]++;
		}
		$memberDates = array();
		foreach ($activeMembers as $member) {
			$date = strtotime(date('Y-m-d',$member->getTheTime()));
			if (!isset($memberDates[$date])) {
				$memberDates[$date] = 0;
			}
			if (!isset($allDates[$date])) {
				$allDates[$date] = 0;
			}
			$memberDates[$date] = $member->getValue();
		}
		ksort($allDates);
		$sums = array();
		$sums['total'] = array(0);
		$sums['active'] = array(0);
		$totalMembers = 0;
		$lastActive = 0;
		$lastTotal = 0;
 		foreach ($allDates as $date => $junk) {
 			//echo $date."\n";
			if (isset($dates[$date])) {
				$lastTotal += $dates[$date];
			}
			if (isset($memberDates[$date])) {
				$lastActive = $memberDates[$date];
			}
			array_push($sums['total'],$lastTotal);
			array_push($sums['active'],$lastActive);
 		}

		require(dirname(__FILE__).'/../classes/SVGGraph/SVGGraph.php');
		$settings = array(
				'back_colour'       => '#fff',    'stroke_colour'      => '#000',
				'back_stroke_width' => 0,         'back_stroke_colour' => '#eee',
				'axis_font'         => 'Georgia', 'axis_font_size'     => 10,
				'pad_right'         => 20,        'pad_left'           => 20,
				'fill_under'        => array(true,true),
				'marker_size'       => 3,
				'marker_type'       => array('circle', 'circle'),
				'marker_colour'     => array('blue', 'red'),
				'label_h' 			=> 'day',
				'label_v'			=> 'members',
				'legend_position'   => 'top left 3 -3',
				'graph_title'		=> 'grcpool.com membership'
		);
		$settings['legend_entries'] = array('Total Members','Active Members');
		$this->view->taskGraph= new SVGGraph($this->args(0,Controller::VALIDATION_NUMBER)??1000,$this->args(1,Controller::VALIDATION_NUMBER)??500,$settings);
		$this->view->taskGraph->auto_fit = true;
		$this->view->taskGraph->colours = array('#ffaaaa','#aaffaa','#aaaaff','#fffa67','#67ffef');
		$this->view->taskGraph->Values($sums);
		$this->view->taskGraph->Render('MultiLineGraph');
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
		foreach ($data as $d) {
			if ($d->getAvgCredit() == 0) {continue;}
			if (!isset($allProjects[$d->getAccountId()])) {
				$allProjects[$d->getAccountId()] = 1;
			}
			$time = strtotime(date('m/d/Y',$d->getTheTime()));
			if (!isset($dataPoints[$time])) {
				$dataPoints[$time] = array();
			}
			if (!isset($dataPoints[$time][$d->getAccountId()])) {
				$dataPoints[$time][$d->getAccountId()]['avgCredit'] = 0;
				$dataPoints[$time][$d->getAccountId()]['count'] = 0;
			}
			$dataPoints[$time][$d->getAccountId()]['count']++;
			$dataPoints[$time][$d->getAccountId()]['avgCredit'] += $d->getAvgCredit();
		}
		ksort($dataPoints);
		$names = array();
		$ids = array();
		if ($dataPoints) {		
			foreach ($accounts as $a) {
				$names[$a->getId()] = $a->getName();
				$ids[$a->getName()] = $a->getId();
			}
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
		} else {
			// setup data for blank graph
			$projData = array('' => array(0 => 0,1 => 0));
			$settings['axis_max_v'] = 1;
		}
		$colors = new Colors();
		$pallette = $colors->getAsArray();
		$colours = array();
		foreach ($projData as $name => $data) {
			if (isset($ids[$name])) {
				array_push($colours,$pallette[$ids[$name]-1]);
			}
		}
		$settings['legend_entries'] = array_keys($projData);
		$this->view->taskGraph= new SVGGraph($this->args(1,Controller::VALIDATION_NUMBER)??1000,$this->args(2,Controller::VALIDATION_NUMBER)??500,$settings);
		if ($colours) {
			$this->view->taskGraph->Colours($colours);
		}
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
		foreach ($data as $d) {
			if ($d->getMag() == 0) {continue;}
			$time = strtotime(date('m/d/Y',$d->getTheTime()));
			if (!isset($dataPoints[$time])) {
				$dataPoints[$time] = array();
				$dataPoints[$time]['mag'] = 0;
			}
			$dataPoints[$time]['mag'] += $d->getMag();
		}
		if ($dataPoints) {
			ksort($dataPoints);
			foreach ($dataPoints as $time => $dp) {
				if (isset($dp['mag'])) {
					$projData['mag'][floor((time()-$time)/86400)] = number_format($dp['mag'],2,'.','');
				} else {
					$projData['mag'][floor((time()-$time)/86400)] = NULL;
				}
			}
		} else {
			$projData = array('' => array(0 => 0,1 => 0));
			$settings['axis_max_v'] = 1;
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
		foreach ($data as $d) {
			if ($d->getMag() == 0) {continue;}
			if (!isset($allProjects[$d->getAccountId()])) {
				$allProjects[$d->getAccountId()] = 1;
			}
			$time = strtotime(date('m/d/Y',$d->getTheTime()));
			if (!isset($dataPoints[$time])) {
				$dataPoints[$time] = array();
			}
			if (!isset($dataPoints[$time][$d->getAccountId()])) {
				$dataPoints[$time][$d->getAccountId()]['mag'] = 0;
				$dataPoints[$time][$d->getAccountId()]['count'] = 0;
			}
			$dataPoints[$time][$d->getAccountId()]['count']++;
			//$dataPoints[$time][$d->getAccountId()]['mag'] = ($dataPoints[$time][$d->getAccountId()]['mag']+$d->getMag())/$dataPoints[$time][$d->getAccountId()]['count'];
			$dataPoints[$time][$d->getAccountId()]['mag'] += $d->getMag();
		}
		$names = array();
		$ids = array();
		foreach ($accounts as $a) {
			$names[$a->getId()] = $a->getName();
			$ids[$a->getName()] = $a->getId();
		}
		foreach ($allProjects as $url=>  $p) {
			$projData[$names[$url]] = array();
		}
		
		if ($dataPoints) {
			ksort($dataPoints);
			foreach ($dataPoints as $time => $dp) {
				foreach ($allProjects as $url=>  $p) {
					if (isset($dp[$url])) {
						$projData[$names[$url]][floor((time()-$time)/86400)] = number_format($dp[$url]['mag'],2,'.','');
					} else {
						$projData[$names[$url]][floor((time()-$time)/86400)] = NULL;
					}
				}
			}
		} else {
			$projData = array('' => array(0 => 0,1 => 0));
			$settings['axis_max_v'] = 1;
		}
		
		$colors = new Colors();
		$pallette = $colors->getAsArray();
		$colours = array();
		foreach ($projData as $name => $data) {
			if (isset($ids[$name])) {
				array_push($colours,$pallette[$ids[$name]-1]);
			}
		}
		$settings['legend_entries'] = array_keys($projData);
		$this->view->taskGraph= new SVGGraph($this->args(1,Controller::VALIDATION_NUMBER)??1000,$this->args(2,Controller::VALIDATION_NUMBER)??500,$settings);
		if ($colours) {
			$this->view->taskGraph->Colours($colours);
		}
		$this->view->taskGraph->auto_fit = true;
		$this->view->taskGraph->Values($projData);
		header('Content-type: image/svg+xml');
		$this->view->taskGraph->Render('MultiLineGraph');
		exit;
	}

}

