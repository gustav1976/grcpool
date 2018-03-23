<?php
class GrcPool_Controller_Payout extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction() {
		$this->grcAction();
	}
	
	public function sparcAction() {
		$numberToShow = 50;
		$payoutDao = new GrcPool_Member_Payout_DAO();
		$start = 0;
		if (is_numeric($this->args(0))) {
			$start = $this->args(0,Controller::VALIDATION_NUMBER);
		}
		$pagination = new Bootstrap_Pagination();
		$pagination->setGroup($numberToShow);
		$pagination->setHref('/payout/grc/?');
		$pagination->setHowMany($payoutDao->getCurrencyCount(Constants::CURRENCY_SPARC));
		$pagination->setArrows(false);
		$pagination->setAdjacents(2);
		$pagination->setStart($start);
		$this->view->pagination = $pagination->render();
		$payouts = $payoutDao->getLatest(array($start*$numberToShow,$numberToShow),Constants::CURRENCY_SPARC);
		$this->view->payouts = $payouts;
		$this->view->currency = Constants::CURRENCY_SPARC;
		$this->setRenderView('payoutIndex');
	}
	
	public function grcAction() {
		$numberToShow = 50;
		$payoutDao = new GrcPool_Member_Payout_DAO();
		$start = 0;
		if (is_numeric($this->args(0))) {
			$start = $this->args(0,Controller::VALIDATION_NUMBER);
		}
		$pagination = new Bootstrap_Pagination();
		$pagination->setGroup($numberToShow);
		$pagination->setHref('/payout/grc/?');
		$pagination->setHowMany($payoutDao->getCurrencyCount(Constants::CURRENCY_GRC));
		$pagination->setArrows(false);
		$pagination->setAdjacents(2);
		$pagination->setStart($start);
		$this->view->pagination = $pagination->render();
		$payouts = $payoutDao->getLatest(array($start*$numberToShow,$numberToShow),Constants::CURRENCY_GRC);
		$this->view->payouts = $payouts;
		$this->view->currency = Constants::CURRENCY_GRC;
		$this->setRenderView('payoutIndex');
	}
	
}