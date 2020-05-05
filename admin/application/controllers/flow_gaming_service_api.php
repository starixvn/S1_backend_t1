<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Flow_gaming_service_api extends BaseController {

	const ENDPOINT_COMMON = 'common';
	const ENDPOINT_AUTH = 'auth';
	const ENDPOINT_BALANCE = 'balance';
	const ENDPOINT_TRANSACTION = 'transaction';
	const ENDPOINT_ENDROUND = 'endround';
	const ENDPOINT_DEFAULT = 'default';
	const ENDPOINT_REDIRECTOR = 'redirect';

	const TRANSACTION_WAGER = 'WAGER';
	const TRANSACTION_WAGER_INVALID = 'WAGER_INVALID';
	const TRANSACTION_PAYOUT = 'PAYOUT';
	const TRANSACTION_PAYOUT_INVALID = 'PAYOUT_INVALID';
	const TRANSACTION_REFUND = 'REFUND';
	const TRANSACTION_REFUND_INVALID = 'REFUND_INVALID';

	const LOGS_REFUND = 'refunded';
	const LOGS_SETTLE_API_REQUEST = 'settled_api_request';
	const LOGS_SETTLE_API_RESPONSE = 'settled_api_response';
	const LOGS_REFUND_API_RESPONSE = 'refunded_api_response';

	const STATUS_CODES = [
		'ERROR' => 500,
		'SUCCESS' => 200,
		'INVALID_TOKEN' => 401,
		'INVALID_FORMAT' => 400,
		'INSUFFICIENT_BALANCE' => 402,
		'DUPLICATE_TRANSACTION' => 409
	];

	const GAME_LOGS = 'game';
	const TRANSACTION_LOGS = 'transaction';

	const SUCCESS = true;
	const FAIL = false;

	const ERROR_MESSAGE = array(
		'duplicate_entry'   	=> 'Duplicate transaction entry',
		'insufficient_balance'  => 'Insufficient balance',
	);

	const API_REQUEST = 'api_request_';
	const ENDPOINT_RESPONSE = 'endpoint_response_';
	const ENDPOINT_ADD_SUBWALLET = 'endpoint_add_subwallet';
	const ENDPOINT_SUBTRACT_SUBWALLET = 'endpoint_subtract_subwallet';

	function __construct() {
		parent::__construct();
		$this->load->model(array('wallet_model','game_provider_auth','common_token','player_model','original_game_logs_model'));
		$this->game_api = null;
		$this->status_code = null;
		$this->is_error_wager = false;
		$this->refund_in_process = false;
	}

	private function init($game_platform_id) {
		$this->game_api = $this->utils->loadExternalSystemLibObject($game_platform_id);
		$this->gamePlatformId = $game_platform_id;
		$this->original_game_logs_table = $this->game_api->original_gamelogs_table;
		$this->transaction_game_logs_table = $this->game_api->original_transaction_table;
	}

	public function index($game_platform_id, $version = 'v1', $end_point) {
		$this->init($game_platform_id);

		if ($this->external_system->isGameApiActive($game_platform_id)) {
			$postJson = file_get_contents("php://input");
			$postData = !empty($postJson) ? json_decode($postJson,true) : array();

			switch ($end_point) {
				case self::ENDPOINT_COMMON:
					$this->processCommon($postData);
					break;
				case self::ENDPOINT_AUTH:
					$result = $this->processAuth($postData);
					return $this->output->set_status_header($this->status_code)->set_content_type('application/json')->set_output(json_encode($result));
					break;
				case self::ENDPOINT_BALANCE:
					$result = $this->processBalance($postData);
					return $this->output->set_status_header($this->status_code)->set_content_type('application/json')->set_output(json_encode($result));
					break;
				case self::ENDPOINT_TRANSACTION:
					$result = $this->processTransaction($postData);
					return $this->output->set_status_header($this->status_code)->set_content_type('application/json')->set_output(json_encode($result));
					break;
				case self::ENDPOINT_ENDROUND:
					$result = $this->processEndround($postData);
					return $this->output->set_status_header($this->status_code)->set_content_type('application/json')->set_output(json_encode($result));
					break;
				case self::ENDPOINT_REDIRECTOR:
					$postData = $this->CI->input->get();
					$result = $this->processRedirector($postData);
					break;
				default:
					$this->utils->debug_log("FLOW_GAMING_SEAMLESS ENDPOINT_ERROR ====> ", $end_point);
					$this->game_api->saveToResponseResult(self::FAIL, self::API_REQUEST . self::ENDPOINT_DEFAULT, $postData, self::API_REQUEST . self::ENDPOINT_DEFAULT);
					break;
			}
		}

	}

	private function processCommon($data) {
		$this->utils->debug_log("FLOW_GAMING_SEAMLESS ENDPOINT_COMMON ====> ", $data);
		$this->game_api->saveToResponseResult(self::SUCCESS, self::API_REQUEST . self::ENDPOINT_COMMON, $data, self::API_REQUEST . self::ENDPOINT_COMMON);
	}

	private function processRedirector($data) {
		$this->utils->debug_log("FLOW_GAMING_SEAMLESS ENDPOINT_REDIRECTOR ====> ", $data);
		$this->game_api->saveToResponseResult(self::SUCCESS, self::API_REQUEST . self::ENDPOINT_REDIRECTOR, $data, self::API_REQUEST . self::ENDPOINT_REDIRECTOR);
		redirect($this->game_api->mobile_lobby_url);
	}

	public function processAuth($data = null) {
		$this->game_api->saveToResponseResult(self::SUCCESS, self::API_REQUEST . self::ENDPOINT_AUTH, $data, self::API_REQUEST . self::ENDPOINT_AUTH);
		$this->utils->debug_log("FLOW_GAMING_SEAMLESS ENDPOINT_AUTH ====> ", $data);
		$start_time = strtotime(date('Y-m-d H:i:s'));
		$apiResult = $this->game_api->processVerifyToken($data);
		$end_time = strtotime(date('Y-m-d H:i:s'));
        $processing_time = $end_time - $start_time;

		if ($apiResult['success']) {
			$this->status_code = self::STATUS_CODES['SUCCESS'];
			$responseData = [
        		'req_id'    	  => $apiResult['data']['req_id'],
        		'processing_time' => $processing_time,
            	'token'     	  => $apiResult['data']['token'],
            	'username'  	  => $apiResult['data']['username'],
            	'account_ext_ref' => $apiResult['data']['account_ext_ref'],
            	'balance'  		  => $apiResult['data']['balance'],
            	'currency' 		  => $apiResult['data']['currency'],
            	'country'  	 	  => $apiResult['data']['country'],
            	'lang'     	  	  => $apiResult['data']['lang'],
            	'timestamp'	      => $apiResult['data']['timestamp']
            ];
			$this->game_api->saveToResponseResult(self::SUCCESS, self::ENDPOINT_RESPONSE . self::ENDPOINT_AUTH, $responseData, self::ENDPOINT_RESPONSE . self::ENDPOINT_AUTH);
			return $responseData;
		}
		$this->status_code = self::STATUS_CODES['INVALID_TOKEN'];
		$apiResult['data']['processing_time'] = $processing_time;
		$this->game_api->saveToResponseResult(self::SUCCESS, self::ENDPOINT_RESPONSE . self::ENDPOINT_AUTH, $apiResult['data'], self::ENDPOINT_RESPONSE . self::ENDPOINT_AUTH);
		return $apiResult['data'];
	}

	private function processBalance($data) {
		$this->utils->debug_log("FLOW_GAMING_SEAMLESS ENDPOINT_BALANCE ====> ", $data);
		$this->game_api->saveToResponseResult(self::SUCCESS, self::API_REQUEST . self::ENDPOINT_BALANCE, $data, self::API_REQUEST . self::ENDPOINT_BALANCE);
		$start_time = strtotime(date('Y-m-d H:i:s'));
		$apiResult = $this->game_api->processVerifyToken($data);

		$end_time = strtotime(date('Y-m-d H:i:s'));
        $processing_time = $end_time - $start_time;

		$this->status_code = self::STATUS_CODES['INVALID_TOKEN'];
		if ($apiResult['success']) {
			$this->status_code = self::STATUS_CODES['SUCCESS'];
			$responseData = [
				'req_id'          => $apiResult['data']['req_id'],
				'processing_time' => $processing_time,
				'token'			  => $apiResult['data']['token'],
				'balance'		  => $apiResult['data']['balance']
			];

            $responseResult = [
            	'verify_token' => $apiResult,
            	'response'	   => $responseData
            ];

			$this->game_api->saveToResponseResult(self::SUCCESS, self::ENDPOINT_RESPONSE . self::ENDPOINT_BALANCE, $responseResult, self::ENDPOINT_RESPONSE . self::ENDPOINT_BALANCE);
			return $responseData;
		} else {
            $responseResult = [
            	'verify_token' => $apiResult,
            	'response'	   => $apiResult['data']
            ];
			$this->game_api->saveToResponseResult(self::FAIL, self::ENDPOINT_RESPONSE . self::ENDPOINT_BALANCE, $responseResult, self::ENDPOINT_RESPONSE . self::ENDPOINT_BALANCE);
			return $apiResult['data'];
		}

	}

	private function processTransaction($data, $extra = null) {
		$this->utils->debug_log("FLOW_GAMING_SEAMLESS ENDPOINT_TRANSACTION ====> ", $data);
		$trans_category = $data['category'];
		$trans_amount = $data['amount'];
		$player_id = $this->game_api->getPlayerIdInGameProviderAuth($data['account_ext_ref']);
		$start_time = strtotime(date('Y-m-d H:i:s'));
		$apiResult = $this->game_api->processVerifyToken($data);

		$this->refund_in_process = false;

		$this->game_api->saveToResponseResult(self::SUCCESS, self::API_REQUEST . self::ENDPOINT_TRANSACTION . '_' . $trans_category, $data, self::API_REQUEST . self::ENDPOINT_TRANSACTION . '_' . $trans_category);

		if ($apiResult['success']) {
			$this->status_code = self::STATUS_CODES['SUCCESS'];
			$prev_balance = $this->processPlayerBalance($apiResult['data']['player_username']);
			$bExist = $this->queryExistingTransaction($data['round_id'], $data['category'], $data['tx_id'], true, true);

        	if (!$bExist) {
        		if ($trans_category == self::TRANSACTION_WAGER) {
        			if (isset($data['amount']) && $data['amount'] > $prev_balance) {
        				$this->is_error_wager = true;
        			} else {
						$this->subtract_amount($player_id, $trans_amount, $trans_category);
        			}
        		} else if ($trans_category == self::TRANSACTION_PAYOUT) {
					// Insufficient balance wagers has a category of WAGER_INVALID while successful wagers has a WAGER category upon refund
					// it will check first if there's a VALID WAGER
					$bExist = $this->queryExistingTransaction($data['round_id'], self::TRANSACTION_WAGER, null, true, false);
		        	if ($bExist) {
						$this->add_amount($player_id, $trans_amount, $trans_category);
		        	} else {
		        		$trans_category = self::TRANSACTION_PAYOUT_INVALID;
		        	}
				} else if ($trans_category == self::TRANSACTION_REFUND || $trans_category == self::TRANSACTION_REFUND_INVALID) {
					// Refund transaction from ENDROUND has a value on $extra which identify what kind of refund it comes like if the refund for successful WAGER then we will adjust the wallet, If the $extra has a INVALID_WAGER then we will not adjust the wallet
		        	if (!empty($extra) && $extra['transaction_upon_endround'] == true && $extra['refund_wager_category'] == self::TRANSACTION_WAGER) {
						$this->add_amount($player_id, $trans_amount, $trans_category);
		        	}

				}
				$after_balance = $this->processPlayerBalance($apiResult['data']['player_username']);
				$ext_tx_id = $this->game_api->getUniqueTicket();

				$transaction_data = $this->rebuildTransaction($data);

				$transaction_data['ext_tx_id'] = isset($ext_tx_id) ? $ext_tx_id : null;
				$transaction_data['before_balance'] = isset($prev_balance) ? $prev_balance : null;
				$transaction_data['after_balance'] = isset($after_balance) ? $after_balance : null;

				$this->game_api->doSyncOriginal(array($transaction_data), $this->transaction_game_logs_table, self::TRANSACTION_LOGS);

				$end_time = strtotime(date('Y-m-d H:i:s'));
		        $processing_time = $end_time - $start_time;

				$responseData = [
	        		'req_id'    	  => $apiResult['data']['req_id'],
	        		'processing_time' => $processing_time,
	            	'token'     	  => $apiResult['data']['token'],
	            	'balance'  	  	  => $after_balance,
	            	'ext_tx_id' 	  => $ext_tx_id,
	            	'timestamp'       => $apiResult['data']['timestamp'],
	            ];

	            $responseResult = [
	            	'verify_token' => $apiResult,
	            	'insert_data'  => $transaction_data,
	            	'response'	   => $responseData
	            ];

	            // For Insufficient amount upon WAGER we will save it on database but we will not adjust the wallet but we will save it on database
	            if ($this->is_error_wager) {
					return $this->returnErrorResponse($data['req_id'], $start_time, $apiResult['data']['token'], self::ERROR_MESSAGE['insufficient_balance'], self::ENDPOINT_TRANSACTION . '_' . $trans_category, $apiResult, self::STATUS_CODES['INSUFFICIENT_BALANCE']);
	            }
	            // If this transaction process upon ENDROUND not upon the PROVIDER API REQUEST then we will not return any RESPONSE
	            if (!empty($extra) && $extra['transaction_upon_endround'] == true) {
	            	return;
	            }

				$this->game_api->saveToResponseResult(self::SUCCESS, self::ENDPOINT_RESPONSE . self::ENDPOINT_TRANSACTION . '_' . $trans_category, $responseResult, self::ENDPOINT_RESPONSE . self::ENDPOINT_TRANSACTION . '_' . $trans_category);
	            return $responseData;
	    	} else {
				$this->game_api->saveToResponseResult(self::FAIL, self::ENDPOINT_RESPONSE . self::ENDPOINT_TRANSACTION . '_' . $trans_category, $apiResult, self::ENDPOINT_RESPONSE . self::ENDPOINT_TRANSACTION . '_' . $trans_category);
				return $this->returnErrorResponse($data['req_id'], $start_time, $apiResult['data']['token'], self::ERROR_MESSAGE['duplicate_entry'], self::ENDPOINT_TRANSACTION . '_' . $trans_category, $apiResult, self::STATUS_CODES['DUPLICATE_TRANSACTION']);
	    	}
		} else {
			$this->status_code = self::STATUS_CODES['INVALID_TOKEN'];
            $responseResult = [
            	'verify_token' => $apiResult,
            	'response'	   => $apiResult['data']
            ];
			$this->game_api->saveToResponseResult(self::FAIL, self::ENDPOINT_RESPONSE . self::ENDPOINT_TRANSACTION . '_' . $trans_category, $responseResult, self::ENDPOINT_RESPONSE . self::ENDPOINT_TRANSACTION . '_' . $trans_category);
			return $apiResult['data'];
		}
	}

	private function rebuildTransaction($postData) {
		// $round_id = isset($postData['round_id']) ? strtok($postData['round_id'], '-') . '-' . strtok('-') . '-' . strtok('-') : null;
		$round_id = isset($postData['round_id']) ? $postData['round_id'] : null;
		$trans_category = isset($postData['category']) ? $postData['category'] : null;
		if ($this->is_error_wager) {
			$trans_category = self::TRANSACTION_WAGER_INVALID;
		}
		
		return [
			'req_id'    		 => isset($postData['req_id']) ? $postData['req_id'] : null,
			'timestamp' 		 => isset($postData['timestamp']) ? $this->game_api->gameTimeToServerTime($postData['timestamp']) : null,
			'token'              => isset($postData['token']) ? $postData['token'] : null,
			'account_ext_ref' 	 => isset($postData['account_ext_ref']) ? $postData['account_ext_ref'] : null,
			'category' 			 => $trans_category,
			'tx_id' 			 => isset($postData['tx_id']) ? $postData['tx_id'] : null,
			'refund_tx_id' 	   	 => isset($postData['refund_tx_id']) ? $postData['refund_tx_id'] : null,
			'amount' 		 	 => isset($postData['amount']) ? $postData['amount'] : null,
			'pool_amount' 		 => isset($postData['pool_amount']) ? $postData['pool_amount'] : null,
			'application_id' 	 => isset($postData['application_id']) ? $postData['application_id'] : null,
			'item_id' 		 	 => isset($postData['item_id']) ? $postData['item_id'] : null,
			'external_game_id'   => (isset($postData['application_id']) && isset($postData['item_id'])) ? $postData['application_id'] . '-' . $postData['item_id'] : null,
			'round_id' 			 => isset($postData['round_id']) ? $round_id : null,
			'response_result_id' => isset($postData['response_result_id']) ? $postData['response_result_id'] : null,
			'external_uniqueid'  => isset($postData['tx_id']) ? $postData['tx_id'] : null,
		];
	}


	private function processEndround($data) {
		$this->utils->debug_log("FLOW_GAMING_SEAMLESS ENDPOINT_ENDROUND ====> ", $data);
		$this->game_api->saveToResponseResult(self::SUCCESS, self::API_REQUEST . self::ENDPOINT_ENDROUND, $data, self::API_REQUEST . self::ENDPOINT_ENDROUND);
		$start_time = strtotime(date('Y-m-d H:i:s'));
		$apiResult = $this->game_api->processVerifyToken($data);

		if ($apiResult['success']) {
			$this->status_code = self::STATUS_CODES['SUCCESS'];

			$endround_data = $this->rebuildEndround($apiResult, $data);     

			$this->game_api->doSyncOriginal(array($endround_data), $this->original_game_logs_table, self::GAME_LOGS);
			$end_time = strtotime(date('Y-m-d H:i:s'));
	        $processing_time = $end_time - $start_time;

			$current_balance = $this->processPlayerBalance($apiResult['data']['player_username']);

			$responseData = [
        		'req_id'    	  => $apiResult['data']['req_id'],
        		'processing_time' => $processing_time,
            	'token'     	  => $apiResult['data']['token'],
            	'balance'  	  	  => $current_balance,
            	'timestamp'       => $apiResult['data']['timestamp'],
            ];

            $responseResult = [
            	'verify_token' => $apiResult,
            	'insert_data'  => $endround_data,
            	'response'	   => $responseData
            ];
			$this->game_api->saveToResponseResult(self::SUCCESS, self::ENDPOINT_RESPONSE . self::ENDPOINT_ENDROUND, $responseResult, self::ENDPOINT_RESPONSE . self::ENDPOINT_ENDROUND);

            return $responseData;
    	} else {
			$this->status_code = self::STATUS_CODES['INVALID_TOKEN'];
            $responseResult = [
            	'verify_token' => $apiResult,
            	'response'	   => $apiResult['data']
            ];
			$this->game_api->saveToResponseResult(self::FAIL, self::ENDPOINT_RESPONSE . self::ENDPOINT_ENDROUND, $responseResult, self::ENDPOINT_RESPONSE . self::ENDPOINT_ENDROUND);
			return $apiResult['data'];
		}
	}


	private function rebuildEndround($verifyTokenData, $postData) {
		$player_id = $this->game_api->getPlayerIdInGameProviderAuth($postData['account_ext_ref']);
		$round_stats = isset($postData['round_stats']) ? $postData['round_stats'] : array();
		$txs = isset($postData['txs']) ? json_encode($postData['txs']) : '';
		$ext_tx_id = $this->game_api->getUniqueTicket();
		$start_time = strtotime(date('Y-m-d H:i:s'));
		$wager_count = null;
		$wager_sum = null;
		$payout_count = null;
		$payout_sum = null;
		$refund_count = null;
		$refund_sum = null;

		$current_balance = $this->processPlayerBalance($verifyTokenData['data']['player_username']);
		$after_balance = $current_balance;
		$prev_balance = $current_balance;

		foreach ($round_stats as $round) {
			if (isset($round['category']) && $round['category'] == self::TRANSACTION_WAGER) {
				$wager_count = isset($round['count']) ? $round['count'] : null;
				$wager_sum = isset($round['sum']) ? $round['sum'] : null;
			}
			if (isset($round['category']) && $round['category'] == self::TRANSACTION_PAYOUT) {
				$payout_count = isset($round['count']) ? $round['count'] : null;
				$payout_sum = isset($round['sum']) ? $round['sum'] : null;
			}
			if (isset($round['category']) && $round['category'] == self::TRANSACTION_REFUND) {
				$refund_count = isset($round['count']) ? $round['count'] : null;
				$refund_sum = isset($round['sum']) ? $round['sum'] : null;
				if ($refund_count >= 1) {
					$this->processRefundTransaction($postData, $player_id, $refund_count, $refund_sum);
				}
			}
		}

		if (isset($wager_count) && isset($wager_sum) && $wager_count >= 1) {
			$prev_balance = $prev_balance + $wager_sum;
		}
		if (isset($payout_count) && isset($payout_sum) && $payout_count >= 1) {
			$prev_balance = $prev_balance - $payout_sum;
		}
		if (isset($refund_count) && isset($refund_sum) && $refund_count >= 1) {
			$prev_balance = $prev_balance - $refund_sum;
		}

		// $round_id = isset($postData['round_id']) ? strtok($postData['round_id'], '-') . '-' . strtok('-') . '-' . strtok('-') : null;
		$round_id = isset($postData['round_id']) ? $postData['round_id'] : null;

		$endround_data = [
			'req_id'    		 => isset($postData['req_id']) ? $postData['req_id'] : null,
			'timestamp' 		 => isset($postData['timestamp']) ? $this->game_api->gameTimeToServerTime($postData['timestamp']) : null,
			'token'              => isset($postData['token']) ? $postData['token'] : null,
			'account_ext_ref' 	 => isset($postData['account_ext_ref']) ? $postData['account_ext_ref'] : null,
			'tx_id' 			 => isset($postData['tx_id']) ? $postData['tx_id'] : null,
			'application_id' 	 => isset($postData['application_id']) ? $postData['application_id'] : null,
			'item_id' 		 	 => isset($postData['item_id']) ? $postData['item_id'] : null,
			'external_game_id'   => (isset($postData['application_id']) && isset($postData['item_id'])) ? $postData['application_id'] . '-' . $postData['item_id'] : null,
			'round_id' 			 => isset($postData['round_id']) ? $round_id : null,
			'complete_round_id'  => isset($postData['round_id']) ? $postData['round_id'] : null,
			'txs' 				 => isset($postData['txs']) ? $txs : null,
			'wager_count' 		 => $wager_count,
			'wager_sum' 		 => $wager_sum,
			'payout_count' 		 => $payout_count,
			'payout_sum' 		 => $payout_sum,
			'refund_count' 		 => $refund_count,
			'refund_sum' 		 => $refund_sum,
			'status' 			 => (isset($refund_count) && isset($refund_sum) && $refund_count >= 1) ? self::LOGS_REFUND_API_RESPONSE : self::LOGS_SETTLE_API_RESPONSE,
			'response_result_id' => isset($postData['response_result_id']) ? $postData['response_result_id'] : null,
			'external_uniqueid'  => isset($postData['tx_id']) ? $postData['tx_id'] : null,
			'ext_tx_id' 		 => $ext_tx_id,
			'before_balance' 	 => isset($prev_balance) ? $prev_balance : null,
			'after_balance' 	 => isset($after_balance) ? $after_balance : null,
			'category' 	 		 => self::ENDPOINT_ENDROUND,
		];
		return $endround_data;
	}

	/*
	 * For some instances that the API did'nt send a REFUND request on ENDPOINT:TRANSACTION that the ENDPOINT:ENDROUND has. Because upon ENDPOINT:TRANSACTION if they receive an 
	 * invalid format on response they will not send a REFUND request on ENDPOINT:TRANSACTION because that TRANSACTION is invalid anymore then upon ENDPOINT:ENDROUND when they 
	 * receive an invalid format on response on ENDPOINT:TRANSACTION it will have a REFUND context
	 *
	 * As per provider if the transaction is successfull between API and ENDPOINT the API requesting on ENDPOINT will not be terminated/ cancelled. Like for example upon 
	 * ENDPOINT:TRANSACTION ( WAGER ) request if the ENDPOINT response contains an error then that transaction is INVALID anymore and no other transaction will be requested on 
	 * ENDPOINT: TRANSACTION 
	 */ 
	private function processRefundTransaction($data, $player_id, $refund_count, $refund_sum) {
		$this->refund_in_process = true;
		if (isset($data['txs'])) {
			foreach ($data['txs'] as $tx_id) {
				// Will check if there's a valid wager before processing refund
				$bExistValidWager = $this->queryExistingTransaction($data['round_id'], self::TRANSACTION_WAGER, null, true, false);
				// Will check if the current TX_ID is already existing on transaction then if not it means it is the REFUND because WAGER and PAYOUT always been saved upon callback
				$bExist = $this->queryExistingTransaction($data['round_id'], null, $tx_id, false, true);

		    	if (empty($bExist) == true && $bExistValidWager == true) {
					$newData = [
						'req_id'    		 => isset($data['req_id']) ? $data['req_id'] : null,
						'timestamp' 		 => isset($data['timestamp']) ?$data['timestamp'] : null,
						'token'              => isset($data['token']) ? $data['token'] : null,
						'account_ext_ref' 	 => isset($data['account_ext_ref']) ? $data['account_ext_ref'] : null,
						'category' 			 => self::TRANSACTION_REFUND,
						'tx_id' 			 => isset($tx_id) ? $tx_id : null,
						'refund_tx_id' 	   	 => null,
						'amount' 		 	 => (isset($this->refund_in_process) && $this->refund_in_process == true) ? $refund_sum : 0,
						'pool_amount' 		 => isset($data['pool_amount']) ? $data['pool_amount'] : null,
						'item_id' 		 	 => isset($data['item_id']) ? $data['item_id'] : null,
						'application_id' 	 => isset($data['application_id']) ? $data['application_id'] : null,
						'round_id' 			 => isset($data['round_id']) ? $data['round_id'] : null
					];

					$extra = [
						'refund_wager_category' => self::TRANSACTION_WAGER,
						'transaction_upon_endround' => true 
					];
					
					$this->processTransaction($newData, $extra);
		    	} else if (empty($bExist) == true && $bExistValidWager == false) {
					$newData = [
						'req_id'    		 => isset($data['req_id']) ? $data['req_id'] : null,
						'timestamp' 		 => isset($data['timestamp']) ?$data['timestamp'] : null,
						'token'              => isset($data['token']) ? $data['token'] : null,
						'account_ext_ref' 	 => isset($data['account_ext_ref']) ? $data['account_ext_ref'] : null,
						'category' 			 => self::TRANSACTION_REFUND_INVALID,
						'tx_id' 			 => isset($tx_id) ? $tx_id : null,
						'refund_tx_id' 	   	 => null,
						'amount' 		 	 => (isset($this->refund_in_process) && $this->refund_in_process == true) ? $refund_sum : 0,
						'pool_amount' 		 => isset($data['pool_amount']) ? $data['pool_amount'] : null,
						'item_id' 		 	 => isset($data['item_id']) ? $data['item_id'] : null,
						'application_id' 	 => isset($data['application_id']) ? $data['application_id'] : null,
						'round_id' 			 => isset($data['round_id']) ? $data['round_id'] : null
					];

					$extra = [
						'refund_wager_category' => self::TRANSACTION_WAGER_INVALID,
						'transaction_upon_endround' => true 
					];
					$this->processTransaction($newData, $extra);
		    	}
			}
		}
		return;
	}

	private function processPlayerBalance($player_username) {
		$current_balance = $this->game_api->queryPlayerBalance($player_username);
		return $current_balance['balance'];
	}

	private function queryExistingTransaction($round_id, $category = null, $tx_id = null, $by_category = true, $by_tx_id = true) {
        $sql = <<<EOD
            SELECT id, req_id, tx_id, token, round_id
            FROM {$this->transaction_game_logs_table}
            WHERE round_id = ?
EOD;
        $params = [
            $round_id
        ];

		if ($by_category) {
			$sql .= ' AND category = ?';
			array_push($params, $category);
		}

		if ($by_tx_id) {
			$sql .= ' AND tx_id = ?';
			array_push($params, $tx_id);
		}

    	$queryResult = $this->CI->original_game_logs_model->commonGetOriginalGameLogs($sql,$params);
    	if (!empty($queryResult)) {
    		return true;
    	}
    	return false;
	}

	private function subtract_amount($playerId, $amount, $transaction = null) {
		$lockedKey = NULL;
		$lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
		$this->utils->debug_log('lock subtract_amount', 'id', $playerId, $lock_it);

		if ($lock_it) {
			try {
				$this->startTrans();
				$this->wallet_model->decSubWallet($playerId, $this->gamePlatformId, $amount);
				$this->endTransWithSucc();
			} finally {
				$this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
				$this->utils->debug_log('release subtract_amount lock', 'id', $playerId);
				$this->game_api->saveToResponseResult(self::SUCCESS, self::ENDPOINT_SUBTRACT_SUBWALLET . '_' . $transaction, ['player_id' => $playerId, 'amount' => $amount], self::ENDPOINT_SUBTRACT_SUBWALLET . '_' . $transaction);
			}
		}
	}

	private function add_amount($playerId, $amount, $transaction = null) {
		$lockedKey = NULL;
		$lock_it = $this->utils->lockResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
		$this->utils->debug_log('lock add_amount', 'id', $playerId, $lock_it);

		if ($lock_it) {
			try {
				$this->startTrans();
				$this->wallet_model->incSubWallet($playerId, $this->gamePlatformId, $amount);
				$this->endTransWithSucc();
			} finally {
				$this->utils->releaseResourceBy($playerId, Utils::LOCK_ACTION_BALANCE, $lockedKey);
				$this->utils->debug_log('release add_amount lock', 'id', $playerId);
				$this->game_api->saveToResponseResult(self::SUCCESS, self::ENDPOINT_ADD_SUBWALLET . '_' . $transaction, ['player_id' => $playerId, 'amount' => $amount], self::ENDPOINT_ADD_SUBWALLET . '_' . $transaction);
			}
		}
	}

	private function returnErrorResponse($req_id, $start_time, $token, $err_desc, $end_point, $extra = [], $status_code) {
		$end_time = strtotime(date('Y-m-d H:i:s'));
        $processing_time = $end_time - $start_time;
		$responseData = [
    		'req_id'          => $req_id,
    		'processing_time' => $processing_time,
        	'token'     	  => $token,
			'err_desc'        => $err_desc
		];

		$responseResult = [
			'result'   => $extra,
			'response' => $responseData
		];
		$this->is_error_wager = false;
		$this->status_code = $status_code;
		$this->game_api->saveToResponseResult(self::FAIL, self::ENDPOINT_RESPONSE . $end_point, $responseResult, self::ENDPOINT_RESPONSE . $end_point);
		return $responseData;
	}
}

///END OF FILE////////////