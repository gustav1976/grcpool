<?php
class CryptoHelper {
	public static function getCoinbaseBTCValue() {
		try {

			$url = "https://api.gdax.com/products/BTC-USD/trades?limit=1";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			$json = json_decode($result,true);
			//$json = json_decode(file_get_contents('https://api.gdax.com/products/BTC-USD/trades?limit=1'),true);
			$result = number_format($json[0]['price'],2);
			if ($result) {
				return $result;
			} else {
				return null;
			}			
		} catch (Exception $e) {
			return null;
		}
	}
	public static function getCEXGRCValue() {
		try {
			$json = json_decode(file_get_contents('https://c-cex.com/t/grc-btc.json'),true);
			$json = $json['ticker'];
			if (isset($json['lastprice']) && $json['lastprice'] > 0) {
				return number_format($json['lastprice'],8);
			} else {
				return null;
			}
		} catch (Exception $e) {
			return null;
		}		
	}
	public static function getPoloniexGRCValue() {
		try {
			$json = json_decode(file_get_contents('https://poloniex.com/public?command=returnTicker'),true);
			if (isset($json['BTC_GRC'])) {
				$ticker = $json['BTC_GRC'];
				if (isset($ticker['last']) && $ticker['last'] > 0) {
					return number_format($ticker['last'],8,'.','');
				} else {
					return null;
				}
			}
		} catch (Exception $e) {
			return null;
		}
	}
}
