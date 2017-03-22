<?php

$webPage->setPageTitle('Change Password or Email');



$form = new Bootstrap_Form();

$input = new Bootstrap_StaticInput();
$input->setInputSize(8);
$input->setDefault('
	You can use this form to change either your password OR email address. Just fill in the parts you need to change, and authorize your change with your password or token. Note there are some special
	situations with changing your password, so be sure to read that below.
');
$form->addField($input);


$input = new Bootstrap_TextInput();
$input->setId('emailAddress');
$input->setLabel('Email Address');
$input->setDefault($this->view->emailAddress);
$form->addField($input);

$input = new Bootstrap_TextInput();
$input->setId('password');
$input->setLabel('New Password');
$input->setHelp('8 characters minimum');
$input->setPassword(true);
$input->setDefault('');
$form->addField($input);

$input = new Bootstrap_TextInput();
$input->setId('confirmPassword');
$input->setLabel('Confirm Password');
$input->setPassword(true);
$input->setDefault('');
$form->addField($input);

$input = new Bootstrap_SelectInput();
$input->setLabel('BOINC Password');
$input->setId('boinc');
$input->setOptions(array('0'=>'I do not want to change my BOINC password','1'=>'I want to also change my BOINC password'));
$form->addField($input);

$input = new Bootstrap_StaticInput();
$input->setInputSize(8);
$input->setDefault('
	<em>When you used BOINC to add grcpool as an account manager, you used your username and password to make the connection. 
	BOINC has stored your password internally which allows it to contact the pool without you needing to enter your password every time.
	If you are just trying to reset your password for the website, you have the option only changing it for the website login and not BOINC.
	This will allow BOINC to continue operating as it has. If you need to change the password for both of them, you will need to reconnect
	your BOINC to the pool with the new password.</em>
');
$form->addField($input);

$input = new Bootstrap_TextInput();
$input->setLabel($this->view->twoFactor?'Two Factor Token':'Current Password');
$input->setPassword(!$this->view->twoFactor);
$input->setId('authorization');
$input->setPlaceholder('authorize these changes');
$form->addField($input);

$form->setButtons('
	<button id="submitButton" type="submit" class="btn btn-primary">Update Email or Password</button>
	<input type="hidden" name="cmd" value="submit"/>
');

$webPage->append('
	'.$form->render().'
');




