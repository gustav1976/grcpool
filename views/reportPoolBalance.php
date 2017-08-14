<?php
$webPage->appendTitle('Pool Financial Information');
$panel = new Bootstrap_Panel();
$panel->setHeader('Pool Financials');
$panelContent = '';

$stakeBalance0 = (($this->view->superblockData->balance[0]*COIN)-($this->view->superblockData->owed[0]*COIN)-($this->view->superblockData->interest[0]*COIN)-($this->view->superblockData->basis[0]))/COIN;
$stakeBalance1 = (($this->view->superblockData->balance[1]*COIN)-($this->view->superblockData->owed[1]*COIN)-($this->view->superblockData->interest[1]*COIN)-($this->view->superblockData->basis[1]))/COIN;
$profit0 = (($this->view->superblockData->balance[0]*COIN)-($this->view->seed*COIN)-($this->view->superblockData->interest[0]*COIN)-($stakeBalance0<0?0:$stakeBalance0*COIN)-($this->view->superblockData->owed[0]*COIN))/COIN;
$profit1 = (($this->view->superblockData->balance[1]*COIN)-($this->view->seed2*COIN)-($this->view->superblockData->interest[1]*COIN)-($stakeBalance1<0?0:$stakeBalance1*COIN)-($this->view->superblockData->owed[1]*COIN))/COIN;

$panelContent .= '		
	<div class="rowpad">
		This information is from when superblock <strong>'.$this->view->superblockData->block.'</strong> was created.
	</div>
	<table class="table table-striped table-hover">
		<tr><th>Data</th><th style="width:25%;" class="text-right">Pool 1</th><th style="width:25%;" class="text-right">Pool 2</th></tr>
		<tr><td>Magnitude</td><td class="text-right">'.$this->view->superblockData->mag[0].'</td><td class="text-right">'.$this->view->superblockData->mag[1].'</td></tr>
		<tr><td>Transaction Count</td><td class="text-right">'.$this->view->superblockData->txCount[0].'</td><td class="text-right">'.$this->view->superblockData->txCount[1].'</td></tr>
		<tr><td>Expected Daily Earnings</td><td class="text-right">'.$this->view->superblockData->expectedDailyEarnings[0].'</td><td class="text-right">'.$this->view->superblockData->expectedDailyEarnings[1].'</td></tr>
		<tr><td>Fulfillment</td><td class="text-right">'.Utils::truncate($this->view->superblockData->fulfillment[0],2).'%</td><td class="text-right">'.Utils::truncate($this->view->superblockData->fulfillment[1],2).'%</td></tr>
	</table>
	<br/>
	<table class="table table-striped table-hover">
		<tr><th>Financials</th><th style="width:25%;" class="text-right">Pool 1</th><th style="width:25%;" class="text-right">Pool 2</th></tr>
		<tr><td>Balance</td><td class="text-right">'.$this->view->superblockData->balance[0].'</td><td class="text-right">'.$this->view->superblockData->balance[1].'</td></tr>
		<tr><td>Interest</td><td class="text-right">'.$this->view->superblockData->interest[0].'</td><td class="text-right">'.$this->view->superblockData->interest[1].'</td></tr>
		<tr><td>Research Awards</td><td class="text-right">'.$this->view->superblockData->research[0].'</td><td class="text-right">'.$this->view->superblockData->research[1].'</td></tr>
		<tr><td>Total Owed to Researchers</td><td class="text-right">'.$this->view->superblockData->owed[0].'</td><td class="text-right">'.$this->view->superblockData->owed[1].'</td></tr>
		<tr><td>&nbsp;&nbsp;&nbsp;Owed to No GRC Address</td><td class="text-right">'.$this->view->superblockData->grcNoAddress[0].'</td><td class="text-right">'.$this->view->superblockData->grcNoAddress[1].'</td></tr>
		<tr><td>&nbsp;&nbsp;&nbsp;Owed to Unknown Host Owner</td><td class="text-right">'.$this->view->superblockData->grcOwnerUnknown[0].'</td><td class="text-right">'.$this->view->superblockData->grcOwnerUnknown[1].'</td></tr>
		<tr><td>Basis</td><td class="text-right">'.($this->view->superblockData->basis[0]/COIN).'</td><td class="text-right">'.($this->view->superblockData->basis[1]/COIN).'</td></tr>
		<tr><td>Paid Out</td><td class="text-right">'.$this->view->superblockData->paidOut[0].'</td><td class="text-right">'.$this->view->superblockData->paidOut[1].'</td></tr>
		<tr><td>POR Available</td><td class="text-right">'.($stakeBalance0<0?'0':$stakeBalance0).'</td><td class="text-right">'.($stakeBalance1<0?'0':$stakeBalance1).'</td></tr>
	</table>
	<br/>	
	<table class="table table-striped table-hover">
		<tr><th>Profit</th><th style="width:25%;" class="text-right">Pool 1</th><th style="width:25%;" class="text-right">Pool 2</th></tr>
		<tr><td>Balance</td><td class="text-right">'.$this->view->superblockData->balance[0].'</td><td class="text-right">'.$this->view->superblockData->balance[1].'</td></tr>
		<tr><td>Seed</td><td class="text-right">-'.$this->view->seed.'</td><td class="text-right">-'.$this->view->seed2.'</td></tr>
		<tr><td>Interest</td><td class="text-right">-'.$this->view->superblockData->interest[0].'</td><td class="text-right">-'.$this->view->superblockData->interest[1].'</td></tr>
		<tr><td>POR Available</td><td class="text-right">-'.($stakeBalance0<0?'0':$stakeBalance0).'</td><td class="text-right">-'.($stakeBalance1<0?'0':$stakeBalance1).'</td></tr>
		<tr><td>Owed to Researchers</td><td class="text-right">-'.$this->view->superblockData->owed[0].'</td><td class="text-right">-'.$this->view->superblockData->owed[1].'</td></tr>
		<tr><td><strong>Pool Profit from fees &amp; donations</strong></td><td class="text-right"><strong>'.Utils::truncate($profit0,8).' GRC</strong></td><td class="text-right"><strong>'.Utils::truncate($profit1,8).' GRC</strong></td></tr>
	</table>	
';
$panel->setContent($panelContent);
$webPage->append($panel->render());