<?php

$webPage->setPageTitle('My Host');

$webPage->appendHead('
	<script>
		function confirmDelete(projectId) {
			if (confirm("Are you sure you want to delete this project?")) {
				location.href="/account/host/'.$this->view->host->getId().'/delete/"+projectId;
			}
		}
	</script>
');

$webPage->append('
	<h3>Details</h3>
	<table class="table table-striped table-hover table-condensed">
		<tr><td>CPID</td><td>'.$this->view->host->getCpid().'</td></tr>
		<tr><td>Host Name</td><td>'.$this->view->host->getHostName().'</td></tr>
		<tr><td>BOINC Version</td><td>'.$this->view->host->getClientVersion().'</td></tr>
		<tr><td>Model</td><td>'.$this->view->host->getModel().'</td></tr>
		<tr><td>OS</td><td>'.$this->view->host->getOsName().'</td></tr>
		<tr><td>OS Version</td><td>'.$this->view->host->getOsVersion().'</td></tr>
		<tr><td>Product</td><td>'.$this->view->host->getProductName().'</td></tr>
		<tr><td>CPUs</td><td>'.$this->view->host->getNumberOfCpus().'</td></tr>
		<tr><td>Intel GPU</td><td>'.$this->view->host->getNumberOfIntels().'</td></tr>
		<tr><td>AMD GPU</td><td>'.$this->view->host->getNumberOfAmds().'</td></tr>
		<tr><td>NVidia GPU</td><td>'.$this->view->host->getNumberOfCudas().'</td></tr>
		<tr><td>First Contact</td><td>'.Utils::getTimeAgo($this->view->host->getFirstContact()).'</td></tr>
		<tr><td>Last Contact</td><td>'.Utils::getTimeAgo($this->view->host->getLastContact()).'</td></tr>		
	</table>
	<br/>
');

$haveIds = array();
$html = '';

foreach ($this->view->projects as $p) {
	$host = null;
	foreach ($this->view->hostProjects as $proj) {
		if ($p->getUrl() == $proj->getProjectUrl()) {
			$host = $proj;
			break;			
		}
	}
	if ($host) {
		$id = $p->getId();
		$haveIds[$p->getId()] = 1;
		$html .= '
			<tr>
				<td>
					'.($this->view->hasDeleteNotice?'
						<button type="button" onclick="confirmDelete('.$host->getId().')" class="btn btn-danger btn-xs">X</button>
					':'').'
					<input type="hidden" id="project_'.$id.'" name="ids[]" value="'.$id.'"/>
					'.$p->getName().'
					'.($p->getAttachable()?'':'<small><br/><span class="text-danger"><i class="fa fa-warning"></i> <a href="/project/#'.$p->getId().'">check project status</a></span></small>').'
					'.($host->getHostDbId()==0?'<small><br/><span class="text-danger"><i class="fa fa-warning"></i> <a href="/help/topics/1">This project may not be attached correctly, or needs sync.</a>':'').'
				</td>
				<td><input class="form-control" style="width:80px;" type="text" name="resourceShare_'.$id.'" value="'.$proj->getResourceShare().'"/></td>
				<td style="text-align:center;"><input value="1" type="checkbox" name="nocpu_'.$id.'" '.($proj->getNoCpu()?'checked':'').'/></td>
				<td style="text-align:center;"><input value="1" type="checkbox" name="nonvidiagpu_'.$id.'" '.($proj->getNoNvidiaGpu()?'checked':'').'/></td>
				<td style="text-align:center;"><input value="1" type="checkbox" name="noatigpu_'.$id.'" '.($proj->getNoAtiGpu()?'checked':'').'/></td>
				<td style="text-align:center;"><input value="1" type="checkbox" name="nointelgpu_'.$id.'" '.($proj->getNoIntelGpu()?'checked':'').'/></td>
				<td style="text-align:center;"><input '.($p->getWhiteList()?'':'checked disabled readonly').' value="1" type="checkbox" name="detach_'.$id.'" '.($proj->getAttached()==1&&$p->getWhiteList()?'':'checked').'/></td>					
			</tr>
		';
	}
}

$options = '';
foreach ($this->view->projects as $project) {
	if ($project->getWhiteList() && $project->getAttachable() && !isset($haveIds[$project->getId()])) {
		$options .= '<option value="'.$project->getId().'">'.$project->getName().'</opton>';
	}
}
$webPage->append('<h3 class="pull-left">Host Projects</h3>');
if ($options) {
	$webPage->append('
		<form class="form-inline pull-right rowpad">
			<div class="form-group">
				<select class="form-control" id="projects"><option value=""></option>'.$options.'</select>
				<button type="button" id="chooseButton" class="btn btn-info">choose</button>
				<p class="help-block"><small><a href="/help/chooseProject">project advice</a></small></p>
			</div>
		</form>
	');
}


	$webPage->append('
		<form method="post" action="/account/host/'.$this->view->host->getId().'">		
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Project</th>
						<th>Resource Share</th>
						<th style="text-align:center;">No Cpu</th>
						<th style="text-align:center;">No Nvidia GPU</th>
						<th style="text-align:center;">No ATI Gpu</th>
						<th style="text-align:center;">No Intel GPU</th>
						<th style="text-align:center;">Detach</th>
					</tr>
				</thead>
				<tbody id="projectTbody">
					'.$html.'
				</tbody>
			</table>
			<button type="submit" class="btn btn-primary">Save Project Settings</button>
			<input type="hidden" name="cmd" value="saveSettings"/>
		</form>
	');
$webPage->appendScript('
	<script>
		$("#chooseButton").click(function() {
			let selectBox = $("#projects");
			let notExists = $("#project_"+selectBox.val()).length==0;
			if (notExists && selectBox.val() != "") {
				let tbody = $("#projectTbody");
				let row = $("<tr></tr>");
				row.append("<td><input type=\"hidden\" id=\"project_"+selectBox.val()+"\" name=\"ids[]\" value=\""+selectBox.val()+"\"/>"+$("#projects option:selected").text()+"</td>");
				row.append("<td><input style=\"width:80px;\" class=\"form-control\" type=\"text\" name=\"resourceShare_"+selectBox.val()+"\" value=\"100\"/></td>");
				row.append("<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"nocpu_"+selectBox.val()+"\" value=\"1\"/></td>");
				row.append("<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"nonvidiagpu_"+selectBox.val()+"\" value=\"1\"/></td>");
				row.append("<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"noatigpu_"+selectBox.val()+"\" value=\"1\"/></td>");
				row.append("<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"nointelgpu_"+selectBox.val()+"\" value=\"1\"/></td>");
				row.append("<td style=\"text-align:right;\"><button onclick=\"discard("+selectBox.val()+")\" type=\"button\" class=\"btn btn-xs btn-danger discard\"><i class=\"fa fa-trash\"></i></button></td>");
				tbody.append(row);
			}			
		});
		function discard(id) {
			let proj = $("#project_"+id).parent().parent();
			proj.remove();
		};
	</script>
');
