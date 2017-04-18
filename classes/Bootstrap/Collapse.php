<?php
class Bootstrap_Collapse {
	private $_items = array();
	private $_id = '';
	private $_autoCollapse = true;
	public function setAutoCollapse($b) {
		$this->_autoCollapse = $b;
	}
	public function setId($str) {
		$this->_id = $str;
	}
	public function addItem($title,$content,$active = false) {
		array_push($this->_items,array(
			'title' => $title,
			'content' => $content,
			'active' => $active
		));
	}
	
	public function render() {
		if ($this->_id == '') {
			$this->_id = 'panel';
		}
		$result = '';
		$result .= '<div class="panel-group" id="accordion">';
		foreach ($this->_items as $idx => $item) {
			$result .= '
				  <div id="'.$this->_id.'_'.$idx.'" class="panel panel-default">
				    <div class="panel-heading">
				      <h4 class="panel-title">
				        <a class="accordion-toggle" data-toggle="collapse" '.($this->_autoCollapse?'data-parent="#accordion"':'').' href="#collapse'.$this->_id.'_'.$idx.'">
				          '.$item['title'].'
				        </a>
				      </h4>
				    </div>
				    <div id="collapse'.$this->_id.'_'.$idx.'" class="panel-collapse collapse '.($item['active']?'in':'').'">
				      <div class="panel-body">
				        '.$item['content'].'
				      </div>
				    </div>
				  </div>
  			';
		}
		$result .= '</div>';
		return $result;
	}
}