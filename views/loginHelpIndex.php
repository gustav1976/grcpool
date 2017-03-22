<?php

$webPage->appendHead("<script src='https://www.google.com/recaptcha/api.js'></script>");

$webPage->setPageTitle('Login Help');

$form = new Bootstrap_Form();
$form->setAction('/loginHelp');

$input = new Bootstrap_TextInput();
$input->setId('username');
$input->setLabel('username');
$form->addField($input);

$input = new Bootstrap_ReCaptchaInput();
$input->setSiteKey(GOOGLE_RECAPTCHA_PUBLIC);
$form->addField($input);

$form->setButtons(
	'<button id="submitButton" type="submit" class="btn btn-primary">Send Me the Link</button>'
);

$webPage->append('
	If you have forgotten your password, you can reset your password from here. You will receive an email with a link, which will authorize you to create a new password.
	<br/><br/>
	You will have the option of also changing the password BOINC is using to access the account manager portion of this site when you enter in your new password.
	<br/><br/><br/>
	'.$form->render().'
');




