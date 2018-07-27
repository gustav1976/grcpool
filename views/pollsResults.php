<?php

$totalMag = 0;
$totalBalance = 0;
$totalWeight = 0;
$totalVotes = 0;
$totalAdjWeight = 0;
$answers = array();
foreach ($this->view->answers as $answer) {
	$answers[$answer->getId()] = array();
	$answers[$answer->getId()]['weight'] = 0;
	$answers[$answer->getId()]['count'] = 0;
	$answers[$answer->getId()]['mag'] = 0;
	$answers[$answer->getId()]['balance'] = 0;
	$answers[$answer->getId()]['adjWeight'] = 0;
}
foreach ($this->view->voters as $voter) {
	$totalVotes++;
	$answers[$voter->getAnswerId()]['weight'] += $voter->getWeight();
	$answers[$voter->getAnswerId()]['balance'] += $voter->getBalance();
	$answers[$voter->getAnswerId()]['mag'] += $voter->getMag();
	$answers[$voter->getAnswerId()]['count'] += 1;
	$answers[$voter->getAnswerId()]['adjWeight'] += $this->view->poll->getTotalPoolShares()*$voter->getWeight()/$this->view->poll->getPoolCalcShares();
	$totalAdjWeight += $this->view->poll->getTotalPoolShares()*$voter->getWeight()/$this->view->poll->getPoolCalcShares();
	$totalMag += $voter->getMag();
	$totalBalance += $voter->getBalance();
	$totalWeight += $voter->getWeight();
}

$title = str_replace('_',' ',$this->view->poll->getQuestion());

$webPage->append('
	<strong>'.$title.'</strong><br/><br/>
	<table class="table table-striped">
		<tr>
			<th>Vote</th>
			<th class="text-right">Count</th>
			<th class="text-right">Pool Mag</th>
			<th class="text-right">Balance</th>
			<th class="text-right">Share</th>
			<th class="text-right">Adj Share</th>
		</tr>
');
foreach ($this->view->answers as $answer) {
	$webPage->append('
		<tr>
			<td>'.$answer->getPrettyAnswer().'</td>
			<td class="text-right">'.$answers[$answer->getId()]['count'].'</td>
			<td class="text-right">'.number_format($answers[$answer->getId()]['mag'],2).'</td>
			<td class="text-right">'.number_format($answers[$answer->getId()]['balance'],2).'</td>
			<td class="text-right">'.number_format($answers[$answer->getId()]['weight'],2).'</td>
			<td class="text-right">'.number_format($answers[$answer->getId()]['adjWeight'],2).'</td>
		</tr>
	');
}
$webPage->append('
		<tr>
			<td></td>
			<td class="text-right"><strong>'.$totalVotes.'</strong></td>
			<td class="text-right"><strong>'.number_format($totalMag,2).'</strong></td>
			<td class="text-right"><strong>'.number_format($totalBalance,2).'</strong></td>
			<td class="text-right"><strong>'.number_format($totalWeight,2).'</strong></td>
			<td class="text-right"><strong>'.number_format($totalAdjWeight,2).'</strong></td>
		</tr>
	</table>
	<br/>
');

$webPage->append('
	<table class="table table-striped">
		<tr>
			<th>Username</th>
			<th class="text-right">Mag</th>
			<th class="text-right">Balance</th>
			<th class="text-right">Share</th>
			<th class="text-right">Adj Share</th>
		</tr>
');
foreach ($this->view->voters as $voter) {
	
	//$weight = (($vote->getMag() * ($moneySupply/Constants::GRC_MAG_MULTIPLIER + 0.01)/5.67) + $vote->getBalance());
	
	$webPage->append('
		<tr>
			<td>'.$voter->getUsername().'</td>
			<td class="text-right">'.number_format($voter->getMag(),2).'</td>
			<td class="text-right">'.number_format($voter->getBalance(),2).'</td>
			<td class="text-right">'.number_format($voter->getWeight(),2).'</td>
			<td class="text-right">'.number_format($this->view->poll->getTotalPoolShares()*$voter->getWeight()/$this->view->poll->getPoolCalcShares(),2).'</td>
		</tr>
	');
}
$webPage->append('
	</table>
');
