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
define('BOOTSTRAP_TIME_PICKER_INCL','
	<link href="/libs/datepicker/20150924/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
	<script src="/libs/moment/20150924/min/moment.min.js"></script>
	<script src="/libs/datepicker/20150924/build/js/bootstrap-datetimepicker.min.js"></script>
');
if (PHP_OS == 'WINNT') {
	define('SEP','\\');
} else if (PHP_OS == 'Darwin') {
	define('SEP','/');
} else {
	define('SEP','/');
}
define('COIN',100000000);
define('URL_SIGNING_KEY','1024
b0bc5bc6722bd70d333b401ada9a9565c807e775ffc27f124fc54714d5ee7f4f
0ba76ab0cdca1276799c03278c798522dc0054c0db49f118b5755bc7310a56fc
e73144ac178b785f677e320166041f165e777160ceaea2952f5ee4914c875d7b
55d93d4ba3a34663f9921fc95eaddd21408ba1e0f9facce691a3b6b1792107e9
0000000000000000000000000000000000000000000000000000000000000000
0000000000000000000000000000000000000000000000000000000000000000
0000000000000000000000000000000000000000000000000000000000000000
0000000000000000000000000000000000000000000000000000000000010001
.');
require(dirname(__FILE__).'/autoload.php');
$PROPERTY = new Property(Constants::PROPERTY_FILE);
$DATABASE = new Database($PROPERTY->get('databaseUser'),$PROPERTY->get('databasePassword'),$PROPERTY->get('databaseName'),$PROPERTY->get('databaseServer'));
$DATABASE->connect();
$memberDao = new GrcPool_Member_DAO();
$USER = $memberDao->initWithSession();