<?php
$webPage->setPageTitle('Top Magnitudes by Host');

$webPage->append('
	<table class="table table-striped table-hover">
		<tr>
			<th>#</th>
			<th>Researcher</th>
			<th>Computer Model</th>
			<th class="text-right">Mag</th>
		</tr>
');
$pos = 1;
foreach ($this->view->hosts as $host) {
	if ($host['magTotal'] > 0) {
		$details = '';
		if (isset($this->view->hostDetails[$host['hostId']])) {
			$detail = $this->view->hostDetails[$host['hostId']];
			$details .= $detail->getModel().'<br/>cpus - '.$detail->getNumberOfCpus().', 
				cuda - '.$detail->getnumberOfCudas().', 
				amd - '.$detail->getNumberOfAmds();
// 			if (isset($this->view->projects[$host['hostId']])) {
// 				$details .= '<br/>';
// 				foreach ($this->view->projects[$host['hostId']] as $h) {
// 					if ($h->getAttached()) {
// 						$details .= $h->getProjectUrl().' ';
// 					}
// 				}
// 			}
		}
		$webPage->append('		
			<tr>
				<td>'.$pos++.'</td>
				<td><a href="/report/researcher/'.$host['id'].'/'.$host['hostId'].'">'.$host['username'].'</a></td>
				<td>'.$details.'</td>
				<td class="text-right">'.number_format($host['magTotal'],2).'</td>
			</tr>
		');
	}
}
$webPage->append('
	</table>	
');