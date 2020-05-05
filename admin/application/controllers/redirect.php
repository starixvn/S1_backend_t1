<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

/**
 * Class Redirect
 *
 * General behaviors include :
 *
 * * Load Template
 * * Payment Transaction
 * * Direct payment
 * * Redirect to live chat
 * * Admin Service
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Redirect extends BaseController {

	/**
	 * overview : Redirect constructor.
	 */
	function __construct() {
		parent::__construct();
		$this->load->model(array('external_system'));
		$this->load->library(array('authentication', 'session'));
	}

	/**
	 * overview : loads template for view based on regions in
	 *
	 * detail : config > template.php
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 * @param string $activenav
	 */
	private function loadTemplate($title = '', $description = '', $keywords = '', $activenav = '') {
		$this->load->library('template');
		$this->template->set_template($this->utils->getPlayerCenterTemplate(false));
		$this->template->add_js('resources/js/player/player.js');
		$this->template->write('skin', 'template1.css');
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('player_id', $this->authentication->getPlayerId());
		$this->template->write('isLogged', $this->authentication->isLoggedIn());

		$data['data'] = $this->player_functions->getPlayerMainWallet($this->authentication->getPlayerId());
		//var_dump($data['data']['mainwallet']['totalBalanceAmount']);exit();
		$this->template->write('mainwallet', $data['data']['mainwallet']['totalBalanceAmount']);
	}

	/**
	 * overview : load configured template
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 * @param string $activenav
	 */
	private function loadConfiguredTemplate($title = '', $description = '', $keywords = '', $activenav = '') {
		$this->load->library('template');
		$this->template->set_template($this->utils->getPlayerCenterTemplate(false));
		$this->template->add_js('resources/js/player/player.js');
		$this->template->write('skin', 'template1.css');
		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('player_id', $this->authentication->getPlayerId());
		$this->template->write('isLogged', $this->authentication->isLoggedIn());

		$data['data'] = $this->player_functions->getPlayerMainWallet($this->authentication->getPlayerId());
		//var_dump($data['data']['mainwallet']['totalBalanceAmount']);exit();
		$this->template->write('mainwallet', $data['data']['mainwallet']['totalBalanceAmount']);
	}

	// function repay($systemId, $orderId) {
	// 	list($loaded, $managerName) = $this->utils->loadExternalSystemLibObject($systemId);
	// 	if ($loaded) {
	// 		$api = $this->$managerName->getApi($systemId);
	// 		$this->load->model(array('sale_order'));
	// 		$order = $this->sale_order->getSaleOrderById($orderId);
	// 		//check status
	// 		if ($order->status == Sale_order::STATUS_SETTLED || $order->status == Sale_order::STATUS_BROWSER_CALLBACK) {
	// 			$this->goPaymentResultPage($systemId, $orderId, array('success' => true, 'message' => null, 'next_url' => null));
	// 			return;
	// 		}
	// 		$rlt = $api->generatePaymentUrlForm($order->id, $order->player_id, $order->amount, new DateTime());
	// 		if ($rlt && $rlt['success']) {

	// 			if ($rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_URL) {
	// 				if (!empty($rlt['url'])) {
	// 					redirect($rlt['url']);
	// 				}
	// 			} else if ($rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_FORM) {
	// 				if (!empty($rlt['url'])) {
	// 					list($html, $formId) = $api->createHtmlForm($rlt);
	// 					// echo $html;
	// 					$data = array('form_html' => $html, 'form_id' => $formId);
	// 					$this->load->view('player/redirect', $data);
	// 				}
	// 			} else if ($rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_DIRECT_PAY) {
	// 				// $this->session->set_flashdata('order_player_id', $playerId);
	// 				redirect('/redirect/direct_pay/' . $orderId);
	// 			}
	// 		}
	// 		return;
	// 	}

	// 	show_error('Bad Request', 400);
	// }

	/**
	 * overview : payment
	 *
	 * @param int		$systemId
	 * @param double	$amount
	 * @param int		$playerId
	 * @param int		$player_promo_id
	 * @param string	$enabledSecondUrl
	 * @param int		$bankId
	 * @param int		$orderId
	 */
	function payment($systemId, $amount, $playerId = null, $player_promo_id = null,
		$enabledSecondUrl = 'true', $bankId = null, $orderId = null) {
        $this->load->model(array('sale_order', 'sale_orders_status_history'));

		$api = $this->utils->loadExternalSystemLibObject($systemId);
		$this->load->vars('content_template', 'default_iframe.php'); # this page uses default content template in stable_center2
		if ($api) {
			if ($player_promo_id == '0') {
				$player_promo_id = null;
			}
			if ($bankId == '0') {
				$bankId = null;
			}
			// $amount = $this->input->post('amount');

			$this->utils->debug_log('[redirect/payment]', 'playerId', $playerId, 'player_promo_id', $player_promo_id, 'enabledSecondUrl', $enabledSecondUrl, 'bankId', $bankId, 'orderId', $orderId);

			if (!$this->sale_order->existsSaleOrder($orderId)) {
				$this->utils->debug_log('=========validate [redirect/payment] parameters order_id, not exist.');
				$this->returnBadRequest();
				return;
			}

			/*
			 * validte redirect url parameters:
			 * api id
			 * player_id
			 * amount diff < 1
			 */
			$saleOrder = $this->sale_order->getSaleOrderById($orderId);
			if($saleOrder->player_id != $playerId) {
				$this->utils->debug_log('=========validate [redirect/payment] parameters player_id, should be: ', $saleOrder->player_id, ', but the player_id of redirect url is: ', $playerId, ', secure_id: ', $saleOrder->secure_id);
				$this->returnBadRequest();
				return;
			}

			//get player id from session, if don't exists use playerId

			$sessionPlayerId = $this->authentication->getPlayerId();
			if (!empty($sessionPlayerId)) {
				$playerId = $sessionPlayerId;
			}
			if (empty($playerId)) {
				return $this->returnBadRequest();
			}
			$data['playerStatus'] = $this->utils->getPlayerStatus($playerId);

			if($saleOrder->system_id != $systemId) {
				$this->utils->debug_log('=========validate [redirect/payment] parameters system_id, should be: ', $saleOrder->system_id, ', but the system_id of redirect url is: ', $systemId, ', secure_id: ', $saleOrder->secure_id);
				$this->returnBadRequest();
				return;
			}

			if ( abs($saleOrder->amount - number_format($amount, 2, '.', '') ) > 1) {
				$this->utils->debug_log('=========validate [redirect/payment] parameters amount, should be: ', $saleOrder->amount, ', but the amount of redirect url is: ', $amount, ', secure_id: ', $saleOrder->secure_id);
				$this->returnBadRequest();
				return;
			}

			// check sale order detail status, if greater than 100, then take it as duplicately submitting
			if( ($saleOrder->detail_status > Sale_order::DEPOSIT_STATUS_CREATE_ORDER) && (!$api->allowSubmitSameOrderId()) ) {
				$message = lang('notify.duplicately_submiting_the_order_with_the_same_order_id');
				$this->utils->debug_log('=========get exist saleOrder detail_status: ', $saleOrder->secure_id, $saleOrder->detail_status);
				$this->returnText($message);
				return;
			}

			$info = $api->getInfoByEnv();
			$enableSecondBool = ($enabledSecondUrl == 'true');

			//don't create order again
			if (!$enableSecondBool && !empty($orderId)) {
                //current is second url, so don't create order again
				//check order id
				// if (!$this->sale_order->existsSaleOrder($orderId)) {
				// 	$this->returnBadRequest();
				// 	return;
				// }
			} else {
				if (empty($orderId)) {
					// $orderId = $api->createSaleOrder($playerId, $amount, $player_promo_id,
					// $this->getInputGetAndPost());
                    $this->returnBadRequest();
                    return;
                }

                $saleOrder = $this->sale_order->getSaleOrderWithPlayerById($orderId);
                if(empty($saleOrder)){
                    $this->returnBadRequest();
                    return;
                }
                $amount = $saleOrder->amount;
			}

            $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_CREATE_ORDER);
			$rlt = $api->generatePaymentUrlForm($orderId, $playerId, $amount,
				new DateTime(), $player_promo_id, $enableSecondBool, $bankId);
			$use_second_url = $api->shouldRedirect(true);
			if(!$use_second_url) {
				$this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_SUBMIT_ORDER);
			}
            if ($rlt && $rlt['success']) {
				$this->utils->debug_log('generatePaymentUrlForm result', $rlt);
				if ($rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_URL) {
					if(!$use_second_url) {
						$this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RERIERCT_URL);
					}
					if (!empty($rlt['url'])) {
						redirect($rlt['url']);
					} else {
						$this->returnBadRequest();
					}
				} else if ($rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_HTML) {
                    $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RERIERCT_FORM);
					if (!empty($rlt['html'])) {
						echo $rlt['html'];
					} else {
						$this->returnBadRequest();
					}
				}else if ($rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_FORM) {
                    $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RERIERCT_FORM);
					if (!empty($rlt['url'])) {
						$response = 'content.redirected';
						$response_result_id = $api->submitPreprocess((isset($rlt['params']) ? $rlt['params'] : []), $response, $rlt['url'], $response , array('errCode' => null, 'error' => null, 'statusCode' => null), $saleOrder->secure_id);

						list($html, $formId) = $api->createHtmlForm($rlt);
						$data = array('form_html' => $html, 'form_id' => $formId);
						$this->utils->debug_log('redirect type form', $data);
						$this->load->view('player/redirect', $data);
					} else {
						$this->returnBadRequest();
					}
				} else if ($rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_DIRECT_PAY) {
                    $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RERIERCT_DIRECT_PAY);
					redirect('/redirect/direct_pay/' . $systemId . '/' . $orderId);
				} else if ($rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_QRCODE) {
                    $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RERIERCT_QRCODE);
					if (!empty($rlt['url']) || !empty($rlt['base64_url']) || !empty($rlt['base64']) || !empty($rlt['image_url'])) {
						$this->load->model('sale_order');
						$saleOrder = $this->sale_order->getSaleOrderById($orderId);

						$title = lang('QRCode Payment');
						$data['title'] = $title;
						$data['order_info'] = $saleOrder;
						$data['currentLang'] = $this->language_function->getCurrentLanguage();
						if($api->getSystemInfo('qrcode_upper_msg')) {
							$data['qrcode_upper_msg'] = $api->getSystemInfo('qrcode_upper_msg');
						}
						if($api->getSystemInfo('qrcode_lower_msg')) {
							$data['qrcode_lower_msg'] = $api->getSystemInfo('qrcode_lower_msg');
						}
						if(!empty($rlt['url'])) {
							$data['qrcodeUrl'] = $rlt['url'];
						}
                        if(!empty($rlt['base64_url'])) {
                            $data['qrcodeBase64Url'] = $rlt['base64_url'];
                        }
                        if(!empty($rlt['base64'])) {
                            $data['qrcodeBase64'] = $rlt['base64'];
                        }
                        if(!empty($rlt['image_url'])) {
                            $data['qrcodeImage'] = $rlt['image_url'];
                        }
						if(!empty($rlt['status_url'])) { # URL used to poll for payment status
							$data['statusUrl'] = $rlt['status_url'];
							$data['statusSuccessKey'] = $rlt['status_success_key'];
						}
						$data['player_center_template']=$this->utils->getPlayerCenterTemplate(false);
						$data['hide_timeout']= @isset($rlt['hide_timeout'])?$rlt['hide_timeout']:false;

						$this->loadTemplate($title);
						$this->template->add_css('resources/third_party/fancybox/jquery.fancybox.css?v=2.1.5');
						$this->template->add_js('resources/third_party/fancybox/jquery.fancybox.pack.js?v=2.1.5');
						$this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
						$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);
						$this->template->render();
					} else {
						$this->returnBadRequest();
					}
				} else if ($rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_QRCODE_MODAL){
                    $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RERIERCT_QRCODE_MODAL);
					$this->load->vars('force_ext_template', 'iframe');
					$this->load->vars(['content_template' => null]);
					if (!empty($rlt['url']) || !empty($rlt['base64'])) {
						$this->load->model('sale_order');
						$saleOrder = $this->sale_order->getSaleOrderById($orderId);

						$title = lang('QRCode Payment');
						$data['title'] = $title;
						$data['order_info'] = $saleOrder;
						$data['currentLang'] = $this->language_function->getCurrentLanguage();
						if(!empty($rlt['url'])) {
							$data['qrcodeUrl'] = $rlt['url'];
						}
						if(!empty($rlt['base64'])) {
							$data['qrcodeBase64'] = $rlt['base64'];
						}
						if(!empty($rlt['status_url'])) { # URL used to poll for payment status
							$data['statusUrl'] = $rlt['status_url'];
							$data['statusSuccessKey'] = $rlt['status_success_key'];
						}
						$data['player_center_template']=$this->utils->getPlayerCenterTemplate(false);


						$this->loadTemplate($title);
						$this->template->add_css('resources/third_party/fancybox/jquery.fancybox.css?v=2.1.5');
						$this->template->add_js('resources/third_party/fancybox/jquery.fancybox.pack.js?v=2.1.5');
						$this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
						$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);
						$this->template->render();
					} else {
						$this->returnBadRequest();
					}
				} else if ($rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_STATIC) {
                    $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RERIERCT_STATIC);
					# This type displays data, an array of key=>value, in a table
					if (!empty($rlt['data'])){
						$this->load->model('sale_order');
						$saleOrder = $this->sale_order->getSaleOrderById($orderId);

						$title = lang('Confirm Deposit Info');
						$data['site']=$this->utils->getSystemUrl('www');
						$data['title'] = $title;
						$data['is_error'] = false;
						$data['order_info'] = $saleOrder;
						$data['currentLang'] = $this->language_function->getCurrentLanguage();
						$data['staticData'] = $rlt['data'];
						if(isset($rlt['style_data'])){
							$data['style_data']=$rlt['style_data'];
						}
						if(isset($rlt['getExternalApi_btn'])){
							$data['getExternalApi_btn']=$rlt['getExternalApi_btn'];
						}
						if(isset($rlt['setExternalApi_btn'])){
							$data['setExternalApi_btn']=$rlt['setExternalApi_btn'];
						}
						$data['hide_timeout']= @isset($rlt['hide_timeout'])?$rlt['hide_timeout']:false;
						$data['hide_system_confirmation']= @isset($rlt['hide_system_confirmation'])?$rlt['hide_system_confirmation']:'';
						$data['player_bank_info']= @isset($rlt['player_bank_info'])?$rlt['player_bank_info']:'';
                        $data['collection_text_transfer'] = @isset($rlt['collection_text_transfer'])?$rlt['collection_text_transfer']:'';
                        $data['is_not_display_recharge_instructions']=@isset($rlt['is_not_display_recharge_instructions'])?$rlt['is_not_display_recharge_instructions']:'';


						$this->loadTemplate($title);
						$this->template->add_css('resources/third_party/fancybox/jquery.fancybox.css?v=2.1.5');
						$this->template->add_js('resources/third_party/fancybox/jquery.fancybox.pack.js?v=2.1.5');
						$this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
						$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);
						$this->template->render();
					}
					else {
						$this->returnBadRequest();
					}
				}
				return;
			} else if (@$rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_ERROR) {
                $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RERIERCT_ERROR);
				# This return type is for displaying error messages
				if (!empty($rlt['message'])) {
					$data['site']=$this->utils->getSystemUrl('www');
					$data['is_error'] = true;
					$data['title'] = lang('Payment Failed');
					$data['message'] = $rlt['message'];

					$this->loadConfiguredTemplate(lang('Error'));
					$this->template->add_css('resources/third_party/fancybox/jquery.fancybox.css?v=2.1.5');
					$this->template->add_js('resources/third_party/fancybox/jquery.fancybox.pack.js?v=2.1.5');
					$this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
					$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);
					$this->template->render();
					return;
				}
			} else if (@$rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_ERROR_MODAL) {
                $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RERIERCT_ERROR_MODAL);
				$this->load->vars('force_ext_template', 'iframe');
				$this->load->vars(['content_template' => null]);
				# This return type is for displaying error messages
				if (!empty($rlt['message'])) {
					$data['site']=$this->utils->getSystemUrl('www');
					$data['is_error'] = true;
					$data['title'] = lang('Payment Failed');
					$data['message'] = $rlt['message'];

					$this->loadConfiguredTemplate(lang('Error'));
					$this->template->add_css('resources/third_party/fancybox/jquery.fancybox.css?v=2.1.5');
					$this->template->add_js('resources/third_party/fancybox/jquery.fancybox.pack.js?v=2.1.5');
					$this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
					$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);
					$this->template->render();
					return;
				}
			} else if (@$rlt['type'] == Abstract_payment_api::REDIRECT_TYPE_STATIC) {
                $this->sale_orders_status_history->createSaleOrderStatusHistory($orderId,Sale_order::DEPOSIT_STATUS_RERIERCT_STATIC);
				if (!empty($rlt['data'])){

					$data = $rlt['data'];
					$data['site']=$this->utils->getSystemUrl('www');

					$this->loadConfiguredTemplate(lang('Error'));
					$this->template->add_css('resources/third_party/fancybox/jquery.fancybox.css?v=2.1.5');
					$this->template->add_js('resources/third_party/fancybox/jquery.fancybox.pack.js?v=2.1.5');
					$this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
					$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);
					$this->template->render();
					return;
				}
			}
		}

		$this->returnBadRequest();

	}

	public function static_response($redirect_type) {
		$secure_id = isset($_GET['secure_id']) ? $_GET['secure_id'] : 'D987654321123';
		$payment_account_name = isset($_GET['payment_account_name']) ? $_GET['payment_account_name'] : '李荣皓';
		$payment_account_number = isset($_GET['payment_account_number']) ? $_GET['payment_account_number'] : '6214623721003088704';
		$payment_branch_name = isset($_GET['payment_branch_name']) ? $_GET['payment_branch_name'] : '广发银行股份有限公司北京东直门分行';
		$amount = isset($_GET['amount']) ? $_GET['amount'] : '3';
		$created_at = isset($_GET['created_at']) ? $_GET['created_at'] : '2019-01-09 13:35:09';
		$timeout_at = isset($_GET['timeout_at']) ? $_GET['timeout_at'] : '2019-01-09 14:35:09';
		$Beneficiary_Bank = isset($_GET['Beneficiary_Bank']) ? $_GET['Beneficiary_Bank'] : '广发银行股份有限公司';
		$Beneficiary_Account = isset($_GET['Beneficiary_Account']) ? $_GET['Beneficiary Account'] : '6214623721003088704';
		$Beneficiary_Name = isset($_GET['Beneficiary_Name']) ? $_GET['Beneficiary_Name Account'] : '李荣皓';
		$Deposit_Amount = isset($_GET['Deposit_Amount']) ? $_GET['Deposit_Amount'] : '3';
		$Beneficiary_Bank_Address = isset($_GET['Beneficiary_Bank_Address']) ? $_GET['Beneficiary_Bank_Address'] : '广发银行股份有限公司北京东直门分行';
		$Beneficiary_note = isset($_GET['Beneficiary_note']) ? $_GET['Beneficiary_note'] : '86597';
		$is_not_display_recharge_instructions = '';
		if (isset($is_not_display_recharge_instructions)){
			// $is_not_display_recharge_instructions = ($_GET['is_not_display_recharge_instructions']=='1') ? true : false;
			if($_GET['is_not_display_recharge_instructions']=='1'){
				$is_not_display_recharge_instructions = true;
			}else{
				$is_not_display_recharge_instructions = false;
			}
		}


		if($redirect_type == "static") {
			$title = lang('Confirm Deposit Info');
			$data['site']=$this->utils->getSystemUrl('www');
			$data['title'] = $title;
			$data['is_error'] = false;
			$data['order_info'] = (object)array(
				'secure_id' => $secure_id,
				'payment_account_name' => $payment_account_name,
				'payment_account_number' => $payment_account_number ,
				'payment_branch_name' => $payment_branch_name ,
				'amount' => $amount,
				'created_at' => $created_at,
				'timeout_at' => $timeout_at
			);
			$data['currentLang'] = $this->language_function->getCurrentLanguage();
			$rlt['Beneficiary Bank'] = $Beneficiary_Bank ;
			$rlt['Beneficiary Account'] = $Beneficiary_Account;
			$rlt['Beneficiary Name'] = $Beneficiary_Name;
			$rlt['Deposit Amount'] = $Deposit_Amount;
			$rlt['Beneficiary Bank Address'] = $Beneficiary_Bank_Address;
			$rlt['Beneficiary note'] = $Beneficiary_note;
			$data['staticData'] = $rlt;
			$data['is_not_display_recharge_instructions']=$is_not_display_recharge_instructions;
			$this->loadStaticResponseTemplate($title,$data);
		}elseif($redirect_type == "qrcode"){
			$data['qrcodeUrl'] = 'http';
			$title = lang('QRCode Payment');
			$data['title'] = $title;
			$data['order_info'] = (object)array(
				'secure_id' => $secure_id,
				'payment_account_name' => $payment_account_name,
				'payment_account_number' => $payment_account_number ,
				'payment_branch_name' => $payment_branch_name ,
				'amount' => $amount,
				'created_at' => $created_at,
				'timeout_at' => $timeout_at
			);
			$data['currentLang'] = $this->language_function->getCurrentLanguage();
			$data['player_center_template']=$this->utils->getPlayerCenterTemplate(false);
			$this->loadStaticResponseTemplate($title,$data);
		}else{
			$this->returnBadRequest();
		}
	}

	public function loadStaticResponseTemplate($title,$data){
		$this->loadTemplate($title);
		$this->template->add_css('resources/third_party/fancybox/jquery.fancybox.css?v=2.1.5');
		$this->template->add_js('resources/third_party/fancybox/jquery.fancybox.pack.js?v=2.1.5');
		$this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);
		$this->template->render();
	}

	/**
	 * overview : direct payment
	 *
	 * @param int	$systemId
	 * @param int	$orderId
	 */
	function direct_pay($systemId, $orderId) {
		$api = $this->utils->loadExternalSystemLibObject($systemId);

		// $playerId = $this->session->flashdata('order_player_id');
		$sessionPlayerId = $this->authentication->getPlayerId();
		if (!empty($sessionPlayerId)) {
			$playerId = $sessionPlayerId;
		}
		if ($api && $orderId) {
			//check order and player id
			$this->load->model(array('sale_order', 'payment_account'));
			$ord = $this->sale_order->getSaleOrderById($orderId);
			if ($ord && $playerId && $ord->player_id == $playerId) {
				$rlt = $api->directPay($ord);
				$this->utils->debug_log('rlt', $rlt);
				if ($rlt['success'] && isset($rlt['next_url']) && !empty($rlt['next_url'])) {
					redirect($rlt['next_url']);
				} else {
					//result or pending
					$this->goPaymentResultPage($systemId, $orderId, $rlt);
				}
				//goto pending page
				return;
			}
			// $this->session->set_flashdata('message', $rlt['message']);
			// $this->session->set_flashdata('next_url', $rlt['next_url']);
			// //redirect to page, so don't happen another submit when refresh page
			// $this->goPaymentResultPage($systemId, $orderId, $rlt);

			// }
		}

		$this->returnBadRequest();
		// show_error('Bad Request', 400);
	}

	/**
	 * overview : redirect to live chat
	 */
	function gotolivechat() {
		//only for admin
		$adminUsername = $this->authentication->getUsername();
		$adminUserId = $this->authentication->getUserId();

		if (!empty($adminUsername)) {
			redirect($this->utils->generateLiveChatAutoLoginLink($adminUsername, $adminUserId));
			return;
		}

		$this->returnBadRequest();
	}

	/**
	 * overview : admin service
	 */
	public function admin_service(){
		//read session
		$adminUsername = $this->authentication->getUsername();
		$adminUserId = $this->authentication->getUserId();
		//goto new admin
		// $apiPath=realpath(APPPATH.'/../../api');
		// $dotenv = new Dotenv\Dotenv($apiPath);
		// $dotenv->load();
		// $jwtSecret=getenv('JWT_SECRET');

		if(!empty($adminUsername) && !empty($adminUserId)){
			// $this->load->library('third_party/jwt');

			// $token=JWT::encode(['operator'=>[
			// 	'unique_name'=> $this->utils->getConfig('operator_unique_name'),
			// 	'admin_user'=>['user_id'=>$adminUserId, 'username'=>$adminUsername],
			// ]
			// ]);

			//load token
			$token=$this->getAdminToken($adminUserId);

			redirect($this->utils->getConfig('service_api_login').'/'.$token);
		}else{
			$this->returnBadRequest();
		}

	}

	/**
	 *
	 * game backoffice
	 *
	 * @param  string $apiId
	 * @return post form
	 *
	 */
	public function game_bo($apiId, $as_master = null){

		try {
			//load api
			$api=$this->utils->loadExternalSystemLibObject($apiId);
			if (empty($api)) {
				throw new Exception("No permission, or API $apiID not ready", 1);
			}

			$this->load->library(['permissions', 'user_functions', 'authentication', 'lottery_bo_roles']);
			$this->permissions->setPermissions();

			//create view and redirect it to bo
			$title = lang('Lottery BoxOffice');

			$data=['backoffice_info'=>null, 'title'=>$title];


			// if(!empty($api)){
			$boInfo=$api->getBackOfficeInfo();

			$this->utils->debug_log(__METHOD__, 'boInfo', $boInfo);

	        //validate info
	        // if(empty($boInfo['backoffice_url']) || empty($boInfo['backoffice_username']) || empty($boInfo['backoffice_password'])){
	        if(empty($boInfo['backoffice_url'])){
				throw new Exception('backoffice_url not set', 2);
	        }

	        $data['backoffice_info'] = $boInfo;

			// }
			// else {
			// 	$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('role.nopermission'));
			// 	redirect('/home');
			// 	return;
			// }

			$roles = $this->lottery_bo_roles->roles_fetch();

			$admin_user = $this->authentication->getUsername();

			$this->utils->debug_log(__METHOD__, 'roles - admin_user', [ 'roles' => $roles, 'admin_user' => $admin_user ]);

			switch ($admin_user) {
			// OGP-6087
				case 'superadmin' :
				case 'admin' :
					$this->utils->debug_log(__METHOD__, [ 'lbo_user_group' => 'superadmin,admin' ]);
					if (empty($boInfo['backoffice_username']) || empty($boInfo['backoffice_password'])) {
						throw new Exception('backoffice_username or backoffice_password not set', 3);
					}
					$data['backoffice_info']['backoffice_username'] = $boInfo['backoffice_username'];
					$data['backoffice_info']['backoffice_password'] = $boInfo['backoffice_password'];

					break;

				case 'master' :
				case 't1lottery_master' :
					$this->utils->debug_log(__METHOD__, [ 'lbo_user_group' => 'master' ]);
					$bo_master_username = $api->getSystemInfo('backoffice_master_username');
					$bo_master_password = $api->getSystemInfo('backoffice_master_password');
					if (empty($bo_master_username) || empty($bo_master_password)) {
						throw new Exception('backoffice_master_username or backoffice_master_password not set', 4);
					}
					$data['backoffice_info']['backoffice_username'] = $bo_master_username;
					$data['backoffice_info']['backoffice_password'] = $bo_master_password;

					break;

				default :
					// For other admin users registered in extra_info
					$this->utils->debug_log(__METHOD__, [ 'lbo_user_group' => 'other_admin_users' , 'array_key_exists' => array_key_exists($admin_user, $roles) ]);
					if (array_key_exists($admin_user, $roles)) {
						$prefix = $api->getSystemInfo('prefix_for_bo_username');

						$data['backoffice_info']['backoffice_username'] = "{$prefix}_{$admin_user}";
						$passwd_plain = $this->lottery_bo_roles->getPasswordPlainByUsername($admin_user);
						$data['backoffice_info']['backoffice_password'] = $passwd_plain;
					}
					// Or no permission
					else {
						throw new Exception('role.nopermission', 5);
					}

					break;
			}

			$this->utils->debug_log(__METHOD__, 'lottery bo signin', [ 'admin_user' => $admin_user, 'backoffice_info' => $data['backoffice_info'] ]);

			$description='';
			$keywords='';
			$activenav='t1lottery_bo';  //change nav

			$this->load->library('template');
			// $this->template->add_js('resources/js/moment.min.js');
			// $this->template->add_js('resources/js/jquery.daterangepicker.js');
			$this->template->add_js('resources/js/datatables.min.js');
			// $this->template->add_js('resources/js/jquery.dataTables.min.js');
			// $this->template->add_js('resources/js/dataTables.responsive.min.js');

			$this->template->add_css('resources/css/general/style.css');
			// $this->template->add_css('resources/css/general/fontawesome/font-awesome.css');
			$this->template->add_css('resources/css/general/fontawesome/build.css');
			// $this->template->add_css('resources/css/daterangepicker.css');
			$this->template->add_css('resources/css/datatables.min.css');
			// $this->template->add_css('resources/css/jquery.dataTables.css');
			// $this->template->add_css('resources/css/dataTables.responsive.css');
			$this->template->add_js('resources/js/select2.min.js');
			$this->template->add_css('resources/css/select2.min.css');

			$this->template->add_css('resources/css/player_management/style.css');
			$this->template->write('title', $title);
			$this->template->write('description', $description);
			$this->template->write('keywords', $keywords);
			$this->template->write('activenav', $activenav);
			$this->template->write('username', $this->authentication->getUsername());
			$this->template->write('userId', $this->authentication->getUserId());

			$this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
			//maybe have multiple integration
			$this->template->write_view('main_content', '/system_management/t1lottery_bo', $data);
			$this->template->render();
		}
		catch (Exception $ex) {
			// $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('role.nopermission'));
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang($ex->getMessage()) . " ({$ex->getCode()})");
			redirect('/home');
			return;
		}

	}

	public function t1lottery_bo_boss() {
		return $this->game_bo(T1LOTTERY_API, 'boss');
	}

	public function t1lottery_bo(){

		return $this->game_bo(T1LOTTERY_API);

	// 	$this->load->library(['permissions']);
	// 	$this->permissions->setPermissions();

	// 	//create view and redirect it to bo
	// 	$title = lang('Lottery BoxOffice');

	// 	$data=['backoffice_info'=>null, 'title'=>$title];
	// 	//load api
	// 	$api=$this->utils->loadExternalSystemLibObject(T1LOTTERY_API);
	// 	if(!empty($api)){

	// 		$boInfo=$api->getBackOfficeInfo();

	//         //validate info
	//         if(!empty($boInfo['backoffice_url']) && !empty($boInfo['backoffice_username']) &&
	//         		!empty($boInfo['backoffice_password'])){

	// 			$data['backoffice_info']=$boInfo;

	//         }

	// 	}

	// // private function loadTemplate($title, $description, $keywords, $activenav) {

	// 	$description='';
	// 	$keywords='';
	// 	$activenav='t1lottery_bo';

	// 	$this->load->library('template');
	// 	// $this->template->add_js('resources/js/moment.min.js');
	// 	// $this->template->add_js('resources/js/jquery.daterangepicker.js');
	// 	$this->template->add_js('resources/js/datatables.min.js');
	// 	// $this->template->add_js('resources/js/jquery.dataTables.min.js');
	// 	// $this->template->add_js('resources/js/dataTables.responsive.min.js');

	// 	$this->template->add_css('resources/css/general/style.css');
	// 	// $this->template->add_css('resources/css/general/fontawesome/font-awesome.css');
	// 	$this->template->add_css('resources/css/general/fontawesome/build.css');
	// 	// $this->template->add_css('resources/css/daterangepicker.css');
	// 	$this->template->add_css('resources/css/datatables.min.css');
	// 	// $this->template->add_css('resources/css/jquery.dataTables.css');
	// 	// $this->template->add_css('resources/css/dataTables.responsive.css');
	// 	$this->template->add_js('resources/js/select2.min.js');
	// 	$this->template->add_css('resources/css/select2.min.css');

	// 	$this->template->add_css('resources/css/player_management/style.css');
	// 	$this->template->write('title', $title);
	// 	$this->template->write('description', $description);
	// 	$this->template->write('keywords', $keywords);
	// 	$this->template->write('activenav', $activenav);
	// 	$this->template->write('username', $this->authentication->getUsername());
	// 	$this->template->write('userId', $this->authentication->getUserId());

	// 	// $this->loadTemplate($title);
	// 	$this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
	// 	$this->template->write_view('main_content', '/system_management/t1lottery_bo', $data);
	// 	$this->template->render();
	}

	public function agency(){
		//get session player id
		$playerId=$this->getLoggedPlayerId();
		if(empty($playerId)){
			return redirect('/');
		}

		$uri=$this->input->post('next');

        $token = $this->common_token->getPlayerToken($playerId);
		//logged player id
		$agency_url=$this->utils->getSystemUrl('agency', $uri.'?login_by_player_token='.$token);

		return redirect($agency_url);
	}

}

/// END OF FILE/////////////