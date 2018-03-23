<?php
$webPage->appendTitle('Pool Financial Information');
$numberOfPools = Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS);
$profits = array();
$stakeBalances = array();
for ($i = 1; $i <= $numberOfPools; $i++) {
	if ($this->view->walletMode=='SINGLE') {
		$stakeBalance = (($this->view->superblockData->balance[$i-1]*COIN)-($this->view->superblockData->owed[$i-1]*COIN)-($this->view->superblockData->interest[$i-1]*COIN)-($this->view->superblockData->basis[$i-1]))/COIN;
		$profit = (($this->view->superblockData->balance[$i-1]*COIN)-($this->view->seeds[$i]*COIN)-($this->view->superblockData->interest[$i-1]*COIN)-($stakeBalance<0?0:$stakeBalance*COIN)-($this->view->superblockData->owed[$i-1]*COIN))/COIN;
	} else {
		$stakeBalance = (($this->view->superblockData->balance[$i-1]*COIN)-($this->view->superblockData->owed[$i-1]*COIN)-($this->view->superblockData->basis[$i-1]))/COIN;
		$profit = (($this->view->superblockData->balance[$i-1]*COIN)-($this->view->seeds[$i]*COIN)-($stakeBalance<0?0:$stakeBalance*COIN)-($this->view->superblockData->owed[$i-1]*COIN))/COIN;
	}
	$profits[$i] = $profit;
	$stakeBalances[$i] = $stakeBalance;
}

$tabs = new Bootstrap_Tabs();
$startIdx = $numberOfPools> 1?0:1;

$first = true;
for ($i = $startIdx; $i <= $numberOfPools; $i++) {
	$tab = new Bootstrap_Tab();
	$tab->setTitle($i?'Pool '.$i:'Combined');
	$tab->setActive($first);
	$first = false;
	$mag = $i?$this->view->superblockData->mag[$i-1]:array_sum($this->view->superblockData->mag);
	$txCount = $i?$this->view->superblockData->txCount[$i-1]:array_sum($this->view->superblockData->txCount);
	$expectedDailyEarnings = $i?$this->view->superblockData->expectedDailyEarnings[$i-1]:array_sum($this->view->superblockData->expectedDailyEarnings);
	$fulfillment = $i?$this->view->superblockData->fulfillment[$i-1]:array_sum($this->view->superblockData->fulfillment);
	$balance = $i?$this->view->superblockData->balance[$i-1]:array_sum($this->view->superblockData->balance);
	$interest = $i?$this->view->superblockData->interest[$i-1]:array_sum($this->view->superblockData->interest);
	$research = $i?$this->view->superblockData->research[$i-1]:array_sum($this->view->superblockData->research);
	$owed = $i?$this->view->superblockData->owed[$i-1]:array_sum($this->view->superblockData->owed);
	$grcNoAddress = $i?$this->view->superblockData->grcNoAddress[$i-1]:array_sum($this->view->superblockData->grcNoAddress);
	$grcOwnerUnknown= $i?$this->view->superblockData->grcOwnerUnknown[$i-1]:array_sum($this->view->superblockData->grcOwnerUnknown);
	$basis = $i?$this->view->superblockData->basis[$i-1]:array_sum($this->view->superblockData->basis);
	$paidOut = $i?$this->view->superblockData->paidOut[$i-1]:array_sum($this->view->superblockData->paidOut);
	$stakeBalance = $i?$stakeBalances[$i]:array_sum($stakeBalances);
	$balance = $i?$this->view->superblockData->balance[$i-1]:array_sum($this->view->superblockData->balance);
	$seed = $i?$this->view->seeds[$i]:array_sum($this->view->seeds);
	$interest = $i?$this->view->superblockData->interest[$i-1]:array_sum($this->view->superblockData->interest);
	$profit = $i?$profits[$i]:array_sum($profits);
	$withdrawnProfit = $i?$this->view->profits[$i]:array_sum($this->view->profits);
	$profit += $withdrawnProfit;
	
	$tab->setContent('
		<table class="table table-striped table-hover">
			<tr><th colspan="2">Data</th></tr>
			<tr><td>Magnitude</td><td class="text-right">'.$mag.'</td></tr>
			<tr><td>Transaction Count</td><td class="text-right">'.$txCount.'</td></tr>
			<tr><td>Expected Daily Earnings</td><td class="text-right">'.$expectedDailyEarnings.'</td></tr>
			<tr><td>Fulfillment</td><td class="text-right">'.Utils::truncate($fulfillment,2).'%</td></tr>

			<tr><td colspan="2">&nbsp;</td></tr>

			<tr><th colspan="2">Financials</th></tr>
			<tr><td>Balance</td><td class="text-right">'.$balance.'</td></tr>
			'.($this->view->walletMode=='SINGLE'?'<tr><td>Interest</td><td class="text-right">'.$interest.'</td></tr>':'').'
			<tr><td>Research Awards</td><td class="text-right">'.$research.'</td></tr>
			<tr><td>Total Owed to Researchers</td><td class="text-right">'.$owed.'</td></tr>
			<tr><td>&nbsp;&nbsp;&nbsp;Owed to No GRC Address</td><td class="text-right">'.$grcNoAddress.'</td></tr>
			<tr><td>&nbsp;&nbsp;&nbsp;Owed to Unknown Host Owner</td><td class="text-right">'.$grcOwnerUnknown.'</td></tr>
			<tr><td>Basis</td><td class="text-right">'.($basis/COIN).'</td></tr>
			<tr><td>Paid Out</td><td class="text-right">'.$paidOut.'</td></tr>
			<tr><td>POR Available</td><td class="text-right">'.($stakeBalance<0?'0':$stakeBalance).'</td></tr>

			<tr><td colspan="2">&nbsp;</td></tr>

			<tr><th colspan="2">Pool Profit</th></tr>
			<tr><td>Balance</td><td class="text-right">'.$balance.'</td></tr>
			<tr><td>Seed</td><td class="text-right">-'.$seed.'</td></tr>
			'.($this->view->walletMode=='SINGLE'?'<tr><td>Interest</td><td class="text-right">-'.$interest.'</td></tr>':'').'
			<tr><td>POR Available</td><td class="text-right">-'.($stakeBalance<0?'0':$stakeBalance).'</td></tr>
			<tr><td>Owed to Researchers</td><td class="text-right">-'.$owed.'</td></tr>
			<tr><td>Withdrawn Profit</td><td class="text-right">'.$withdrawnProfit.'</td></tr>
			<tr><td><strong>Pool Profit from fees &amp; donations</strong></td><td class="text-right"><strong>'.Utils::truncate($profit,8).' GRC</strong></td></tr>
		</table>
	');
	$tabs->addTab($tab);
}
$webPage->append($tabs->render());