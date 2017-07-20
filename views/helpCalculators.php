<?php
//$webPage->setPageTitle('Cal');

$form = new Bootstrap_Form();

$input = new Bootstrap_TextInput();
$input->setId('netWeight');
$input->setLabel('Net Weight');
$input->setDefault('100,000,000');
$form->addField($input);

$input = new Bootstrap_TextInput();
$input->setId('myWeight');
$input->setLabel('My Weight');
$input->setDefault('10,000');
$form->addField($input);

$input = new Bootstrap_StaticInput();
$input->setDefault('<button class="btn btn-primary" type="button" onclick="calculate();">Calculate</button>');
$form->addField($input);

$content = '
	<div class="row">
		<div class="col-sm-7">
			'.$form->render().'
		</div>
		<div class="col-sm-5">
			<div class="rowpad"><strong>Probability staking within:</strong></div>
			<table class="table table-condensed table-striped table-hover">
				<tr><th class="text-center">Blocks</th><th class="text-center">Time</th><th class="text-center">Probability</th></tr>
				<tr><td class="text-center">1</td><td class="text-center">1.5 min</td><td class="text-center" id="1block"></td></tr>
				<tr><td class="text-center">960</td><td class="text-center">1 day</td><td class="text-center" id="960block"></td></tr>
				<tr><td class="text-center">6720</td><td class="text-center">1 week</td><td class="text-center" id="6720block"></td></tr>
				<tr><td class="text-center">28800</td><td class="text-center">1 month</td><td class="text-center" id="28800block"></td></tr>
			</table>
		</div>
	</div>	
';

$webPage->appendScript('
	<script>	
		function calculate() {
			var netWeight = $("#netWeight").val().replace(/\,/g,\'\');
			var myWeight = $("#myWeight").val().replace(/\,/g,\'\');
			var prob = 100*(1 - Math.pow(((netWeight-myWeight)/netWeight),1));
			$("#1block").html(prob.toFixed(4)+"%");
			var prob = 100*(1 - Math.pow(((netWeight-myWeight)/netWeight),960));
			$("#960block").html(prob.toFixed(4)+"%");
			var prob = 100*(1 - Math.pow(((netWeight-myWeight)/netWeight),6720));
			$("#6720block").html(prob.toFixed(4)+"%");
			var prob = 100*(1 - Math.pow(((netWeight-myWeight)/netWeight),28800));
			$("#28800block").html(prob.toFixed(4)+"%");
		}
		calculate();
	</script>
');

$panel = new Bootstrap_Panel();
$panel->setHeader('Stake Probability');
$panel->setContext('info');
$panel->setContent($content);
$webPage->append($panel->render());
