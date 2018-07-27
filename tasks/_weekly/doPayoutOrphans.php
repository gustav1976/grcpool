<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../../bootstrap.php');

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT START ".date("Y.m.d H.i.s")."\n";
set_time_limit(120);

$settingsDao = new GrcPool_Settings_DAO();

if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE\n\n";
	exit;
}

$taskDao = new GrcPool_Task_DAO();
$taskObj = new GrcPool_Task_OBJ();
$taskObj->setName(GrcPool_TaskEnum::ORPHAN_PAYOUT);
$taskObj->setMessage('Running');
$taskObj->setSuccess(0);
$taskObj->setTheTime(time());
$taskObj->setInfo('');
$taskObj->setTimeStarted(microtime(true));
$taskDao->save($taskObj);

$fp = fopen(Constants::PAYOUT_LOCK_FILE,"w");
if (!flock($fp, LOCK_EX | LOCK_NB)) {
	echo('CRITICAL: !!!!!!!!!!!! LOCKED !!!!!!!!!!!!!');
	$taskObj->setMessage('Payout Was Locked');
	$taskDao->save($taskObj);
	exit;
}

$dao = new GrcPool_Status_DAO();

if (!$dao->isInSync()) {
	echo "WALLETS ARE NOT IN SYNC LETS WAIT 5 SECONDS";
	sleep(5);
	if (!$dao->isInSync()) {
		echo "WALLETS ARE NOT IN SYNC AGAIN LETS DIE";
		$taskObj->setMessage('Wallets Out Of Sync');
		$taskDao->save($taskObj);
		exit;
	}
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

$taskMessage = '';
$payoutCount = 0;
$payoutTotal = 0;

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
		$taskMessage .= 'Total Balance Error Pool: '.$poolId.' -- ';
		continue;
	}
	
	echo 'INFO: Wallet Basis: '.$WALLETBASIS."\n";
	echo 'INFO: Hot Wallet Total Balance: '.$totalBalance."\n";
	echo 'INFO: Hot Wallet Available Balance: '.$availableBalance."\n";
	
	$owes = $viewDao->getAvailableOrphanPayoutsForPool($poolId,$settingsDao->getValueWithName(Constants::SETTINGS_MIN_ORPHAN_PAYOUT_WITH_MAG));
	
	if (!$owes) {
		echo 'INFO: No Owes '."\n";
		$taskMessage .= ' No Owes Pool '.$poolId.' -- ';
		continue;
	}
	
	/**
	 * 
	 * @var GrcPool_PayoutGroup[]
	 */
	$owedGroups = array();
	$totalAmountOwed = 0;
	$totalAmountOwedNoMemberId = 0;
	foreach ($owes as $owe) {
		$oweObj = new GrcPool_View_Member_Host_Project_Credit_OBJ();
		if ($owe->getMemberIdCredit() != 0) {
			$oweObj->setMemberId($owe->getMemberIdCredit());
		} else {
			$oweObj->setMemberId($owe->getMemberIdPayout());
		}
		$memberObj = $memberDao->initWithKey($oweObj->getId());
		if (!$memberObj || $memberObj->getId() == 0) {
			//echo "!!!!!!! NO MEMBER ID ".$oweObj->getId()." -- CreditID: ".$owe->getId()."\n";
			$totalAmountOwedNoMemberId += $owe->getOwed();
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
 	echo "INFO: Total Owed No Member ".$totalAmountOwedNoMemberId."\n";	
	echo "INFO: Total Owed: ".$totalAmountOwed."\n";
	if ($totalAmountOwed > $availablePOR) {
		echo "CRITICAL: !!!!!!!!!! Trying to pay out to much ".$totalAmountOwed." > ".$availablePOR."\n";
		$taskMessage .= 'Owed To High Pool '.$poolId.' -- ';
		continue;
	}
	foreach ($owedGroups as $owedGroup) {	
		set_time_limit(60);		
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
		 		$taskMessage .= 'Daemon Failure Pool '.$poolId.' -- ';
		 	}
		 	echo 'Tx '.$tx."\n";
		}
		
	 	if ($tx == '') {
	 		echo "CRITICAL: !!!!!!!!!!!!!! DAEMON TX BLANK\n";
	 		$taskMessage .= 'Transaction Failure '.$poolId.' -- ';
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
	 		$payout->setAmount($owedGroup->getOwed());
	 		$payout->setFee($payoutData->fee);
	 		$payout->setDonation($payoutData->donation);
	 		$payout->setMemberId($owedGroup->getId());
	 		$payout->setUsername($owedGroup->getUsername());
	 		$payout->setThetime(time());
	 		$payout->setPoolId($poolId);
	 		$payout->setTx($tx=='DONATION'?'':$tx);
	 		$payout->setAddress($owedGroup->getGrcAddress());
	 		$payoutTotal += $owedGroup->getOwed();
	 		$payoutCount++;
	 		print_r($payout);
	 		$payoutDao->save($payout);
	
	 		$basisIncr = $payoutData->fee+$payoutData->donation;
	 		echo 'Increase wallet basis by: '.$basisIncr."\n";

	 		$walletObj->setBasis($walletObj->getBasis()+($basisIncr*COIN));
	 		if (!$walletDao->save($walletObj)) {
	 			echo "CRITICAL: !!!!!!!!!!!!! Wallet Basis not incremented \n";
	 			$taskMessage .= 'Wallet Basis Failure '.$poolId.' -- ';
	 			echo $walletDao->getError();
	 			exit;
	 		}
	 	}
	}
	$payoutDao = new GrcPool_Member_Payout_DAO();
	$totalPaid = $payoutDao->getTotalAmountForPool($poolId,Constants::CURRENCY_GRC);
	if ($totalPaid) {
		$settingsDao->setValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT.($poolId==1?'':$poolId),$totalPaid);
	}
}

$taskObj->setInfo('Number of payouts: '.$payoutCount.' Amount: '.$payoutTotal);
$taskObj->setSuccess(1);
$taskObj->setMessage($taskMessage==''?'OK':$taskMessage);
$taskObj->setTimeCompleted(microtime(true));
$taskDao->save($taskObj);

GrcPool_Task_Helper::runPayoutTasks();

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT END ".date("Y.m.d H.i.s")."\n";
