<?php
$webPage->appendTitle('Top Donators');
$panel = new Bootstrap_Panel();
$panel->setHeader('Top Donators');
$panelContent = '';
$panelContent .= '
	<table class="table table-striped table-hover">
		<tr>
			<th>#</th>
			<th>Researcher</th>
			<th class="text-right">Amount</th>
		</tr>
';
$pos = 1;
foreach ($this->view->members as $host) {
	if ($host['totalAmount'] > 0) {
		$panelContent .= '
			<tr>
				<td>'.$pos++.'</td>
				<td><a href="/report/researcher/'.$host['memberId'].'">'.$host['username'].'</a></td>
				<td class="text-right">'.$host['totalAmount'].'</td>
			</tr>
		';
	}
}
$panelContent .= '
	</table>	
';
$panel->setContent($panelContent);
$webPage->append($panel->render());