<?php
require_once(dirname(__FILE__).'/../bootstrap.php');
$projectConfig = new BoincApi_ProjectConfig();
echo $projectConfig->toXml();
