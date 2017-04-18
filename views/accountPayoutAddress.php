<?php
$webPage->setPageTitle('GRC Payout Address');

if ($this->view->clientOn == '1') {

	$form = new Bootstrap_Form();
	$form->setAction('/account/payoutAddress');
	
	$input = new Bootstrap_StaticInput();
	$input->setInputSize(8);
	$input->setDefault('
		<div class="rowpad">
			In order to receive GRC for your research, you need to have a Gridcoin address. You can find your address in the Gridcoin client and selecting &quot;Receive Coins&quot;.
		</div>
		<div class="rowpad">
			You will need to authorize this change with your password or two factor authentication token. An email will be sent to the email address on file when the address is changed.
		</div>		
	');
	$form->addField($input);
	
	$input = new Bootstrap_TextInput();
	$input->setLabel('GRC Address');
	$input->setId('grcAddress');
	$input->setDefault($this->view->grcAddress);
	$form->addField($input);
	
	$input = new Bootstrap_TextInput();
	$input->setLabel($this->view->twoFactor?'Two Factor Token':'Password');
	$input->setPassword($this->view->twoFactor);
	$input->setPassword(!$this->view->twoFactor);
	$input->setId('password');
	$form->addField($input);
	
	$form->setButtons('
		<input type="hidden" name="cmd" value="grcAddress"/>
		<button id="" class="btn btn-primary type="submit">update</button>	
	');
	
	$webPage->append('
		'.$form->render().'	
	');

} else {
	$webPage->append(Bootstrap_Callout::error('The Gridcoin hot wallet is currently offline. The client is used to verify GRC addresses. Please return when the client is online. You may want to follow the facebook page to monitor progress.'));
}