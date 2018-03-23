<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../bootstrap.php');

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';

$maxNumberOfPayouts = 50;

//$runOnHours = array('8','11','12'); // run pool for index
//$searchResult = array_search(date('G'),$runOnHours);
//if (!$FORCE && $searchResult === false) {
//	echo "NOT TIME TO RUN ".date('G')."\n";
//	exit;
//}

if ($FORCE) {
	if (isset($argv[2])) {
		$RUNFORPOOL = $argv[2];
	} else {
		echo 'No Pool # Argument'."\n\n";
		exit;
	}
} else {
	$RUNFORPOOL = isset($argv[1])?$argv[1]:0;
	//$searchResult+1;
}

set_time_limit(120);

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT START ".date("Y.m.d H.i.s")."\n";

$dao = new GrcPool_Status_DAO();

if (!$dao->isInSync()) {
	echo "WALLETS ARE NOT IN SYNC LETS WAIT 5 SECONDS";
	sleep(5);
	if (!$dao->isInSync()) {
		echo "WALLETS ARE NOT IN SYNC AGAIN LETS DIE";
		exit;
	}
}


////////////////////////////////////////////
// $memberDao = new GrcPool_Member_DAO();
// $skipMemberIds = array();
// $data = file_get_contents('../0901.txt');
// $lines = explode("\n",$data);
// foreach ($lines as $line) {
//         $data = explode(",",str_replace('"','',$line));
//         $id = $data[0];
//         $member = $memberDao->initWithKey($id);
//         if ($member->getId() != 0) {
//                 if ($member->getGrcAddress() != $data[8]) {
//                         //echo $member->getId().' '.$member->getUsername().' '.$member->getEmail().' '.$data[1];
//                         //$emails .= $member->getEmail().",";
// 			$skipMemberIds[$id] = 1;
//                 }
//         }
// }
//////////////////////////////

$settingsDao = new GrcPool_Settings_DAO();

if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE\n\n";
	exit;
}
$MINOWEPAYOUT = $settingsDao->getValueWithName(Constants::SETTINGS_MIN_OWE_PAYOUT);

$fp = fopen(Constants::PAYOUT_LOCK_FILE,"w");
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
//$creditPaidDao = new GrcPool_Member_Host_Credit_Paid_DAO();

// PROPERTIES OF PAYOUT
$PAYOUTFEE = $settingsDao->getValueWithName(Constants::SETTINGS_PAYOUT_FEE);

for ($poolId = 1; $poolId <= Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS); $poolId++) {
	if ($poolId != $RUNFORPOOL && $RUNFORPOOL != 0) {continue;}
	echo '%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% POOL # '.$poolId."\n";

	$daemon = GrcPool_Utils::getDaemonForPool($poolId);
	
	$numberOfPayouts = 0;
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
			$numberOfPayouts++;
	 		$creditIds = $owedGroup->getCreditIds();
	 		foreach ($creditIds as $creditId) {
	 			$credit = $creditDao->initWithKey($creditId);
	 			echo "SETTING TO ZERO ".$credit->getId()." from: ".$credit->getOwed()."\n";
	 			$credit->setOwed(0);
	 			$credit->setOwedCalc('');
	 			$credit->setMemberIdPayout($owedGroup->getId());
 				$creditDao->save($credit);
 				
 				//$creditPaidObj = new GrcPool_Member_Host_Credit_Paid_OBJ();
 				//$creditPaidObj->setAccountId($credit->getAccountId());
 				//$creditPaidObj->setAvgCredit($credit->getAvgCredit());
 				//$creditPaidObj->setHostDbid($credit->getHostDbId());
 				//$creditPaidObj->setMag($credit->getMag());
 				//$creditPaidObj->setMemberId($credit->getMemberIdPayout());
 				//$creditPaidObj->setOwed($credit->getOwed());
 				//$creditPaidObj->setPoolId($credit->getPoolId());
 				//$creditPaidObj->setTheTime(time());
 				//$creditPaidObj->setTotalCredit($credit->getTotalCredit());
 				//$creditPaidDao->save($creditPaidObj);
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
		if ($numberOfPayouts >= $maxNumberOfPayouts) {
			break;
		}
		usleep(100000);
	}
	$payoutDao = new GrcPool_Member_Payout_DAO();
	$totalPaid = $payoutDao->getTotalAmountForPool($poolId,Constants::CURRENCY_GRC);
	if ($totalPaid) {
		$settingsDao->setValueWithName(Constants::SETTINGS_TOTAL_PAID_OUT.($poolId==1?'':$poolId),$totalPaid);
	}
}

GrcPool_Task_Helper::runPayoutTasks();

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DOPAYOUT END ".date("Y.m.d H.i.s")."\n";
