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
		'.Bootstrap_Callout::error('The pool wallet is currently offline, please check the facebook page for status updates.').'		
	':'').'
	<div class="row rowpad" style="display:flex;align-items: center;font-family: \'Exo 2\', sans-serif;">
		<div style="font-size:1.5em;" class="col-sm-5 col-xs-6 text-right">
			<a href="https://www.facebook.com/gridcoinpool/">
				<span class="fa-stack fa-lg">
			  		<i style="color:black;" class="fa fa-circle fa-stack-2x"></i>
			  		<i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
				</span>
			</a>			
		</div>
		<div style="font-size:2em;" class="col-sm-2 hidden-xs text-center"><img class="img-responsive" src="/assets/images/gpLogo.png"/></div>
		<div style="font-size:1.5em;" class="col-sm-5 col-xs-6">
			<a href="https://www.youtube.com/c/GridcoinPool">
				<span class="fa-stack fa-lg">
			  		<i style="color:black;" class="fa fa-circle fa-stack-2x"></i>
			  		<i class="fa fa-youtube fa-stack-1x fa-inverse"></i>
				</span>
			</a>			
		</div>
	</div>
		
	<div class="row">
		<div class="col-sm-6 rowpad">
			<div class="embed-responsive embed-responsive-16by9" style="border:1px solid #ccc;"><iframe class="embed-responsive-item" width="560" height="315" src="https://www.youtube.com/embed/79rTDNjdOf8" frameborder="0" allowfullscreen></iframe></div>
		</div>
		<div class="col-sm-6 rowpad">
			<div class="embed-responsive embed-responsive-16by9" style="border:1px solid #ccc;"><iframe class="embed-responsive-item" width="560" height="315" src="https://www.youtube.com/embed/FjVjqZnhMZA" frameborder="0" allowfullscreen></iframe></div>
		</div>
	</div>
');
$webPage->append('
	<div class="row rowpad">
		<div class="col-sm-6">
			'.Bootstrap_Callout::info('
				<h3>Gridcoin Client</h3>
				<table class="table table-striped table-hover table-condensed">
					<tr><td>Version</td><td style="text-align:right;" id="version"></td></tr>
					<tr><td>Last Superblock</td><td style="text-align:right;" id="lastSuperblock"></td></tr>
					<tr><td>Superblock Age</td><td style="text-align:right;" id="superblockAge"></td></tr>
					<tr><td>Pending Superblock</td><td style="text-align:right;" id="pendingSuperblock">---</td></tr>
					<tr><td>Pool Magnitude</td><td id="poolMag" style="text-align:right;"></td></tr>
					<tr><td>Whitelisted Projects</td><td id="whiteListCount" class="text-right"></td></tr>
				</table>					
			',true).'		
		</div>
		<div class="col-sm-6">
			'.Bootstrap_Callout::info('		
				<h3>Pool Details</h3>
				<table class="table table-striped table-hover table-condensed rowpad">
					<tr><td>Pool CPID</td><td style="text-align:right;">
						'.$this->view->cpid.'<br/>
						<i class="fa fa-external-link"></i> <a href="'.GrcPool_Utils::getCpidUrl($this->view->cpid).'">gridcoinstats.eu</a>
						|
						<i class="fa fa-external-link"></i> <a href="http://boinc.netsoft-online.com/e107_plugins/boinc/get_user.php?cpid='.$this->view->cpid.'&format=xml">Netsoft</a>
					
					</td></tr>					
					<tr><td>Hosts Total Magnitude</td><td style="text-align:right;">'.$this->view->totalMag.'</td></tr>					
					<tr><td>Total Paid Out</td><td class="text-right">'.$this->view->totalPaidOut.' GRC</td></tr>
					<tr><td>Pool Fee</td><td style="text-align:right;">'.$this->view->txFee.' GRC per payout</td></tr>
					<tr><td>Min Pool Payout</td><td style="text-align:right;">'.$this->view->minPayout.' GRC</td></tr>
					<tr><td>Min POR Balance <a href="#" data-toggle="tooltip" title="This is the minimum balance from POR needed to update the amount owed."><i style="color:black;" class="fa fa-info-circle"></i></a></td><td style="text-align:right;">'.$this->view->minStake.' GRC</td></tr>
					<tr><td>Number of Active Hosts</td><td style="text-align:right;">'.$this->view->numberOfActiveHosts.'</td></tr>
				</table>
				<div class="text-right"><a href="/report/poolBalance">pool financials &raquo;</a></div>
			',true).'
		</div>
	</div>
');
$webPage->appendScript('
	<script>
		$.get( "/api/superBlockAge", function( data ) {
			$("#version").html(data.version);
			$("#lastSuperblock").html(data.block);
			$("#superblockAge").html(data.ageText);
			$("#poolMag").html(data.mag);
			$("#whiteListCount").html(data.whiteListCount);
			if (data.pending) {
				$("#pendingSuperblock").html(data.pending);
			}
		});
		$(\'[data-toggle="tooltip"]\').tooltip();		
	</script>
');