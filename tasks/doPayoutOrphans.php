<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT START ".date("Y.m.d H.i.s")."\n";

set_time_limit(240);

$settingsDao = new GrcPool_Settings_DAO();
if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE\n\n";
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
$viewDao = new GrcPool_View_All_Orphans_DAO();
$creditDao = new GrcPool_Member_Host_Credit_DAO();
$payoutDao = new GrcPool_Member_Payout_DAO();
$memberDao = new GrcPool_Member_DAO();

// PROPERTIES OF PAYOUT
$PAYOUTFEE = $settingsDao->getValueWithName(Constants::SETTINGS_PAYOUT_FEE);
$MINORPHANPAYOUT = $settingsDao->getValueWithName(Constants::SETTINGS_MIN_ORPHAN_PAYOUT_ZERO_MAG);

for ($poolId = 1; $poolId <= Constants::NUMBER_OF_POOLS; $poolId++) {
	echo '%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%';
	echo '%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% POOL # '.$poolId."\n";
	echo '%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%';
	$daemon = null;
	
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
	
	echo 'INFO: Wallet Basis: '.$WALLETBASIS."\n";
	echo 'INFO: Hot Wallet Total Balance: '.$totalBalance."\n";
	echo 'INFO: Hot Wallet Available Balance: '.$availableBalance."\n";
	
	$owes = $viewDao->getAvailableOrphanPayoutsForPool($poolId,$settingsDao->getValueWithName(Constants::SETTINGS_MIN_ORPHAN_PAYOUT_WITH_MAG));
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
		$oweObj = new GrcPool_View_Member_Host_Project_Credit_OBJ();
		if ($owe->getMemberIdCredit() != 0) {
			$oweObj->setId($owe->getMemberIdCredit());
		} else {
			$oweObj->setId($owe->getMemberId());
		}
		$memberObj = $memberDao->initWithKey($oweObj->getId());
		if (!$memberObj || $memberObj->getId() == 0) {
			echo "!!!!!!! NO MEMBER ID ".$oweObj->getId()." -- CreditID: ".$owe->getId()."\n";
			continue;
		}
		$oweObj->setCreditId($owe->getId());
		$oweObj->setDonation(0);
		$oweObj->setEmail($memberObj->getEmail());
		$oweObj->setGrcAddress($memberObj->getGrcAddress());
		$oweObj->setHostDbid($owe->gethostDbid());
		$oweObj->setHostId(0);
		$oweObj->setHostName('UNKNOWN');
		$oweObj->setMag($owe->getMag());
		$oweObj->setOwed($owe->getOwed());
		$oweObj->setOwedCalc($owe->getOwedCalc());
		$oweObj->setPoolId($owe->getPoolId());
		$oweObj->setProjectPoolId($owe->getPoolId());
		$oweObj->setTotalCredit($owe->getTotalCredit());
		$oweObj->setUsername($memberObj->getUsername());
		$oweObj->setVerified($memberObj->getVerified());
		if (!isset($owedGroups[$oweObj->getId()])) {
			$owedGroups[$oweObj->getId()] = new GrcPool_PayoutGroup();
		}
		$owedGroups[$oweObj->getId()]->add($oweObj);
		$totalAmountOwed += $oweObj->getOwed();
	}
	$owe = null;
	
	echo "INFO: Total Owed: ".$totalAmountOwed."\n";
	if ($totalAmountOwed > $availablePOR) {
		echo "CRITICAL: !!!!!!!!!! Trying to pay out to much ".$totalAmountOwed." > ".$availablePOR."\n";
		continue;
	}
	foreach ($owedGroups as $owedGroup) {	
		echo '~~~~ PAYOUT FOR '.$owedGroup->getUsername()." ".$owedGroup->getId()." Owed: ".$owedGroup->getOwed()."\n";
		$payoutObj = new GrcPool_Payout();
		$member = $memberDao->initWithKey($owedGroup->getId());
		$payoutObj->setMinOwePayout($MINORPHANPAYOUT);
		$payoutObj->setPayoutFee($PAYOUTFEE);	
		$payoutData = $payoutObj->process($owedGroup);
		print_r($payoutData);
                if ($payoutData->error) {
                        echo "     ERROR: ".$payoutData->error."\n";
                        continue;
                }
                if (isset($argv[1]) && $argv[1] == 'TEST') {
                        print_r($owedGroup);
			print_r($payoutData);
			exit;
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
	 			$credit->setMemberId($owedGroup->getId());
	 			$creditDao->save($credit);
	 		}
	 		$payout = new GrcPool_Member_Payout_OBJ();
	 		$payout->setCalculation($owedGroup->getOwedCalc());
	 		$payout->setAmount($owedGroup->getOwed());
	 		$payout->setFee($payoutData->fee);
	 		$payout->setDonation($payoutData->donation);
	 		$payout->setMemberId($owedGroup->getId());
	 		$payout->setThetime(time());
	 		$payout->setPoolId($poolId);
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
	$payoutViewDao = new GrcPool_View_Member_Payout_DAO();
	$totalPaid = $payoutViewDao->getTotalAmountForPool($poolId);
	if ($totalPaid) {
		$settingsDao->setValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT.($poolId==1?'':$poolId),$totalPaid);
	}
}


echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT END ".date("Y.m.d H.i.s")."\n";
