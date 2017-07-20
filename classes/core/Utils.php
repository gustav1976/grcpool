<?php 
class Utils {
	public static function kStyleNumber($value) {
		if ($value > 999 && $value <= 999999) {
			$result = floor($value / 1000) . ' K';
		} elseif ($value > 999999) {
			$result = floor($value / 1000000) . ' M';
		} else {
			$result = $value;
		}
	}
	public static function getTimeAgo($time) {
		$full = false;
		$now = new DateTime;
		$ago = new DateTime(date('m/d/Y H:i:s',$time));
		$diff = $now->diff($ago);
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
		$string = array(
				'y' => 'year',
				'm' => 'month',
				'w' => 'week',
				'd' => 'day',
				'h' => 'hour',
				'i' => 'min',
				's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
	static function isValidEmail($email) {
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		} else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				$isValid = false;
			} else if ($domainLen < 1 || $domainLen > 255) {
				$isValid = false;
			} else if ($local[0] == '.' || $local[$localLen-1] == '.') {
				$isValid = false;
			} else if (preg_match('/\\.\\./', $local)) {
				$isValid = false;
			} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				$isValid = false;
			} else if (preg_match('/\\.\\./', $domain)) {
				$isValid = false;
			} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))) {
				if (!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))) {
					$isValid = false;
				}
			}
			//if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
			// $isValid = false;
			// }
		}
		return $isValid;
	}
	static function truncate($number,$places=0) {
		if (strlen($number) == 0) return '';
		if (strstr($number,'.')) {

			$factor = '1';
			for ($i = 1; $i <= $places; $i++) {
				$factor .= '0';
			}
			$number = floor($number * $factor) / $factor;
		} else {
			if ($places != 0) {
				$number .= '.';
				for ($i = 0; $i < $places; $i++) {
					$number .= '0';
				}
			}
		}
		return $number;
	}
	
}