<?php
require_once(dirname(__FILE__).'/../bootstrap.php');
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ POLLS START ".date("Y.m.d H.i.s")."\n";

$settingsDao = new GrcPool_Settings_DAO();
if ($settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE";
	exit;
}

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ POLLS END ".date("Y.m.d H.i.s")."\n";
