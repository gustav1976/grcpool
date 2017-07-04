<?php
$webPage->setPageTitle('Researcher - '.$this->view->member->getUsername());

if ($this->view->hosts) {
	$webPage->append('
		<table class="table table-striped table-hover table-condensed">
			<tr><th>Host</th><th class="text-right">Mag</th></tr>
	');
	$magGrandTotal = 0;
	foreach ($this->view->hosts as $host) {
		$magTotal = 0;
		foreach ($this->view->credits as $credit) {
			if ($credit->getHostId() == $host->getId()) {
				$magTotal += $credit->getMag();
			}
		}
		$magGrandTotal += $magTotal;
		$webPage->append('
			<tr>
				<td>
					<a href="/report/researcher/'.$host->getMemberId().'/'.$host->getId().'">'.$host->getOsName().' '.$host->getOsVersion().' '.$host->getProductName().'</a>
				</td>
				<td>
					CPUS: '.$host->getNumberOfCpus().',
					Intel GPU: '.$host->getNumberOfIntels().',
					AMD: '.$host->getNumberOfAmds().',
					NVIDIA: '.$host->getNumberOfCudas().'
				</td>
				<td class="text-right">
					'.$magTotal.'
				</td>
			</tr>
		');
	}
	$webPage->append('
			<tr>
				<td></td><td></td><td class="text-right"><strong>'.$magGrandTotal.'</strong></td></tr>
		</table>
	');
			
}

if ($this->view->host) {
	$webPage->append('
		<table class="table table-striped table-hover table-condensed">
			<tr><td>BOINC Version</td><td>'.$this->view->host->getClientVersion().'</td></tr>
			<tr><td>Model</td><td>'.$this->view->host->getModel().'</td></tr>
			<tr><td>OS</td><td>'.$this->view->host->getOsName().' '.$this->view->host->getOsVersion().' '.$this->view->host->getProductName().'</td></tr>
			<tr><td>Capability</td><td>
				CPUS: '.$this->view->host->getNumberOfCpus().',
				Intel GPU: '.$this->view->host->getNumberOfIntels().',
				AMD: '.$this->view->host->getNumberOfAmds().',
				NVIDIA: '.$this->view->host->getNumberOfCudas().'
			</td></tr>
		</table>		
		<br/>		
	');
	$webPage->append('
	<table class="table table-striped table-hover table-condensed">
		<tr>
			<th>Project</th>
			<th class="text-center">Pool</th>
			<th class="text-right">Mag</th>
		</tr>
');
	
	$magTotal = 0;
	foreach ($this->view->credits as $credit) {
		$magTotal += $credit->getMag();
		$webPage->append('
		<tr>
			<td>'.$this->view->accounts[$credit->getProjectUrl()]->getName().'</td>
			<td class="text-center">'.$credit->getProjectPoolId().'</td>
			<td class="text-right">'.$credit->getMag().'</td>
		</tr>
	');
	}
	
	$webPage->append('
		<tr>
			<td></td>
			<td></td>
			<td class="text-right"><strong>'.$magTotal.'</strong></td>
		</tr>
	</table>
');
}



