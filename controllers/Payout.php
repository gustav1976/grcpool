<?php
class GrcPool_Controller_Payout extends GrcPool_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction() {
		$numberToShow = 50;
		$payoutDao = new GrcPool_Member_Payout_DAO();
		$start = 0;
		if (is_numeric($this->args(0))) {
			$start = $this->args(0,Controller::VALIDATION_NUMBER);
		}
		$pagination = new Bootstrap_Pagination();
		$pagination->setGroup($numberToShow);
		$pagination->setHref('/payout/index/?');
		$pagination->setHowMany($payoutDao->getCount());
		$pagination->setArrows(false);
		$pagination->setAdjacents(2);
		$pagination->setStart($start);
 		$this->view->pagination = $pagination->render();
 		$payouts = $payoutDao->getLatest(array($start*$numberToShow,$numberToShow));
		$this->view->payouts = $payouts;
	}
}