<?php
if(php_sapi_name() != 'cli') {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}
mb_language('uni');
mb_internal_encoding('UTF-8');
date_default_timezone_set("CST6CDT");
define('COIN',100000000);
require(dirname(__FILE__).'/autoload.php');
$DATABASE = new Database(Property::getValueFor('databaseUser'),Property::getValueFor('databasePassword'),Constants::DATABASE_NAME,Constants::DATABASE_SERVER);
$DATABASE->connect();
$memberDao = new GrcPool_Member_DAO();
$USER = $memberDao->initWithSession();