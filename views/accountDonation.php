<?php
$webPage->setPageTitle('Donation Amount');

$form = new Bootstrap_Form();
$form->setAction('/account/donation');

$input = new Bootstrap_StaticInput();
$input->setInputSize(8);
$input->setDefault('If you feel like giving a little back to support the pool\'s efforts, you can give as little as .01%. You can also see how much GRC the pool is making on the <a href="/report/poolBalance">financial report</a>.');
$form->addField($input);

$input = new Bootstrap_TextInput();
$input->setLabel('Donation Amount');
$input->setId('donation');
$input->setDefault($this->view->donation);
$input->setButtonAddon('%');
$input->setHelp('valid range: 0.01-100.00');
$form->addField($input);

$form->setButtons('
	<input type="hidden" name="cmd" value="donation"/>
	<button id="" class="btn btn-primary type="submit">update</button>
');

$webPage->append('
	'.$form->render().'
');

