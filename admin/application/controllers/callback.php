<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Class Callback
 *
 * General behaviors include
 * * get wallet account record using transaction code
 * * get a certain order records
 * * validate tokens
 * * game authentication
 *
 * @category Payment Controller
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Callback extends BaseController {

	function __construct() {
		parent::__construct();
		$this->load->model('external_system');
		$this->load->library('session');
		//CHECK BY SESSION
		$this->setLangByPlayer();
		// $this->lang->load('main');
	}

	/**
	 * Payment service provider callback handlling
	 *
	 * URI: callback/process/{$systemId}/{$orderId}/[$flds[?reSendBySecureId=D146418550384]]
	 *
	 * Steps,
	 * loadExternalSystemLib()
	 * Check getInputGetAndPost()
	 * Check orderId
	 * Check validateWhiteIp()
	 * Check depositOrder() and getWalletAccountByTransactionCode()
	 * Check isOrderExpired()
	 * Check isApiDisabled()
	 * Check isDisabledPaymentAccountByOrderId()
	 * Check isDefaultCollectionAccount()
	 * Check getPlayerIdByOrderId()
	 * Check getSaleOrderStatusById()
	 * Do callbackFromServer().
	 *
	 * Case "success",
	 * 	Check go_success_page then goPaymentResultPage().
	 * 		for server callback then returnText(message|return_status|json_result)
	 *
	 * Case "no success",
	 * 	Do createSaleOrderStatusHistory()
	 * 		or returnText( return_error | return_status | return_error_msg)
	 *
	 * @param int system_id link to external_system
	 * @param int data_id link to sale_orders.id, response_results.related_id1(while request_api=deposit)
	 * @param array $flds
	 */
	public function process($systemId=null, $orderId=null, $flds=null) {
		$orderId = rtrim(urldecode($orderId), '"'); #trim '%22' when click from skype

		$orderMsg = 'system id: ' . $systemId . ', order id: ' . $orderId;
		$this->utils->debug_log('=====callback process===== ' . $orderMsg);

		$flds = empty($flds)? $this->getInputGetAndPost() : $flds;
		$this->utils->debug_log('=====callback process.flds===== ' , $flds);

		#just for log record
		$raw_post = file_get_contents("php://input");
		$this->utils->debug_log('=====callback process.raw_post===== ' , $raw_post);

		//load config
        $this->load->model(['sale_orders_status_history', 'external_system', 'sale_order', 'operatorglobalsettings']);
		list($loaded, $managerName) = $this->utils->loadExternalSystemLib($systemId);

		if ($loaded) {
			$api = $this->$managerName;

			if (empty($orderId)) {
				if (!empty($flds)) {
					$result = $api->callbackException($flds);
				} else {
					$msg = "Wrong callback, can not get orderId nor any params from callback";
					$this->utils->error_log($msg.' '.$orderMsg);
					$api->callbackFailed($orderId, $flds, 'server', $msg);
					return $this->returnBadRequest();
				}
			} else {
				#if callback no deposit or withdrawal orderid, decline it
				$depositOrder = $this->CI->sale_order->getSaleOrderById($orderId);
				$withdrawalOrder = $this->CI->wallet_model->getWalletAccountByTransactionCode($orderId);
				if(is_null($depositOrder) && is_null($withdrawalOrder)){
					$msg = 'Wrong callback, lost deposit or withdrawal orderid';
					$this->utils->error_log($msg.' '.$orderMsg);
					$api->callbackNeedToBeNotify($orderId, $flds, 'server', $msg);
					return $this->returnBadRequest();
				}

				#if resend, skip ip check; if not validate ip, decline it
				if(!empty($flds['reSendBySecureId'])){
					$isResendCallback = $this->CI->response_result->checkResendCallbackExists($systemId ,$orderId ,$flds['reSendBySecureId']);
					if(!$isResendCallback){
						$msg = 'Wrong resend callback, resend callback does not exist';
						$this->utils->debug_log($msg.' '.$orderMsg);
						$api->callbackNeedToBeNotify($orderId, $flds, 'server', $msg);
						return $this->returnBadRequest();
					}
				}else{
					$ip = $api->getClientIP();
					if(!$api->validateWhiteIp($ip)){
						$msg = 'Wrong callback, callback ip : ['.$ip.'] not in white_ip_list';
						$this->utils->debug_log($msg.' '.$orderMsg, implode(',', $api->white_ip_list));
						$api->callbackNeedToBeNotify($orderId, $flds, 'server', $msg);
						return $this->returnBadRequest();
					}
				}

				#if callback time over valid hour, decline it
				$isOrderExpired = $api->isOrderExpired($orderId);
				if($isOrderExpired){
					if( empty($flds['reSendBySecureId']) ){ // resend is exception
						$validHour = $api->getValidHour();
						$msg = 'Wrong callback, callback time over valid hour. Valid hour: '.$validHour.' hours';
						$this->utils->error_log($msg.' '.$orderMsg);
						$api->callbackFailed($orderId, $flds, 'server', $msg);
						return $this->returnBadRequest();
					}
				}

				#if payment api is disabled, decline it
				$isApiDisabled = $this->external_system->isApiDisabled($systemId);
				if($isApiDisabled){
					$msg = 'Wrong callback, api is disabled.';
					$this->utils->error_log($msg.' '.$orderMsg);
					$api->callbackFailed($orderId, $flds, 'server', $msg);
					return $this->returnBadRequest();
				}

				#only check payment_account when deposit
				if(substr($orderId, 0, 1) != 'W'){
					#if payment account is disabled, decline it
					$isPaymentAccountDisabled = $this->sale_order->isDisabledPaymentAccountByOrderId($orderId);
					if($isPaymentAccountDisabled){
						$msg = 'Wrong callback, payment account is disabled.';
						$this->utils->error_log($msg.' '.$orderMsg);
						$api->callbackFailed($orderId, $flds, 'server', $msg);
						return $this->returnBadRequest();
					}

					#if is not default collection account, decline it
					$saleOrder = $this->sale_order->getSaleOrderById($orderId);
					$isDefaultCollectionAccount = $this->operatorglobalsettings->isDefaultCollectionAccount($saleOrder->payment_account_id);
					if(!$isDefaultCollectionAccount){
						$msg = 'Wrong callback, payment account is not default collection account.';
						$this->utils->error_log($msg.' '.$orderMsg);
						$api->callbackFailed($orderId, $flds, 'server', $msg);
						return $this->returnBadRequest();
					}
				}

				$result = array('success' => false, 'return_error' => 'FAILED');

				$playerId = $this->getPlayerIdByOrderId($orderId);
				if ($playerId) {
					$controller = $this;
					$success = $this->lockAndTransForPlayerBalance($playerId, function ()
						use ($controller, $orderId, $flds, $managerName, &$result) {

                        $sale_order_status = $this->sale_order->getSaleOrderStatusById($orderId);
                        if($sale_order_status != Sale_order::STATUS_SETTLED && $sale_order_status != Sale_order::STATUS_DECLINED){
                            $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_GET_CALLBACK);
                        }
                        if(! empty($flds['reSendBySecureId'])){
                        	unset($flds['reSendBySecureId']);
                        }
						$result = $controller->$managerName->callbackFromServer($orderId, $flds);

						$success=true;
						return $success;
					});
					if(substr($orderId, 0, 1) != 'W'){
						$sale_order_status = $this->sale_order->getSaleOrderStatusById($orderId);
						$this->utils->error_log('===triggerDepositEvent and processSaleOrderAddonFunction=== , status_settled:', $sale_order_status);
						if($sale_order_status == Sale_order::STATUS_SETTLED){
							$this->utils->error_log('===triggerDepositEvent and processSaleOrderAddonFunction is success, status_settled:', $sale_order_status);
							$this->triggerDepositEvent($playerId, $orderId, null, $systemId, null, null);
							$this->$managerName->processSaleOrderAddonFunction($orderId, $success);
						}
					}
				} else {
					$this->utils->error_log('lost order and player id', $orderId);
				}
			}

			$this->utils->debug_log('result', $result);
			if ($result['success']) {
				if (isset($result['go_success_page']) && @$result['go_success_page']) {
					//browser callback
					$this->goPaymentResultPage($systemId, $orderId, $result);

				} else {
					//only for server callback
					if (isset($result['message'])) {
						$this->returnText($result['message']);
					}
					else if(isset($result['return_status'])){
						$this->returnText($result['return_status']);
						return;
					}
                    if (isset($result['json_result'])) {
                        $this->returnJsonResult($result['json_result']);
                    }
				}

				return;
			} else {
                $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_CHECK_CALLBACK_ORDER_FAILED);

				if (isset($result['return_error']) && !empty($result['return_error'])) {
					$this->returnText($result['return_error']);
					return;
				}else if(isset($result['return_status'])){
					$this->returnText($result['return_status']);
					return;
				}else if(isset($result['return_error_msg'])){
					return $this->returnText($result['return_error_msg']);
				}else if(isset($result['return_error_json'])){
					return $this->returnJsonResult($result['return_error_json']);
				}
			}
		} else {
			$msg = "Wrong callback, can not load ExternalSystemLib";
			$this->utils->error_log($msg.' '.$orderMsg);
			#use DUMMY to access api
			list($loaded, $managerName) = $this->utils->loadExternalSystemLib(DUMMY_PAYMENT_API);
			$api = $this->$managerName;
			$api->callbackNeedToBeNotify($orderId, $flds, 'server', $msg);
			return $this->returnBadRequest();
		}

		$this->returnBadRequest();
	}

	/**
	 * @param int $systemId
	 * @return Boolean
	 */
	public function fixed_process($systemId){
		list($loaded, $managerName) = $this->utils->loadExternalSystemLib($systemId);

		$params = $this->getInputGetAndPost();
		if(empty($params)){
			$params = $_REQUEST;
		}

        $this->utils->debug_log('System', $systemId, 'params', $params);
		if(!$loaded){
			$this->utils->error_log("Unknown system [$systemId]");
			return;
		}

		$orderId = $this->$managerName->getOrderIdFromParameters($params);

		return $this->process($systemId, $orderId, $params);
	}

	/**
	 * @param int $systemId
	 * @param int $orderId
	 * @return void
	 */
	public function show_success($systemId, $orderId) {
		$this->setLangByPlayer();
		$message = $this->session->flashdata('message');
		if (empty($message)) {
			$message = lang('payment.success');
		}
		$nextUrl = $this->session->flashdata('next_url');
		if (empty($nextUrl)) {
			//go home or go game
			$nextUrl = $this->utils->getPlayerHomeUrl();
		}
		//load sale order
		$saleOrder = $this->sale_order->getSaleOrderById($orderId);
		list($saleOrder, $transaction, $promoTrans) = $this->getSaleOrderAndTransaction($orderId);

		$data = array('message' => $message, 'next_url' => $nextUrl,
			'system_id' => $systemId, 'order_id' => $orderId,
			'sale_order' => $saleOrder, 'transaction' => $transaction, 'promo_trans' => $promoTrans);
		$this->load->view('player/callback_success', $data);
	}

	/**
	 * @param int $systemId
	 * @param int $orderId
	 * @return void
	 */
	public function show_pending($systemId, $orderId) {
		$nextUrl = $this->session->flashdata('next_url');
		if (empty($nextUrl)) {
			//go home or go game
			$nextUrl = $this->utils->getPlayerHomeUrl();
		}
		//load sale order
		$saleOrder = $this->sale_order->getSaleOrderById($orderId);

		$data = array('home_url' => '/iframe_module/iframe_viewCashier', 'next_url' => $nextUrl, 'sale_order' => $saleOrder,
					'system_id' => $systemId, 'order_id' => $orderId);

		$this->load->view('player/request_pending', $data);
	}

	/**
	 * @param int $orderId
	 * @return array
	 */
	private function getSaleOrderAndTransaction($orderId) {
		$this->load->model(array('sale_order', 'transactions'));
		$saleOrder = $this->sale_order->getSaleOrderById($orderId);
		$transaction = null;
		$promoTrans = null;
		if ($saleOrder) {
			if (isset($saleOrder->transaction_id) && $saleOrder->transaction_id) {
				$transaction = $this->transactions->getTransaction($saleOrder->transaction_id);
			}
			if (isset($saleOrder->player_promo_id) && $saleOrder->player_promo_id) {
				$promoTrans = $this->transactions->getTransactionByPlayerPromoId($saleOrder->id, $saleOrder->player_promo_id);
			}

		}
		return array($saleOrder, $transaction, $promoTrans);
	}

	/**
	 * @param int $systemId
	 * @param int $orderId
	 * @return load template
	 */
	public function show_error($systemId, $orderId) {
		// $this->setLangByPlayer();
		$message = $this->session->flashdata('message');
		if (empty($message)) {
			$message = lang('error.payment.failed');
		}
		$nextUrl = $this->session->flashdata('next_url');
		if (empty($nextUrl)) {
			//retry payment
			$this->load->model(array('sale_order'));
			$ord = $this->sale_order->getSaleOrderById($orderId);
			$nextUrl = $this->getPaymentUrl($systemId, $ord->amount, $ord->player_id);
		}
		$data = array('message' => $message, 'next_url' => $nextUrl, 'home_url' => '/player_center2',
			'system_id' => $systemId, 'order_id' => $orderId);
		$this->load->view('player/callback_error', $data);
	}

	/**
	 * @param int $systemId
	 * @return void
	 */
	public function fixed_browser($systemId) {
		$resultCallback = array('success' => false, 'message' => lang('error.gateway.down'), 'next_url' => null, 'order_id' => null);

		list($loaded, $managerName) = $this->utils->loadExternalSystemLib($systemId);

		$this->utils->debug_log('systemId', $systemId, 'managerName', $managerName);
		if ($loaded) {
			$flds = $this->getInputGetAndPost();
			$orderId = $this->$managerName->getOrderIdFromParameters($flds);

			$playerId = $this->getPlayerIdByOrderId($orderId);
			if ($playerId) {
				$controller=$this;
				$success = $this->lockAndTransForPlayerBalance($playerId, function ()
					use ($controller, $orderId, $flds, $managerName, &$resultCallback) {
					$resultCallback = $controller->$managerName->callbackFromBrowser($orderId, $flds);
					$controller->utils->debug_log('resultCallback', $resultCallback);
					$success=false;
					return $success;
				});
			} else {
				$this->utils->debug_log('lost order and player id', $orderId);
			}

		}

		//go result page
		$this->goPaymentResultPage($systemId, $orderId, $resultCallback);
	}

	/**
	 * note: browser -> success/failed -> goPlayerCallbackPage -> show_success/show_error(redirect) -> callback_success/callback_error(view)
	 *
	 * @param string $result
	 * @param int $systemId
	 * @param int $orderId
	 */
	public function browser($result, $systemId, $orderId) {
		$flds = $this->getInputGetAndPost();
		$this->utils->debug_log('callback browser', $flds);
		if ($result == 'success') {
			$this->success($systemId, $orderId, $flds);
		} else if ($result == 'failed') {
			$this->failed($systemId, $orderId, $flds);
		}
	}

	public function fixed_validation($systemId) {
		list($loaded, $managerName) = $this->utils->loadExternalSystemLib($systemId);
		$params = $this->getInputGetAndPost();
        $this->utils->debug_log('fixed_validation: System', $systemId, 'params', $params);

		if($loaded) {
			$returnText = $this->$managerName->getOrderValidation($params);

			if(is_array($returnText)){
				return $this->returnJsonResult($returnText);
			} else {
				return $this->returnText($returnText);
			}
		}
	}

	/**
	 *
	 *
	 * @param int $systemId
	 * @param string $action
	 */
	public function general_routing($systemId, $action) {
        $this->utils->debug_log("general_routing: System [$systemId], Action [$action]");
		list($loaded, $managerName) = $this->utils->loadExternalSystemLib($systemId);

		if($loaded) {
			$type = $this->$managerName->getRoutingType($action);
		}

		switch ($type) {
			case 'validation':
				return $this->fixed_validation($systemId);
				break;

			case 'process':
				$this->fixed_process($systemId);
				break;

			default:
				return $this->returnBadRequest();
				break;
		}
	}

	/**
	 * @param int $systemId
	 * @param int $orderId
	 * @param array $flds
	 *
	 * @return void
	 */
	private function success($systemId, $orderId, $flds) {
		$resultCallback = array('success' => false, 'message' => lang('error.gateway.down'), 'next_url' => null, 'order_id' => $orderId);

		list($loaded, $managerName) = $this->utils->loadExternalSystemLib($systemId);

		$this->utils->debug_log('systemId', $systemId, 'managerName', $managerName, 'orderId', $orderId);
		if ($loaded) {
			$playerId = $this->getPlayerIdByOrderId($orderId);
			if ($playerId) {
				$controller = $this;
				$success = $this->lockAndTransForPlayerBalance($playerId, function ()
					use ($controller, $orderId, $flds, $managerName, &$resultCallback) {
						$resultCallback = $controller->$managerName->callbackFromBrowser($orderId, $flds);
						return false;
				});
			} else {
				$this->utils->debug_log('lost order and player id', $orderId);
			}
		}

		//go result page
		$this->goPaymentResultPage($systemId, $orderId, $resultCallback);
	}

	/**
	 * @param int $systemId
	 * @param int $orderId
	 * @param array $flds
	 *
	 * @return void
	 */
	private function failed($systemId, $orderId, $flds) {
		$resultCallback = array('success' => false, 'message' => lang('error.payment.failed'), 'next_url' => null, 'order_id' => $orderId);

		$this->load->model(array('sale_order'));
		$oldStatus = $this->sale_order->getStatus($orderId);
		if ($oldStatus == Sale_order::STATUS_PROCESSING || $oldStatus == Sale_order::STATUS_CHECKING) {
			//set failed
			$this->sale_order->setStatusToFailed($orderId);
		} else {
			$this->utils->debug_log('cannot set status to failed', $orderId);
		}
		//save error message
		$this->utils->debug_log('systemId:', $systemId, ' ,orderId:', $orderId, ' ,flds:', $flds);
		$this->goPaymentResultPage($systemId, $orderId, $resultCallback);
	}

	const API_METHOD_VALIDATE_MEMBER = 'ValidateMember';
	const API_METHOD_VALIDATE_PASSWORD = 'validate_password';

	/**
	 * detail: callback auth
	 *
	 * @param  string $gamePlatformId game platform id
	 * @param  string $method         callback method
	 * @param  string $platform       web/pc/mobile/android/ios
	 * @return mixin  by api
	 */
	public function game($gamePlatformId, $method = null, $platform = 'web') {
		$this->utils->debug_log('------------------ init callback --------------------');

        $load_api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
        if( !$load_api->checkGameIpWhitelistByGameProvider($this->input->ip_address()) ){
            $response = array(
                "status" => "error",
                "code" => 'W_01',
                "message" => 'IP restricted',
		    );
            return $this->returnJsonResult($response);
        };

        switch ($gamePlatformId) {
        	case ONE88_API:
	        	if ($method == self::API_METHOD_VALIDATE_MEMBER) {
					if ($this->isPostMethod()) {
						$result = file_get_contents('php://input');
						$encryptedXML = $result;
						$this->utils->debug_log('(Callback) ENCRYPTED-XML-INPUT:', count($encryptedXML), ' RAW-RESULT: ', $encryptedXML);

						$key = $this->config->item('one88_merchant_key');
						$decryptedXML = $this->CI->utils->aes128_cbc_decrypt($key, base64_decode($encryptedXML), $key);
						$this->utils->debug_log('(Callback) ENCRYPTED-XML:', count($encryptedXML), ' (Callback) DECRYPTED-XML:', $decryptedXML);

						$one88_api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
						return $this->returnText($one88_api->callback(strtolower($method), $decryptedXML));
					}
				} else {
					return show_error('Bad Parameters [Platform Id]', 400);
				}
        	break;

        	case LB_API:
        		if ($method == self::API_METHOD_VALIDATE_MEMBER || $method == self::API_METHOD_VALIDATE_PASSWORD) {
					$this->utils->debug_log('(LB - Callback) ValidateMember or validate_password');
					if ($this->isPostMethod()) {
						$result = file_get_contents('php://input');
						$this->utils->debug_log('(LB - Callback) RAW-RESULT: ', $result);

						$lb_api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
						return $this->returnText($lb_api->callback(strtolower($method), $result));
					}
				} else {
					return show_error('Bad Parameters [Platform Id]', 400);
				}
        	break;

        	case GAMEPLAY_API:
        		if (strtolower($method) == strtolower(self::API_METHOD_VALIDATE_MEMBER)) {
					$this->utils->debug_log('(GAMEPLAY - Callback) ValidateMember');
					$gameplay_api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					if ($platform == 'web') {
						$result = $this->getInputGetAndPost();
						$this->utils->debug_log('(CALLBACK_POST_GET_GAMEPLAY_WEB)  - Callback RAW-RESULT: ', $result);
						return $this->returnXml($gameplay_api->callback(strtolower($method), $result['ticket']));
					}else{
	                    # MOBILE PC
						//$result = $this->getInputGetAndPost();
						$result = file_get_contents('php://input');
						$this->utils->debug_log('(CALLBACK_POST_GET_GAMEPLAY_MOBILE_PC)  - Callback RAW-RESULT: ', $result);
						return $this->returnText($gameplay_api->callback(strtolower($method), $result, $platform));
					}
				} else {
					return show_error('Bad Parameters [Platform Id]', 400);
				}
        	break;

        	case OPUS_API:
        	case OPUS_SPORTSBOOK_API:
        	case OPUS_KENO_API:
        		if (strtolower($method) == strtolower(self::API_METHOD_VALIDATE_MEMBER)) {
					$this->utils->debug_log('(OPUS - Callback) ValidateMember');
					if ($this->isPostMethod()) {
						$result = ($platform == 'web') ? file_get_contents('php://input') : $this->input->post();
					$this->utils->debug_log('(POST) OPUS - Callback RAW-RESULT: ', $result);
						$opus_api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
						return $this->returnXml($opus_api->callback(strtolower($method), $result, $platform));
					} elseif ($this->isGetMethod()) {
						$this->utils->debug_log('(GET) OPUS - Callback Result: ' . $_GET["ticket"]);
						if ($_GET["ticket"]) {
							$opus_api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
							return $this->returnXml($opus_api->callback(strtolower($method), $_GET["ticket"]));
						}
					}
				} else {
					return show_error('Bad Parameters [Platform Id]', 400);
				}
        	break;

        	case BS_API:
        		if (in_array($method, array('authenticate', 'transfer', 'endGameSession', 'transferStatus', 'getAccountInfo', 'bonusRelease'))) {
					$get = $this->input->get() ?: array();
					$post = $this->input->post() ?: array();
					$params = array_merge($get, $post);
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$data = $api->callback($method, $params);

					$xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><EXTSYSTEM></EXTSYSTEM>");
					$xmlData = $this->CI->utils->arrayToXml($data, $xml_object);

					return $this->returnXml($xmlData);
				} else {
					return show_error('Bad Parameters [method]', 400);
				}
        	break;

        	case ENTWINE_API:
        		$this->utils->debug_log('(ENTWINE - Callback) LOGIN METHOD: ', $method);
				if (in_array($method, array('dlogin', 'wlogin', 'deposit', 'withdraw',
					'player_balance', 'auto_cashier', 'cashier_cs',
					'cashier_ct', 'cashier_en', 'cashier_th', 'cashier_ind',
					'reg_cs', 'reg_ct', 'reg_en', 'reg_th', 'reg_ind'))) {

					if ($this->isPostMethod()) {
						$result = file_get_contents('php://input');
						$this->utils->debug_log('(POST) ENTWINE - Callback RAW-RESULT: ', $result);
						$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
						$xmlData = $api->callback($method, $result);
						$this->CI->utils->debug_log("XML RETURN: " . $this->returnXml($xmlData));
						return $this->returnXml($xmlData);
					}
				}
        	break;

        	case EBET_API:
        	case EBET2_API:
        	case EBET_THB_API:
        	case EBET_USD_API:
        		if ($this->external_system->isGameApiActive($gamePlatformId)) {
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$request = file_get_contents('php://input');
					$this->utils->debug_log('(POST) EBET - Callback RAW-RESULT: ' . $request);
					$params = array_merge(json_decode($request, true) ?: array(), ($this->input->post() ?: array()));
					$data = $api->callback($params);
				} else {
					$data = array('status' => 505);
				}
				return $this->returnJsonResult($data);
			break;
			case QT_API:
				if (!empty($method)){
					if ($this->external_system->isGameApiActive($gamePlatformId)) {
						$gameplay_api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
						$result = file_get_contents('php://input');
						$this->utils->debug_log('(POST) QT - Callback RAW-RESULT: ' . $result);
						$data = $gameplay_api->callback(strtolower($method), $result, $platform);
					}
					if ($data['status'] == 401) { // invalid INVALID_LOGIN
						http_response_code(401);
						return $this->returnJsonResult($data['header']);
					} else if ($data['status'] == 400) { // INVALID_TOKEN
						http_response_code(400);
						return $this->returnJsonResult($data['header']);
					} else { // SUCCESS
						http_response_code(200);
						return $this->returnJsonResult($data['header']);
					}
				} else {
					return show_error('Bad Parameters [method]', 400);
				}
			break;
        	case AG_API:
        	case AGIN_API:
        		if ($this->external_system->isGameApiActive($gamePlatformId)) {
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$request = file_get_contents('php://input');
					$this->utils->debug_log('(POST) AG - Callback RAW-RESULT: ' . $request);
					$data = $api->callback($request, $platform, $method);
					$this->utils->debug_log('AG callback return', $data);
					return $this->returnXml($data);
				} else {
					return show_error('Bad Parameters [method]', 400);
				}
        	break;

			case ONEWORKS_API:
				$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
				if(!empty($method) && $method == "login"){
					if($this->isGetMethod()){
						return $this->load->view('iframe/player/goto_login_oneworks');
					}
				}

				if(!empty($method) && $method == "transfer"){
					if($this->isGetMethod()){
						$token = $this->input->get('token');
						$this->utils->debug_log('ONEWORKS - CASHIER TOKEN: ', $token);
						if (empty($token)) {
							$this->utils->debug_log('ONEWORKS - CASHIER EMPTY TOKEN: ');
							redirect('/');
						}
						$redirect_url = "/iframe/auth/login_with_token/{$token}?next=/player_center/mobile_transfer_view/null/".ONEWORKS_API;
						$this->utils->debug_log('ONEWORKS - REDIRECT: ', $redirect_url);
						redirect($redirect_url);
					}
				}

        		$resultArr = array();
				if ($platform == 'web') {
					$result = file_get_contents('php://input');
					$this->utils->debug_log('(POST) ONEWORKS - Web Callback RAW-RESULT : ', $result);
					parse_str($result, $resultArr);
					$this->utils->debug_log('(POST) ONEWORKS - Web Callback ARRAY-RESULT : ', $resultArr);
				} else {
					$get = $this->input->get() ?: array();
					$post = $this->input->post() ?: array();
					$resultArr = array_merge($get, $post);
					$this->utils->debug_log('(POST) ONEWORKS - Mobile Callback ARRAY-RESULT : ', $resultArr);
				}
				$xmlData = $api->callback($resultArr);
				$this->CI->utils->debug_log("XML RETURN: " , $xmlData);
				return $this->returnXml($xmlData);
        	break;

        	case SPORTSBOOK_API:
				$this->utils->debug_log('(SPORTSBOOK - Callback) RESULT: ', $method);

				//if logout , just redirect back
				if($method=='logout'){
					//go back
					$this->utils->debug_log('(SPORTSBOOK - Callback) logout go back');
					$home_www=$this->utils->getSystemUrl('www');
					return redirect($home_www);
				}

				$get = $this->input->get() ?: array();
				$post = $this->input->post() ?: array();
				$params = array_merge($get, $post);
				$this->utils->debug_log('(GETPOST) SPORTSBOOK - Callback RAW-RESULT: ', $params);
				$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
				$jsonData = $api->callback($method, $params);
				$this->CI->utils->debug_log("JSON RETURN: " , $jsonData);
				return $this->returnJsonResult($jsonData);
			break;

			case SA_GAMING_API:
				$this->utils->debug_log('(SA GAMING - Callback) RESULT: ', $method);
				if (strtolower($method)=="checkkey"){
					if($this->isGetMethod()) {
						if (isset($_GET["checkkey"])) {
							$this->utils->debug_log('(GET) SA GAMING - Callback Result: ' . $_GET["checkkey"]);
							if ($_GET["checkkey"]) {
								$gameplay_api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
								$ret = $gameplay_api->callback(strtolower($method), $_GET["checkkey"]);
								$this->utils->debug_log('(GET) SA GAMING RETURN: ' . $ret);
								echo $ret;
								return;
							}
						}
					}
				}
			break;

			case SPADE_GAMING_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$request = file_get_contents('php://input');
					$this->utils->debug_log('(POST) Spade - Callback RAW-RESULT: ' . $request);
					$olparams = array_merge(json_decode($request,true) ?: array(), ($this->input->post() ?: array()));
					$params = json_decode($request,true);
					$data = $api->callback($params,$platform);
				} else {
					$data = array('status' => 505);
				}
				return $this->returnJsonResult($data);
			break;

			case PNG_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					if ($this->isPostMethod()) {
						if($method == "syncLDF"){
							$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
							$request = file_get_contents('php://input');
							$this->utils->debug_log('(POST) PNG - Callback RAW-RESULT: ' . $request);
							$params = json_decode($request,true);
							$data = $api->syncLDF($params,$platform);
						}else {
							return show_error('Bad Parameters [method]', 400);
						}
					}
				} else {
					$data = array('status' => 505);
				}
				return $this->returnJsonResult($data);
			break;

			case EBET_BBTECH_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					if ($this->isPostMethod()) {
						if($method == "login"){
							$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
							$request = file_get_contents('php://input');
							$this->utils->debug_log('(POST) EBET_BBTECH_API - Callback RAW-RESULT: ' . $request);
							$params = json_decode($request,true);
							$data = $api->callback($params,$platform);
						}else {
							return show_error('Bad Parameters [method]', 400);
						}
					}
				} else {
					$data = array('status' => 505);
				}
				return $this->returnJsonResult($data);
			break;

			case EBET_SPADE_GAMING_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
	                if ($this->isPostMethod()) {
	                    if($method == "authenticate_player"){
	                        $api        = $this->utils->loadExternalSystemLibObject($gamePlatformId);
	                        $request    = file_get_contents('php://input');
	                        $params     = json_decode($request,true);
	                        $data       = $api->callback($params,$platform);

	                        $this->utils->debug_log('(POST) EBET_BBTECH_API - Callback RAW-RESULT: ' . $request);

	                    }else {
	                        return show_error('Bad Parameters [method]', 400);
	                    }
	                }
	            } else {
	                $data = array('status' => 505);
	            }
	            return $this->returnJsonResult($data);
			break;

			case EBET_OPUS_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
	                if ($this->isPostMethod()) {
	                    if ($method == "authenticate_player"){
	                        $api        = $this->utils->loadExternalSystemLibObject($gamePlatformId);
	                        $request    = file_get_contents('php://input');
	                        $params     = json_decode($request,true);
	                        $data       = $api->callback($params, $platform);

	                        print_r($data);
	                        die();
	                        $this->utils->debug_log('(POST) EBET_OPUS_API - Callback RAW-RESULT: ' . $request);

	                    }else {
	                        return show_error('Bad Parameters [method]', 400);
	                    }
	                }
	            } else {
	                $data = array('status' => 505);
	            }
	            return $this->returnJsonResult($data);
			break;

			case SBTECH_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					if($method == "status" || $method == 'refresh_session') {
						$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
						$token = isset($_GET['token']) ? $_GET['token'] : '';
						$data = $api->callback($token, $method);
					} else {
						return show_error('Bad Parameters [method]', 400);
					}
				} else {
					$data = array('status' => 505);
				}
				echo "jsoncb"."(".json_encode($data).");";
			break;

			case SBTECH_BTI_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					if($method == "status" || $method == 'refresh_session') {
						$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
						$token = isset($_GET['token']) ? $_GET['token'] : '';
						$data = $api->callback($token, $method);
					} else {
						return show_error('Bad Parameters [method]', 400);
					}
				} else {
					$data = array('status' => 505);
				}
				echo "jsoncb"."(".json_encode($data).");";
			break;

			case PGSOFT_API:
				if($this->external_system->isGameApiActive($gamePlatformId)) {
					$post_params = $this->getInputGetAndPost();
					$this->utils->debug_log('(POST) PGSOFT - Callback : ', $post_params);
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$data = $api->callback($post_params, $method);
				}else{
					$data = array(
						'data' => null,
						'error'=> [
							'code' => '1200',
							'message' => 'Invalid request.'
						]
					);
				}
				if(!is_array($data)){
					$data = json_decode($data);
				}
				return $this->returnJsonResult($data);
			break;
			case GL_API :
				if($this->external_system->isGameApiActive($gamePlatformId)) {
					$post_params = $this->getInputGetAndPost();
					$this->utils->debug_log(__METHOD__, 'GL_API callback', [ 'method' => $method, 'post_params' => $post_params ]);
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$ret = $api->callback($post_params, $method);
				}
				else {
					$ret = [ 'status' => 505 ];
				}

				return $this->returnJsonResult($ret);
			break;

			case BETSOFT_API:
				if (in_array($method, ['authenticate', 'transfer', 'endGameSession', 'transferStatus', 'getAccountInfo', 'bonusRelease', 'betResult'] )) {
					$get = $this->input->get() ?: array();
					$post = $this->input->post() ?: array();
					$params = array_merge($get, $post);

					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$data = $api->callback($method, $params);

					$xml_object = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><EXTSYSTEM></EXTSYSTEM>");
					$xmlData = $this->CI->utils->arrayToXml($data, $xml_object);

					return $this->returnXml($xmlData);
				} else {
					return show_error('Bad Parameters [method]', 400);
				}
				break;

			case HG_API:
					$this->utils->debug_log('(HOGAMING - Callback) ValidateMember');
					$hoGamingApi = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$result = file_get_contents('php://input');
					$this->utils->debug_log('(CALLBACK_POST_GET_HOGAMING)  - Callback RAW-RESULT: ', $result);
					return $this->returnXml($hoGamingApi->callback(strtolower($method), $result));
				break;

			case GGPOKER_GAME_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					$authorization_key = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
					$request = file_get_contents('php://input');
					$this->utils->debug_log('(POST) GGPOKER_GAME_API - Callback RAW-RESULT: ' . $request);
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$params = json_decode($request,true);
					$loginData = array();
					if(!empty($method) && $method == "login"){
						$brandId = filter_input(INPUT_GET,"brandId",FILTER_SANITIZE_STRING);
						$redirectUri = filter_input(INPUT_GET,"redirectUri",FILTER_SANITIZE_STRING);
						$loginData = array(
							"brandId" => $brandId,
							"redirectUri" => $redirectUri
						);
					}
					$data = $api->callback($params,$authorization_key,$loginData);
					$this->utils->debug_log('(POST) GGPOKER_GAME_API - Callback RAW-RESPONSE: ' . json_encode($data));
					$this->CI->load->model("response_result");
        			$flag = $data ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;
        			$apiName = isset($params['requestType']) ? $params['requestType']: null;
        			$field = array(
        				'decoded_result_text'=>json_encode($data),
        			);
        			$this->CI->response_result->saveResponseResultForFile(GGPOKER_GAME_API, $flag, $apiName, json_encode($params), null, $field);
					if(isset($data['login']) && $data['success'] && $data['login']){
						return $this->load->view('iframe/player/goto_login_ggpokerew',$data);
					}
					return $this->returnJsonResult($data);
				} else {
					http_response_code(400);
					$data = array(
						"code" 		=> "INTERNAL_ERROR",
						"message" 	=> "Game not active."
					);
				}
				return $this->returnJsonResult($data);
			break;

			case PLAYSTAR_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					$request = $this->input->get() ?: array();
					$this->utils->debug_log('(GET) PLAYSTAR_GAME_API - Callback RAW-RESULT: ' . json_encode($request));
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$data = $api->callback($request);
				}else {
					http_response_code(400);
					$data = [
						"code" 		=> "INTERNAL_ERROR",
						"message" 	=> "Game not active."
					];
				}
				return $this->returnJsonResult($data);
			break;

			case N2LIVE_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);

					if($method == "login_cashier"){
						 $ticket = filter_input(INPUT_GET, 'ticket', FILTER_SANITIZE_URL);
						 $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_URL);
						 $request = array(
						 	"username" => $username,
						 	"ticket" => $ticket
						 );
						 $result = $api->callback($request, $method);
						 return redirect($result['forward_url']);
					} else {
						$request = file_get_contents('php://input');
						$this->utils->debug_log('(POST) N2LIVE_API - Callback RAW-RESULT: ' . json_encode($request));
						$data = $api->callback($request, $method);
					}

				}else {
					http_response_code(400);
					$sxe  = new SimpleXMLElement("<message/>");
					$status = $sxe->addChild('status','fail');
					$result = $sxe->addChild('result','Game not active.');
					$data =  $sxe->asXML();
				}
				return $this->returnXml($data);
			break;

			case ISIN4D_API:
			case ISIN4D_IDR_B1_API:
			case ISIN4D_CNY_B1_API:
			case ISIN4D_THB_B1_API:
			case ISIN4D_USD_B1_API:
			case ISIN4D_VND_B1_API:
			case ISIN4D_MYR_B1_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					$postJson = file_get_contents("php://input");
					$this->utils->debug_log("ISIN4D Callback ====> ",$postJson);

					$post = !empty($postJson) ? json_decode($postJson,true) : array();
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$response = $api->callback($method, $post);
					return $this->returnJsonResult($response);
				}
			break;

			case QQKENO_QQLOTTERY_API:
			case QQKENO_QQLOTTERY_IDR_B1_API:
			case QQKENO_QQLOTTERY_CNY_B1_API:
			case QQKENO_QQLOTTERY_THB_B1_API:
			case QQKENO_QQLOTTERY_USD_B1_API:
			case QQKENO_QQLOTTERY_VND_B1_API:
			case QQKENO_QQLOTTERY_MYR_B1_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					$postJson = file_get_contents("php://input");
					$this->utils->debug_log("QQKENO_QQLOTTERY Callback ====> ",$postJson);

					$post = !empty($postJson) ? json_decode($postJson,true) : array();
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$response = $api->callback($method, $post);
					return $this->returnJsonResult($response);
				}
			break;

			case TFGAMING_ESPORTS_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					$postJson = file_get_contents("php://input");
					$this->utils->debug_log("TFGAMING_ESPORTS_API Callback -------> ", $postJson);

					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$post = !empty($postJson) ? json_decode($postJson,true) : array();
					$result = $api->callback($method, $post);
					if($result == "Invalid Token") {
						http_response_code(400);
					}
					return $this->returnJsonResult($result);
				} else {
					http_response_code(400);
					$sxe  = new SimpleXMLElement("<message/>");
					$status = $sxe->addChild('status','fail');
					$result = $sxe->addChild('result','Game not active.');
					$data =  $sxe->asXML();
				}
				return $this->returnXml($data);
			break;

			case ASIASTAR_API:
				if ($this->external_system->isGameApiActive($gamePlatformId)) {
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);

					$method = filter_input(INPUT_GET, 'method', FILTER_SANITIZE_URL);
					$username = filter_input(INPUT_GET, 'userName', FILTER_SANITIZE_URL);
					$timestamp = filter_input(INPUT_GET, 'timestamp', FILTER_SANITIZE_URL);
					$key = filter_input(INPUT_GET, 'key', FILTER_SANITIZE_URL);

					$request = array(
						"method" => $method,
						"username" => $username,
						"timestamp" => $timestamp,
						"key" => $key
					);

					$result = $api->callback($request, $method);
					return redirect($result['forward_url']);
				}
			break;

			case RGS_API:
				if($this->external_system->isGameApiActive($gamePlatformId)) {
					$params = $this->getInputGetAndPost();
					$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
					$data = $api->callback($method, $params);
					$this->utils->debug_log('RGS - Callback : ', $params, $method, $data);
				}

				if(!is_array($data)){
					$data = json_decode($data);
				}
				return $this->returnJsonResult($data);
			break;

			default :
				$this->utils->debug_log(__METHOD__, "case {$gamePlatformId} not implemented");
			break;
        }
	}

	private function isJSON($string){
	   return is_string($string) && is_array(json_decode($string, true)) ? true : false;
	}

	/**
	 * detail: validate tokens
	 *
	 * @param int $type
	 * @param string $token
	 * @return json
	 */
	public function validate_token($type, $token) {
		$this->load->model(array('common_token', 'player_model', 'affiliatemodel', 'users'));
		$id = null;
		$info = null;
		if ($type == 'player') {
			$id = $this->common_token->getPlayerIdByToken($token);
			if (!empty($id)) {
				$info = $this->player_model->getPublicInfoById($id);
			}
		} else if ($type == 'adminuser') {
			$id = $this->common_token->getAdminUserIdByToken($token);
			if (!empty($id)) {
				$info = $this->users->getPublicInfoById($id);
			}
		} else if ($type == 'affiliate') {
			$id = $this->common_token->getAffiliateIdByToken($token);
			if (!empty($id)) {
				$info = $this->affiliatemodel->getPublicInfoById($id);
			}
		} else {
			return $this->returnBadRequest();
		}
		$success = !empty($id);
		$this->returnJsonResult(array('success' => $success, 'public_info' => $info));
	}

	/**
	 * detail: get player id using order id
	 *
	 * @param int $oderId
	 */
	protected function getPlayerIdByOrderId($orderId) {
		//search walletaccount if starts with 'W'
		if (strtoupper(substr($orderId, 0, 1)) == 'W') {
			//search walletaccount
			$walletAccount = $this->getWalletAccountInfo($orderId);

			if ($walletAccount && array_key_exists('playerId', $walletAccount)) {
				return $walletAccount['playerId'];
			}
		} else {
			$ord = $this->getOrderInfo($orderId);
			if ($ord) {
				return $ord->player_id;
			}
		}
		return null;
	}

	/**
	 * detail: get wallet account records
	 *
	 * @param string $transCode
	 * @return array
	 */
	protected function getWalletAccountInfo($transCode) {
		$this->load->model(array('wallet_model'));
		$walletAccount = null;
		if (!empty($transCode)) {
			$walletAccount = $this->wallet_model->getWalletAccountByTransactionCode($transCode);
		}
		return $walletAccount;
	}

	/**
	 * detail: get order records
	 *
	 * @param int $orderId
	 * @return int
	 */
	protected function getOrderInfo($orderId) {
		$this->load->model(array('sale_order'));
		$ord = null;
		if ($orderId) {
			$ord = $this->sale_order->getSaleOrderById($orderId);
		}
		return $ord;
	}
}

///END OF FILE////////////
