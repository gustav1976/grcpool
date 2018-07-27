<?php
$webPage->setHome(true);
$projects = '
	<table class="table table-striped table-hover table-condensed">
		<tr>
			<th>Project</th>
			<th style="text-align:center;">hosts</th>
			<th style="text-align:center;">mag</th>
		</tr>
';

$webPage->appendHomeBody('
	'.($this->view->online != '1'?'
		'.Bootstrap_Callout::error($this->view->onlineMessage).'		
	':'').'		
	<div class="row">
		<div class="col-sm-6 rowpad">
			<div class="embed-responsive embed-responsive-16by9" style="border:1px solid #ccc;"><iframe class="embed-responsive-item" width="560" height="315" src="https://www.youtube.com/embed/jm2E6pQ-Ifw" frameborder="0" allowfullscreen></iframe></div>
		</div>
		<div class="col-sm-6 rowpad">
			<div class="embed-responsive embed-responsive-16by9" style="border:1px solid #ccc;"><iframe class="embed-responsive-item" width="560" height="315" src="https://www.youtube.com/embed/Ws4BUte-2b8" frameborder="0" allowfullscreen></iframe></div>
		</div>
	</div>
');

$cpids = '';
for ($i = 1; $i <= count($this->view->mags); $i++) {
	if (isset($this->view->cpids[$i-1])) {
		$cpids .= '
			<tr>
				<td>'.$i.'</td>
				<td>'.substr($this->view->cpids[$i-1],0,5).'...</td>
				<td class="text-center"><a target="_blank" href="'.GrcPool_Utils::getCpidUrl($this->view->cpids[$i-1]).'"><i class="fa fa-external-link"></i></a></td>
				<td class="text-center"><a target="_blank" href="http://boinc.netsoft-online.com/e107_plugins/boinc/get_user.php?cpid='.$this->view->cpids[$i-1].'&format=xml"><i class="fa fa-external-link"></i></a></td>
				<td class="text-right">'.number_format($this->view->activeHosts[$i],0).'</td>
				<td class="text-right">'.number_format(isset($this->view->superblockData->mag[$i-1])?$this->view->superblockData->mag[$i-1]:0,0).'</td>
				<td class="text-right">'.number_format($this->view->mags[$i],0).'</td>
			</tr>		
		';
	}
}
$webPage->append('
	<div class="row rowpad">
		<div class="col-sm-6">
			'.Bootstrap_Callout::info('
				<h3>Gridcoin Client</h3>
				<table class="table table-striped table-hover table-condensed">
					<tr><td>Last Superblock</td><td style="text-align:right;" id="lastSuperblock">'.$this->view->superblockData->block.'</td></tr>
					<tr><td>Superblock Age</td><td style="text-align:right;" id="superblockAge">'.$this->view->superblockData->ageText.'</td></tr>
					'.($this->view->superblockData->pending==0?'':'
						<tr><td>Pending Superblock</td><td style="text-align:right;" id="pendingSuperblock">'.$this->view->superblockData->pending.'</td></tr>
					').'
					<tr><td>Network Whitelist Count</td><td id="whiteListCount" class="text-right">'.$this->view->superblockData->whiteListCount.'</td></tr>
				</table>					
			',true).'		
		</div>
		<div class="col-sm-6">
			'.Bootstrap_Callout::info('		
				<h3>Pool Settings</h3>
				<table class="table table-striped table-hover table-condensed rowpad">
					<tr><td>Total Paid Out</td><td class="text-right">'.$this->view->totalPaidOut.' GRC</td></tr>
					<tr><td>Pool Fee</td><td style="text-align:right;">'.$this->view->txFee.' GRC per payout</td></tr>
					<tr><td>Min Pool Payout</td><td style="text-align:right;">'.$this->view->minPayout.' GRC</td></tr>
					<tr><td>Pool Whitelist Count</td><td style="text-align:right;">'.$this->view->poolWhiteListCount.'</td></tr>
				</table>
			',true).'
		</div>
	</div>
	<div class="row rowpad">
		<div class="col-sm-12">
			'.Bootstrap_Callout::info('
				<h3>Pool Details</h3>
				<table class="table table-striped">
					<tr>
						<th>Pool #</th>
						<th>CPID</th>
						<th class="text-center">Stats</th>
						<th class="text-center">Netsoft</th>
						<th class="text-right">Hosts + Projects</th>
						<th class="text-right">Net Mag</th>
						<th class="text-right">Pool Mag</th>
					</tr>
					'.$cpids.'
					'.(count($this->view->mags)>1?'<tr class="text-right"><td></td><td></td><td></td><td></td>
						<td class="text-right"><strong>'.number_format(array_sum($this->view->activeHosts),0).'</td>
						<td class="text-right"><strong>'.number_format(array_sum($this->view->superblockData->mag),0).'</td>
						<td class="text-right"><strong>'.number_format(array_sum($this->view->mags),0).'</td></tr>':''
					).'
				</table>
			',true).'
		</div>
	</div>
');
 $webPage->appendScript('
 	<script>
 		$(\'[data-toggle="tooltip"]\').tooltip();		
 	</script>
');
