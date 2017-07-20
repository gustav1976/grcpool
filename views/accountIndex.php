<?php
//$webPage->setPageTitle('My Account');
$webPage->appendScript('
	<script>$(\'.bs-tooltip\').tooltip();</script>	
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
			<i class="fa fa-trash"></i> <a href="/account/delete">Delete Account</a> 
		</div>		
	</div>
');
$payoutPanel = new Bootstrap_Panel();
$payoutPanel->setContent('
	<div style="color:#555;">
		<div class="pull-right text-success" style="font-size:2.5em;"><a class="text-success" href="/account/payouts">'.$this->view->totalPaid.'</a></div>
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
			<i class="fa fa-bank"></i> Owed: <a href="/account/payouts">'.number_format($this->view->owed,3).' GRC</a>
		</div>		
		
		'.($this->view->orphans?'
			<div style="margin-bottom:8px;">
				<i class="fa fa-chain-broken"></i> Orphans: <a href="/account/orphans">'.count($this->view->orphans).' for '.number_format($this->view->orphansOwed,2).'</a>
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
			<div style="margin-bottom:15px;"><span style="font-size:2.0em;">Tasks</span></div>
			<div style="margin-bottom:8px;">
				<div style="margin-bottom:8px;"><i class="fa fa-file-o"></i> Number of Tasks: '.$this->view->numberOfTasks.'</div>
				<div class="row">
					<div class="col-xs-3 col-sm-3 col-md-5 col-lg-4">
						'.$this->view->taskGraph->Fetch('PieGraph').'
					</div>
				</div>
			</div>
			<div class="pull-left"><small>Tasks are only available for WCG</small></div>
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

$webPage->append('
	<div class="row">
		<div class="col-xs-12">
			<img src="/api/memberMagChart/'.$this->getUser()->getId().'" class="img-responsive"/>
			<br/><br/>
			<img src="/api/memberRacChart/'.$this->getUser()->getId().'" class="img-responsive"/>
		</div>
	</div>
');
