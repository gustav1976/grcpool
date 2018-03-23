<?php
$this->view->loggedIn = false;
$collapse = new Bootstrap_Collapse();
$collapse->setAutoCollapse(false);
$open = true;

foreach ($this->view->polls as $poll) {
	$title = str_replace('_',' ',$poll->getQuestion());
	$answers = '';
	$totalW = 0;
	foreach ($poll->weights as $w) {
		$totalW += $w['weight'];
	}
	$allWeights = $poll->getTotalShares()+$totalW;
	$poolAdjTotal = 0;
	$poolWeight = 0;
	$grcWeight = 0;
	$totalVotes = 0;
	foreach ($poll->answers as $answer) {
		$weight = 0;
		$vote = 0;
		if (isset($poll->weights[$answer->getId()])) {
			$weight = $poll->weights[$answer->getId()]['weight'];
			$vote = $poll->weights[$answer->getId()]['howMany'];
			$totalVotes += $vote;
		}
		$poolShareAdj = Utils::truncate($poll->getTotalPoolShares()*$weight/$poll->getPoolCalcShares());
		$poolSharePercent = number_format(100*($answer->getShare()+$poolShareAdj)/($allWeights),2);
		$poolAdjTotal += $poolShareAdj;
		$poolWeight += Utils::truncate($weight);
		$grcWeight += Utils::truncate($answer->getShare());
		$answers .= '
			<tr>
				<td>'.$answer->getPrettyAnswer().'</td> 
				<td class="text-right">'.$vote.'</td>
				<td class="text-right">'.number_format(Utils::truncate($answer->getShare())).'</td>								
				<td class="text-right">'.number_format(100*$answer->getShare()/$poll->getTotalShares(),2).'%</td>
				<td class="text-right">'.number_format(Utils::truncate($weight)).'</td>
				<td class="text-right">'.number_format($poolShareAdj).'</td>
				<td class="text-right">'.$poolSharePercent.'%</td>
			</tr>
		';
	}
	$answers .= '
		<tr>
			<td></td>
			<td class="text-right"><strong>'.$totalVotes.'</strong></td>
			<td class="text-right"><strong>'.number_format($grcWeight).'</strong></td>
			<td></td>
			<td class="text-right"><strong>'.number_format($poolWeight).'</strong></td>
			<td class="text-right"><strong>'.number_format($poolAdjTotal).'</strong></td>
		</tr>
	';
	$content = '
		<form method="post">
			<div class="rowpad">
				<strong>Poll Ended</strong>: '.date('Y-m-d H:i:s',$poll->getExpire()).' GMT	
			</div>
			'.($poll->getMoreInfo()!=''?'
				<div class="rowpad">
					<strong>Discussion</strong>: <a href="'.$poll->getMoreInfo().'">'.$poll->getMoreInfo().'</a>
				</div>
			':'').'
			<div class="rowpad">
				<strong>Poll Details</strong>:
				<i class="fa fa-external-link"></i> <a target="_blank" href="https://gridcoinstats.eu/poll/'.$poll->getTitle().'">GRC Network</a> |
				<a href="/polls/results/'.$poll->getId().'/'.urlencode($poll->getTitle()).'">Pool</a>
			</div>
			<div>
				<table class="table table-striped">
					<thead>
						<th></th>
						<th class="text-right">Votes</th>
 						<th class="text-right">GRC</th>
						<th class="text-right">GRC %</th>
						<th class="text-right">Pool</th>
						<th class="text-right">Pool Adj</th>
						<th class="text-right">GRC+Pool Adj %</th>
					</thead>
					'.$answers.'
				</table>
			</div>
			'.($this->view->loggedIn?'
				<div>
					<button style="margin-right:20px;" class="btn btn-primary" name="cmd" value="vote_'.$poll->getId().'" type="submit">vote</button>
					<button style="margin-right:20px;" class="btn btn-danger" name="cmd" value="remove_'.$poll->getId().'" type="submit">remove vote</button>
				</div>
			':'').'
		</form>
		<br clear="all"/>
		<div class="pull-right"><small>last updated '.(Utils::getTimeAgo($poll->getTimeUpdated())).'</small></div>
		<br clear="all"/>		
	';
	$collapse->addItem($title,$content,$open);
	$open = false;
}

$webPage->append($collapse->render());

