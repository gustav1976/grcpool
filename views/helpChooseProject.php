<?php
$webPage->appendTitle('Help with Choosing a BOINC Project');
$panel = new Bootstrap_Panel();
$panel->setHeader('Help with Choosing a BOINC Project');
$panelContent = '';
$panelContent .= '
	<p><em>Thanks to Quez for putting this information together</em></p>
	<p><strong>Tactical Mining / Research</strong></p>
	<p>Foreword: Mining means researching in the Gridcoin world. Research is never bad. It\'s a good thing to do, especially if you like to participate in a research field that you are interested in. The following guide is not meant to stop you from doing research for your favorite projects. Its purpose is to show you how you can optimize your Gridcoin earnings. This might include that you will work on projects that have no scientific purpose if you follow this guide.</p>
	<p><strong>First of all:</strong></p>
	<p>This guide does not claim to be perfect as many variables have to be considered to find the best GRC/kWh projects.<br/> 
	The basics:
	<ul>
		<li>Only whitelisted projects are eligible to grant you GRC payments. Have a look at <a href="http://gridcoinstats.eu/projects.php">http://gridcoinstats.eu/projects.php</a>.</li>
		<li>Don\'t use your CPU in projects that also have GPU workunits. Also don\'t use a GPU or CPU in ASIC projects like Bitcoin Utopia (it was once whitelisted). <!--The grcpool.com has been preconfigured, this should not happen if you do not touch default settings once added a project.--></li> 
		<li>Your GRC payments within one project are share based. Every project gets a certain share of the daily coin production. If you are contributing 10% of the calculation power in one project, you get 10% of the GRC that are assigned to this particular project. If you participate in 2 projects with 5% contribution in each project, you get the same amount of GRC like 10% in a single project. This means mining Gridcoin is a competition against other researchers in team Gridcoin within projects. The more you contribute to a project the more you get, in relation to the others.</li>
		<li>The more CPU cores or GPUs you have the more workunits you can solve and the more GRC you will get.</li>
		<li>The more relative contribution you have within the whitelisted BOINC projects, the higher your magnitude will be. Magnitude is the figure you have to observe. 1 magnitude equals about 0.25 GRC/day.</li>
	</ul>
	<p><strong>Advanced hints:</strong></p>
	<p>Have a look on <a href="http://gridcoinstats.eu/projects.php">http://gridcoinstats.eu/projects.php</a>. This is the homepage of a so called block explorer. You can see a list of all whitelisted projects. The more TeamRAC a project has, the more competitive it is. GPUs generate more BOINC credits or RAC (Recent Average Credit) than CPUs, so you see GPU projects on the top end of the list with more TeamRAC (once you sorted for Team RAC). For example, compare Leiden Classical (pure CPU) with GPUgrid (GPU workunits). They have a similar number of participants but the TeamRAC of GPUgrid is way higher. 
CPUs have a better GRC/kWh ratio than GPUs. Let\'s focus on CPU only projects. The ~20 projects with the lowest TeamRAC are most probably CPU only projects or do not have GPU workunits very often. You can cross reference with <a href="https://boinc.berkeley.edu/projects.php">https://boinc.berkeley.edu/projects.php</a>. This list indicates what hardware you can use in which project, but is not necessarily up to date and contains non-whitelisted projects. 
Conclusion: Choose the project(s) with the lowest TeamRAC to get the best GRC/kWh ratio. But be aware, sometimes projects do have special requirements (e.g. high RAM requirements, virtualization necessary...), give less-than-average credits/RAC or do not have a constant workunit flow, which is why they have low TeamRAC. 
Optimization of GRC mining is more like trial-and-error, but this guide shows you the best way to start.</p>
	<strong>More Community Articles</strong>
	<ul>
		<li><a href="https://steemit.com/gridcoin/@dutch/hardware-and-project-selection-part-1-cpu-vs-gpu">CPU vs GPU</a></li>
		<li><a href="https://steemit.com/gridcoin/@dutch/hardware-and-project-selection-part-2-gpu-projects">GPU Projects</a></li>
		<li><a href="https://steemit.com/gridcoin/@dutch/hardware-and-project-selection-part-3-cpu-projects">CPU Projects</a></li>
	</ul>
';
$panel->setContent($panelContent);
$webPage->append($panel->render());