<?php
$webPage->setPageTitle('Hot Wallet');

$webPage->append('
	<div class="panel panel-default">
		<div class="panel-heading">
   			<h3 class="panel-title"><i class="fa fa-fire"></i> Pool Hot Wallet</h3>
		</div>
		<div class="panel-body">
			The pool\'s hot wallet address is: <i class="fa fa-external-link"></i> <a href="'.GrcPool_Utils::getGrcAddressUrl($this->view->hotWalletAddress).'">'.$this->view->hotWalletAddress.'</a> 
			This wallet maintains the magnitude, receives the proof of research awards, and interest payments.
			Also the wallet handles the payouts to pool members. 
			The pool was funded initially with '.$this->view->seed.' GRC (gridcoinstats.eu may not reflect the total balance).
			This should allow for more frequent staking and regular payouts.
			<br/><br/>
			If you send coins to the hot wallet, you will effectively &quot;rain&quot; coins on the hosts in the pool.
			This is because payout amount is determined based on the <a href="/about/calculations">wallet balance, minus the basis and interest</a>.
			Also any project rain received into the hot wallet will be distributed to the hosts in the pool regardless of the project they are crunching on.
		</div>
	</div>
		
');