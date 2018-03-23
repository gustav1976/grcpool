<?php
$webPage->setPageTitle('Pool Status');
$panel = new Bootstrap_Panel();
$panel->setHeader('Current Pool Gridcoin Wallet Status');
$panel->setContent('<div id="blockInfo"></div>');
$panel->setId('blockInfoPanel');
$webPage->append($panel->render());

$panel = new Bootstrap_Panel();
$panel->setHeader('Past Block\'s Diff and Hashes');
$panel->setContent('<div id="blockInfoPast"></div>');
$webPage->append($panel->render());

//$this->view->block->lastBlocks = array_reverse($this->view->block->lastBlocks);

$webPage->appendScript('
	<script>
		function getBlockData() {
			$.get("/status/api",function(data) {
				$("#blockInfo").animate({"opacity":0},1000,function() {
					$("#blockInfoPanel").removeClass("panel-success");
					$("#blockInfoPanel").removeClass("panel-danger");
					$("#blockInfoPanel").removeClass("panel-default");
					if (data.inSync) {
						$("#blockInfoPanel").addClass("panel-success");
					} else {
						$("#blockInfoPanel").addClass("panel-danger");
					}
					$(this).empty();
 					var current = data.current;
					var table = jQuery(\'<table class="table"></table>\');
					table.append(\'\
						<thead><tr>\
							<th>Client</th>\
							<th class="text-center">Version</th>\
							<th class="text-center">Block</th>\
							<th class="text-center">Hash</th>\
							<th class="text-center">Diff</th>\
							<th class="text-center">Conns</th>\
							<th class="text-right">Balance</th>\
						</tr></thead>\
					\');
					var tableBody = jQuery(\'<tbody></tbody>\');
					for (var key in current) {
						tableBody.append(\'\
							<tr class="\'+current[key].status+\'">\
								<td>\'+current[key].client+\'</td>\
								<td class="text-center">\'+current[key].version+\'</td>\
								<td class="text-center">\'+current[key].block+\'</td>\
								<td class="text-center">\'+current[key].hash.substring(0,10)+\'...</td>\
								<td class="text-center">\'+current[key].diff+\'</td>\
								<td class="text-center">\'+current[key].connections+\'</td>\
								<td class="text-right">\'+current[key].balance+\'</td>\
							</tr>\
						\');
					}
					table.append(tableBody);
					$(this).prepend(table);
				}).animate({"opacity":1},1000);

				if (data.history) {
					$("#blockInfoPast").animate({"opacity":0},1000,function() {
						$(this).empty();
						var lastBlocks = data.history.lastBlocks;
						for (var key in lastBlocks) {
							var container = jQuery(\'<div style=\"margin-bottom:5px;\" class=\"rowpad\"></div>\');
							container.append("<div><strong>"+key+"</strong></div>");
							var poolHtml = "";
							for (var pool in lastBlocks[key]) {
		 						var hash = lastBlocks[key][pool].hash;
		 						var diff = lastBlocks[key][pool].diff;
								container.append("<div style=\"margin-left:10px;\">#"+pool+" "+diff+" "+hash+"</div>");
							}
		 					$(this).prepend(container);
						}
					}).animate({"opacity":1},1000);
				}	
			});			
		}
		getBlockData();
		socket.on("updatePoolBlocks",function() {
			getBlockData();
		});
	</script>
');
