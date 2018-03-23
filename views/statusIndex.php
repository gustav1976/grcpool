<?php

$webPage->appendHead('<meta http-equiv="refresh" content="30"/>');

$highest = 0;
foreach ($this->view->objs as $obj) {
	if ($obj->getBlock() > $highest) {
		$highest = $obj->getBlock();
	}
}

$webPage->append('<table class="table">
	<tr>
		<th>Client</th>
		<th>Version</th>
		<th>Block</th>
		<th>Hash</th>
		<th class="text-right">Diff</th>
		<th class="text-center">Conns</th>
		<th class="text-right">Balance</th>
	</tr>
');

foreach ($this->view->objs as $obj) {
	$title = $obj->getName();
	if (isset($this->view->names[$title])) {
		$title = $this->view->names[$title];
	}
	$status = 'success';
	if ($obj->getBlock() != $highest || $obj->getDiff() < .3) {
		$status = 'danger';
	}
	$webPage->append('
		<tr class="'.$status.'">
			<td>'.$title.'</td>
			<td>'.$obj->getVersion().'</td>
			<td>'.$obj->getBlock().'</td>
			<td>'.substr($obj->getHash(),0,10).'...'.substr($obj->getHash(),54).'</td>
			<td class="text-right">'.number_format($obj->getDiff(),3).'</td>
			<td class="text-center">'.$obj->getConnections().'</td>
			<td class="text-right">'.number_format($obj->getBalance(),8).'</td>
		</tr>
	');	
}

$webPage->append('</table>');