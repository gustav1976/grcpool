<?php
$webPage->appendScript('
 	<script>
 		$(\'[data-toggle="tooltip"]\').tooltip();
 	</script>
');

$collapse = new Bootstrap_Collapse();
$collapse->setAutoCollapse(false);

foreach ($this->view->polls as $poll) {
	$newContent = '
		<form method="post">
			<div class="rowpad">
				<strong>Poll Ends</strong>: '.date('Y-m-d H:i:s',$poll->getExpire()).' GMT	
			</div>
			'.($poll->getMoreInfo()!=''?'
				<div class="rowpad">
					<strong>Discussion</strong>: <i class="fa fa-external-link"></i> <a target="_blank" href="'.$poll->getMoreInfo().'">'.$poll->getMoreInfo().'</a>
				</div>
			':'').'
			<div class="rowpad">
				<strong>Poll Details</strong>:
				<i class="fa fa-external-link"></i> <a target="_blank" href="https://gridcoinstats.eu/poll/'.$poll->getTitle().'">GRC Network</a> |
				<a href="/polls/results/'.$poll->getId().'/'.urlencode($poll->getTitle()).'">Pool</a>
			</div>
			<table class="table table-striped">
				<thead>
					<th>Vote</th>
					<th class="text-right">GRC<br/>Votes</th>
					<th class="text-right">
						GRC
						<a href="#" data-toggle="tooltip" title="This is the amount of shares in Gridcoin network for the poll."><i style="color:black;" class="fa fa-info-circle"></i></a>						
					</th>
					<th class="text-right">
						GRC %
						<a href="#" data-toggle="tooltip" title="This is the percentage of shares in Gridcoin network for the poll."><i style="color:black;" class="fa fa-info-circle"></i></a>						
					</th>
					<th class="text-right">
						Pool<br/>Votes
					</th>
					<th class="text-right">
						Pool
						<a href="#" data-toggle="tooltip" title="This is the pool shares based on magnitude and balance."><i style="color:black;" class="fa fa-info-circle"></i></a>						
					</th>
					<th class="text-right">
						Pool<br/>Adj
						<a href="#" data-toggle="tooltip" title="This is the pool shares adjusted to reflect the pool magnitude in the network."><i style="color:black;" class="fa fa-info-circle"></i></a>						
					</th>
					<th class="text-right">
						GRC + Pool<br/>Adj %
						<a href="#" data-toggle="tooltip" title="This is the GRC network shares percentage combined with the pool shares."><i style="color:black;" class="fa fa-info-circle"></i></a>
					</th>
				</thead>
				<tbody>
	';
	$data = new GrcPool_Poll_Data($poll);
	$numberOfAnswers = $data->getNumberOfAnswers();
	for ($i = 0; $i < $numberOfAnswers; $i++) {
		$newContent .= '
			<tr>
				<td>
					'.($this->view->loggedIn?'
						<input '.(array_search($data->getAnswerId($i),$this->view->answerIds)!==false?'checked':'').' type="radio" name="poll_'.$poll->getId().'" value="'.$data->getAnswerId($i).'">
					':'
						<input type="radio" readonly disabled>
					').'
					'.$data->getPrettyAnswerText($i).'
				</td>
				<td class="text-right">'.$data->getAnswerGrcVoteCount($i).'</td>
				<td class="text-right">'.number_format($data->getAnswerGrcShares($i)).'</td>
				<td class="text-right">'.$data->getAnswerGrcPercent($i).'%</td>
				<td class="text-right">'.$data->getAnswerPoolVoteCount($i).'</td>
				<td class="text-right">'.number_format($data->getAnswerPoolShares($i)).'</td>
				<td class="text-right">'.number_format($data->getAnswerPoolAdjustedShare($i)).'</td>
				<td class="text-right">'.$data->getAnswerPoolAdjustedPercent($i).'%</td>
			</tr>		
		';
	}
	$newContent .= '
		<tr>
			<td></td>
			<td class="text-right"><strong>'.$data->getAnswerGrcVoteCountTotal().'</strong></td>
			<td class="text-right"><strong>'.number_format($data->getAnswerGrcSharesTotal()).'</strong></td>
			<td></td>
			<td class="text-right"><strong>'.$data->getAnswerPoolVoteCountTotal().'</strong></td>
			<td class="text-right"><strong>'.number_format($data->getAnswerPoolSharesTotal()).'</strong></td>
			<td class="text-right"><strong>'.number_format($data->getAnswerPoolAdjustedSharesTotal()).'</strong></td>
			<td></td>
		</tr>
	';
	$newContent .= '
				</tbody>
			</table>
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
	
	$collapse->addItem($data->getPrettyPollTitle(),$newContent,true);
}

if (!$this->view->polls) {
	$webPage->append(Bootstrap_Callout::info('There are no polls in progress.'));
}

if (!$this->view->loggedIn && $this->view->polls) {
	$webPage->append(Bootstrap_Callout::info('Please login to vote.'));
}

$webPage->append($collapse->render());

