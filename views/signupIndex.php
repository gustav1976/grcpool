<?php
	
$webPage->appendHead("<script src='https://www.google.com/recaptcha/api.js'></script>");


$webPage->setPageTitle('Pool Registration');

$webPage->append('
	'.Bootstrap_Callout::info('Please take a moment to read how this pool <a href="/about/fees">fees and donations</a>, <a href="/about/calculations">calculations</a>, and <a href="/about/hotWallet">hot wallet</a> works. Thanks...').'	
');

$form = new Bootstrap_Form();
$form->setOnSubmit('return submitSignup();');
$form->setId('poolRegistrationForm');
$form->setAction('/signup');

$input = new Bootstrap_TextInput();
$input->setId('memberEmail');
$input->setLabel('Email Address');
$input->setDefault($this->view->email);
$form->addField($input);

$input = new Bootstrap_TextInput();
$input->setClass('has-error');
$input->setId('memberName');
$input->setLabel('Researcher Name');
$input->setHelp('6 characters minimum');
$input->setDefault($this->view->username);
$form->addField($input);

$input = new Bootstrap_TextInput();
$input->setId('password');
$input->setLabel('Password');
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

$input = new Bootstrap_ReCaptchaInput();
$input->setSiteKey(GOOGLE_RECAPTCHA_PUBLIC);
$form->addField($input);

$form->setButtons(
	'<button id="submitButton" type="submit" class="btn btn-primary">Register</button>'		
);

$webPage->append($form->render());

$webPage->appendScript("
	<script>
		$('#memberEmail').focus();
    	$('#memberName').on('blur', function() {
			if (validateUsername()) {
				$('#memberNameHelp').html('6 characters minimum');
				$.get('/api/isMemberNameAvailable', {name: $(this).val()} )
  				.done(function( data ) {
    				if (data.result) {
						
					} else {
						$('#memberNameGroup').addClass('has-error');
						$('#memberNameHelp').html('username is not available');		
					}
  				});
			}
    	});		
		function submitSignup() {
			var valid = true;
			if (!validateEmail()) valid = false;
			if (!validateUsername()) valid = false;
			if (!validatePassword()) valid = false;
			if (!validateConfirmPassword()) valid = false;
			return valid;
		}
		function isEmail(email) {
			if (email == '') return false; 
    		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    		return re.test(email);
		}
		function validateUsername() {
			$('#memberNameGroup').removeClass('has-error');
			$('#memberNameGroup').removeClass('has-success');
			if (document.getElementById('memberName').value == '' || document.getElementById('memberName').value.length < 6 || document.getElementById('memberName').value > 25) {
				$('#memberNameGroup').addClass('has-error');
				return false;
			} else {
				$('#memberNameGroup').removeClass('has-error');
				return true;
			}			
		}		
		function validatePassword() {
			if (document.getElementById('password').value == '' || document.getElementById('password').value.length < 8 || document.getElementById('password').value > 100) {
				$('#passwordGroup').addClass('has-error');
				return false;
			} else {
				$('#passwordGroup').removeClass('has-error');
				return true;
			}			
		}
		function validateConfirmPassword() {
			if (document.getElementById('confirmPassword').value != '' && document.getElementById('confirmPassword').value == document.getElementById('confirmPassword').value) {
				$('#confirmPasswordGroup').removeClass('has-error');
				return true;
			} else {
				$('#confirmPasswordGroup').addClass('has-error');
				return false;
			}
		}
		function validateEmail() {
			if (isEmail(document.getElementById('memberEmail').value)) {
				$('#memberEmailGroup').removeClass('has-error');
				return true;
			} else {
				$('#memberEmailGroup').addClass('has-error');
				return false;
			}
		}
	</script>
");

