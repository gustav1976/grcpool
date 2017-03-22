<?php
class GrcPool_Controller extends Controller {
	
	private $_webPage;
	
	public function __construct() {
		parent::__construct();
		$this->_webPage	= new GrcPool_WebPage();
	}
	
	public function getWebPage() {
		return $this->_webPage;
	}
	
	public function render() {
		$controller = $this->view;
		$webPage = $this->_webPage;
		$webPage->append($this->renderMessages());
		require(getenv("DOCUMENT_ROOT").'/../views/'.$this->getRenderView().'.php');
		$webPage->display();
	}
	
	
}