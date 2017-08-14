<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../bootstrap.php');

$cache = new Cache();

$webSocket = new WebSocket();
$result = array();

try {
	$grcValue = CryptoHelper::getPoloniexGRCValue();
	if ($grcValue != null) {
		$result['poloniex'] = $grcValue;
		echo 'POLO: '.$grcValue."  ";
		$cache->set($grcValue,Constants::CACHE_POLONIEX_GRC_VALUE);
	}
} catch (Exception $e) {
	error_log('getPriceData Poloniex error');
}

$grcValue = CryptoHelper::getCEXGRCValue();
if ($grcValue != null) {
	$result['cex'] = $grcValue;
	echo 'CEX'.$grcValue."  ";
	$cache->set($grcValue,Constants::CACHE_CEX_GRC_VALUE);
}

$btcPrice = CryptoHelper::getCoinbaseBTCValue();
if ($btcPrice != null) {
	$result['coinbase'] = $btcPrice;
	echo 'COIN: '.$btcPrice."  ";
	$cache->set($btcPrice,Constants::CACHE_COINBASE_BTC_VALUE);
}

$webSocket->updateTicker($result);
