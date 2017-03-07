<?php
require_once(dirname(__FILE__).'/../bootstrap.php');
$data = file_get_contents('php://input');
$api = new BoincApi_Rpc($data,true);
$api->process();
echo $api->getResponseXml();