<?php

$webPage->addBreadcrumb('account','user','/account');

$webPage->appendHead('

');

$table = '';
$totalOwed = 0;
$totalMag = 0;
foreach ($this->view->orphans as $orphan) {
	$table .= '
		<tr>
			<td>'.$this->view->accounts[$orphan->getAccountId()]->getName().'</td>
			<td class="text-center">'.$orphan->getPoolId().'</td>
			<td class="text-right">'.$orphan->getAvgCredit().'</td>
			<td class="text-right">'.$orphan->getMag().'</td>
			<td class="text-right">'.$orphan->getOwed().'</td>
		</tr>
	';
	$totalMag += $orphan->getMag();
	$totalOwed += $orphan->getOwed();
}

$panel = new Bootstrap_Panel();
$panel->setHeader('Orphans');
$panel->setContext('info');
$panel->setContent('
	Orphans are projects which are no longer linked to your account and are unable to be paid out under normal circumstances.
	These instances possibly occur from host/project deletion, OS resinstalls, or other odd technical reasons within the BOINC network where your project gets new ids from BOINC.
	<br/><br/>
	In order to handle the amount owed these items might collect, their payouts are handled slightly differently.
	Payouts will occur for orphans in two circumstances:
	<ul>
		<li>The project reaches a zero magnitude and there is more than '.$this->view->payoutNoMag.' GRC</li>
		<li>They have more than '.$this->view->payoutWithMag.' GRC owed</li>
	</ul>
	The payouts are different for these orphans because these projects should have a decaying magnitude. I would like to try and avoid a bunch of dust GRC if possible, so that is why payment might be delayed until 
	zero mag is reached.
	<br/><br/>
	<table class="table table-condensed table-hover table-striped">
		<tr>
			<th>Project</th>
			<th class="text-center">Pool</th>
			<th class="text-right">RAC</th>
			<th class="text-right">Mag</th>
			<th class="text-right">Owed</th>
		</tr>
		'.$table.'
		<tr>
			<td style="background-color:#f0f0f0;font-weight:bold;">Totals</td>
			<td style="background-color:#f0f0f0;font-weight:bold;"></td>
			<td style="background-color:#f0f0f0;font-weight:bold;"></td>
			<td class="text-right" style="background-color:#f0f0f0;font-weight:bold;">'.$totalMag.'</td>
			<td class="text-right" style="background-color:#f0f0f0;font-weight:bold;">'.$totalOwed.'</td>
		</tr>
	</table>
');
$webPage->append($panel->render());
