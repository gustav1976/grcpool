<?php
$webPage->appendTitle('Pool SPARC Report');

$webPage->append('
	<div class="panel panel-default">
		<div class="panel-heading">
   			<h3 class="panel-title">SPARC Summary</h3>
		</div>
		<div class="panel-body">
			<strong>SPARC Balances</strong>
			<ul>
				<li>Pool 1: <a href="https://etherscan.io/address/0xbe7191f56918c3d8c6df199663b74b0ff9e7b1b9">0xbe7191f56918c3d8c6df199663b74b0ff9e7b1b9</a></li>
				<li>Pool 2: <a href="https://etherscan.io/address/0x897dddb94e33956ff159f4a501df460a91a157c5">0x897dddb94e33956ff159f4a501df460a91a157c5</a></li>
				<li>Pool 3: <a href="https://etherscan.io/address/0x09dfc7deb08fd86a4d59b65fc6cf562ee10da529">0x09dfc7deb08fd86a4d59b65fc6cf562ee10da529</a></li>
			</ul>			

			<br/>

			<strong>SPARC Owed by Pool</strong>
			<ul>
');
foreach ($this->view->sparcOwed as $i => $o) {
	$webPage->append('<li>Pool '.$i.': '.$o.'</li>');
}
$webPage->append('
			</ul>
		</div>
	</div>
');

$webPage->append('
	<div class="panel panel-default">
		<div class="panel-heading">
   			<h3 class="panel-title">SPARC Distributed Per Project</h3>
		</div>
		<div class="panel-body">
			<div class="embed-responsive embed-responsive-16by9">
				<iframe class="embed-responsive-item" src="/chart/projectSparc"></iframe>
			</div>
			<div class="pull-right"><a href="/chart/projectSparc">full screen &raquo;</a></div>
		</div>
	</div>
');


