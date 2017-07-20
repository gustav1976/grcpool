<?php

$webPage->addBreadcrumb('account','user','/account');

$panel = new Bootstrap_Panel();
$panel->setContext('danger');
$panel->setHeader('Delete My Account');

$panel->setContent('
	<form method="post" action="/account/delete" class="form-inline">
		'.Bootstrap_Callout::error('
			By clicking the button below, you understand your account will be entirely removed from the pool. This action cannot be undone.
			You will lose access to any future GRC you may be awarded if your hosts still have a magnitude. Any remaining GRC will be donated to 
			the pool.<br/><br/>
			<input type="hidden" name="cmd" value="submit"/>
				
			<b>'.($this->view->twoFactor?'Two Factor Token':'Account Password').':</b>
			<input class="form-control" name="password" type="'.($this->view->twoFactor?'text':'password').'"></input>
			<br/><br/>
			<button id="" class="btn btn-danger type="submit">delete my account now</button>	
		').'	
	</form>
');

$webPage->append($panel->render());


