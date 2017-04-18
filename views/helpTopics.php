<?php
$webPage->setPageTitle('Help Topics');

if ($this->view->topic == 1) {
	$webPage->append('
		<br/>
		<h3>This project may not be attached correctly, or needs sync</h3><br/>
		<div class="rowpad">			
			This message indicates there could be problem related to linking your host to the project data pulled from the project\'s api.
			This is not a problem if:
			<ul>
				<li>You recently added the project, but BOINC has not yet synchronized after adding the project.</li>
				<li>A project was added or removed before the host could be identified by the BOINC project</li>
			</ul>
			This could be a problem if:
			<ul>
				<li>The BOINC client is not property synchronizing with the pool</li>
				<li>Projects in BOINC are not under the pool\'s control. In otherwords they still may be using your personal account instead of the pool account.</li>
			</ul>
			<br/><br/>
			To typically fix this if needed, try synchronizing first. If the problem still persists you may need to remove your projects from BOINC, and then synchronize with the pool.
		</div>
	');
}