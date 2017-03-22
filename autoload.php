<?php
function globalAutoloader($className) {
	$objPath = dirname(__FILE__);
	$objPaths = explode("_",$className);
	$lastPart = array_pop($objPaths);
	if (count($objPaths) === 0) {
		array_unshift($objPaths,'classes');
		array_push($objPaths,'core');
	} else {
		if ($lastPart === 'DAO') {
			array_unshift($objPaths,'classes');
			$lastPart = '';
		} else if ($lastPart === 'OBJ') {
			array_unshift($objPaths,'classes');
			$lastPart = '';			
		} else if ($lastPart === 'MODEL') {
			array_unshift($objPaths,'models');
			$lastPart = '';
		} else if (strstr($className,'_Controller_')) {
			array_pop($objPaths);
			$objPaths[0] = 'controllers';
		} else {
			array_unshift($objPaths,'classes');
		}
	}
	if ($lastPart != '') {
		array_push($objPaths,$lastPart);
	}
	foreach ($objPaths as $path) {
		$objPath .= DIRECTORY_SEPARATOR.$path;
	}
	$objPath .= '.php';
	//echo $className .' => '.$objPath.'<BR/>';
	if (file_exists($objPath)) {
		require($objPath);
	}
}
spl_autoload_register('globalAutoloader');
require(dirname(__FILE__).'/composer/vendor/autoload.php');