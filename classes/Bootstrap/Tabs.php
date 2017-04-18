<?php
class Bootstrap_Tab {
	private $active;
	private $title;
	private $content;
	public function getActive() {return $this->active;}
	public function setActive($b) {$this->active = $b;}
	public function getTitle() {return $this->title;}
	public function setTitle($s) {$this->title = $s;}
	public function getContent() {return $this->content;}
	public function setContent($s) {$this->content = $s;}
}
class Bootstrap_Tabs {
	private $tabs = array();
	
	public function addTab(Bootstrap_Tab $tab) {
		array_push($this->tabs,$tab);
	}
	
	public function render() {
		$result = '';
		$id = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
		
		$result .= '<ul class="nav nav-tabs" id="tabs_'.$id.'">';
		foreach ($this->tabs as $idx => $tab) {
			$result .= '<li class="'.($tab->getActive()?'active':'').'"><a data-toggle="tab" href="#tab_'.$id.'_'.$idx.'">'.$tab->getTitle().'</a></li>';
		}
		$result .= '</ul>';

		$result .= '<div class="tab-content">';
		foreach ($this->tabs as $idx => $tab) {
			$result .= '<div class="tab-pane '.($tab->getActive()?'active':'').'" id="tab_'.$id.'_'.$idx.'">'.$tab->getContent().'</div>';
		}
		$result .= '</div>';
		
		return $result;
	}
}