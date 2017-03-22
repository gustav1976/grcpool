<?php
$webPage->setPageTitle('Fees and Donations');

$webPage->append('
	<div class="panel panel-default rowpad">
		<div class="panel-heading">
   			<h3 class="panel-title"><i class="fa fa-bitcoin"></i> Fees</h3>
		</div>
		<div class="panel-body">
			The pool is currently operating at a flat payout fee of only <strong>'.$this->view->payoutFee.'</strong> GRC. 
			This rate may be increased depending on the ability to cover network fees. A notice will appear on the home page
			prior to the increase.
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
   			<h3 class="panel-title"><i class="fa fa-bitcoin"></i> Donations</h3>
		</div>
		<div class="panel-body">
			The default donation setting is zero. Any donation amount would be very much appreciated.
			Donations are optional and can be configured from your account page.
			If you would like to directly donate to the support of the pool, please use address: <i class="fa fa-external-link"></i> <a href="'.GrcPool_Utils::getGrcAddressUrl($this->view->donationAddress).'">'.$this->view->donationAddress.'</a>.
		</div>
	</div>
');