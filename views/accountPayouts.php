<?php

$webPage->addBreadcrumb('account','user','/account');


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
				<th class="text-center">Currency</th>
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
				<td class="text-center">'.$payout->getPoolId().'</td>
				<td>
					<a href="'.GrcPool_Utils::getTxUrl($payout->getTx(),$payout->getCurrency()).'">'.substr($payout->getTx(),0,10).'...</a><br/>
					'.($payout->getCurrency() == 'GRC'?'
						<a href="'.GrcPool_Utils::getGrcAddressUrl($payout->getAddress()).'">'.substr($payout->getAddress(),0,10).'...</a>	
					':'
						'.substr($payout->getAddress(),0,10).'...
					').'
				</td>
				<td><small>'.GrcPool_Utils::displayCalculation($payout->getCalculation()).'</small></td>
				<td class="text-center">'.$payout->getCurrency().'</td>
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