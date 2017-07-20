<?php
class GrcPool_Boinc_OutcomeEnum extends Enum {

	const SUCCESS = 'Success';
	const ERROR = 'Error';
	const NOREPLY = 'No Reply';
	const VALIDATIONERROR = 'Valid Error';
	const ABANDONED = 'Abandoned';

	public static function codeToText($code) {
		switch ($code) {
			case 0 : return '-';
			case 1 : return 'Success';
			case 3 : return 'Error';
			case 4 : return 'No Reply';
			case 6 : return 'Valid Error';
			case 7 : return 'Abandoned';
			default : return $code;
		}
	}

}