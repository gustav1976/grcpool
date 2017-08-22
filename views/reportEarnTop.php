<?php
$webPage->appendTitle('Top Earners');
$panel = new Bootstrap_Panel();
$panel->setHeader('Top Earners');
$panelContent = '';
$panelContent .= '
	<table class="table table-striped table-hover">
		<tr>
			<th>#</th>
			<th>Researcher</th>
			<th class="text-right">Total GRC</th>
			<th class="text-right">%</th>
		</tr>
';
$pos = 1;
foreach ($this->view->members as $host) {
	$panelContent .= '
		<tr>
			<td>'.$pos++.'</td>
			<td><a href="/report/researcher/'.$host['memberId'].'">'.$host['username'].'</a></td>
			<td class="text-right">'.$host['totalAmount'].'</td>
			<td class="text-right">'.number_format(100*$host['totalAmount']/$this->view->totalGrc,2).'</td>
		</tr>
	';
}
$panelContent .= '
	</table>	
';
$panel->setContent($panelContent);
$webPage->append($panel->render());