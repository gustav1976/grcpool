<?php
require_once(dirname(__FILE__).'/../bootstrap.php');
$settingsDao = new GrcPool_Settings_DAO();
if ($settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE";
	exit;
}
$walletDao = new GrcPool_Wallet_Basis_DAO();
$settingsDao = new GrcPool_Settings_DAO();

$minStakeBalance = $settingsDao->getValueWithName(SETTINGS_MIN_STAKE_BALANCE);
$WALLETBASIS = $walletDao->getBasis();

echo "Wallet Basis: ".$WALLETBASIS."\n";
echo "Min Stake Balance: ".$minStakeBalance."\n";

$daemon = GrcPool_Utils::getDaemonForEnvironment();

$totalBalance = $daemon->getTotalBalance();
echo "Current Balance: ".$totalBalance."\n";
$totalInterest = $daemon->getTotalInterest();
echo "Wallet Interest: ".$totalInterest."\n";
$totalBalance = $totalBalance-$totalInterest;
echo "Available Balance: ".$totalBalance."\n";

if ($totalBalance < $WALLETBASIS) {
	echo $totalBalance .' to low < '.$WALLETBASIS."\n";
	exit;
}

$hostCreditDao = new GrcPool_Member_Host_Credit_DAO();
$hostDao = new GrcPool_Member_Host_DAO();

$totalOwed = $hostCreditDao->getTotalOwed();

echo "Total Owed: ".$totalOwed."\n";

$stakeBalance = $totalBalance - $WALLETBASIS - $totalOwed;

echo 'Stake Balance: '.$stakeBalance." = ".$totalBalance." - ".$WALLETBASIS." - ".$totalOwed."\n";

