<?php
class Constants {
	
	const CACHE_COINBASE_BTC_VALUE = "CACHE_COINBASE_BTC_VALUE";
	const CACHE_CEX_GRC_VALUE = "CACHE_CEX_GRC_VALUE";
	const CACHE_POLONIEX_GRC_VALUE = "CACHE_POLONIEX_GRC_VALUE";
	const CACHE_LAST_SUPERBLOCK_HEIGHT = "CACHE_LAST_SUPERBLOCK_HEIGHT";
	const CACHE_SUPERBLOCK_DATA = "CACHE_SUPERBLOCK_DATA";
	
	const GRC_MAG_MULTIPLIER = 115000;
	const MIN_MAG_MAG_FOR_MIN_RAC = .01;
	
	const COIN = 100000000;
	
	const PAYOUT_LOCK_FILE = 'payout.lock';

	const SETTINGS_TOTAL_PAID_OUT = "TOTAL_PAID_OUT";
	const SETTINGS_CPID = 'CPID';
	const SETTINGS_MIN_OWE_PAYOUT = 'MIN_OWE_PAYOUT';
	const SETTINGS_PAYOUT_FEE = 'PAYOUT_FEE';
	const SETTINGS_SEED = 'SEED';
	const SETTINGS_GRC_CLIENT_ONLINE = 'GRC_CLIENT_ONLINE';
	
	const PAYOUT_ERROR_NO_GRC_ADDR = 'No GRC Address';
	const PAYOUT_ERROR_MIN_NOT_MET = 'Minimum owed payout not met';
	const PAYOUT_ERROR_NO_MIN_AMOUNT = 'No minimum specified';
	const PAYOUT_ERROR_MIN_CALC_AMOUNT_NOT_MET = 'Mimimum calculated amount to send not met';
	
	const API_ERROR_001 = 'Please provide a longer username, at least 6 characters';
	const API_ERROR_002 = 'Username already exists';
	const API_ERROR_003 = 'Email address is not valid';
	const API_ERROR_004 = 'Email address already exists';
	const API_ERROR_005 = 'Please provide a longer password, at least 8 characters';
	const API_ERROR_006 = 'Researcher not found';
	
	
}
	