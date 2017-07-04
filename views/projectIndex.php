<?php
$webPage->appendHead('
	<link rel="stylesheet" href="/assets/libs/tablesorter/2.28.7/theme.bootstrap.css">
');
$webPage->appendScript('
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
	</script>  
');
$webPage->setPageTitle('Projects');
$projects = '
	<table style="padding:3px;" id="projectTable" class="table table-striped table-hover table-condensed tablesorter">
		<thead>
			<tr>
				<th>Project</th>
				<th style="text-align:center;">White List</th>
				<th style="text-align:center;">Pool&nbsp;1</th>
				<th style="text-align:center;">Pool&nbsp;2</th>
				<th>Last Contact</th>
				<th class="text-right">Avg Credit</th>
				<th class="text-right">Min RAC</th>
				<th class="text-right">Hosts&nbsp;1</th>
				<th class="text-right">Mag&nbsp;1</th>
				<th class="text-right">Hosts&nbsp;2</th>
				<th class="text-right">Mag&nbsp;2</th>
			</tr>
		</thead>
		<tbody>
';
foreach ($this->view->accounts as $account) {
	$mag_1 = 0;
	$hostCount_1 = 0;
	$mag_2 = 0;
	$hostCount_2 = 0;
	if (isset($this->view->projStats[$account->getUrl()])) {
		//echo '<pre>';print_r($this->view->projStats[$account->getUrl()]);exit;
		if (isset($this->view->projStats[$account->getUrl()]['hostCount_1'])) {
			$hostCount_1 = $this->view->projStats[$account->getUrl()]['hostCount_1'];
			$mag_1 = $this->view->projStats[$account->getUrl()]['mag_1'];
		}
		if (isset($this->view->projStats[$account->getUrl()]['hostCount_2'])) {
			$hostCount_2 = $this->view->projStats[$account->getUrl()]['hostCount_2'];
			$mag_2 = $this->view->projStats[$account->getUrl()]['mag_2'];
		}
	}
	$projects .= '
		<tr>
			<td>
				<a href="'.$account->getUrl().'">'.$account->getName().'</a>
				'.($account->getMessage()?'<br/><i class="fa fa-warning text-danger"></i> '.$account->getMessage():'').'
			</td>
			<td style="text-align:center;">'.($account->getWhiteList()?'<i class="fa fa-thumbs-up text-success"></i>':'<i class="fa fa-thumbs-down text-danger"></i>').'</td>
			<td style="text-align:center;">'.($account->getAttachable()&&$account->getWeakKey()!=''?'<i class="fa fa-thumbs-up text-success"></i>':'<i class="fa fa-thumbs-down text-danger"></i>').'</td>
			<td style="text-align:center;">'.($account->getAttachable()&&$account->getWeakKey2()!=''?'<i class="fa fa-thumbs-up text-success"></i>':'<i class="fa fa-thumbs-down text-danger"></i>').'</td>
			<td>'.Utils::getTimeAgo($account->getLastSeen()).'</td>					
			<td class="text-right">'.number_format($account->getRac(),2).'</td>
			<td class="text-right">'.number_format($account->getMinRac(),2).'</td>
			<td class="text-right">'.$hostCount_1.'</td>
			<td class="text-right">'.number_format($mag_1,2).'</td>
			<td class="text-right">'.$hostCount_2.'</td>
			<td class="text-right">'.number_format($mag_2,2).'</td>					
		</tr>
	';
}
$projects .= '</tbody></table>';
$webPage->append('
	'.$projects.'	
	<br/>
	<img src="/api/projectChart" class="img-responsive"/>		
');