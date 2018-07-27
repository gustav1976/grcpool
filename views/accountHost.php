<?php
$webPage->addBreadcrumb('account','user','/account');
$webPage->addBreadcrumb('hosts','desktop','/account/hosts');
$webPage->appendHead('
	<script>
		function confirmDelete(projectId) {
			if (confirm("Are you sure you want to delete this project? This will delete the project from the pool, but it may remain in your client if you have not detached with a syncronization first.")) {
				$.ajax({
	  				type: "POST",
	  				url: "/api/hostProjectDelete/'.$this->view->host->getId().'/"+projectId,
					success: function(data) {
						processJson(data);
					},
					error: function(data) {

					}
				});
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
		function submitForm() {
			$("#saveIcon").hide();
			$("#saveRefresh").show();
			$.ajax({
  				type: "POST",
  				url: "/api/hostSettings/'.$this->view->host->getId().'",
  				data: $("#theForm").serialize(),
				success: function(data) {
					$("#saveIcon").show();
					$("#saveRefresh").hide();
					$("#saveMessage").removeClass("text-danger");
					$("#saveMessage").addClass("text-success");
					$("#saveMessage").html("Saved!");
					$("#saveMessage").show().fadeOut(3000);					
					processJson(data);
				},
				error: function(data) {
					$("#saveIcon").show();
					$("#saveRefresh").hide();
					$("#saveMessage").removeClass("text-success");
					$("#saveMessage").addClass("text-danger");
					$("#saveMessage").html("An error occurred...");
					$("#saveMessage").show().fadeOut(3000);
				}
			});
		}
		function processJson(json) {
			var count = json.length;
			var select = $("#projects");
			var tbody = $("#projectTbody");
			tbody.empty();
			select.find("option").remove();
			select.append(\'<option value="">--- choose project ---</option>\');
			for (var i = 0; i < count; i++) {
				var proj = json[i];
				if (!proj.inClient && proj.attachable) {
					select.append(\'<option value="\'+proj.id+\'">\'+proj.name+\'</option>\');
				} else if (proj.inClient) {
					var row = $("<tr></tr>");
					row.append("<td><input type=\"hidden\" id=\"project_"+proj.id+"\" name=\"ids[]\" value=\""+proj.id+"\"/>"+proj.name+"</td>");
					row.append("<td class=\"text-center\"><input style=\"width:80px;height:24px;line-height:24px;\" class=\"form-control\" type=\"text\" name=\"resourceShare_"+proj.id+"\" value=\""+(proj.resourceShare)+"\"/></td>");
					row.append("<td style=\"text-align:center;\"><input "+(proj.noCpu?"checked":"")+" type=\"checkbox\" name=\"nocpu_"+proj.id+"\" value=\"1\"/></td>");
					row.append("<td style=\"text-align:center;\"><input "+(proj.noNvidiaGpu?"checked":"")+" type=\"checkbox\" name=\"nonvidiagpu_"+proj.id+"\" value=\"1\"/></td>");
					row.append("<td style=\"text-align:center;\"><input "+(proj.noAtiGpu?"checked":"")+" type=\"checkbox\" name=\"noatigpu_"+proj.id+"\" value=\"1\"/></td>");
					row.append("<td style=\"text-align:center;\"><input "+(proj.noIntelGpu?"checked":"")+" type=\"checkbox\" name=\"nointelgpu_"+proj.id+"\" value=\"1\"/></td>");
					row.append("<td style=\"text-align:center;\"><input "+(proj.attached?"":"checked")+" type=\"checkbox\" name=\"detach_"+proj.id+"\" value=\"1\"/></td>");
					tbody.append(row);
					row = $("<tr></tr>");
					row.append("\
						<td style=\"background-color:#fafafa;border-top:0px;\" colspan=\"6\">\
							"+(proj.warning?"<div class=\"text-danger\"><small><i class=\"fa fa-warning\"></i>&nbsp;"+proj.warning+"</small></div>":"")+"\
							"+(proj.message?"<div><small>"+proj.message+"</small></div>":"")+"\
						</td>\
						<td style=\"background-color:#fafafa;border-top:0px;\" class=\"text-center\">\
							'.($this->view->hasDeleteNotice?'\
								<small>\
									<a href=\"javascript:confirmDelete("+proj.projectId+");\" class=\"text-danger\">delete project</a>\
								</small>\
							':'').'\
						</td>\
					");
					tbody.append(row);
				}					
			}
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
				//$host = $proj;
			} else {
				array_push($otherPoolHosts,$proj);				
			}
		}
	}
}

$panel = new Bootstrap_Panel();
$panel->setHeader('Projects for Pool #'.$this->getUser()->getPoolId());
$panel->setContext('info');
$content = '';

$content .= '
	<form class="form-inline rowpad">
		<div class="form-group">
			<select class="form-control" id="projects"></select>
			<button type="button" id="chooseButton" class="btn btn-info">add</button>
			&nbsp;&nbsp;&nbsp;&nbsp;<small><a href="/help/chooseProject">project advice</a></small>
		</div>
	</form>
';

$content .= '
	<form id="theForm" method="post" action="/account/host/'.$this->view->host->getId().'">		
		<table class="table">
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
			</tbody>
		</table>
		<button type="button" onclick="submitForm();" class="btn btn-primary">
			<i id="saveRefresh" style="display:none;" class="fa fa-refresh fa-spin"></i>
			<i id="saveIcon" style="" class="fa fa-gear"></i>
			Save Project Settings
		</button>
		<span class="" id="saveMessage" style="margin-left:20px;"></span>
	</form>
';
$panel->setContent($content);
$webPage->append($panel->render());

if ($otherPoolHosts) {
	$panel = new Bootstrap_Panel();
	$panel->setHeader('Unlinked/Orphan Projects');
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
				row.append("\
					<td>\
						<input type=\"hidden\" id=\"project_"+selectBox.val()+"\" name=\"ids[]\" value=\""+selectBox.val()+"\"/>\
						"+$("#projects option:selected").text()+"\
						<br/>&nbsp;&nbsp;<small><a href=\"javascript:void();\" onclick=\"discard("+selectBox.val()+")\">remove</a></small>\
					</td>\
				");
				row.append("<td><input style=\"width:80px;\" class=\"form-control\" type=\"text\" name=\"resourceShare_"+selectBox.val()+"\" value=\"100\"/></td>");
				row.append("<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"nocpu_"+selectBox.val()+"\" value=\"1\"/></td>");
				row.append("<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"nonvidiagpu_"+selectBox.val()+"\" value=\"1\"/></td>");
				row.append("<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"noatigpu_"+selectBox.val()+"\" value=\"1\"/></td>");
				row.append("<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"nointelgpu_"+selectBox.val()+"\" value=\"1\"/></td><td>&nbsp;</td>");
				tbody.append(row);
			}			
		});
		function discard(id) {
			let proj = $("#project_"+id).parent().parent();
			proj.remove();
		};
		processJson('.$this->view->json.');
	</script>
');
