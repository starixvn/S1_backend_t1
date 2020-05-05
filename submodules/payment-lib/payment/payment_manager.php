<?php
require_once dirname(__FILE__) . '/payment_api_interface.php';
require_once dirname(__FILE__) . '/../abstract_external_system_manager.php';

class Payment_manager extends Abstract_external_system_manager implements Payment_api_interface {

	protected $systemType = SYSTEM_PAYMENT;

	// const API_MAPS = array(
	// 	IPS_PAYMENT_API => "payment_api_ips",
	// );

	// const LIB_PATH = 'payment';

	// private $API;
	// public $platformCode;

	public function __construct($params = null) {
		parent::__construct($params);
		//load all class
		// $this->CI = &get_instance();

		// // log_message("error", var_export(self::API_MAPS, true));

		// if ($params && !empty($params["platform_code"]) && $params["platform_code"]) {
		// 	$this->initApi($params["platform_code"], $params);
		// }

	}

	public function initCustom($platformCode, $params = null) {

	}

	// public function initApi($platformCode = null, $params = null) {
	// 	// log_message("error", 'platformCode : ' . $platformCode);
	// 	if (!empty($platformCode)) {
	// 		$this->platformCode = $platformCode;
	// 		$cls = self::API_MAPS[$platformCode];
	// 		log_message("debug", 'class : ' . $cls);
	// 		$this->CI->load->library(self::LIB_PATH . '/' . $cls, $params);
	// 		$this->API = $this->CI->$cls;
	// 	}
	// 	return $this->API;
	// }

	// public function getApi($platform_code = null, $params = null) {
	// 	if (!empty($platformCode)) {
	// 		//reinit
	// 		$this->initApi($platformCode, $params);
	// 	}

	// 	log_message("debug", 'API : ' . ($this->API == null));
	// 	return $this->API;
	// }

	//====implements Payment_api_interface start===================================
	public function createSaleOrder($playerId, $amount, $player_promo_id) {
		return $this->API->createSaleOrder($playerId, $amount, $player_promo_id);
	}
	public function generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId = null, $enabledSecondUrl = true, $bankId = null) {
		return $this->API->generatePaymentUrlForm($orderId, $playerId, $amount, $orderDateTime, $playerPromoId, $enabledSecondUrl, $bankId);
	}
	public function callbackFromBrowser($orderId, $callbackExtraInfo) {
		return $this->API->callbackFromBrowser($orderId, $callbackExtraInfo);
	}
	public function callbackFromServer($orderId, $callbackExtraInfo) {
		return $this->API->callbackFromServer($orderId, $callbackExtraInfo);
	}
	public function directPay($order) {
		return $this->API->directPay($order);
	}
	//====implements Payment_api_interface end===================================

}

/*end of file*/