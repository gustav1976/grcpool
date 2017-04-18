<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

echo "############## UPDATEOWED ".date("Y.m.d H.i.s")."\n";
$settingsDao = new GrcPool_Settings_DAO();
if ($settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE";
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

$minStakeBalance = $settingsDao->getValueWithName(SETTINGS_MIN_STAKE_BALANCE)*COIN;
$WALLETBASIS = $walletDao->getBasis()*COIN;

echo "Wallet Basis: ".($WALLETBASIS/COIN)."\n";
echo "Min Stake Balance: ".($minStakeBalance/COIN)."\n";

$daemon = GrcPool_Utils::getDaemonForEnvironment();

$totalBalance = $daemon->getTotalBalance()*COIN;
echo "Current Balance: ".($totalBalance/COIN)."\n";
$totalInterest = $daemon->getTotalInterest()*COIN;
echo "Wallet Interest: ".($totalInterest/COIN)."\n";
$totalBalance = $totalBalance-$totalInterest;
echo "Available Balance: ".($totalBalance/COIN)."\n";

if ($totalBalance < $WALLETBASIS) {
	echo ($totalBalance/COIN) .' to low < '.($WALLETBASIS/COIN)."\n";
	exit;
}

$hostCreditDao = new GrcPool_Member_Host_Credit_DAO();
$hostDao = new GrcPool_Member_Host_DAO();

$totalOwed = $hostCreditDao->getTotalOwed()*COIN;

echo "Total Owed: ".($totalOwed/COIN)."\n";

$stakeBalance = $totalBalance - $WALLETBASIS - $totalOwed;

echo 'Stake Balance: '.($stakeBalance/COIN)." = ".($totalBalance/COIN)." - ".($WALLETBASIS/COIN)." - ".($totalOwed/COIN)."\n";

if ($totalBalance - $stakeBalance < $WALLETBASIS) {
	echo 'Funds Too Low: '."\n";
}

$totalMag = $hostCreditDao->getTotalMag();
echo 'Total Mag: '.$totalMag."\n";

if ($stakeBalance < $minStakeBalance) {
	echo 'not enough stake balance '.($stakeBalance/COIN)."\n";
	exit;
}

$sql = 'update grcpool.member_host_credit set grcpool.member_host_credit.owed = grcpool.member_host_credit.owed + ((grcpool.member_host_credit.mag/'.$totalMag.') * '.($stakeBalance/COIN).'), 
		grcpool.member_host_credit.owedCalc = concat(grcpool.member_host_credit.owedCalc,\'+((\',grcpool.member_host_credit.mag,\'/\','.$totalMag.',\')*\','.($stakeBalance/COIN).',\')\') where mag > 0';
$hostCreditDao->executeQuery($sql);


// $sql = 'update grcpool.member_host_credit set grcpool.member_host_credit.owed = grcpool.member_host_credit.owed + ((grcpool.member_host_credit.mag/'.$totalMag.') * '.($stakeBalance/COIN).')';
// $hostCreditDao->executeQuery($sql);
// $sql = 'update grcpool.member_host_credit set grcpool.member_host_credit.owedCalc = concat(grcpool.member_host_credit.owedCalc,\'+((\',grcpool.member_host_credit.mag,\'/\','.$totalMag.',\')*\','.($stakeBalance/COIN).',\')\') where mag != 0';
// $hostCreditDao->executeQuery($sql);




