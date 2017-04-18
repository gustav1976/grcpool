<?php
class GrcPool_Controller_Help extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function chooseProjectAction() {	}
	public function androidAction() {	}
	public function topicsAction() {
		
		$id = $this->args(0,Controller::VALIDATION_NUMBER);
		$this->view->topic = $id;
	
	}
}