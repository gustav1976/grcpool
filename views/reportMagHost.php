<?php
$webPage->setPageTitle('Top Mag for Project Host');

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
			$details .= $detail->getModel();
		}
		$webPage->append('		
			<tr>
				<td>'.$pos++.'</td>
				<td>'.$host['username'].'</td>
				<td>'.$details.'</td>
				<td class="text-right">'.$host['magTotal'].'</td>
			</tr>
		');
	}
}
$webPage->append('
	</table>	
');