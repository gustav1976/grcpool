<?php
$numberOfPools = Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS);
$webPage->appendTitle('Pool Status');
$webPage->appendHead('
	<link rel="stylesheet" href="/assets/libs/bootstrapToggle/css/bootstrap-toggle.min.css">
	<link rel="stylesheet" href="/assets/libs/tablesorter/2.28.7/theme.bootstrap.css">
');
$poolFilters = '';
$poolAttach = '';
if ($numberOfPools > 1) {
	for ($p = 1; $p <= $numberOfPools; $p++) {
		$poolFilters .= 'if ($("#pool'.$p.'Select").is(":checked")) poolFilter.push('.$p.');';
		$poolAttach .= '
			if (poolFilter.includes('.$p.')) {
				$(".pool'.$p.'Attach").show();
				mag += Number(magCol.data("pool-'.$p.'"));
				host += Number(hostCol.data("pool-'.$p.'"));
			} else {
				$(".pool'.$p.'Attach").hide();
			}
		';		
	}
} else {
	$poolAttach .= '
		$(".pool1Attach").show();
		mag += Number(magCol.data("pool-1"));
		host += Number(hostCol.data("pool-1"));
	';
}
$webPage->appendScript('
	<script src="/assets/libs/bootstrapToggle/js/bootstrap-toggle.min.js"></script>
	<script src="/assets/libs/tablesorter/2.28.7/jquery.tablesorter.min.js"></script>
	<script src="/assets/libs/tablesorter/2.28.7/jquery.tablesorter.widgets.js"></script>
  	<script>
		$(function() {
		  $.tablesorter.themes.bootstrap = {
		    // these classes are added to the table. To see other table classes available,
		    // look here: http://getbootstrap.com/css/#tables
		    table        : "table table-bordered table-striped table-hover table-condensed",
		    // header class names
		    header       : "bootstrap-header", // give the header a gradient background (theme.bootstrap_2.css)
		    sortNone     : "",
		    sortAsc      : "",
		    sortDesc     : "",
		    active       : "", // applied when column is sorted
		    hover        : "", // custom css required - a defined bootstrap style may not override other classes
		    // icon class names
		    icons        : "", // add "icon-white" to make them white; this icon class is added to the <i> in the header
		    iconSortNone : "bootstrap-icon-unsorted", // class name added to icon when column is not sorted
		    iconSortAsc  : "glyphicon glyphicon-chevron-up", // class name added to icon when column has ascending sort
		    iconSortDesc : "glyphicon glyphicon-chevron-down", // class name added to icon when column has descending sort
// 		    filterRow    : "", // filter row class; use widgetOptions.filter_cssFilter for the input/select element
// 		    footerRow    : "",
// 		    footerCells  : "",
//		    even         : "", // even row zebra striping
//		    odd          : ""  // odd row zebra striping
		  };
		
		  // call the tablesorter plugin and apply the uitheme widget
		  $("#projectTable").tablesorter({
		    // this will apply the bootstrap theme if "uitheme" widget is included
		    // the widgetOptions.uitheme is no longer required to be set
		    theme : "bootstrap",
		    widthFixed: true,
		    headerTemplate : "{content} {icon}", // new in v2.7. Needed to add the bootstrap icon!
		    // widget code contained in the jquery.tablesorter.widgets.js file
		    // use the zebra stripe widget if you plan on hiding any rows (filter widget)
//		    widgets : [ "uitheme", "filter", "columns", "zebra" ],
		    widgets : [ "uitheme"],
		    widgetOptions : {
		      // using the default zebra striping class name, so it actually isnt included in the theme variable above
		      // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
		      //zebra : ["even", "odd"],
  		      // class names added to columns when sorted
		      //columns: [ "primary", "secondary", "tertiary" ],
		
		      // reset filters button
		      //filter_reset : ".reset",
		
		      // extra css class name (string or array) added to the filter element (input or select)
		      //filter_cssFilter: "form-control",
		
		      // set the uitheme widget to use the bootstrap theme class names
		      // this is no longer required, if theme is set
		      // ,uitheme : "bootstrap"
		
		    }
		  })
     	});
		$(\'[data-toggle="tooltip"]\').tooltip();
		function filterCapability() {
			var filter = [];
			//var mode = $("#mode").is(":checked");
			var mode = true;
			if ($("#linuxSelect").is(":checked")) filter.push("linux");
			if ($("#windowsSelect").is(":checked")) filter.push("windows");
			if ($("#macSelect").is(":checked")) filter.push("mac");
			if ($("#intelSelect").is(":checked")) filter.push("intel");
			if ($("#amdSelect").is(":checked")) filter.push("amd");
			if ($("#nvidiaSelect").is(":checked")) filter.push("nvidia");
			if ($("#virtualBoxSelect").is(":checked")) filter.push("virtualBox");
			if ($("#androidSelect").is(":checked")) filter.push("android");
			if ($("#raspberrypiSelect").is(":checked")) filter.push("raspberrypi");
			var poolFilter = [];
			'.$poolFilters.'
			$(\'#projectTable > tbody  > tr\').each(function() {
				if (filter.length) {
					var show = true;
					for (var i =0 ; i < filter.length; i++) {
						if (mode) {
							var check = $(this).find("."+filter[i]);
							if (check.hasClass("grayscale")) {
								show = false;
								break;
							}							
						} else {
							var check = $(this).find("."+filter[i]);
							if (!check.hasClass("grayscale")) {
								show = false;
							}							
						}
					}
					if (show) {
						$(this).show();
					} else {
						$(this).hide();
					}
				} else {
					$(this).show();
				}

				var mag = 0;
				var host = 0;
				var magCol = ($(this).find(".magCol"));
				var hostCol = ($(this).find(".hostCol"));
				'.$poolAttach.'
				magCol.html(mag.toFixed(2));
				hostCol.html(host.toFixed(0));
			});
			
		}
	</script>  
');
$buttons = '';
if ($numberOfPools > 1) {
	$buttons .= '
		<div class="btn-group rowpad">
			<div class="btn-group rowpad">
				<div class="btn-group" data-toggle="buttons">
	';
	for ($p = 1; $p <= $numberOfPools; $p++) {
		$buttons .= '
		  			<label class="btn btn-default active">
	    				<input onchange="filterCapability();" id="pool'.$p.'Select" type="checkbox" autocomplete="off" checked>
						Pool '.$p.'
	  				</label>
		';	
	}
	$buttons .= '
				</div>
			</div>
		</div>
	';
}
$projects = '
	'.$buttons.'
	<div class="pull-right">
		<!--
		<div class="btn-group rowpad">
   			<input onchange="filterCapability();" id="mode" type="checkbox" checked autocomplete="off" data-toggle="toggle" data-on="include" data-off="exclude" data-onstyle="success" data-offstyle="danger">
		</div>
		-->
		<div class="btn-group rowpad">
			<div class="btn-group" data-toggle="buttons">
	  			<label class="btn btn-default">
	    			<input onchange="filterCapability();" id="linuxSelect" type="checkbox" autocomplete="off">
					<img title="Linux" style="height:20px;" src="/assets/images/svg/linux.svg"/>
	  			</label>
	  			<label class="btn btn-default">
	    			<input onchange="filterCapability();" id="windowsSelect" type="checkbox" autocomplete="off">
					<img title="Windows" style="height:20px;" src="/assets/images/svg/windows.svg"/>
	  			</label>
	  			<label class="btn btn-default">
	    			<input onchange="filterCapability();" id="macSelect" type="checkbox" autocomplete="off">
					<img title="Mac" style="height:20px;" src="/assets/images/svg/mac.svg"/>
	  			</label>
			</div>
		</div>
		<div class="btn-group rowpad">
			<div class="btn-group" data-toggle="buttons">
	  			<label class="btn btn-default">
	    			<input onchange="filterCapability();" id="intelSelect" type="checkbox" autocomplete="off">
					<img title="Intel GPU" style="height:20px;" src="/assets/images/svg/intel.svg"/>
	  			</label>
	  			<label class="btn btn-default">
	    			<input onchange="filterCapability();" id="amdSelect" type="checkbox" autocomplete="off">
					<img title="AMD GPU" style="height:20px;" src="/assets/images/svg/amd.svg"/>
	  			</label>
	  			<label class="btn btn-default">
	    			<input onchange="filterCapability();" id="nvidiaSelect" type="checkbox" autocomplete="off">
					<img title="Nvidia GPU" style="height:20px;" src="/assets/images/svg/nvidia.svg"/>
	  			</label>
			</div>
		</div>
		<div class="btn-group rowpad">
			<div class="btn-group" data-toggle="buttons">
	  			<label class="btn btn-default">
	    			<input onchange="filterCapability();" id="virtualBoxSelect" type="checkbox" autocomplete="off">
					<img title="Virtual Box" style="height:20px;" src="/assets/images/svg/virtualBox.svg"/>
	  			</label>
	  			<label class="btn btn-default">
	    			<input onchange="filterCapability();" id="androidSelect" type="checkbox" autocomplete="off">
					<img title="Android" style="height:20px;" src="/assets/images/svg/android.svg"/>
	  			</label>
	  			<label class="btn btn-default">
	    			<input onchange="filterCapability();" id="raspberrypiSelect" type="checkbox" autocomplete="off">
					<img title="Raspberry Pi" style="height:20px;" src="/assets/images/svg/raspberrypi.svg"/>
	  			</label>
			</div>
		</div>
	</div>
	<br clear="all"/>
	<div class="table-responsive">
		<table style="padding:3px;" id="projectTable" class="table table-striped table-hover table-condensed tablesorter">
			<thead>
				<tr>
					<th>Project</th>
					<th style="text-align:center;" title="Project is in the Gridcoin network.">GRC</th>
					<th style="text-align:center;" title="Project is in the SPARC network.">SPARC</th>
					<th style="text-align:center;" title="Project is in the pool white list.">Pool</th>
					<th style="text-align:center;" title="Project is attachable in the pool to your host.">Attach</th>
					<th class="text-center">Updated</th>
					<th class="text-right" title="The minimum RAC a host must achieve to receive pool magnitude.">Min RAC</th>
					<th class="text-right">Hosts</th>
					<th class="text-right">Mag</th>
					<th class="text-right" title="This is the minimum amount of RAC needed by a host to achieve a magnitude.">Tasks</th>
					<th class="text-center sorter-false">CPU</th>
					<th class="text-center sorter-false">GPU</th>
					<th class="text-center sorter-false">Other</th>
				</tr>
			</thead>
			<tbody>
';

foreach ($this->view->accounts as $account) {
	
 	if ($account->getLastSeen() < time()-60*60*24*60 && $account->getSparc() == 0) {
 		continue;
 	}
	
	$mag = 0;
	$hostCount = 0;
	$hostColData = '';
	$magColData = '';
	$attachable = '';

	for ($p = 1; $p <= $numberOfPools; $p++) {
		if ($this->view->poolFilter == 0 || $this->view->poolFilter == $p) {
			$attachable .= '
				<span class="fa-stack pool'.$p.'Attach">
					<i class="fa fa-circle text-'.($account->{'pool'.$p.'Attach'}?'success':'danger').' fa-stack-2x"></i>
					<strong class="fa-stack-1x" style="color:white;">'.$p.'</strong>
				</span>
			';
			if (isset($this->view->projStats[$account->getId()])) {
				if (isset($this->view->projStats[$account->getId()]['hostCount_'.$p])) {
					$hostCount += $this->view->projStats[$account->getId()]['hostCount_'.$p];
					$mag += $this->view->projStats[$account->getId()]['mag_'.$p];
				}
			}
		}
		$hostColData .= 'data-pool-'.$p.'="'.($this->view->projStats[$account->getId()]['hostCount_'.$p]??0).'" ';
		$magColData .= 'data-pool-'.$p.'="'.($this->view->projStats[$account->getId()]['mag_'.$p]??0).'" ';
	}
	
	$projects .= '
		<tr>
			<td>
				<!--<a href="/project/detail/'.$account->getId().'">'.$account->getName().'</a>-->
				'.$account->getName().'
				'.($account->getMessage()?'<a href="#" data-toggle="tooltip" title="'.$account->getMessage().'"><i class="text-danger fa fa-warning"></i></a>':'').'<br/>
				<small><em>'.$this->view->boincUrls[$account->getUrlId()]->getUrl().'</em></small>
			</td>
			<td style="text-align:center;">
				<i class="fa fa-circle fa-2x text-'.(array_search($account->getGrcName(),$this->view->networkProjects)!==false?'success':'danger').'"></i>
			</td>
			<td style="text-align:center;">
				<i class="fa fa-circle fa-2x text-'.($account->getSparc()==1?'success':'danger').'"></i>
			</td>			
			<td style="text-align:center;">
				<i class="fa fa-circle fa-2x text-'.($account->getWhiteList()?'success':'danger').'"></i>
			</td>
			<td style="text-align:center;">'.$attachable.'</td>
			<td class="text-center">'.Utils::getTimeAgo($account->getLastSeen()).'</td>					
			<td class="text-right">'.number_format($account->getMinRac(),2).'</td>
			<td 
				'.$hostColData.'
				class="text-right hostCol">'.$hostCount.'</td>
			<td
				'.$magColData.'
				class="text-right magCol">'.number_format($mag,2).'</td>
			<td class="text-right">'.(isset($this->view->tasksToSend[$account->getId()])?number_format($this->view->tasksToSend[$account->getId()]):'').'</td>
			<td class="text-center">
				<img class="'.($account->getLinux()?'':'grayscale').' linux" title="Linux" style="height:22px;" src="/assets/images/svg/linux.svg"/>
				<img class="'.($account->getWindows()?'':'grayscale').' windows" title="Windows" style="height:22px;" src="/assets/images/svg/windows.svg"/>
				<img class="'.($account->getMac()?'':'grayscale').' mac" title="Mac" style="height:22px;" src="/assets/images/svg/mac.svg"/>
			</td>
			<td class="text-center">
				<img class="'.($account->getIntel()?'':'grayscale').' intel" title="Intel GPU" style="height:22px;" src="/assets/images/svg/intel.svg"/>
				<img class="'.($account->getAmd()?'':'grayscale').' amd" title="AMD GPU" style="height:22px;" src="/assets/images/svg/amd.svg"/>
				<img class="'.($account->getNvidia()?'':'grayscale').' nvidia" title="Nvidia GPU" style="height:22px;" src="/assets/images/svg/nvidia.svg"/>
			</td>
			<td class="text-center">
				<img class="'.($account->getVirtualBox()?'':'grayscale').' virtualBox" title="Virtualbox" style="height:22px;" src="/assets/images/svg/virtualBox.svg"/>
				<img class="'.($account->getAndroid()?'':'grayscale').' android" title="Android" style="height:22px;" src="/assets/images/svg/android.svg"/>
				<img class="'.($account->getRaspberryPi()?'':'grayscale').' raspberrypi" title="Raspberry Pi" style="height:22px;" src="/assets/images/svg/raspberrypi.svg"/>
			</td>
		</tr>
	';
}
$projects .= '</tbody></table></div>';
$webPage->append('
	'.$projects.'	
');
