<?php
$webPage->appendTitle('Top Mags for Host');
$panel = new Bootstrap_Panel();
$panel->setHeader('Top Mags for Host');
$panelContent = '';
$panelContent .= '
	<table class="table table-striped table-hover">
		<tr>
			<th>#</th>
			<th>Researcher</th>
			<th class="text-center">Pool</th>
			<th>Computer Model</th>
			<th class="text-right">Mag</th>
		</tr>
';
$pos = 1;
if ($this->view->hosts) {
	foreach ($this->view->hosts as $host) {
		if ($host['magTotal'] > 0) {
			$panelContent .= '
				<tr>
					<td>'.$pos++.'</td>
					<td><a href="/report/researcher/'.$host['memberId'].'/'.$host['hostId'].'">'.$host['username'].'</a></td>
					<td class="text-center">'.$host['poolId'].'</td>
					<td>'.
						$host['detail']->getModel().'<br/>
						cpus - '.$host['detail']->getNumberOfCpus().', 
						cuda - '.$host['detail']->getnumberOfCudas().', 
						amd - '.$host['detail']->getNumberOfAmds().'				
					</td>
					<td class="text-right">'.number_format($host['magTotal'],2).'</td>
				</tr>
			';
		}
	}
}
$panelContent .= '
	</table>	
';
$panel->setContent($panelContent);
$webPage->append($panel->render());