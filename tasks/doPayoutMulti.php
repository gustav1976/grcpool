<?php
require_once(dirname(__FILE__).'/../bootstrap.php');
set_time_limit(240);

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';

// $walletDao = new GrcPool_Wallet_Basis_DAO();
// $walletObj = $walletDao->initWithKey(2);
// $walletDao->beginTransaction();
// echo $walletObj->getBasis();
// $walletObj->setBasis(10000000000000);
// $walletDao->save($walletObj);
// echo "SAVED";
// echo $walletDao->isTransactionError();
// echo $walletDao->getError();
// // $walletObj->setBasis(99);
// // $walletDao->save($walletObj);
// echo "TRYING TO END";
// $walletDao->endTransaction();
// echo 'ENDED';


// exit;
 echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT START ".date("Y.m.d H.i.s")."\n";
// //exit;

$settingsDao = new GrcPool_Settings_DAO();
if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE";
	exit;
}

$lockFile = Constants::PAYOUT_LOCK_FILE;

$fp = fopen(dirname(__FILE__).'/'.$lockFile,"w");
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
$MINOWEPAYOUT = $settingsDao->getValueWithName(Constants::SETTINGS_MIN_OWE_PAYOUT);

for ($poolId = 1; $poolId <= Constants::NUMBER_OF_POOLS; $poolId++) {
	echo "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%\n";
 	echo '%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% POOL # '.$poolId."\n";
 	echo "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%\n";
 	
 	if ($poolId == 1) {
 		$daemon = GrcPool_Utils::getDaemonForEnvironment();
 	} else if ($poolId == 2) {
 		$daemon = GrcPool_Utils::getDaemonForEnvironment(Constants::DAEMON_POOL_2_PATH,Constants::DAEMON_POOL_2_DATADIR);
 	}
	
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
 		echo "CRITICAL: !!!!!!!!!! Trying to pay out to much ".$totalAmountOwed." > ".$availablePOR."\n";
 		continue;
 	}

 	/////////////////////////////////////////////

 	$creditDao->beginTransaction();
 	$tempTransactionId = time();
 	$sendMany = array();
 	
 	foreach ($owedGroups as $owedGroup) {
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

 		$creditIds = $owedGroup->getCreditIds();
 		foreach ($creditIds as $creditId) {
 			$credit = $creditDao->initWithKey($creditId);
 			echo "SETTING TO ZERO ".$credit->getId()." from: ".$credit->getOwed()."\n";
 			$credit->setOwed(0);
 			$credit->setOwedCalc('');
 			$credit->setMemberId($owedGroup->getId());
			$creditDao->save($credit);
 		}
 		
 		if ($payoutData->amount > 0) {
			echo "sending ".($payoutData->amount)." to ".$owedGroup->getGrcAddress()."\n";
			array_push($sendMany,array('address'=>$owedGroup->getGrcAddress(),'amount'=>$payoutData->amount));
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
 		$payout->setThetime(time());
 		$payout->setPoolId($poolId);
 		$payout->setTx($payoutData->amount == 0?'':$tempTransactionId);
 		print_r($payout);
		$payoutDao->save($payout);
 		
 		$basisIncr = $payoutData->fee+$payoutData->donation;
 		echo 'Increase wallet basis by: '.$basisIncr."\n";
		$walletObj->setBasis($walletObj->getBasis()+($basisIncr*COIN));
		$walletDao->save($walletObj);

 	}
 	
	print_r($sendMany);
	
	$tx = $daemon->sendMany($sendMany);
	
	$creditDao->rollback();
 	
	
	
// 	$payoutViewDao = new GrcPool_View_Member_Payout_DAO();
// 	$totalPaid = $payoutViewDao->getTotalAmountForPool($poolId);
// 	if ($totalPaid) {
// 		$settingsDao->setValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT.($poolId==1?'':$poolId),$totalPaid);
// 	}
}

// echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT END ".date("Y.m.d H.i.s")."\n";
