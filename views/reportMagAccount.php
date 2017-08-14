<?php
$webPage->appendTitle('Top Mag for Account');
$panel = new Bootstrap_Panel();
$panel->setHeader('Top Mag for Account');
$panelContent = '';
$panelContent .= '
	<table class="table table-striped table-hover">
		<tr>
			<th>#</th>
			<th>Researcher</th>
			<th class="text-right">Mag</th>
		</tr>
';
$pos = 1;
foreach ($this->view->hosts as $host) {
	if ($host['magTotal'] > 0) {
		$panelContent .= '
			<tr>
				<td>'.$pos++.'</td>
				<td><a href="/report/researcher/'.$host['memberId'].'">'.$host['username'].'</a></td>
				<td class="text-right">'.$host['magTotal'].'</td>
			</tr>
		';
	}
}
$panelContent .= '
	</table>	
';
$panel->setContent($panelContent);
$webPage->append($panel->render());