<?php

$webPage->addBreadcrumb('account','user','/account');

$webPage->appendHead('

');

$table = '';
$totalOwed = 0;
$totalOwedSparc = 0;
$totalMag = 0;
foreach ($this->view->orphans as $orphan) {
	$table .= '
		<tr>
			<td><a target="_blank" href="'.$this->view->accounts[$orphan->getAccountId()]->getBaseUrl().'show_host_detail.php?hostid='.$orphan->getHostDbid().'">'.$this->view->accounts[$orphan->getAccountId()]->getName().'</a> <i class="fa fa-external-link"></i></td>
			<td class="text-center">'.$orphan->getPoolId().'</td>
			<td class="text-right">'.$orphan->getAvgCredit().'</td>
			<td class="text-right">'.$orphan->getMag().'</td>
			<td class="text-right">'.$orphan->getOwed().'</td>
			<td class="text-right">'.$orphan->getSparc().'</td>
		</tr>
	';
	$totalMag += $orphan->getMag();
	$totalOwed += $orphan->getOwed();
	$totalOwedSparc += $orphan->getSparc();
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
			<th class="text-right">GRC</th>
			<th class="text-right">SPARC</th>
		</tr>
		'.$table.'
		<tr>
			<td style="background-color:#f0f0f0;font-weight:bold;">Totals</td>
			<td style="background-color:#f0f0f0;font-weight:bold;"></td>
			<td style="background-color:#f0f0f0;font-weight:bold;"></td>
			<td class="text-right" style="background-color:#f0f0f0;font-weight:bold;">'.$totalMag.'</td>
			<td class="text-right" style="background-color:#f0f0f0;font-weight:bold;">'.$totalOwed.'</td>
			<td class="text-right" style="background-color:#f0f0f0;font-weight:bold;">'.$totalOwedSparc.'</td>
		</tr>
	</table>
');
$webPage->append($panel->render());
