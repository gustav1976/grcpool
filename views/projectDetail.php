<?php

$webPage->append('
		
	'.Bootstrap_Callout::error("Project pages are still being tested and under development.").'	
		
	<br/><br/>	
		
	<a target="_blank" href="'.$this->view->url->getUrl().'">'.$this->view->project->getName().'</a>
	
	<br/>Total Credit: '.$this->view->stats['TOTAL_CREDIT'].'
	<br/>Total Users: '.$this->view->stats['TOTAL_USERS'].'
	<br/>Total Hosts: '.$this->view->stats['TOTAL_HOSTS'].'
	<br/>Total Teams: '.$this->view->stats['TOTAL_TEAMS'].'
	<br/>Total Team Credit: '.$this->view->stats['TEAM_CREDIT'].'
	<br/>Total Team RAC: '.$this->view->stats['TEAM_AVGCREDIT'].'
	<br/>Pool Hosts: '.$this->view->stats['poolHosts'].'
	<br/>Pool Credit '.$this->view->stats['totalCredit'].'
	<br/>Pool Total Mag:: '.$this->view->stats['totalMag'].'
	<br/><br/>
');

$badges = json_decode($this->view->stats['BADGES'],true);

foreach ($badges as $badge) {
	$webPage->append('
		'.$badge['title'].'
		<img src="'.$this->view->url->getUrl().''.$badge['image'].'" title="'.$badge['title'].'"/>	
	');
}

$webPage->append('
	<table class="table table-condensed table-striped table-hover">	
		<tr>
			<th>Name</th>
			<th class="text-right">Total Credit</th>
			<th class="text-right">Avg Credit</th>
			<th class="text-right">Est. Mag</th>
			<th class="text-right">Est. Daily GRC</th>
		</tr>
');
foreach ($this->view->users as $user) {
	$mag = GrcPool_Utils::calculateMag($user->getAvgCredit(),$this->view->project->getRac(),$this->view->poolWhiteListCount,2);
	$webPage->append('
		<tr>
			<td><a target="_blank" href="https://gridcoinstats.eu/cpid/'.$user->getCpid().'">'.$user->getName().' <i class="fa fa-external-link"></i></a></td>
			<td class="text-right">'.number_format($user->getTotalCredit(),4).'</td>
			<td class="text-right">'.number_format($user->getAvgCredit(),4).'</td>
			<td class="text-right">'.$mag.'</td>
			<td class="text-right">'.(Utils::truncate($mag*$this->view->magUnit,3)).'</td>
		</tr>
	');	
}
$webPage->append('
	</table>
');