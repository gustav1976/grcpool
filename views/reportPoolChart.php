<?php
$webPage->appendTitle('Pool Charts');
$numberOfPools = Property::getValueFor(Constants::PROPERTY_NUMBER_OF_POOLS);

$webPage->append('
	<div style="background-color:white;">
		<div class="embed-responsive embed-responsive-16by9">
			<iframe class="embed-responsive-item" src="/chart/memberRegistration/"></iframe>
		</div>
		<div class="pull-right"><a href="/chart/memberRegistration">full screen &raquo;</a></div>
	</div>
');


