<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../bootstrap.php');

set_time_limit(120);

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT START ".date("Y.m.d H.i.s")."\n";

$settingsDao = new GrcPool_Settings_DAO();

if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE\n\n";
	exit;
}
$MINOWEPAYOUT = $settingsDao->getValueWithName(Constants::SETTINGS_MIN_OWE_PAYOUT);

$lockFile = Constants::PAYOUT_LOCK_FILE;

$fp = fopen(dirname(__FILE__).'/../'.$lockFile,"w");
if (!flock($fp, LOCK_EX | LOCK_NB)) {
	echo('CRITICAL: !!!!!!!!!!!! LOCKED !!!!!!!!!!!!!');
	exit;
}

// DAO OBJECTS
$walletDao = new GrcPool_Wallet_Basis_DAO();
$viewDao = new GrcPool_View_Member_Host_Project_Credit_DAO();
$creditDao = new GrcPool_Member_Host_Credit_DAO();
$payoutDao = new GrcPool_Member_Payout_DAO();
$memberDao = new GrcPool_Member_DAO();

// PROPERTIES OF PAYOUT
$PAYOUTFEE = $settingsDao->getValueWithName(Constants::SETTINGS_PAYOUT_FEE);

for ($poolId = 1; $poolId <= Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS); $poolId++) {
	echo '%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% POOL # '.$poolId."\n";

	$daemon = GrcPool_Utils::getDaemonForPool($poolId);
	
	$walletObj = $walletDao->initWithKey($poolId);
	$WALLETBASIS = $walletObj->getBasis()/COIN;
	$availableBalance = $daemon->getAvailableBalance();
	$totalBalance = $daemon->getTotalBalance();
	$interest = $daemon->getTotalInterest();
	$availablePOR = $totalBalance-$interest-$WALLETBASIS;
	
	if (!$totalBalance || $totalBalance < $WALLETBASIS) {
		echo 'CRITICAL: !!!!!!!!!!!!!!!! Total Balance error (1): '.$totalBalance."\n";
		continue;
	}
	
	echo 'INFO: Min Payout: '.$MINOWEPAYOUT."\n";
	echo 'INFO: Wallet Basis: '.$WALLETBASIS."\n";
	echo 'INFO: Hot Wallet Total Balance: '.$totalBalance."\n";
	echo 'INFO: Hot Wallet Available Balance: '.$availableBalance."\n";
	
	$owes = $viewDao->getOwedForPool($poolId);
	if (!$owes) {
		echo 'INFO: No Owes '."\n";
		continue;
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
		echo "CRITICAL: !!!!!!!!!! Trying to pay out to much ".($totalAmountOwed)." > ".($availablePOR)."\n";
		continue;
	}
	
	foreach ($owedGroups as $owedGroup) {
		set_time_limit(60);
	
		echo '~~~~ PAYOUT FOR '.$owedGroup->getUsername()." Owed: ".$owedGroup->getOwed()."\n";
		$payoutObj = new GrcPool_Payout();
		
		$member = $memberDao->initWithKey($owedGroup->getId());
		if ($member && $member->getId() && $member->getMinPayout()) {
			$payoutObj->setMinOwePayout($member->getMinPayout());
		} else {
			$payoutObj->setMinOwePayout($MINOWEPAYOUT);
		}
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
			try {
				$tx = $daemon->send($owedGroup->getGrcAddress(),$payoutData->amount);
			} catch (Exception $e) {
				echo "CRITICAL: !!!!!!!!!!!  DAEMON SEND TRY FAILED\n";
			}
			echo 'Tx '.$tx."\n";
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
	 			$credit->setMemberIdPayout($owedGroup->getId());
 				$creditDao->save($credit);
	 		}
	 		$payout = new GrcPool_Member_Payout_OBJ();
	 		$payout->setCalculation($owedGroup->getOwedCalc());
	 		if (strlen($payout->getCalculation()) > 490) {
	 			$payout->setCalculation(substr($payout->getCalculation(),0,490).'...');
	 		}
	 		$payout->setAmount($owedGroup->getOwed());
	 		$payout->setFee($payoutData->fee);
	 		$payout->setDonation($payoutData->donation);
	 		$payout->setMemberId($owedGroup->getId());
	 		$payout->setUsername($owedGroup->getUsername());
	 		$payout->setThetime(time());
	 		$payout->setPoolId($poolId);
	 		$payout->setAddress($owedGroup->getGrcAddress());
	 		$payout->setTx($tx=='DONATION'?'':$tx);
 			print_r($payout);
			$payoutDao->save($payout);
	
	 		$basisIncr = $payoutData->fee+$payoutData->donation;
	 		echo 'Increase wallet basis by: '.$basisIncr."\n";

	 		$walletObj->setBasis($walletObj->getBasis()+($basisIncr*COIN));
	 		if (!$walletDao->save($walletObj)) {
	 			echo "CRITICAL: !!!!!!!!!!!!! Wallet Basis not incremented \n";
	 			echo $walletDao->getError();
	 			exit;
	 		}
	 	
	 	}
	}
	$payoutDao = new GrcPool_Member_Payout_DAO();
	$totalPaid = $payoutDao->getTotalAmountForPool($poolId);
	if ($totalPaid) {
		$settingsDao->setValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT.($poolId==1?'':$poolId),$totalPaid);
	}
}

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT END ".date("Y.m.d H.i.s")."\n";