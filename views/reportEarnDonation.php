<?php
$webPage->setPageTitle('Top Donators');

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
	if ($host['totalAmount'] > 0) {
		$webPage->append('		
			<tr>
				<td>'.$pos++.'</td>
				<td>'.$host['username'].'</td>
				<td class="text-right">'.$host['totalAmount'].'</td>
			</tr>
		');
	}
}
$webPage->append('
	</table>	
');