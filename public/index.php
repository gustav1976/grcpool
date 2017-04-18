<?php
require_once(dirname(__FILE__).'/../bootstrap.php');
$router = new GrcPool_Router($_SERVER['REQUEST_URI']);
$router->dispatch();