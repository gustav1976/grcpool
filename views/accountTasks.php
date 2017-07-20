<?php
$webPage->addBreadcrumb('account','user','/account');

$webPage->appendHead('

');

$table = '';
foreach ($this->view->tasks as $task) {
	$hostName = $this->view->hostNames[$task->getDeviceId()];
	$table .= '
		<tr>
			<td title="'.$hostName.'">'.(strlen($hostName) > 15?substr($hostName,0,5).'...'.substr($hostName,strlen($hostName)-5,5):$hostName).'</td>
			<td><span title="'.$task->getName().'">'.substr($task->getName(),0,10).'...</span></td>
			<!--<td>'.$task->getAppName().'</td>-->
			<!--<td>'.$task->getWorkUnitId().'</td>-->
			<td class="text-right">'.number_format($task->getClaimedCredit(),2).'</td>
			<td class="text-right">'.number_format($task->getGrantedCredit()).'</td>
			<td class="text-right">'.number_format($task->getCpuTime(),2).'</td>
			<td class="text-right">'.number_format($task->getElapsedTime(),2).'</td>
			<!--<td>'.$task->getExitStatus().'</td>-->
			<!--<td>'.$task->getResultId().'</td>-->
			<td class="text-center">'.date('Y-m-d',$task->getSentTime()).'</td>
			<!--<td class="text-center">'.($task->getReceivedTime()?date('Y-d-m',$task->getReceivedTime()):'-').'</td>-->
			<td class="text-center">'.date('Y-m-d',$task->getReportDeadline()).'</td>
			<td class="text-center">'.GrcPool_Boinc_ServerStateEnum::codeToText($task->getServerState()).'</td>
			<td class="text-center">'.GrcPool_Boinc_ValidationEnum::codeToText($task->getValidateState()).'</td>
			<!--<td>'.GrcPool_Boinc_OutcomeEnum::codeToText($task->getOutcome()).'</td>-->
			<!--<td>'.$task->getFileDeleteState().'</td>-->
		</tr>
	';
}

function getQuery($view,$exclude,$val) {
	$elements = array(
		'filter_app','filter_host','sort','filter_valid'
	);
	$result = '/account/tasks/0?';
	foreach ($elements as $element) {
		if ($element != $exclude) {
			$result .= $element.'='.$view->$element."&";
		}
	}
	$result .= $exclude.'='.$val;
	return $result;
}

$webPage->append('
	<div class="pull-right rowpad">
		'.$this->view->pagination.'
	</div>
	<div class="rowpad">
		<div class="btn-group">
			<div class="dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			    	Sort By
			    	<span class="caret"></span>
			  	</button>
			  	<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				    <li><a href="'.getQuery($this->view,'sort','mod').'">'.($this->view->sort == 'mod'?'<i class="fa fa-check"></i>':'').' Last Modified</a></li>
					<li><a href="'.getQuery($this->view,'sort','cpu').'">'.($this->view->sort == 'cpu'?'<i class="fa fa-check"></i>':'').' CPU Time</a></li>
			  	</ul>
			</div>
		</div>
	</div>
	
	<table class="table table-striped table-hover table-condensed">
		<tr>
			<th>
				<div class="dropdown">
			  		<button class="btn btn-'.($this->view->filter_host==''?'default':'success').' dropdown-toggle" type="button" data-toggle="dropdown">
			    		<strong>Host</strong>
			    		<span class="caret"></span>
			  		</button>
			  		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
						<li><a href="'.getQuery($this->view,'filter_host','').'">'.($this->view->filter_host==''?'<i class="fa fa-check"></i>':'').' -- ALL --</a></li>
');
foreach ($this->view->hosts as $host) {
	$webPage->append('
		<li><a href="'.getQuery($this->view,'filter_host',$host->getId()).'">'.($this->view->filter_host==$host->getId()?'<i class="fa fa-check"></i>':'').' '.$host->getHostName().'</a></li>
	');
}
	
$webPage->append('
					</ul>
				</div>
			</th>
			<th>
				<div class="dropdown">
			  		<button class="btn btn-'.($this->view->filter_app==''?'default':'success').' dropdown-toggle" type="button" data-toggle="dropdown">
			    		<strong>Application</strong>
			    		<span class="caret"></span>
			  		</button>
			  		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
						<li><a href="'.getQuery($this->view,'filter_app','').'">'.($this->view->filter_app==''?'<i class="fa fa-check"></i>':'').' -- ALL --</a></li>
');
foreach ($this->view->appNames as $app) {
	$webPage->append('
		<li><a href="'.getQuery($this->view,'filter_app',$app).'">'.($this->view->filter_app==$app?'<i class="fa fa-check"></i>':'').' '.strtoupper($app).'</a></li>
	');
}
	
if (!$table) {
	$table .= '<tr><td colspan="10">No results found.</td></tr>';
}

$webPage->append('
					</ul>
				</div>
			</th>
			<!--<th>Work Unit</th>-->
			<th class="text-right">Claimed<br/>Credit</th>
			<th class="text-right">Granted<br/>Credit</th>
			<th class="text-right">Cpu<br/>Time</th>
			<th class="text-right">Elapsed<br/>Time</th>
			<th class="text-center">Sent</th>
			<!--<th class="text-center">Received</th>-->
			<th class="text-center">Deadline</th>
			<th class="text-center">Server</th>
			<th class="text-center">
				<div class="dropdown">
			  		<button class="btn btn-'.($this->view->filter_valid==''?'default':'success').' dropdown-toggle" type="button" data-toggle="dropdown">
			    		<strong>Valid</strong>
			    		<span class="caret"></span>
			  		</button>
			  		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
						<li><a href="'.getQuery($this->view,'filter_valid','').'">'.($this->view->filter_valid==''?'<i class="fa fa-check"></i>':'').' -- ALL --</a></li>
');
foreach ($this->view->valids as $key => $valid) {
	$webPage->append('
		<li><a href="'.getQuery($this->view,'filter_valid',$key).'">'.($this->view->filter_valid==$key?'<i class="fa fa-check"></i>':'').' '.$valid.'</a></li>
	');
}
	
$webPage->append('
					</ul>
				</div>

			</th>
		</tr>
		'.$table.'
	</table>
');
