<?php
$webPage->setPageTitle('Pool Login');

$form = new Bootstrap_Form();
$form->setId('poolLoginForm');
$form->setAction('/login');

$input = new Bootstrap_TextInput();
$input->setId('memberName');
$input->setLabel('Researcher Name');
$input->setDefault('');
$form->addField($input);

$input = new Bootstrap_TextInput();
$input->setId('password');
$input->setLabel('Password');
$input->setPassword(true);
$input->setDefault('');
$form->addField($input);

$input = new Bootstrap_TextInput();
$input->setId('token');
$input->setLabel('2FA Token');
$input->setDefault('');
$input->setMaxSize(6);
$input->setPlaceholder('if you have 2fa enabled');
$form->addField($input);

$input = new Bootstrap_CheckboxInput();
$input->setId('rememberMe');
$input->setValue('true');
$input->setLabel('keep me logged in (30 days)');
$form->addField($input);

$form->setButtons('
	<button id="submitButton" type="submit" class="btn btn-primary">Login</button>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="/loginHelp">i need help logging in</a>
		
');

$webPage->append($form->render());