<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';

echo "############## UPDATEOWED ".date("Y.m.d H.i.s")."\n";

$settingsDao = new GrcPool_Settings_DAO();
if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE\n\n";
	exit;
}
$lockFile = 'payout.lock';
$fp = fopen(dirname(__FILE__).'/'.$lockFile,"w");
if (!flock($fp, LOCK_EX | LOCK_NB)) {
	echo('!!!!!!!!!! LOCKED !!!!!!!!!!!!!');
	exit;
}

$walletDao = new GrcPool_Wallet_Basis_DAO();
$settingsDao = new GrcPool_Settings_DAO();

$minStakeBalance = $settingsDao->getValueWithName(Constants::SETTINGS_MIN_STAKE_BALANCE)*COIN;
echo "Min Stake Balance: ".($minStakeBalance/COIN)."\n";

$hostCreditDao = new GrcPool_Member_Host_Credit_DAO();
$hostDao = new GrcPool_Member_Host_DAO();

for ($poolId = 1; $poolId <= Constants::NUMBER_OF_POOLS; $poolId++) {
	echo "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% POOL # ".$poolId."\n";
	if ($poolId == 1) {
		$daemon = GrcPool_Utils::getDaemonForEnvironment();
	} else if ($poolId == 2) {
		$daemon = GrcPool_Utils::getDaemonForEnvironment(Constants::DAEMON_POOL_2_PATH,Constants::DAEMON_POOL_2_DATADIR);
	}
	
	$basisObj = $walletDao->initWithKey($poolId);
	$WALLETBASIS = $basisObj->getBasis();
	echo "Wallet Basis: ".($WALLETBASIS/COIN)."\n";
	$totalBalance = $daemon->getTotalBalance()*COIN;
	echo "Current Balance: ".($totalBalance/COIN)."\n";
	$totalInterest = $daemon->getTotalInterest()*COIN;
	echo "Wallet Interest: ".($totalInterest/COIN)."\n";
	$totalBalance = $totalBalance-$totalInterest;
	echo "Available Balance: ".($totalBalance/COIN)."\n";
	
	if ($totalBalance < $WALLETBASIS) {
		echo ($totalBalance/COIN) .' to low < '.($WALLETBASIS/COIN)."\n";
		continue;
	}
	
	$totalOwed = $hostCreditDao->getTotalOwedForPool($poolId)*COIN;
	
	echo "Total Owed: ".($totalOwed/COIN)."\n";
	
	$stakeBalance = $totalBalance - $WALLETBASIS - $totalOwed;
	
	echo 'Stake Balance: '.($stakeBalance/COIN)." = ".($totalBalance/COIN)." - ".($WALLETBASIS/COIN)." - ".($totalOwed/COIN)."\n";
	
	if ($totalBalance - $stakeBalance < $WALLETBASIS) {
		echo 'Funds Too Low: '."\n";
	}
	
	$totalMag = $hostCreditDao->getTotalMagForPool($poolId);
	echo 'Total Mag: '.$totalMag."\n";
	
	if ($stakeBalance < $minStakeBalance) {
		echo 'not enough stake balance  '.($minStakeBalance/COIN).'  >  '.number_format($stakeBalance/COIN,8)."\n";
		continue;
	}
	
	$sql = 'update grcpool.member_host_credit set grcpool.member_host_credit.owed = grcpool.member_host_credit.owed + ((grcpool.member_host_credit.mag/'.$totalMag.') * '.($stakeBalance/COIN).'), 
			grcpool.member_host_credit.owedCalc = concat(grcpool.member_host_credit.owedCalc,\'+((\',grcpool.member_host_credit.mag,\'/\','.$totalMag.',\')*\','.($stakeBalance/COIN).',\')\') where mag > 0 and poolId = '.$poolId;
	//echo "\n\n".$sql."\n\n";
	$hostCreditDao->executeQuery($sql);
}	

// cleanup rows with a long owedCalc
$sql = 'update grcpool.member_host_credit set owedCalc = concat(\'+\',owed) where char_length(owedCalc) > 500';
$hostDao->executeQuery($sql);


// $sql = 'update grcpool.member_host_credit set grcpool.member_host_credit.owed = grcpool.member_host_credit.owed + ((grcpool.member_host_credit.mag/'.$totalMag.') * '.($stakeBalance/COIN).')';
// $hostCreditDao->executeQuery($sql);
// $sql = 'update grcpool.member_host_credit set grcpool.member_host_credit.owedCalc = concat(grcpool.member_host_credit.owedCalc,\'+((\',grcpool.member_host_credit.mag,\'/\','.$totalMag.',\')*\','.($stakeBalance/COIN).',\')\') where mag != 0';
// $hostCreditDao->executeQuery($sql);


