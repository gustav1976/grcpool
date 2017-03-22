<?php
$webPage->setPageTitle('Projects');
$projects = '
	<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th>Project</th>
				<th style="text-align:center;">White List</th>
				<th style="text-align:center;">Attachable</th>
				<th>Last Contact</th>
				<th class="text-right">Avg Credit</th>
				<th class="text-right">Hosts</th>
				<th class="text-right">Mag</th>
			</tr>
		</thead>
		<tbody>
';
foreach ($this->view->accounts as $account) {
	$mag = 0;
	$hostCount = 0;
	if (isset($this->view->projStats[$account->getUrl()])) {
		$hostCount = $this->view->projStats[$account->getUrl()]['hostCount'];
		$mag = $this->view->projStats[$account->getUrl()]['mag'];
	}
	$projects .= '
		<tr>
			<td>
				<a href="'.$account->getUrl().'">'.$account->getName().'</a>
				'.($account->getMessage()?'<br/><i class="fa fa-warning text-danger"></i> '.$account->getMessage():'').'
			</td>
			<td style="text-align:center;">'.($account->getWhiteList()?'<i class="fa fa-thumbs-up text-success"></i>':'<i class="fa fa-thumbs-down text-danger"></i>').'</td>
			<td style="text-align:center;">'.($account->getAttachable()?'<i class="fa fa-thumbs-up text-success"></i>':'<i class="fa fa-thumbs-down text-danger"></i>').'</td>
			<td>'.Utils::getTimeAgo($account->getLastSeen()).'</td>					
			<td class="text-right">'.$account->getRac().'</td>
			<td class="text-right">'.$hostCount.'</td>
			<td class="text-right">'.$mag.'</td>
		</tr>
	';
}
$projects .= '</tbody></table>';
$webPage->append('
	'.$projects.'	
	<br/>
	<img src="/api/projectChart" class="img-responsive"/>		
');