<?php

$webPage->setPageTitle('Pool Payouts');
if ($this->view->payouts) {
	$webPage->append('
		<div class="pull-right">'.$this->view->pagination.'</div>
		<div class="rowpad"><em>Calculation Definition: totalAmount = ( ( hostMagnitude / totalPoolMagnitude ) * availablePayoutBalance )</em></div>
			
		<table class="table table-striped table-hover">
			<tr>
				<th>Researcher</th>
				<th class="text-center">Pool</th>
				<th>When</th>
				<th>Transaction</th>		
				<th>Calculation</th>
				<th style="text-align:right;">Total Amount</th>
				<th style="text-align:right;">Fee</th>
				<th style="text-align:right;">Donation</th>
			</tr>
	');
	foreach ($this->view->payouts as $payout) {
		$webPage->append('
			<tr>
				<td>'.$payout->getUsername().'</td>
				<td class="text-center">'.$payout->getPoolId().'</td>
				<td>'.date('Y-m-d H:i:s',$payout->getTheTime()).'<br/>'.Utils::getTimeAgo($payout->getTheTime()).'</td>
				<td><a href="'.GrcPool_Utils::getTxUrl($payout->getTx()).'">'.substr($payout->getTx(),0,10).'...</a></td>
				<td><small>'.GrcPool_Utils::displayCalculation($payout->getCalculation()).'</small></td>				
				<td style="text-align:right;">'.$payout->getAmount().'</td>
				<td style="text-align:right;">'.$payout->getFee().'</td>
				<td style="text-align:right;">'.$payout->getDonation().'</td>
			</tr>
		');
	}
	$webPage->append('</table>');
} else {
	$webPage->append('There are currently no payouts.');
}
