<?php
$webPage->appendTitle('GrcPool System Status');

$webPage->append('
	<style>
		.table-striped > tbody > tr > .danger,
		.table-striped > tbody > .danger > td,
		.table-striped > tbody > .danger > th {
		  background-color: #f2dede !important;
		}
		.table-striped > tbody > tr > .success,
		.table-striped > tbody > .success > td,
		.table-striped > tbody > .success > th {
		  background-color: #dff0d8 !important;
		}
	</style>	

	'.($this->view->details?'<a href="/about/system">&lt; Back To Status</a><br/><br/>':'').'
	
	<table class="table table-striped table-hoever">
		<thead><tr>
			<th>Task</th>
			<th>Last Run</th>
			<th>Executed</th>
			<th>Message</th>
			'.($this->view->details?'<th>Info</th>':'').'
		</tr></thead><tbody>
');
foreach ($this->view->tasks as $task) {
	$webPage->append('
		<tr>
			<td class="'.($task->getSuccess()?'success':'danger').'"><a href="/about/system/'.$task->getId().'">'.$task->getName().'</a></td>
			<td>'.Utils::getTimeAgo($task->getTheTime()).'</td>
			<td>'.($task->getTimeCompleted()?number_format($task->getTimeCompleted()-$task->getTimeStarted(),4).' s':'').'</td>
			<td>'.$task->getMessage().'</td>
			'.($this->view->details?'
				<td>'.$task->getInfo().'</td>
			':'').'
		</tr>
	');
}
$webPage->append('
	</tbody></table>
');
