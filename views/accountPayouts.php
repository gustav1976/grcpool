<?php

$webPage->addBreadcrumb('account','user','/account');


$panel = new Bootstrap_Panel();
$panel->setContext('info');
$panel->setHeader('Amount Owed Calculation');
$content = '';
if ($this->view->owed) {
	$content .= '
		<div class="rowpad"><em>Calculation Definition: Owed = ( ( hostMagnitude / totalPoolMagnitude ) * availablePayoutBalance )</em></div>
		<table class="table table-striped table-hover rowpad">
			<tr>
				<th>Project</th>
				<th>Calculation</th>			
				<th style="text-align:right;">Avg Credit</th>
				<th style="text-align:right;">Mag</th>
				<th style="text-align:right;">Owed</th>
			</tr>
	';
	foreach ($this->view->owed as $owe) {
		if ($owe->getOwed() > 0 || $owe->getMag() > 0) {
			$content .= '
				<tr>
					<td>'.$owe->getProjectUrl().'</td>
					<td>'.str_replace('+','+<br/>',substr($owe->getOwedCalc(),1)).'</td>				
					<td style="text-align:right;">'.$owe->getAvgCredit().'</td>
					<td style="text-align:right;">'.$owe->getMag().'</td>
					<td style="text-align:right;">'.$owe->getOwed().'</td>
				</tr>
			';
		}
	}
	$content .= '</table>';
} else {
	$content .= 'Nothing owed yet.';
}
$panel->setContent($content);
$webPage->append($panel->render());

$panel = new Bootstrap_Panel();
$panel->setContext('info');
$panel->setHeader($this->view->numberOfPayouts.' Payouts for '.$this->view->payoutTotal.' GRC');

$content = '';

if ($this->view->payouts) {
	$content .= '
		<div class="pull-right">'.$this->view->pagination.'</div>
		<div class="rowpad"><em>Calculation Definition: totalAmount = ( ( hostMagnitude / totalPoolMagnitude ) * availablePayoutBalance )</em></div>
		<table class="table table-striped table-hover">
			<tr>
				<th>When</th>
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