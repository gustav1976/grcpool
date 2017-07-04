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
					<td>'.$this->view->accounts[$owe->getProjectUrl()]->getName().'</td>
					<td class="text-center">'.$owe->getProjectPoolId().'</td>
					<td>'.str_replace('+','+<br/>',substr($owe->getOwedCalc(),1)).'</td>				
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

$panel = new Bootstrap_Panel();
$panel->setContext('info');
$panel->setHeader('Pool Payouts');

$content = '';

if ($this->view->payouts) {
	$content .= '
		<div class="pull-right">'.$this->view->pagination.'</div>
		<div class="rowpad"><strong>'.$this->view->numberOfPayouts.' Payouts for '.$this->view->payoutTotal.' GRC</strong></div>
		<table class="table table-striped table-hover table-condensed">
			<tr>
				<th>When</th>
				<th class="text-center">Pool</th>
				<th>Transaction</th>
				<th>Calculation</th>
				<th style="text-align:right;">Total Amount</th>
				<th style="text-align:right;">Fee</th>
				<th style="text-align:right;">Donation</th>			
				<th style="text-align:right;">Sent</th>
			</tr>
	';
	foreach ($this->view->payouts as $payout) {
		$content .= '
			<tr>
				<td>'.date('Y-m-d H:i:s',$payout->getTheTime()).'<br/><small>'.Utils::getTimeAgo($payout->getTheTime()).'</small></td>
				<td class="text-center">'.$payout->getPayoutPoolId().'</td>
				<td><a href="http://www.gridresearchcorp.com/gridcoin/?transaction_detail&txid='.$payout->getTx().'">'.substr($payout->getTx(),0,10).'...</a></td>
				<td><small>'.GrcPool_Utils::displayCalculation($payout->getCalculation()).'</small></td>
				<td style="text-align:right;">'.$payout->getAmount().'</td>
				<td style="text-align:right;">'.$payout->getFee().'</td>
				<td style="text-align:right;">'.$payout->getDonation().'</td>		
				<td style="text-align:right;">'.($payout->getAmount()-$payout->getDonation()-$payout->getFee()).'</td>
			</tr>
		';
	}
	$content .= '
		</table>	
	';
} else {
	$content .= 'No payouts yet.';
}
$panel->setContent($content);
$webPage->append($panel->render());