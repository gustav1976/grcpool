<?php
require_once(dirname(__FILE__).'/../../bootstrap.php');
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ POLLS START ".date("Y.m.d H.i.s")."\n";

$settingsDao = new GrcPool_Settings_DAO();
$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';
$NEW = isset($argv[1]) && $argv[1] == 'NEW';
if (!$FORCE && $settingsDao->getValueWithName(Constants::SETTINGS_GRC_CLIENT_ONLINE) != '1') {
	echo "GRC CLIENT OFFLINE\n\n";
	exit;
}



$pollDao = new GrcPool_Poll_Question_DAO();
$answerDao = new GrcPool_Poll_Answer_DAO();
$voteDao = new GrcPool_Poll_Vote_DAO();
$settingsDao = new GrcPool_Settings_DAO();
$hostCreditDao = new GrcPool_Member_Host_Credit_DAO();

$totalPoolMag = 0;
$totalPoolBalance = 0;
$poolCalcMag = 0;
for ($poolId = 1; $poolId <= Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS); $poolId++) {
	$daemon = GrcPool_Utils::getDaemonForPool($poolId);
	$cpid = $settingsDao->getValueWithName(Constants::SETTINGS_CPID.($poolId > 1?$poolId:''));
	$poolMag = $daemon->getMagnitude($cpid);
	if (!$poolMag) {exit;}
	$balance = $daemon->getTotalBalance();
	if (!$balance) {exit;}
	$totalPoolMag += $poolMag;
	$totalPoolBalance += $balance;
	$poolCalcMag += $hostCreditDao->getTotalMagForPool($poolId);
}

$daemon = GrcPool_Utils::getDaemonForPool();

$polls = $daemon->getPolls();
$moneySupply = $daemon->getMoneySupply();

foreach ($polls as $poll) {
	
	$pollObj = $pollDao->initWithTitle($poll->getTitle());
	if ($pollObj == null) {
		if (!$NEW) {
			echo 'SKIPPING NEW POLL';continue;
		} else {
			$pollObj = new GrcPool_Poll_Question_OBJ();
		}
	}
	
	$pollObj->setExpire($poll->getExpire());
	$pollObj->setQuestion($poll->getQuestion());
	$pollObj->setTitle($poll->getTitle());
	$pollObj->setType($poll->getType());
	$pollObj->setBestAnswer($poll->getBestAnswer());
	$pollObj->setTotalShares($poll->getTotalShares());
	$pollObj->setTotalVotes($poll->getTotalVotes());
	$pollObj->setTimeUpdated(time());
	$pollObj->setMoneySupply($moneySupply);
	$pollObj->setPoolCalcShares(GrcPool_Utils::getVoteWeight($poolCalcMag,$totalPoolBalance, $moneySupply));
	$pollObj->setTotalPoolShares(GrcPool_Utils::getVoteWeight($totalPoolMag,$totalPoolBalance,$moneySupply));
	$pollDao->save($pollObj);
	
	foreach ($poll->getAnswers() as $pollAnswer) {
		$answerObj = $answerDao->getWithQuestionIdAndAnswer($pollObj->getId(),$pollAnswer->getAnswer());
		if ($answerObj == null) {
			$answerObj = new GrcPool_Poll_Answer_OBJ();
		}
		$answerObj->setAnswer($pollAnswer->getAnswer());
		$answerObj->setQuestionId($pollObj->getId());
		$answerObj->setShare($pollAnswer->getShare());
		$answerObj->setVotes($pollAnswer->getVotes());
		$answerDao->save($answerObj);
	}
}

///////////////////////////////

$closedPolls = $pollDao->getPollsNeededToClose();
foreach ($closedPolls as $closedPoll) {
	echo "CLOSING POLL ".$closedPoll->getId()."\n";
	// will eliminate this later...
	$votes = $voteDao->getZeroWeightForQuestionId($closedPoll->getId());
	if ($votes) {
		foreach ($votes as $vote) {
			$weight = (($vote->getMag() * ($closedPoll->getMoneySupply()/Constants::GRC_MAG_MULTIPLIER + 0.01)/5.67) + $vote->getBalance());
			$vote->setWeight($weight);
			$voteDao->save($vote);			
		}
	}
	$closedPoll->setClosed(1);
	$pollDao->save($closedPoll);
}

//////////////////////////////

$start = microtime(true);
$creditDao = new GrcPool_Member_Host_Credit_DAO();
$activePolls = $pollDao->getActivePolls();
$users = array();
foreach ($activePolls as $poll) {
	$votes = $voteDao->getWithQuestionId($poll->getId());
	foreach ($votes as $vote) {
		if (!isset($users[$vote->getMemberId()])) {
			$users[$vote->getMemberId()] = array();
			$totals = $creditDao->getTotalsForMemberId($vote->getMemberId());
			$users[$vote->getMemberId()]['mag'] = $totals['mag'];
			$users[$vote->getMemberId()]['owed'] = $totals['owed'];
		}
		$vote->setMag($users[$vote->getMemberId()]['mag']);
		$vote->setBalance($users[$vote->getMemberId()]['owed']);
		$weight = 0;
		if ($poll->getType() == 'Magnitude+Balance') {
			$weight = GrcPool_Utils::getVoteWeight($vote->getMag(),$vote->getBalance(),$moneySupply);
		}
		$vote->setWeight($weight);
		$voteDao->save($vote);
	}
}
echo(microtime(true) - $start)."\n";

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ POLLS END ".date("Y.m.d H.i.s")."\n";
