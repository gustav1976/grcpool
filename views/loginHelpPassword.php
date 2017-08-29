<?php

$webPage->setPageTitle('Login Help - New Password');

if ($this->view->showForm) {

	$form = new Bootstrap_Form();
	
	$input = new Bootstrap_TextInput();
	$input->setId('password');
	$input->setLabel('new password');
	$input->setHelp('8 characters minimum');
	$input->setPassword(true);
	$input->setDefault('');
	$form->addField($input);
	
	$input = new Bootstrap_TextInput();
	$input->setId('confirmPassword');
	$input->setLabel('confirm password');
	$input->setPassword(true);
	$input->setDefault('');
	$form->addField($input);
	
	$input = new Bootstrap_SelectInput();
	$input->setLabel('BOINC Password');
	$input->setId('boinc');
	$input->setOptions(array('0'=>'I do not want to change my BOINC password','1'=>'I want to also change my BOINC password'));
	$form->addField($input);
	
	$input = new Bootstrap_StaticInput();
	$input->setDefault('
		When you used BOINC to add grcpool as an account manager, you used your username and password to make the connection. 
		BOINC has stored your password internally which allows it to contact the pool without you needing to enter your password every time.
		If you are just trying to reset your password for the website, you have the option only changing it for the website login and not BOINC.
		This will allow BOINC to continue operating as it has. If you need to change the password for both of them, you will need to reconnect
		your BOINC to the pool with the new password.
	');
	$form->addField($input);
	
	if ($this->view->twoFactor) {
		$input = new Bootstrap_TextInput();
		$input->setLabel('Two Factor Token');
		$input->setId('authorization');
		$input->setPlaceholder('######');
		$form->addField($input);
	}
	
	$form->setButtons(
		'<button id="submitButton" type="submit" class="btn btn-primary">Change My Password</button>'
	);
	
	$webPage->append('
		'.$form->render().'
	');

}


