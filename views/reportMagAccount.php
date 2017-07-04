<?php
$webPage->setPageTitle('Top Mag for Project Host');

$webPage->append('
	<table class="table table-striped table-hover">
		<tr>
			<th>#</th>
			<th>Researcher</th>
			<th class="text-right">Mag</th>
		</tr>
');
$pos = 1;
foreach ($this->view->hosts as $host) {
	if ($host['magTotal'] > 0) {
		$webPage->append('		
			<tr>
				<td>'.$pos++.'</td>
				<td><a href="/report/researcher/'.$host['id'].'">'.$host['username'].'</a></td>
				<td class="text-right">'.$host['magTotal'].'</td>
			</tr>
		');
	}
}
$webPage->append('
	</table>	
');