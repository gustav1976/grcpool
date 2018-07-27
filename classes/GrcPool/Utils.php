<?php
class GrcPool_Utils {
	
	public static function isHuman($value) {
		$property = new Property(Constants::PROPERTY_FILE);
		$curl = curl_init();
		curl_setopt_array($curl, array(
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify?secret='.$property->get('googleCaptchaPrivate').'&response='.$value,
		));
		$gResult = json_decode(curl_exec($curl),true);
		if (!$gResult['success']) {
			return false;
		} else {
			return true;
		}
	}
	
	public static function getVoteWeight($mag,$balance,$moneySupply) {
		return (($mag * ($moneySupply/Constants::GRC_MAG_MULTIPLIER + 0.01)/5.67) + $balance);
	}
	
	public static function calculateMag($hostRac,$projRac,$numberOfProjects,$precision) {
		return Utils::truncate(Constants::GRC_MAG_MULTIPLIER*(($hostRac/$projRac)/$numberOfProjects),$precision);
	}
	
	public static function getMinRac($projRac,$numberOfProjects) {
		return Utils::truncate((Constants::MIN_MAG_MAG_FOR_MIN_RAC * $projRac * $numberOfProjects) / Constants::GRC_MAG_MULTIPLIER,2);
	}
	
	public static function getCpidUrl($cpid) {
		return 'http://www.gridcoinstats.eu/cpid/'.$cpid;
	}
	
	public static function getTxUrl($tx,$currency = Constants::CURRENCY_GRC) {
		if ($currency == Constants::CURRENCY_SPARC) {
			return 'https://etherscan.io/tx/'.$tx;
		} else {
			return 'http://www.gridcoinstats.eu/tx/'.$tx;
		}
	}
	
	public static function getGrcAddressUrl($addr) {
		return 'http://www.gridcoinstats.eu/address/'.$addr;
	}
	
	public static function displayCalculation($str) {
		$str = substr($str,1);
		if (strlen($str) > 200) {
			$str = '<textarea style="width:100%;height:100px;">'.$str.'</textarea>';
		} else {
			$str = str_replace('+','+<br/>',$str);
		}
		return $str;
	}
	
	public static function getDaemonForPool($poolId = 1) {
		$daemon = new GridcoinDaemon();
		$props = Property::getValueFor(Constants::PROPERTY_DAEMONS);
		if (isset($props[$poolId-1])) {
			$daemon->setPath($props[$poolId-1]['path']);
			$daemon->setDataDir($props[$poolId-1]['datadir']);
			if (isset($props[$poolId-1]['testnet']) && $props[$poolId-1]['testnet']) {
				$daemon->setTestnet(true);
			}
		} else {
			return null;
		}
		return $daemon;
	}
	
}