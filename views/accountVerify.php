<?php

$webPage->addBreadcrumb('account','user','/account');

$webPage->setPageTitle('Email Verification');

$webPage->append('
	'.Bootstrap_Callout::info('An email verification link has been sent to your email address, '.$this->getUser()->getEmail().'. This verification request is only valid for 24 hours.').'
');