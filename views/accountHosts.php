<?php

$webPage->addBreadcrumb('account','user','/account');

$webPage->appendHead('
	<script>
		function confirmDelete(hostId) {
			if (confirm("Are you sure you want to delete this host and all attached projects? This action can not be undone.")) {
				location.href="/account/hosts/delete/"+hostId;
			}
		}
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

		<div class="rowpad"><i>Host Mag Calculation Definition: MAG = '.Constants::GRC_MAG_MULTIPLIER.' * ( ( HRAC / TRAC ) / W )</i></div>
		<div class="rowpad"><i>Est. Daily GRC Calculation Definition: GRC = MAG * '.$this->view->magUnit.'</i></div>
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Host Name</th>
					<th>Project</th>
					<th>Mag Calculation</th>
					<th class="text-right">RAC</th>
					<th class="text-right">Mag</th>
					<th class="text-right">Est. Daily GRC</th>
				</tr>
			</thead>
			<tbody>
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
		$content .= '
			<tr>
				<td rowspan="'.($rows+1).'">
					'.($this->view->hasDeleteNotice?'
						<button onclick="confirmDelete('.$hostId.')" type="button" class="btn btn-danger btn-xs">X</button>&nbsp;&nbsp;
					':'').'
					<a href="/account/host/'.$hostId.'">'.$hostName.'</a>
				</td>
			</tr>
		';
		$totalMag = 0;
		$totalGrc = 0;
		foreach ($host as $h) {
			foreach ($this->view->hosts as $a) {
				if ($h->getId() == $a->getHostId() && $a->getAvgCredit() > $minCredit) {
					$haveProjs = true;
					$totalMag += $a->getMag();
					$totalAllMag += $a->getMag();
					$totalGrc += Utils::truncate($a->getMag()*$this->view->magUnit,8);
					$totalAllGrc += Utils::truncate($a->getMag()*$this->view->magUnit,8);
					$magCalc = Constants::GRC_MAG_MULTIPLIER.' * ( ( '.$a->getAvgCredit().' / '.$this->view->accounts[$a->getProjectUrl()]->getRac().' ) / '.$this->view->accounts[$a->getProjectUrl()]->getWhiteListCount().' )';
					$content .= '
						<tr>
							<td>'.$a->getProjectUrl().'</td>
							<td><small>'.$magCalc.'</small></td>
							<td class="text-right">'.$a->getAvgCredit().'</td>							
							<td class="text-right">'.$a->getMag().'</td>
							<td class="text-right">'.(Utils::truncate($a->getMag()*$this->view->magUnit,3)).'</td>
						</tr>
					';
				}
			}
		}
		if ($rows > 1) {
			$content .= '
				<tr style="background-color:#BBBBBB;">
					<td><strong>Host Total</strong></td>
					<td></td><td></td>
					<td class="text-right"></td>				
					<td class="text-right"><strong>'.$totalMag.'</strong></td>
					<td class="text-right"><strong>'.number_format($totalGrc,8).'</strong></td>
				</tr>
			';
		}				
	}
	if ($hostCount > 1) {
		$content .= '
			<tr style="background-color:#BBBBBB;">
				<td><strong>Hosts Total</strong></td>
				<td></td><td></td>
				<td class="text-right"></td>
				<td class="text-right"><strong>'.$totalAllMag.'</strong></td>
				<td class="text-right"><strong>'.number_format($totalAllGrc,8).'</strong></td>
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