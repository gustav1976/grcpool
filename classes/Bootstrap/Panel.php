<?php
class Bootstrap_Panel {
	
	private $header = '';
	private $content = '';
	private $footer = '';
	private $context = 'default';
	private $subContent = '';
	
	public function setSubContent($s) {$this->subContent = $s;}
	public function setHeader($s) {$this->header = $s;}
	public function setContent($s) {$this->content = $s;}
	public function setFooter($s) {$this->content = $s;}
	public function setContext($s) {$this->context = $s;}
	
	public function render() {
		return '
			<div class="panel panel-'.$this->context.'">
				'.($this->header?'<div class="panel-heading"><h3 class="panel-title">'.$this->header.'</h3></div>':'').'
				'.($this->content?'<div class="panel-body">'.$this->content.'</div>':'').'
				'.($this->footer?'<div class="panel-footer">'.$this->footer.'</div>':'').'
				'.$this->subContent.'
			</div>
		';
	}
	
}