<?php
$webPage->setPageTitle('Two Factor Authentication');

if ($this->view->twoFactor) {
	
	$form = new Bootstrap_Form();
	$form->setAction('/account/twoFactorAuth');
	
	$input = new Bootstrap_StaticInput();
	$input->setLabel('');
	$input->setInputSize(8);
	$input->setDefault('	
		Your two factor authentication is currently enabled.
		<br/><br/>
		If you want to disable 2FA, input your account password and the code from your 2FA device.
	');
	$form->addField($input);

	$form->setButtons('
		<input type="hidden" name="cmd" value="disable"/>
		<button id="" class="btn btn-primary type="submit">Disable 2FA Now</button>
	');

	$input = new Bootstrap_TextInput();
	$input->setId('password');
	$input->setLabel('Accont Password');
	$input->setPassword(true);
	$input->setDefault('');
	$form->addField($input);
	
	$input = new Bootstrap_TextInput();
	$input->setId('token');
	$input->setLabel('Token');
	$input->setMaxSize(6);
	$input->setHelp('the code from your 2FA device');
	$input->setDefault('');
	$form->addField($input);
	
	
	$webPage->append('
		'.$form->render().'
	');
	
} else {

	$form = new Bootstrap_Form();
	$form->setAction('/account/twoFactorAuth');

	$input = new Bootstrap_StaticInput();
	$input->setLabel('<i class="fa fa-warning fa-2x"></i>');
	$input->setInputSize(8);
	$input->setDefault('
		For extra security, you can turn on two factor authentication. In order to use 2FA, you need to scan the QR Code below or enter the key into your 2FA device.
		<br/><br/>
		Your code will change each time you enable 2FA. If you disable 2FA, this code below will no longer be valid.
	');
	$form->addField($input);
	
	$input = new Bootstrap_StaticInput();
	$input->setLabel('Key &amp; QR Code');
	$input->setDefault($this->view->key.'<br/><img class="img-responsive" src="'.$this->view->qrCode.'"/>');
	$form->addField($input);
	
	$input = new Bootstrap_StaticInput();
	$input->setInputSize(8);
	$input->setLabel('');
	$form->addField($input);

	$input = new Bootstrap_CheckboxInput();
	$input->setValue('1');
	$input->setId('backedUp');
	$input->setLabel('Before turning on 2FA, write down or print your 2FA code/key above and store it in a safe place. If your 2FA device gets lost, stolen or erased, you will need the code/key to get back into your account! By checking the box, I indicate I have taken the steps to back up my key/code.');
	$form->addField($input);

	$input = new Bootstrap_TextInput();
	$input->setId('password');
	$input->setLabel('Accont Password');
	$input->setPassword(true);
	$input->setDefault('');
	$form->addField($input);
	
	$input = new Bootstrap_TextInput();
	$input->setId('token');
	$input->setLabel('Token');
	$input->setMaxSize(6);
	$input->setHelp('the code from your 2FA device');
	$input->setDefault('');
	$form->addField($input);
	
	
	$form->setButtons('
		<input type="hidden" name="cmd" value="activate"/>
		<button id="" class="btn btn-primary type="submit">Activate 2FA Now</button>
	');
	
	$webPage->append('
		'.$form->render().'
	');
	
}



