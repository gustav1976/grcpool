<?php

$webPage->addBreadcrumb('account','user','/account');

$webPage->appendScript('
	<script>
		function confirmDelete(hostId) {
			if (confirm("Are you sure you want to delete this host and all attached projects? This action can not be undone.")) {
				location.href="/account/hosts/delete/"+hostId;
			}
		}
		$(\'[data-toggle="tooltip"]\').tooltip();	
	</script>
');

$content = '';

if ($this->view->memHosts) {
	$hosts = array();
	foreach ($this->view->memHosts as $host) {
		if (!isset($hosts[$host->getId()])) {
			$hosts[$host->getId()] = array();
		}
		array_push($hosts[$host->getId()],$host);
	}
	
	$content .= '
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th></th>
					<th class="text-center">Pool</th>
					<th class="text-right">RAC</th>
					<th class="text-right">
						Mag
						<a href="#" data-toggle="tooltip" title="MAG = '.Constants::GRC_MAG_MULTIPLIER.' * ( ( HRAC / TRAC ) / W )"><i style="color:black;" class="fa fa-info-circle"></i></a>			
					</th>
					<th class="text-right">
						Daily GRC
						<a href="#" data-toggle="tooltip" title="GRC = MAG * '.$this->view->magUnit.'"><i style="color:black;" class="fa fa-info-circle"></i></a>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr style="background-color:transparent;"><td colspan="5"><div style="margin:10px;"></div></td></tr>
	';
	$hostCount = 0;
	$totalAllMag = 0;
	$totalAllGrc = 0;
	$minCredit = 0;
	$haveProjs = false;
	foreach ($hosts as $hostId => $host) {
		$hostCount++;
		$rows = 0;		
		foreach ($host as $h) {
			foreach ($this->view->hosts as $a) {
				if ($a->getHostId() == $hostId) {
					if ($a->getAvgcredit() > $minCredit) {
						$rows++;
					}
				}
			}
		}
		$hostName = 'unknown';
		if ($host[0]->getCustomName() != '') {
			$hostName = $host[0]->getCustomName();
		} else {
			if ($host[0]->getHostName() != '') {
				$hostName = $host[0]->getHostName();
			}
		}

		$totalMag = 0;
		$totalGrc = 0;
		$projectContent = '';
		$numberOfProjects = 0;
		$pools = array();
		$racTotal = 0;
		foreach ($host as $h) {
			foreach ($this->view->hosts as $a) {
				if ($h->getId() == $a->getHostId() && $a->getAvgCredit() > $minCredit) {
					$numberOfProjects++;
					$haveProjs = true;
					$totalMag += $a->getMag();
					$totalAllMag += $a->getMag();
					$totalGrc += Utils::truncate($a->getMag()*$this->view->magUnit,8);
					$totalAllGrc += Utils::truncate($a->getMag()*$this->view->magUnit,8);
					$pools[$a->getProjectPoolid()] = 1;
					$racTotal += $a->getAvgCredit();
					$magCalc = Constants::GRC_MAG_MULTIPLIER.' * ( ( '.$a->getAvgCredit().' / '.$this->view->accounts[$a->getProjectUrl()]->getRac().' ) / '.$this->view->accounts[$a->getProjectUrl()]->getWhiteListCount().' )';
					$projectContent .= '
						<tr class="accordion'.$hostId.' collapse">
							<td><a style="margin-left:10px;" title="go to your host details and tasks" target="_blank" href="'.$this->view->accounts[$a->getProjectUrl()]->getBaseUrl().'/show_host_detail.php?hostid='.$a->getHostDbid().'">'.$this->view->accounts[$a->getProjectUrl()]->getName().'</a>&nbsp;<small><i class="fa fa-external-link"></i></small></td>
							<td class="text-center">'.$a->getProjectPoolId().'</td>
							<td class="text-right">'.$a->getAvgCredit().'</td>							
							<td class="text-right">
								<a href="#" data-toggle="tooltip" title="'.$magCalc.'">
									'.number_format($a->getMag(),2).'										
								</a>
							</td>
							<td class="text-right">'.(Utils::truncate($a->getMag()*$this->view->magUnit,3)).'</td>
						</tr>
					';
				}
			}
		}
		ksort($pools);
		$content .= '
			<tr>
				<td style="background-color:#f0f0f0">
					<button style="margin-right:7px;" type="button" class="btn btn-default btn-xs" data-toggle="collapse" data-target=".accordion'.$hostId.'">
						<i class="fa fa-chevron-down"></i>
						<span class="badge">'.$numberOfProjects.'</span>
					</button>
					<strong><a title="grcpool.com host details" href="/account/host/'.$hostId.'">'.$hostName.'</a></strong>
					'.(isset($this->view->errorHosts[$hostId])?'
						<a href="#" data-toggle="tooltip" title="This host possibly has an invalid project attached."><i style="color:darkred;" class="fa fa-warning"></i></a>										
					':'').'		
				</td>
				<td class="text-center" style="font-weight:bold;background-color:#f0f0f0">'.(implode(" & ",array_keys($pools))).'</td>
				<td class="text-right" style="font-weight:bold;background-color:#f0f0f0">'.$racTotal.'</td>
				<td class="text-right" style="font-weight:bold;background-color:#f0f0f0">'.$totalMag.'</td>
				<td class="text-right" style="font-weight:bold;background-color:#f0f0f0">'.number_format($totalGrc,3).'</td>
			</tr>
			'.$projectContent.'
			'.($this->view->hasDeleteNotice?'
				<tr class="accordion'.$hostId.' collapse"><td style="" colspan="5" class="text-right">
					<button onclick="confirmDelete('.$hostId.')" type="button" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> delete host</button>
				</td></tr>
			':'').'
					
			<tr style="background-color:transparent;"><td colspan="5"><div style="margin:10px;"></div></td></tr>					
		';
// 		if (true || $rows > 1) {
// 			$content .= '
// 				<tr style="background-color:#f0f0f0;border-top:2px solid #999;">
// 					<td style="background-color:#f0f0f0;"><strong>Host Total</strong></td>
// 					<td style="background-color:#f0f0f0;"></td>
// 					<td style="background-color:#f0f0f0;" class="text-right"></td>				
// 					<td style="background-color:#f0f0f0;" class="text-right"><strong>'.$totalMag.'</strong></td>
// 					<td style="background-color:#f0f0f0;" class="text-right"><strong>'.number_format($totalGrc,8).'</strong></td>
// 				</tr>
// 				<tr style="background-color:transparent;"><td colspan="7"><div style="margin:20px;"></div></td></tr>
// 			';
// 		}				
	}
	if (true || $hostCount > 1) {
		$content .= '
			<tr style="background-color:#ddd;border-top:4px solid #555;">
				<td style="background-color:#ddd;"><strong>Hosts Total</strong></td>
				<td style="background-color:#ddd;"></td>
				<td style="background-color:#ddd;" class="text-right"></td>
				<td style="background-color:#ddd;" class="text-right"><strong>'.$totalAllMag.'</strong></td>
				<td style="background-color:#ddd;" class="text-right"><strong>'.number_format($totalAllGrc,8).'</strong></td>
			</tr>
		';
	}
	$content .= '</tbody></table>
		'.($haveProjs?'':Bootstrap_Callout::info('Please allow at least 24 hours after you have completed tasks for credit to appear. After tasks are completed, the project site needs to validate and update its statistics. The pool checks with projects several times per day to get credit.')).'	
		'.($this->view->hasDeleteNotice?'':Bootstrap_Callout::error('
			<b>Project and Host Deletion is Disabled</b><br/>
			If you delete a project that has average credit and is generating a magnitude, even if you are no longer researching on it, you will not receive any future Gridcoin for that particular project since the pool will be unaware of the host/project.	
			Also you may want to double check your BOINC client to be sure any projects being deleted are detached.<br/><br/>
			<div class=""><a href="/account/hosts/enableDelete" class="btn btn-danger">I understand, enable delete options please...</a></div>
		')).'						
	';
} else {
	$content .= 'You have not attached any hosts to grcpool.com';
}


$panel = new Bootstrap_Panel();
$panel->setContext('info');
$panel->setHeader('Hosts');
$panel->setContent($content);
$webPage->append($panel->render());