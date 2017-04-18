<?php
use PragmaRX\Google2FA\Google2FA;
class UserHelper {
	
	private static $ITOA64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	
	public static function authenticate(GrcPool_Member_OBJ $member,$token) {
		if ($member->getTwoFactor()) {
			return UserHelper::validToken($token,$member->getTwoFactorKey());
		} else {
			return UserHelper::passwordHashMatch($token,$member->getPassword());
		}
	}
	
	public static function validToken($token,$key) {
		$google2fa = new Google2FA();
		try {
			 return $google2fa->verifyKey($key,$token);
		} catch (Exception $e) {
			
		}
		return false;
	}
	
	public static function generateRandomString($length) {
		$chars = 'bcdfghjkmnpqrstvwxz';
		$pwd = "";
		$size = rand($length,$length);
		for ($i = 1; $i <= $size; $i++) {
			if (rand(0,1)) {
				$chr = substr($chars,rand(0,strlen($chars)-1),1);
				if (rand(0,1)) {
					$pwd .= strtoupper($chr);
				} else {
					$pwd .= $chr;
				}
			} else {
				$pwd .= rand(1,9);
			}
		}
		return $pwd;	
	}
	
	static private function _hash_encode64($input, $count, &$itoa64) {
		$output = '';
		$i = 0;
		do	{
			$value = ord($input[$i++]);
			$output .= $itoa64[$value & 0x3f];
			if ($i < $count)			{
				$value |= ord($input[$i]) << 8;
			}
			$output .= $itoa64[($value >> 6) & 0x3f];
			if ($i++ >= $count)			{
				break;
			}
			if ($i < $count)			{
				$value |= ord($input[$i]) << 16;
			}
			$output .= $itoa64[($value >> 12) & 0x3f];
			if ($i++ >= $count)			{
				break;
			}
			$output .= $itoa64[($value >> 18) & 0x3f];
		}
		while ($i < $count);
		return $output;
	}

	static private function getUniqueId($extra = 'c') {
		$randSeed = UserHelper::generateRandomString(32);
		$val = $randSeed . microtime();
		$val = md5($val);
		return substr($val, 4, 16);
	}
	
	static private function _hash_crypt_private($password, $setting, &$itoa64) {
		$output = '*';
		if (substr($setting, 0, 3) != '$H$') {
			return $output;
		}
		$count_log2 = strpos($itoa64, $setting[3]);
		if ($count_log2 < 7 || $count_log2 > 30)	{
			return $output;
		}
		$count = 1 << $count_log2;
		$salt = substr($setting, 4, 8);
		if (strlen($salt) != 8) 	{
			return $output;
		}
	
		$hash = md5($salt . $password, true);
		do	{
			$hash = md5($hash . $password, true);
		}	while (--$count);
		$output = substr($setting, 0, 12);
		$output .= UserHelper::_hash_encode64($hash, 16, UserHelper::$ITOA64);
		return $output;
	}
	
	static private function _hash_gensalt_private($input, &$itoa64, $iteration_count_log2 = 6) {
		if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31) {
			$iteration_count_log2 = 8;
		}
		$output = '$H$';
		$output .= $itoa64[min($iteration_count_log2 + ((PHP_VERSION >= 5) ? 5 : 3), 30)];
		$output .= UserHelper::_hash_encode64($input, 6, $itoa64);
		return $output;
	}
	
	static public function encodePassword($password) {
		$random_state = UserHelper::getUniqueId();
		$random = '';
		$count = 6;
		if (($fh = @fopen('/dev/urandom', 'rb')))	{
			$random = fread($fh, $count);
			fclose($fh);
		}
		if (strlen($random) < $count)		{
			$random = '';
			for ($i = 0; $i < $count; $i += 16)			{
				$random_state = md5(UserHelper::getUniqueId() . $random_state);
				$random .= pack('H*', md5($random_state));
			}
			$random = substr($random, 0, $count);
		}
		$hash = UserHelper::_hash_crypt_private($password, UserHelper::_hash_gensalt_private($random, UserHelper::$ITOA64), UserHelper::$ITOA64);
		return $hash;
	}

	static public function passwordHashMatch($password,$inHash) {
		$count_log2 = strpos(UserHelper::$ITOA64,substr($inHash,3,1));
		if ($count_log2 < 7 || $count_log2 > 30) {return null;}
		$count = 1 << $count_log2;
		$salt = substr($inHash, 4, 8);
		if (strlen($salt) != 8)	{return null;}
		$hash = md5($salt . $password, true);
		do {
			$hash = md5($hash . $password, true);
		} while (--$count);
		$output = substr($inHash, 0, 12);
		$output .= UserHelper::_hash_encode64($hash, 16, UserHelper::$ITOA64);
		if ($output == $inHash) {
			return true;
		} else {
			return false;
		}
	}	

}