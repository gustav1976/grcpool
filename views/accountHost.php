<?php

$webPage->addBreadcrumb('account','user','/account');
$webPage->addBreadcrumb('hosts','desktop','/account/hosts');

$webPage->appendHead('
	<script>
		function confirmDelete(projectId) {
			if (confirm("Are you sure you want to delete this project?")) {
				location.href="/account/host/'.$this->view->host->getId().'/delete/"+projectId;
			}
		}
		function updateCustomName() {
			$.ajax({
  				type: "POST",
  				url: "/api/hostName/'.$this->view->host->getId().'",
  				data: JSON.stringify({"customName":$("#customName").val()}),
				success: function(data) { closeHostNameEdit(data.customName); },
				error: function(data) { closeHostNameEdit(""); },
				contentType: "application/json",
  				dataType: "json"
			});
		}
		function closeHostNameEdit(name) {
			if (name != "") {
				$("#hostName").html(name);
			}
			$("#hostName").show();
			$("#customNameGroup").hide();
		}
		function editHostName() {
			$("#customName").val($("#hostName").html());
			$("#hostName").hide();
			$("#customNameGroup").show();
			$("#customName").focus();
			$("#customName").select();
		}
	</script>
');

$panel = new Bootstrap_Panel();
$panel->setHeader('Host Details - '.$this->view->host->getHostName());
$panel->setContext('info');
$panel->setContent('
	<table class="table table-striped table-hover table-condensed">
		<tr><td>CPID</td><td>'.$this->view->host->getCpid().'</td></tr>
		<tr><td>Host Name</td><td>
			<a id="hostName" href="javascript:editHostName();" title="customize: '.$this->view->host->getHostName().'">'.($this->view->host->getCustomName()!=''?$this->view->host->getCustomName():$this->view->host->getHostName()).'</a>
			<div id="customNameGroup" style="display:none;">
				<div class="input-group col-xs-6 col-sm-5 col-md-4 col-mg-3">
					<input class="form-control" maxlength="50" type="text" value="" id="customName"/>	
		      		<span class="input-group-btn">
	        			<button onclick="updateCustomName();" class="btn btn-default" type="button"><i class="fa fa-arrow-right"></i></button>
      				</span>
				</div>
				<small>'.$this->view->host->getHostName().'</small>
			</div>
		</td></tr>
		<tr><td>BOINC Version</td><td>'.$this->view->host->getClientVersion().'</td></tr>
		<tr><td>Model</td><td>'.$this->view->host->getModel().'</td></tr>
		<tr><td>OS</td><td>'.$this->view->host->getOsName().' '.$this->view->host->getOsVersion().' '.$this->view->host->getProductName().'</td></tr>
		<tr><td>Capability</td><td>
			CPUS: '.$this->view->host->getNumberOfCpus().',
			Intel GPU: '.$this->view->host->getNumberOfIntels().',
			AMD: '.$this->view->host->getNumberOfAmds().',
			NVIDIA: '.$this->view->host->getNumberOfCudas().'
		</td></tr>
		<tr><td>First Contact</td><td>'.Utils::getTimeAgo($this->view->host->getFirstContact()).'</td></tr>
		<tr><td>Last Contact</td><td>'.Utils::getTimeAgo($this->view->host->getLastContact()).'</td></tr>		
	</table>
');
$webPage->append($panel->render());

$haveIds = array();
$html = '';

$otherPoolHosts = array();
foreach ($this->view->projects as $p) {
	$host = null;
	foreach ($this->view->hostProjects as $proj) {
		if ($p->getId() == $proj->getAccountId()) {
			if ($proj->getPoolId() == $this->getUser()->getPoolId() && $proj->getAttached() != 2) {
				$host = $proj;
			} else {
				array_push($otherPoolHosts,$proj);				
			}
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
					'.($host->getHostDbId()==0?'
						<small><br/><span class="text-danger"><i class="fa fa-warning"></i> <a href="/help/topics/1">This project may not be attached correctly, or needs sync.</a>
					':'
						<small><br/><a target="_blank" href="'.$p->getBaseUrl().'show_host_detail.php?hostid='.$host->getHostDbid().'">host project details</a></small>
					').'
				</td>
				<td><input class="form-control" style="width:80px;" type="text" name="resourceShare_'.$id.'" value="'.$host->getResourceShare().'"/></td>
				<td style="text-align:center;"><input value="1" type="checkbox" name="nocpu_'.$id.'" '.($host->getNoCpu()?'checked':'').'/></td>
				<td style="text-align:center;"><input value="1" type="checkbox" name="nonvidiagpu_'.$id.'" '.($host->getNoNvidiaGpu()?'checked':'').'/></td>
				<td style="text-align:center;"><input value="1" type="checkbox" name="noatigpu_'.$id.'" '.($host->getNoAtiGpu()?'checked':'').'/></td>
				<td style="text-align:center;"><input value="1" type="checkbox" name="nointelgpu_'.$id.'" '.($host->getNoIntelGpu()?'checked':'').'/></td>
				<td style="text-align:center;"><input value="1" type="checkbox" name="detach_'.$id.'" '.($host->getAttached()==1?'':'checked').'/></td>					
			</tr>
		';
	}
}

$options = '';
foreach ($this->view->projects as $project) {
	if ($project->attachable && !isset($haveIds[$project->getId()])) {
		$options .= '<option value="'.$project->getId().'">'.$project->getName().'</opton>';
	}
}

$panel = new Bootstrap_Panel();
$panel->setHeader('Projects for Pool #'.$this->getUser()->getPoolId());
$panel->setContext('info');
$content = '';

if ($options) {
	$content .= '
		<form class="form-inline rowpad">
			<div class="form-group">
				<select class="form-control" id="projects"><option value=""></option>'.$options.'</select>
				<button type="button" id="chooseButton" class="btn btn-info">choose</button>
				&nbsp;&nbsp;&nbsp;&nbsp;<small><a href="/help/chooseProject">project advice</a></small>
			</div>
		</form>
	';
}


$content .= '
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
';
$panel->setContent($content);
$webPage->append($panel->render());

if ($otherPoolHosts) {
	$panel = new Bootstrap_Panel();
	$panel->setHeader('Unlinked Projects');
	$panel->setContext('info');
	$html = '<table class="table table-striped table-hover"><tr><th>Project</th><th class="text-center">Pool #</th></tr>';

	foreach ($otherPoolHosts as $host) {
		$html .= '
			<tr>
				<td>'.$this->view->projects[$host->getAccountId()]->getName().'</td>
				<td class="text-center">'.$host->getPoolId().'</td>
			</tr>
		';
	}
	$html .= '</table>
		<p><em>These projects are still linked to this host, but are not manageable.</em></p>
	';
	$panel->setContent($html);
	$webPage->append($panel->render());
}

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
