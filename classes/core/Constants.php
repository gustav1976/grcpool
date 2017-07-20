<?php
class Constants {
	
	const NUMBER_OF_POOLS = 2;
	
	const PROPERTY_FILE = '/var/www/properties/grcpool.props.json';
	
	const CACHE_COINBASE_BTC_VALUE = "CACHE_COINBASE_BTC_VALUE";
	const CACHE_CEX_GRC_VALUE = "CACHE_CEX_GRC_VALUE";
	const CACHE_POLONIEX_GRC_VALUE = "CACHE_POLONIEX_GRC_VALUE";
	const CACHE_LAST_SUPERBLOCK_HEIGHT = "CACHE_LAST_SUPERBLOCK_HEIGHT";
	const CACHE_SUPERBLOCK_DATA = "CACHE_SUPERBLOCK_DATA";
	
	const SESSION_COOKIE_NAME = 'Gridcoin';
	const SESSION_COOKIE_DOMAIN = '.grcpool.com';
	const GRC_MAG_MULTIPLIER = 115000;
	const MIN_MAG_MAG_FOR_MIN_RAC = .01;
	
	const COIN = 100000000;
	
	const PAYOUT_LOCK_FILE = 'payout.lock';

	const SETTINGS_TOTAL_PAID_OUT = "TOTAL_PAID_OUT";
	const SETTINGS_CPID = 'CPID';
	const SETTINGS_MIN_OWE_PAYOUT = 'MIN_OWE_PAYOUT';
	const SETTINGS_PAYOUT_FEE = 'PAYOUT_FEE';
	const SETTINGS_MIN_ORPHAN_PAYOUT_ZERO_MAG = 'MIN_ORPHAN_PAYOUT_ZERO_MAG';
	const SETTINGS_MIN_ORPHAN_PAYOUT_WITH_MAG = 'MIN_ORPHAN_PAYOUT_WITH_MAG';
	const SETTINGS_SEED = 'SEED';
	const SETTINGS_GRC_CLIENT_ONLINE = 'GRC_CLIENT_ONLINE';
	const SETTINGS_GRC_CLIENT_MESSAGE = 'GRC_CLIENT_MESSAGE';
	const SETTINGS_POOL_SIGN_UP = 'POOL_SIGNUP';
	const SETTINGS_MIN_STAKE_BALANCE = 'MIN_STAKE_BALANCE';
	const SETTINGS_HOT_WALLET_ADDRESS = 'HOT_WALLET_ADDRESS';
	const SETTINGS_GRC_DONATION_ADDRESS = 'GRC_DONATION_ADDRESS';
	const SETTINGS_WCG_CODE = 'WCG_CODE';
	const SETTINGS_WCG_CODE2 = 'WCG_CODE2';
	
	const PAYOUT_ERROR_NO_GRC_ADDR = 'No GRC Address';
	const PAYOUT_ERROR_MIN_NOT_MET = 'Minimum owed payout not met';
	const PAYOUT_ERROR_NO_MIN_AMOUNT = 'No minimum specified';
	const PAYOUT_ERROR_MIN_CALC_AMOUNT_NOT_MET = 'Mimimum calculated amount to send not met';
	
	const API_ERROR_000 = 'Request not authorized.';
	const API_ERROR_001 = 'Please provide a longer username, at least 6 characters';
	const API_ERROR_002 = 'Username already exists';
	const API_ERROR_003 = 'Email address is not valid';
	const API_ERROR_004 = 'Email address already exists';
	const API_ERROR_005 = 'Please provide a longer password, at least 8 characters';
	const API_ERROR_006 = 'Researcher not found';
	
	const DAEMON_POOL_1_PATH = '/usr/bin/gridcoinresearchd';
	const DAEMON_POOL_1_DATADIR = '/home/bgb/.GridcoinResearch';
	const DAEMON_POOL_2_PATH = '/usr/bin/gridcoinresearchd2';
	const DAEMON_POOL_2_DATADIR = '/home/bgb/.GridcoinResearch2';
	
}
	