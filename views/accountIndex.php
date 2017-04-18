<?php
$webPage->setPageTitle('My Account');

foreach ($this->view->messages as $msg) {
	$webPage->append(Bootstrap_Callout::info($msg));
}

$webPage->append('
	<img src="/api/memberMagChart/'.$this->getUser()->getId().'" class="img-responsive"/>
	<img src="/api/memberRacChart/'.$this->getUser()->getId().'" class="img-responsive"/>
');
