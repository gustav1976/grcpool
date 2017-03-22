<?php

$webPage->setPageTitle('Hosts');

if ($this->view->memHosts) {
	$hosts = array();
	foreach ($this->view->memHosts as $host) {
		if (!isset($hosts[$host->getId()])) {
			$hosts[$host->getId()] = array();
		}
		array_push($hosts[$host->getId()],$host);
	}
	
	$webPage->append('
		<div class="rowpad"><i>Host Mag Calculation Definition: MAG = '.Constants::GRC_MAG_MULTIPLIER.' * ( ( HRAC / TRAC ) / W )</i></div>
		<div class="rowpad"><i>Est. Daily GRC Calculation Definition: GRC = MAG * '.$this->view->magUnit.'</i></div>
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Host Name</th>
					<th>Project</th>
					<th>Mag Calculation</th>
					<th class="text-right">Mag</th>
					<th class="text-right">Est. Daily GRC</th>
				</tr>
			</thead>
			<tbody>
	');
	$hostCount = 0;
	$totalAllMag = 0;
	$totalAllGrc = 0;
	foreach ($hosts as $hostId => $host) {
		$hostCount++;
		$rows = 0;		
		foreach ($host as $h) {
			foreach ($this->view->hosts as $a) {
				if ($a->getHostId() == $hostId) {
					if ($a->getMag() > 0) {
						$rows++;
					}
				}
			}
		}
		$webPage->append('
			<tr>
				<td rowspan="'.($rows+1).'"><a href="/account/host/'.$hostId.'">'.($host[0]->getHostName()!=''?$host[0]->getHostName():'unknown').'</a></td>
			</tr>
		');
		$totalMag = 0;
		$totalGrc = 0;
		foreach ($host as $h) {
			foreach ($this->view->hosts as $a) {
				if ($h->getId() == $a->getHostId() && $a->getMag() > 0) {
					$totalMag += $a->getMag();
					$totalAllMag += $a->getMag();
					$totalGrc += Utils::truncate($a->getMag()*$this->view->magUnit,8);
					$totalAllGrc += Utils::truncate($a->getMag()*$this->view->magUnit,8);
					$magCalc = Constants::GRC_MAG_MULTIPLIER.' * ( ( '.$a->getAvgCredit().' / '.$this->view->accounts[$a->getProjectUrl()]->getRac().' ) / '.$this->view->accounts[$a->getProjectUrl()]->getWhiteListCount().' )';
					$webPage->append('
						<tr>
							<td>'.$a->getProjectUrl().'</td>
							<td><small>'.$magCalc.'</small></td>
							<td class="text-right">'.$a->getMag().'</td>
							<td class="text-right">'.(Utils::truncate($a->getMag()*$this->view->magUnit,8)).'</td>
						</tr>
					');
				}
			}
		}
		if ($rows > 1) {
			$webPage->append('
				<tr style="background-color:#BBBBBB;">
					<td><strong>Host Total</strong></td>
					<td></td>
					<td class="text-right"></td>				
					<td class="text-right"><strong>'.$totalMag.'</strong></td>
					<td class="text-right"><strong>'.number_format($totalGrc,8).'</strong></td>
				</tr>
			');
		}				
	}
	if ($hostCount > 1) {
		$webPage->append('
			<tr style="background-color:#BBBBBB;">
				<td><strong>Hosts Total</strong></td>
				<td></td>
				<td class="text-right"></td>
				<td class="text-right"><strong>'.$totalAllMag.'</strong></td>
				<td class="text-right"><strong>'.number_format($totalAllGrc,8).'</strong></td>
			</tr>
		');
	}
	$webPage->append('</tbody></table>');
} else {
	$webPage->append('You have not attached any hosts to grcpool.com');
}
