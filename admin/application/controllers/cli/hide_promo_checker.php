<?php
// if (PHP_SAPI === 'cli') {
// 	exit('No web access allowed');
// }

class Hide_promo_checker extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('promo_library');
	}

	function index() {
		$this->checkPromoForHiding();
	}

	function checkPromoForHiding() {
		$this->promo_library->checkPromoForHiding();
	}
}
