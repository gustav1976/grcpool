<?php
//$webPage->setPageTitle('My Account');
// <div style="margin-bottom:8px;">
// '.($this->getUser()->getSparcAddress()!=''?'
// 		<i class="text-success fa fa-check"></i>
// 		':'
// 				<i title="please enter a sparc payout address" data-placement="right" class="bs-tooltip text-danger fa fa-warning"></i>').
// 			'
// 						SPARC Address: <a href="/account/payoutAddressSparc">
// 						'.($this->getUser()->getSparcAddress()!=''?
// 				$this->getUser()->getSparcAddress():
// 				'set my address'
// 			).'</a>
// 			</div>
$webPage->appendScript('
	<script>
		$(\'.bs-tooltip\').tooltip();
		$( "#loginNotification" ).click(function() {
			var change = $(this).html() == "on"?0:1;
			$.ajax({
  				type: "POST",
  				url: "/api/loginNotification/"+change,
				success: function(data) {
					$("#loginNotification").html(data.loginEmail==0?"off":"on");
				},
				error: function(data) {  },
				contentType: "application/json",
  				dataType: "json"
			});  			
		});
	</script>	

');
if ($this->view->messages) {
	foreach ($this->view->messages as $msg) {
		$webPage->append('<div style="background-color:white;">');
		$webPage->append(Bootstrap_Callout::error($msg));
		$webPage->append('</div>');
	}
}
$accountPanel = new Bootstrap_Panel();
$accountPanel->setContent('
	<div style="color:#555;">
		<div style="margin-bottom:10px;">
			<div class="pull-right">
				<i class="fa fa-gear fa-2x"></i>
			</div>
			<div><span style="font-size:2.0em;">Account</span></div>
		</div>
		<div style="margin-bottom:8px;">
			'.($this->getUser()->getVerified()?'
				<i class="text-success fa fa-check"></i>
			':'
				<i data-placement="right" title="your email address has not been verified" class="bs-tooltip text-danger fa fa-warning"></i>
			').'
			<a href="/account/passwordEmail">'.$this->getUser()->getEmail().'</a>
		</div>
		<div style="margin-bottom:8px;">
			<i class="fa fa-lock"></i> <a href="/account/passwordEmail">Change Password</a>
		</div>
		<div style="margin-bottom:8px;">
			<i class="fa fa-shield"></i> <a href="/account/twoFactorAuth">Two Factor Authentication</a> 
		</div>
		<div style="margin-bottom:8px;">
			<i class="fa fa-power-off"></i> Login Notification (<a id="loginNotification" href="javascript:void(0);">'.($this->getUser()->getLoginEmail()?'on':'off').'</a>)
		</div>
		<div style="margin-bottom:8px;">
			<i class="fa fa-trash"></i> <a href="/account/delete">Delete Account</a> 
		</div>		
	</div>
');
$payoutPanel = new Bootstrap_Panel();
$payoutPanel->setContent('
	<div style="color:#555;">
		<div class="pull-right text-success text-right">
			<a style="font-size:2.5em;" class="text-success" href="/account/payouts">'.$this->view->totalPaid.'</a>
			'.($this->view->totalPaidSparc?'<small><br/>'.$this->view->totalPaidSparc.' SPARC</small>':'').'
		</div>
		<div style=""><span style="font-size:2.0em;">Earnings</span></div>
		<br clear="all"/>

		<div style="margin-bottom:8px;">
			'.($this->getUser()->getGrcAddress()!=''?'
				<i class="text-success fa fa-check"></i>
			':'
				<i title="please enter a grc payout address" data-placement="right" class="bs-tooltip text-danger fa fa-warning"></i>').
			'
			GRC Address: <a href="/account/payoutAddress">
			'.($this->getUser()->getGrcAddress()!=''?
				$this->getUser()->getGrcAddress():
				'set my address'
			).'</a>
		</div>
		
		<div style="margin-bottom:8px;">
			<i class="fa fa-bank"></i>
			Owed: <a href="/account/owed">'.number_format($this->view->owed,3).' GRC</a>, &nbsp;
			<a href="/about/sparc" title="'.number_format($this->view->sparc,8).'">'.number_format($this->view->sparc,3).' SPARC</a>
		</div>		
		
		'.($this->view->orphans?'
			<div style="margin-bottom:8px;">
				<i class="fa fa-chain-broken"></i> Orphans:
				<a href="/account/orphans">
					'.count($this->view->orphans).' for
					'.number_format($this->view->orphansOwed,2).' GRC &amp;
					'.number_format($this->view->orpphansSparcOwed,2).' SPARC
				</a>
			</div>':''
		).'

		<div style="margin-bottom:8px;">
			<i class="fa fa-balance-scale"></i> Minimum Payout: <a href="/account/payoutAddress#minpayout">'.$this->getUser()->getMinPayout().' GRC</a>
		</div>
		
		
		<div style="margin-bottom:8px;">
			'.($this->getUser()->getDonation()==0?'
				<i data-placement="right" title="help support the pool" class="bs-tooltip text-danger fa fa-warning"></i>
			':'
				<i class="text-success fa fa-check"></i>
			').'
			Pool Donation:
			<a href="/account/payoutAddress#donation">'.$this->getUser()->getDonation().'%</a>
		</div>
		
		<div class="pull-right">
			<a href="/account/payouts">
				<span class="fa-stack fa-2x">
					<i class="text-primary fa fa-circle fa-stack-2x"></i>
					<i class="fa fa-arrow-right fa-stack-1x fa-inverse"></i>
				</span>
			</a>
		</div>	
	</div>
');

$hostPanel = new Bootstrap_Panel();
$hostPanel->setContent('
	<a style="color:#555;" href="/account/hosts">
		<div class="pull-right">
			<i class="fa fa-tasks fa-2x"></i>
		</div>
		<div style="margin-bottom:15px;"><span style="font-size:2.0em;">Hosts</span></div>

		<div style="margin-bottom:8px;">
			<i class="fa fa-desktop"></i> Number of Hosts: '.$this->view->numberOfHosts.'
		</div>
		<div style="margin-bottom:8px;">
			<i class="fa fa-dashboard"></i> Magnitude: '.number_format($this->view->totalMag,2).'
		</div>		
		<div style="margin-bottom:8px;">
			<i class="fa fa-bitcoin"></i> Estimated Earnings: '.(Utils::truncate($this->view->totalMag*$this->view->magUnit,3)).' GRC/day
		</div>

		<div class="pull-right">
			<a href="/account/hosts">
				<span class="fa-stack fa-2x">
					<i class="text-primary fa fa-circle fa-stack-2x"></i>
					<i class="fa fa-arrow-right fa-stack-1x fa-inverse"></i>
				</span>
			
		</div>
	</a>	
');
$taskPanel = null;
if ($this->view->numberOfTasks) {
	$taskPanel = new Bootstrap_Panel();
	$taskPanel->setContent('
		<a style="color:#555;" href="/account/tasks">
			<div class="pull-right">
				<i class="fa fa-files-o fa-2x"></i>
			</div>
			<div style="margin-bottom:15px;"><span style="font-size:2.0em;">Task Report</span></div>
			<div style="margin-bottom:8px;">
				<div style="margin-bottom:8px;"><i class="fa fa-file-o"></i> Number of Tasks: '.$this->view->numberOfTasks.'</div>
				<div class="row">
					<div class="col-xs-3 col-sm-3 col-md-5 col-lg-4">
						'.$this->view->taskGraph->Fetch('PieGraph').'
					</div>
				</div>
			</div>
			<div class="pull-left"><small>Tasks reports are only available for WCG. For other projects, use the links on project names.</small></div>
			<div class="pull-right">
				<span class="fa-stack fa-2x">
					<i class="text-primary fa fa-circle fa-stack-2x"></i>
					<i class="fa fa-arrow-right fa-stack-1x fa-inverse"></i>
				</span>
			</div>
		</a>
	');
}
$webPage->append('
	<div class="row">
		<div class="col-md-6">
			'.$accountPanel->render().'
			'.$payoutPanel->render().'
		</div>
		<div class="col-md-6">
			'.$hostPanel->render().'
			'.($taskPanel?$taskPanel->render():'').'
		</div>
	</div>
');

$tabs = new Bootstrap_Tabs();
$tab = new Bootstrap_Tab();
$tab->setActive(true);
$tab->setTitle('Total Mag');
$tab->setContent('
	<div style="background-color:white;">
		<div class="embed-responsive embed-responsive-16by9">
			<iframe class="embed-responsive-item" src="/chart/memberTotalMagChart/'.$this->getUser()->getId().'"></iframe>
		</div>
		<div class="pull-right"><a href="/chart/memberTotalMagChart/'.$this->getUser()->getId().'">full screen &raquo;</a></div>
	</div>
');
$tabs->addTab($tab);
$tab = new Bootstrap_Tab();
$tab->setActive(false);
$tab->setTitle('Mag');
$tab->setContent('
	<div style="background-color:white;">
		<div class="embed-responsive embed-responsive-16by9">
			<iframe class="embed-responsive-item" src="/chart/memberMagChart/'.$this->getUser()->getId().'"></iframe>
		</div>
		<div class="pull-right"><a href="/chart/memberMagChart/'.$this->getUser()->getId().'">full screen &raquo;</a></div>
	</div>
');
$tabs->addTab($tab);
$tab = new Bootstrap_Tab();
$tab->setActive(false);
$tab->setTitle('Rac');
$tab->setContent('
	<div style="background-color:white;">
		<div class="embed-responsive embed-responsive-16by9">
			<iframe class="embed-responsive-item" src="/chart/memberRacChart/'.$this->getUser()->getId().'"></iframe>
		</div>
		<div class="pull-right"><a href="/chart/memberRacChart/'.$this->getUser()->getId().'">full screen &raquo;</a></div>
	</div>
');
$tabs->addTab($tab);
$tab = new Bootstrap_Tab();
$tab->setActive(false);
$tab->setTitle('Earnings');
$tab->setContent('
	<div style="background-color:white;">
		<div class="pull-right">
			<button onclick="$(\'#earningsChart\').attr(\'src\',\'/chart/memberEarningsChart/'.$this->getUser()->getId().'/day\')" class="btn btn-link">day</button>
			<button onclick="$(\'#earningsChart\').attr(\'src\',\'/chart/memberEarningsChart/'.$this->getUser()->getId().'/week\')" class="btn btn-link">week</button>
		</div>
		<br clear="all"/>
		<div class="embed-responsive embed-responsive-16by9">	
			<iframe id="earningsChart" class="embed-responsive-item" src="/chart/memberEarningsChart/'.$this->getUser()->getId().'/day"></iframe>
		</div>
		<div class="pull-right"><a href="/chart/memberEarningsChart/'.$this->getUser()->getId().'">full screen &raquo;</a></div>
	</div>
');
$tabs->addTab($tab);
$webPage->append('
	<div class="row">
		<div class="col-xs-12">
			'.$tabs->render().'
		</div>
	</div>
');