<?php
$webPage->setPageTitle('Pool Financial Information');

$stakeBalance = (($this->view->superblockData->balance*COIN)-($this->view->superblockData->owed*COIN)-($this->view->superblockData->interest*COIN)-($this->view->superblockData->basis*COIN))/COIN;
$profit = (($this->view->superblockData->balance*COIN)-($this->view->seed*COIN)-($this->view->superblockData->interest*COIN)-($stakeBalance<0?0:$stakeBalance*COIN)-($this->view->superblockData->owed*COIN))/COIN;

$webPage->append('
		
	<div class="rowpad">
		This information is from when superblock <strong>'.$this->view->superblockData->block.'</strong> was created.
	</div>
	
	<h3>Data</h3>
		
	<table class="table table-striped table-hover">
		<tr><td>Magnitude</td><td class="text-right">'.$this->view->superblockData->mag.'</td></tr>
		<tr><td>Transaction Count</td><td class="text-right">'.$this->view->superblockData->txCount.'</td></tr>
		<tr><td>Expected Daily Earnings</td><td class="text-right">'.$this->view->superblockData->expectedDailyEarnings.'</td></tr>
		<tr><td>Fulfillment</td><td class="text-right">'.Utils::truncate($this->view->superblockData->fulfillment,2).'%</td></tr>
	</table>
	<br/>
	<h3>Pool Financials</h3>
	<table class="table table-striped table-hover">
		<tr><td>Balance</td><td class="text-right">'.$this->view->superblockData->balance.'</td></tr>
		<tr><td>Interest</td><td class="text-right">'.$this->view->superblockData->interest.'</td></tr>
		<tr><td>Research Awards</td><td class="text-right">'.$this->view->superblockData->research.'</td></tr>
		<tr><td>Total Owed to Researchers</td><td class="text-right">'.$this->view->superblockData->owed.'</td></tr>
		<tr><td>&nbsp;&nbsp;&nbsp;Owed to No GRC Address</td><td class="text-right">'.$this->view->superblockData->grcNoAddress.'</td></tr>
		<tr><td>&nbsp;&nbsp;&nbsp;Owed to Unknown Host Owner</td><td class="text-right">'.$this->view->superblockData->grcOwnerUnknown.'</td></tr>
		<tr><td>Basis</td><td class="text-right">'.$this->view->superblockData->basis.'</td></tr>
		<tr><td>Paid Out</td><td class="text-right">'.$this->view->superblockData->paidOut.'</td></tr>
		<tr><td>POR Available</td><td class="text-right">'.($stakeBalance<0?'0':$stakeBalance).'</td></tr>
	</table>
	<br/>	
	<h3>Pool Profit</h3>
	<table class="table table-striped table-hover">
		<tr><td>Balance</td><td class="text-right">'.$this->view->superblockData->balance.'</td></tr>
		<tr><td>Seed</td><td class="text-right">-'.$this->view->seed.'</td></tr>
		<tr><td>Interest</td><td class="text-right">-'.$this->view->superblockData->interest.'</td></tr>
		<tr><td>POR Available</td><td class="text-right">-'.($stakeBalance<0?'0':$stakeBalance).'</td></tr>
		<tr><td>Owed to Researchers</td><td class="text-right">-'.$this->view->superblockData->owed.'</td></tr>
		<tr><td><strong>Pool Profit from fees &amp; donations</strong></td><td class="text-right"><strong>'.Utils::truncate($profit,8).' GRC</strong></td></tr>
	</table>	
');