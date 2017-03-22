<?php
$webPage->setPageTitle('Owed &amp; Payouts');
$webPage->append('<h3>Owed</h3>');
if ($this->view->owed) {
	$webPage->append('
		<div class="rowpad"><em>Calculation Definition: Owed = ( ( hostMagnitude / totalPoolMagnitude ) * availablePayoutBalance )</em></div>
		<table class="table table-striped table-hover rowpad">
			<tr>
				<th>Project</th>
				<th>Calculation</th>			
				<th style="text-align:right;">Avg Credit</th>
				<th style="text-align:right;">Mag</th>
				<th style="text-align:right;">Owed</th>
			</tr>
	');
	foreach ($this->view->owed as $owe) {
		if ($owe->getOwed() > 0 || $owe->getMag() > 0) {
			$webPage->append('
				<tr>
					<td>'.$owe->getProjectUrl().'</td>
					<td>'.str_replace('+','+<br/>',substr($owe->getOwedCalc(),1)).'</td>				
					<td style="text-align:right;">'.$owe->getAvgCredit().'</td>
					<td style="text-align:right;">'.$owe->getMag().'</td>
					<td style="text-align:right;">'.$owe->getOwed().'</td>
				</tr>
			');
		}
	}
	$webPage->append('</table>');
} else {
	$webPage->append('Nothing owed yet.');
}

$webPage->append('<br/><h3>'.$this->view->numberOfPayouts.' Payouts for '.$this->view->payoutTotal.' GRC</h3>');
if ($this->view->payouts) {
	$webPage->append('
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
	');
	foreach ($this->view->payouts as $payout) {
		$webPage->append('
			<tr>
				<td>'.date('Y-m-d H:i:s',$payout->getTheTime()).'<br/><small>'.Utils::getTimeAgo($payout->getTheTime()).'</small></td>
				<td><a href="http://www.gridresearchcorp.com/gridcoin/?transaction_detail&txid='.$payout->getTx().'">'.substr($payout->getTx(),0,10).'...</a></td>
				<td><small>'.GrcPool_Utils::displayCalculation($payout->getCalculation()).'</small></td>
				<td style="text-align:right;">'.$payout->getAmount().'</td>
				<td style="text-align:right;">'.$payout->getFee().'</td>
				<td style="text-align:right;">'.$payout->getDonation().'</td>		
				<td style="text-align:right;">'.($payout->getAmount()-$payout->getDonation()-$payout->getFee()).'</td>
			</tr>
		');
	}
	$webPage->append('
		</table>	
	');
} else {
	$webPage->append('No payouts yet.');
}