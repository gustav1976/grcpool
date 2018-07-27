<?php
$webPage->appendTitle('Multi Pool');
$panel = new Bootstrap_Panel();
$panel->setHeader('Multi Pool');
$panelContent = '';
$panelContent .= '
	<strong>Background</strong>
	<br/><br/>
	It was brought to my attention by a Gridcoin community member the pool was heading towards 20,000 magnitude. This is significant since the Gridcoin network supports a maximum magnitude of 20,000. This means once the pool reaches more than 20,000, work performed above 20,000 is not awarded.
	'.Bootstrap_Callout::info('8/1/2017 - Note the limit is going to be increased soon to 32,000 with the next mandatory upgrade.').'
	<strong>Solution</strong>
	<br/><br/>
	The easiest solution is to close signupts on www.grcpool.com and start a second pool site, but I wanted to attempt to make things a little more seamless and see if integration was possible. So the pool website has been updated to use two pool\'s in the background while providing one front end. With these mechanisms in place, the pool can be expanded to scale more in the future with 3 or 4 pools if needed. New signups will be automatically directed to the 2nd pool. Of course we will need some current researchers to move to the 2nd pool to keep the magnitude from going over 20,000.
	<br/><br/>
	<strong>Volunteering to Move</strong>
	'.Bootstrap_Callout::info('8/1/2017 - Currently we are not seeking volunteers, thank you to those that did.').'
	If you would like to volunteer to move it would be very helpful. Right now the process will be somewhat manual as I manage and observe how things are running. Therefore if you have a low number of hosts and projects, it will be easier. These are the steps that need to occur for the move:
	<ul>
		<li>Detach (DO NOT DELETE) your projects using the pool website and sync BOINC, verify projects are removed from your BOINC client after doing a sync with the pool.</li>
    	<li>Do one extra sync with pool and let me know you are ready to move. This will allow me on the pool end to verify your client looks ready to move to the second pool.</li>
  	    <li>I will change a setting on your account that points you to the 2nd pool and let you know you are on the 2nd pool.</li>
  		<li>Go back to your host page and choose the projects you want to research on, then sync your BOINC client as always. Note it will take two syncs to remove the warning message about the possibility of an improperly attached project.</li>
	</ul>
	<br/>
	<strong>I Volunteered, What About My Pool 1 Detached Projects?</strong>
	<br/><br/>
    Your pool 1 projects will still accumulate GRC until your RAC/Magnitude goes to zero. You should see these orphaned projects on your host page in a section at the bottom. I will be adding more detail in this section in the future. Also, you will get two payouts since the pool\'s operate on independent wallets.
	<br/><br/>
';
$panel->setContent($panelContent);
$webPage->append($panel->render());