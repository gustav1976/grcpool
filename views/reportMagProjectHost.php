<?php
$webPage->setPageTitle('Top Magnitudes by Project');

$webPage->append('
	<table class="table table-striped table-hover">
		<tr>
			<th>#</th>
			<th>Researcher</th>
			<th>Project</th>
			<th class="text-right">Avg Credit</th>
			<th class="text-right">Mag</th>
		</tr>
');
$pos = 1;
foreach ($this->view->hosts as $host) {
	if ($host->getMag() > 0) {
		$webPage->append('		
			<tr>
				<td>'.$pos++.'</td>
				<td><a href="/report/researcher/'.$host->getId().'/'.$host->getHostId().'">'.$host->getUsername().'</a></td>
				<td>'.$host->getProjectUrl().'</td>
				<td class="text-right">'.$host->getAvgCredit().'</td>
				<td class="text-right">'.number_format($host->getMag(),2).'</td>
			</tr>
		');
	}
}
$webPage->append('
	</table>	
');