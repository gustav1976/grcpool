<?php
class GrcPool_Boinc_ValidationEnum extends Enum {

	const PENDING = 'Pending';
	const VALID = 'Valid';
	const INVALID = 'Invalid';
	const VERIFYING = 'Verifying';
	const DEADLINE = 'Deadline';

	public static function codeToText($code) {
		switch ($code) {
			case 0 : return GrcPool_Boinc_ValidationEnum::PENDING;
			case 1 : return GrcPool_Boinc_ValidationEnum::VALID;
			case 2 : return GrcPool_Boinc_ValidationEnum::INVALID;
			case 4 : return GrcPool_Boinc_ValidationEnum::VERIFYING;
			case 5 : return GrcPool_Boinc_ValidationEnum::DEADLINE;
			default : return $code;
		}
	}
	
	 public static function keyToCode($key) {
	 	switch ($key) {
	 		case 'PENDING' : return 0;
	 		case 'VALID' : return 1;
	 		case 'INVALID' : return 2;
	 		case 'VERIFYING' : return 4;
	 		case 'DEADLINE' : return 5;
	 		default : return key;
	 	}
	 }

}