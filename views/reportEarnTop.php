<?php
$webPage->setPageTitle('Top Earners');

$webPage->append('
	<table class="table table-striped table-hover">
		<tr>
			<th>#</th>
			<th>Researcher</th>
			<th class="text-right">Amount</th>
		</tr>
');
$pos = 1;
foreach ($this->view->members as $host) {
	$webPage->append('		
		<tr>
			<td>'.$pos++.'</td>
			<td><a href="/report/researcher/'.$host['id'].'">'.$host['username'].'</a></td>
			<td class="text-right">'.$host['totalAmount'].'</td>
		</tr>
	');
}
$webPage->append('
	</table>	
');