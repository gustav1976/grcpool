<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../bootstrap.php');
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ SESSIONCLEANUP START ".date("Y.m.d H.i.s")."\n";

$sessionDao = new GrcPool_Session_DAO();
$sessionDao->cleanup();

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ SESSIONCLEANUP END ".date("Y.m.d H.i.s")."\n";
