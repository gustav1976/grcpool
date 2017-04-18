<?php
class Bootstrap_Pills {
	private $_pills = array();
	private $_justified = false;
	private $_stacked = false;
	
	public function setJustified($b) {$this->_justified = $b;}
	public function setStacked($b) {$this->_stacked = $b;}
	
	public function addPill($name,$link,$active = false) {
		array_push($this->_pills,array(
			'name' => $name,
			'link' => $link,
			'active' => $active
		));
	}
		
	public function render() {
		$result = '<ul class="rowpad nav nav-pills '.($this->_justified?'nav-justified':'').' '.($this->_stacked?'nav-stacked':'').'">';
		foreach ($this->_pills as $pill) {
			if (is_array($pill['link'])) {
				$result .= '
					<li role="presentation" class="dropdown '.($pill['active']?'active':'').'">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button">'.$pill['name'].' <span class="caret"></span></a>
						<ul class="dropdown-menu">
				';
				foreach ($pill['link'] as $name => $link) {
					$result .= '
						<li><a href="'.$link.'">'.$name.'</a></li>
					';
				}
				$result .= '</ul></li>';
			} else {
				$result .= '<li class="'.($pill['active']?'active':'').'"><a href="'.$pill['link'].'">'.$pill['name'].'</a></li>';
			}
		}
		$result .= '</ul>';
		return $result;
	}
}