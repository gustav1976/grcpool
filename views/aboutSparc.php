<?php
$webPage->appendTitle('SPARC Token');
$webPage->append('
	<div class="panel panel-default">
		<div class="panel-heading">
   			<h3 class="panel-title">About SPARC</h3>
		</div>
		<div class="panel-body">
			<a href="http://sparc.network/" target="_blank">SPARC</a><i class="fa fa-external-link"></i> is a token being awarded for BOINC computation on the Ethereum network. GRCPool.com is participating in the alpha stage of the SPARC network. 
			In other words, by using the pool, you are essentially dual mining with BOINC. Not all projects are supported by SPARC, check the <a href="/project/poolStats">pool project status page</a>.

			<br/><br/>

			<b>Receiving</b><br/>
			The pool receives SPARC via their network website every 1-2 weeks. I manually send the SPARC to an Ethereum address to store them from the SPARC website.
			<ul>
				<li>Pool 1: <a href="https://etherscan.io/address/0xbe7191f56918c3d8c6df199663b74b0ff9e7b1b9">0xbe7191f56918c3d8c6df199663b74b0ff9e7b1b9</a></li>
				<li>Pool 2: <a href="https://etherscan.io/address/0x897dddb94e33956ff159f4a501df460a91a157c5">0x897dddb94e33956ff159f4a501df460a91a157c5</a></li>
				<li>Pool 3: <a href="https://etherscan.io/address/0x09dfc7deb08fd86a4d59b65fc6cf562ee10da529">0x09dfc7deb08fd86a4d59b65fc6cf562ee10da529</a></li>
				<li>Anon Pool: <a href="https://etherscan.io/address/0x00740CBE37d822Fc6f3A5776007026af51df82b2">0x00740CBE37d822Fc6f3A5776007026af51df82b2</a></li>
			</ul>
			After the tokens are received, I reference the SPARC website and update the amount received per project into the pool\'s database. This is a  completely manual process of entering in the SPARC amount
			for each project into the pool\'s database as there is no API and you can\'t use copy/paste either. At this point, a process is triggered to distribute the tokens
			on a per project basis using your recent average credit to determine the number of tokens you receive, and the owed amount is incremented within your account.
	
			<br/><br/>

			<b>Withdraw</b><br/>
			Withdrawal of SPARC automatically is not currently available, but will be in the future as the project advances. It is not available due  
			to the cost of distributing the token using Ethereum gas. Collecting a SPARC fee to cover the gas
			could be risky if SPARC never has a value, leaving me potentially in the red.
			It is likely withdrawing from the pool will require a small GRC fee associated with it in order to cover my Ethereum gas until the point where SPARC matures enough to have value.
			I would be willing to provide withdrawals of SPARC on a case by case basis, please contact me for this.

			<br/><br/>
			<b>Pool Report</b><br/>
			To get more details regarding the pool SPARC finances, please visit the <a href="/report/sparc">SPARC reporting page</a>.
		</div>
	</div>
');