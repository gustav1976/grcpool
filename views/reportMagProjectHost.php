<?php
$webPage->appendTitle('Top Mags for Project');
$panel = new Bootstrap_Panel();
$panel->setHeader('Top Mags for Project');
$panelContent = '';
$panelContent .= '
	<table class="table table-striped table-hover">
		<tr>
			<th>#</th>
			<th>Researcher</th>
			<th class="text-center">Pool</th>
			<th>Project</th>
			<th class="text-right">Avg Credit</th>
			<th class="text-right">Mag</th>
		</tr>
';
$pos = 1;
foreach ($this->view->hosts as $host) {
	if ($host->getMag() > 0) {
		$panelContent .= '
			<tr>
				<td>'.$pos++.'</td>
				<td><a href="/report/researcher/'.$host->getMemberId().'/'.$host->getHostId().'">'.$host->getUsername().'</a></td>
				<td class="text-center">'.$host->getPoolId().'</td>
				<td>'.$this->view->accounts[$host->getAccountId()]->getName().'</td>
				<td class="text-right">'.$host->getAvgCredit().'</td>
				<td class="text-right">'.number_format($host->getMag(),2).'</td>
			</tr>
		';
	}
}
$panelContent .= '
	</table>	
';
$panel->setContent($panelContent);
$webPage->append($panel->render());
