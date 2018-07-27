<?php

$webPage->addBreadcrumb('account','user','/account');


$panel = new Bootstrap_Panel();
$panel->setContext('info');
$panel->setHeader('Amount Owed');
$content = '';
if ($this->view->owed) {
	$content .= '
		<table class="table table-striped table-hover rowpad table-condensed">
			<tr>
				<th>Project</th>
				<th>Host</th>
				<th class="text-center">Pool</th>
				<th>Calculation</th>			
				<th style="text-align:right;">Avg Credit</th>
				<th style="text-align:right;">
					Mag
					<a href="#" data-toggle="tooltip" title="MAG = '.Constants::GRC_MAG_MULTIPLIER.' * ( ( HRAC / TRAC ) / W )"><i style="color:black;" class="fa fa-info-circle"></i></a>
				</th>
				<th style="text-align:right;">
					Owed
					<a href="#" data-toggle="tooltip" title="Owed = ( ( hostMagnitude / totalPoolMagnitude ) * availablePayoutBalance )"><i style="color:black;" class="fa fa-info-circle"></i></a>
				</th>
			</tr>
	';
	$totalMag = 0;
	$totalOwe = 0;
	foreach ($this->view->owed as $owe) {
		if ($owe->getOwed() > 0 || $owe->getMag() > 0) {
			$totalMag += $owe->getMag();
			$totalOwe += $owe->getOwed();
			$content .= '
				<tr>
					<td>'.$this->view->accounts[$owe->getAccountId()]->getName().'</td>
					<td>'.$this->view->hosts[$owe->getHostId()]->getHostName().'</td>
					<td class="text-center">'.$owe->getProjectPoolId().'</td>
					<td><small>'.str_replace('+','+<br/>',substr($owe->getOwedCalc(),1)).'</small></td>				
					<td style="text-align:right;">'.$owe->getAvgCredit().'</td>
					<td style="text-align:right;">'.$owe->getMag().'</td>
					<td style="text-align:right;">'.$owe->getOwed().'</td>
				</tr>
			';
		}
	}
	$content .= '<tr style="backgrounc-color:#ccc;">
		<tr>
			<td style="background-color:#ccc;"><strong>Totals</td>
			<td style="background-color:#ccc;"></td>
			<td style="background-color:#ccc;"></td>
			<td style="background-color:#ccc;"></td>
			<td style="background-color:#ccc;"></td>
			<td style="background-color:#ccc;font-weight:bold;" class="text-right">'.$totalMag.'</td>
			<td style="background-color:#ccc;font-weight:bold;" class="text-right">'.$totalOwe.'</td>
		</tr>
	</tr>';
	$content .= '</table>';
} else {
	$content .= 'Nothing owed yet.';
}
$panel->setContent($content);
$webPage->append($panel->render());
