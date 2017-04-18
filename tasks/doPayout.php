<?php
require_once(dirname(__FILE__).'/../bootstrap.php');
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT START ".date("Y.m.d H.i.s")."\n";
//exit;

set_time_limit(120);

$settingsDao = new GrcPool_Settings_DAO();
if ($settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE";
	exit;
}
$MINOWEPAYOUT = $settingsDao->getValueWithName(Constants::SETTINGS_MIN_OWE_PAYOUT);
$DOONCE = false;
$DEBUG =isset($argv[1]) && $argv[1] == 'DEBUG';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	if (isset($argv[1]) && $argv[1] == "FORCE") {
		echo "INFO: RUNNING ON WINDOWS - DEV BOX PROBABLY - FORCING 1 ITERATION";
		$DOONCE = true;
	} else {
		echo "INFO: RUNNING ON WINDOWS - DEV BOX PROBABLY - DEBUG = TRUE";
		$DEBUG = true;
	}
}
if (isset($argv[1]) && $argv[1] == 'ONE') {
	$DOONCE = true;
}


$lockFile = Constants::PAYOUT_LOCK_FILE;

$fp = fopen(dirname(__FILE__).'/'.$lockFile,"w");
if (!flock($fp, LOCK_EX | LOCK_NB)) {
	echo('CRITICAL: !!!!!!!!!!!! LOCKED !!!!!!!!!!!!!');
	exit;
}

// DAO OBJECTS
$walletDao = new GrcPool_Wallet_Basis_DAO();
$daemon = GrcPool_Utils::getDaemonForEnvironment();
$viewDao = new GrcPool_View_Member_Host_Project_Credit_DAO();
$creditDao = new GrcPool_Member_Host_Credit_DAO();
$payoutDao = new GrcPool_Member_Payout_DAO();

// PROPERTIES OF PAYOUT
$PAYOUTFEE = $settingsDao->getValueWithName(Constants::SETTINGS_PAYOUT_FEE);
$WALLETBASIS = $walletDao->getBasis();
$availableBalance = $daemon->getAvailableBalance();
$totalBalance = $daemon->getTotalBalance();
$interest = $daemon->getTotalInterest();
$availablePOR = $totalBalance-$interest-$WALLETBASIS;

if (!$totalBalance || $totalBalance < $WALLETBASIS) {
	echo 'CRITICAL: !!!!!!!!!!!!!!!! Total Balance error (1): '.$totalBalance."\n";
	exit;
}

echo 'INFO: Min Payout: '.$MINOWEPAYOUT."\n";
echo 'INFO: Wallet Basis: '.$WALLETBASIS."\n";
echo 'INFO: Hot Wallet Total Balance: '.$totalBalance."\n";
echo 'INFO: Hot Wallet Available Balance: '.$availableBalance."\n";

$owes = $viewDao->getOwed();
if (!$owes) {
	echo 'INFO: No Owes '."\n";
	exit;
}

/**
 * 
 * @var GrcPool_PayoutGroup[]
 */
$owedGroups = array();
$totalAmountOwed = 0;
foreach ($owes as $owe) {
	if (!isset($owedGroups[$owe->getId()])) {
		$owedGroups[$owe->getId()] = new GrcPool_PayoutGroup();
	}
	$owedGroups[$owe->getId()]->add($owe);
	$totalAmountOwed += $owe->getOwed();
}
$owe = null;

echo "INFO: Total Owed: ".$totalAmountOwed."\n";
if ($totalAmountOwed > $availablePOR) {
	echo "CRITICAL: !!!!!!!!!! Trying to pay out to much\n";
	exit;
}

foreach ($owedGroups as $owedGroup) {
	if (!$DEBUG && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		if (!strstr($owedGroup->getUsername(),'bryhardt-')) {
			continue;
		}
	}

	echo '~~~~ PAYOUT FOR '.$owedGroup->getUsername()." Owed: ".$owedGroup->getOwed()."\n";
	$payoutObj = new GrcPool_Payout();
	$payoutObj->setMinOwePayout($MINOWEPAYOUT);
	$payoutObj->setPayoutFee($PAYOUTFEE);

	$payoutData = $payoutObj->process($owedGroup);

	print_r($payoutData);
	
	if ($payoutData->error) {
		echo "     ERROR: ".$payoutData->error."\n";
		continue;
	}

	$tx = '';

	if ($payoutData->amount == 0) {
		$tx = 'DONATION';
	} else {
	 	if ($DEBUG) {
	 		echo "sending ".($payoutData->amount)." to ".$owedGroup->getGrcAddress()."\n";	
	 		$tx = 'DEBUG'; // to bypass critical error below
	 	} else {
			try {
	 			$tx = $daemon->send($owedGroup->getGrcAddress(),$payoutData->amount);
	 		} catch (Exception $e) {
	 			echo "CRITICAL: !!!!!!!!!!!  DAEMON SEND TRY FAILED\n";
	 		}
	 		echo 'Tx '.$tx."\n";
	 	}
	}
	
 	if ($tx == '') {
 		echo "CRITICAL: !!!!!!!!!!!!!! DAEMON TX BLANK\n";
 		continue;
 	} else {
 		$creditIds = $owedGroup->getCreditIds();
 		foreach ($creditIds as $creditId) {
 			$credit = $creditDao->initWithKey($creditId);
 			echo "SETTING TO ZERO ".$credit->getId()." from: ".$credit->getOwed()."\n";
 			$credit->setOwed(0);
 			$credit->setOwedCalc('');
 			if ($DEBUG) {
 				echo "Setting to zero\n";
 			} else {
 				$creditDao->save($credit);
 			}
 		}
 		$payout = new GrcPool_Member_Payout_OBJ();
 		$payout->setCalculation($owedGroup->getOwedCalc());
 		$payout->setAmount($owedGroup->getOwed());
 		$payout->setFee($payoutData->fee);
 		$payout->setDonation($payoutData->donation);
 		$payout->setMemberId($owedGroup->getId());
 		$payout->setThetime(time());
 		$payout->setTx($tx=='DONATION'?'':$tx);
 		if ($DEBUG) {
 			print_r($payout);
 		} else {
 			print_r($payout);
 			$payoutDao->save($payout);
 		}

 		$basisIncr = $payoutData->fee+$payoutData->donation;
 		echo 'Increase wallet basis by: '.$basisIncr."\n";
 		if ($DEBUG) {
 			
 		} else {
 			$result = $walletDao->incrBasis($basisIncr);
 			if ($result == $WALLETBASIS) {
 				echo "CRITICAL: !!!!!!!!!!!!! Wallet Basis not incremented \n";
 				echo $walletDao->getError();
 				exit;
 			}
 		}
 		if ($DOONCE) {
 			echo "COMPLETED ONE ITERATION";
 			exit;
 		}
 	}
}

$payoutDao = new GrcPool_View_Member_Payout_DAO();
$totalPaid = $payoutDao->getTotalAmount();
if ($totalPaid) {
	$settingsDao->setValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT,$totalPaid);
}
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT END ".date("Y.m.d H.i.s")."\n";
