<?php
$webPage->appendTitle('Equations and Calculations');
$webPage->append('		
		
	<div class="panel panel-default">
		<div class="panel-heading">
   			<h3 class="panel-title"><i class="fa fa-signal"></i> Magnitudes</h3>
		</div>
		<div class="panel-body">
		
			At regular intervals throughout a day, the average credit for team Gridcoin is gathered from each project (TRAC). 
			Also the average credit for all hosts on the pool is gathered from the project (HRAC). 
			The final parameter needed to create a host magnitude is the number of whitelisted projects (W). This value is pulled 
			from the gridcoin daemon using <i>list projects</i>. Therefore, a hosts magnitude calculation for each project is:<br/>
			<br/>
			<i>HMAG = '.Constants::GRC_MAG_MULTIPLIER.' * ( ( HRAC / TRAC ) / W )</i>
			<br/><br/>
			The magnitude computed from the Neural Net will differ from this pool calculation due to the timing of events.
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
   			<h3 class="panel-title"><i class="fa fa-bitcoin"></i> Wallet Basis</h3>
		</div>
		<div class="panel-body">
			Before moving onto the amount owed using the host magnitude, we need to determine the wallet basis to get an available payout amount.
			The wallet\'s beginning basis was '.$this->view->seed.' GRC. The wallet basis increases overtime as it collects fees, donations, and interest. 
			The wallet basis may be adjusted overtime to allow for a buildup of fees, donations, or to adjust the amount of staking. These adjustments
			will appear on the website payout reporting. Interest for the basis is obtained by using <i>list rsa</i>. Fees and donations increment the stored wallet basis at runtime during payouts.
			Therefore, the wallet basis is represented via:<br/>
			<br/>
			<i>BASIS = '.$this->view->seed.' + INTEREST + DONATION + FEES</i>
			<br/>
		</div>
	</div>		
		
	<div class="panel panel-default">
		<div class="panel-heading">
   			<h3 class="panel-title"><i class="fa fa-calculator"></i> Amount Owed</h3>
		</div>
		<div class="panel-body">	
			Now that we have the basis and host magnitude we can calculate the amount of GRC a host is owed. To compute this, we need 
			to gather two more data points, total pool mag and the wallet balance. 
			The total pool\'s magnitude (PMAG), is the combined magnitude of all hosts, SUM(HMAG). The hot wallet\'s balance is obtained by 
			using <i>getinfo</i> via the daemon and totaling the balance and stake fields. Therefore, the OWED equation is represented by:<br/>
			<br/>
			<i>OWED = ( HMAG / PMAG ) * ( BALANCE - BASIS )</i>
			<br/>
		</div>
	</div>		
		
	<div class="panel panel-default">
		<div class="panel-heading">
   			<h3 class="panel-title"><i class="fa fa-money"></i> Payouts</h3>
		</div>
		<div class="panel-body">
			A pool payout will be triggered at least once a day. If your owed balance meets a minimum threshold you will receive GRC to your payout address.
			The payout amount is determined by taking the amount you are owed (above) and subtracting a flat fee and any donation amount you may have specified.
			The equation for this looks like:<br/>
			<br/>
			<i>PAYOUT = OWED - FEE - ( OWED * DONATION / 100 )</i>
			<br/>
		</div>
	</div>		
');