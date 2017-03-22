<?php
class BoincCmd {
	
	private function executeDaemon($cmd) {
		if (PHP_OS == 'WINNT' || PHP_OS == 'Darwin') {
			return;
		} else {
			$command = 'boinccmd '.$cmd;
		}
		$stdout = $stderr = $status = null;
		$descriptorspec = array(
				1 => array('pipe', 'w'),
				2 => array('pipe', 'w')
		);
		$process = proc_open($command, $descriptorspec, $pipes);
		if (is_resource($process)) {
			$stdout = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			$stderr = stream_get_contents($pipes[2]);
			fclose($pipes[2]);
			$status = proc_close($process);
		}
		return $stdout;
	}
	
	public function updateProject($url) {
		$this->executeDaemon('--project '.$url.' update');
	}
	
}