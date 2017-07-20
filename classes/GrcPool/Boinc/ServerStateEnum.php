<?php
class GrcPool_Boinc_ServerStateEnum extends Enum {

	const INPROGRESS = 'In Progress';
	const REPORTED = 'Reported';

	public static function codeToText($code) {
		switch ($code) {
			case 4 : return 'In Progress';
			case 5 : return 'Reported';
			default : return $code;
		}
	}

}