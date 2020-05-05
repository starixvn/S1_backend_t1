<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Rwb_service_api extends BaseController {

	private $APIauth;
	private $player_id;

	const Success = 0;
	const Error_Unauthorized = 10;
	const Error_ReplayDetected = 11;
	const Error_NotFound = 20;
	const Error_UserBalanceNotEnough = 22;
	const Error_InvalidInput = 2;
	const Error_UserInactive = 21;
	const Error_System = 1;

	const SETTLED_DEBIT = 1;
	const SETTLED_CREDIT = 2;
	const DUPLICATE_TRANSACTION = 3;

	public function __construct() {
		parent::__construct();
		$this->load->model(array('wallet_model','game_provider_auth','rwb_game_logs','rwb_game_transactions'));
		$this->game_api = $this->utils->loadExternalSystemLibObject(RWB_API);
	}

	public function transaction($method){
		$result = file_get_contents('php://input');
		$hash = filter_input(INPUT_GET,"hash",FILTER_SANITIZE_STRING);
		$debit = $method == "debit";
		return $this->debitCredit($result,$hash,$debit);
	}

	public function debitCredit($result,$hash,$debit = true){
		$secret = $this->game_api->secret_key;
		$authorized = $hash == hash_hmac('sha256', $result, $secret);
		$result = json_decode($result, true);
		$playerName = $this->game_api->getPlayerUsernameByGameUsername($result['UserId']);
		$playerId = $this->CI->game_provider_auth->getPlayerIdByPlayerName($result['UserId'], RWB_API);
		//balance section
		$balance_result = $this->game_api->queryPlayerBalance($playerName);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;
		$difference = (float)$this->utils->roundCurrency(($balance - abs($result['Amount'])));
		$exist = $this->CI->rwb_game_transactions->isRowIdAlreadyExists($result['RequestId']);
		$transactionExist = $this->CI->rwb_game_transactions->isTransactionIdAlreadyExists($result['TransactionId']);
		$isBlocked = $this->game_api->isBlocked($playerName);
		$success = true;
		$is_settled = 0;
		try {
			if(!$result){
				$error = $this->getErrorMessage(self::Error_InvalidInput,$balance);
				throw new Exception(json_encode($error));
			}

			if(!$authorized) {
				$error = $this->getErrorMessage(self::Error_Unauthorized,$balance);
				throw new Exception(json_encode($error));
			}

			if($isBlocked){
				$error = $this->getErrorMessage(self::Error_UserInactive,$balance);
				throw new Exception(json_encode($error));
			}

			if(!$playerId && !$playerName) {
				$error = $this->getErrorMessage(self::Error_NotFound,$balance);
				throw new Exception(json_encode($error));
			}

			if($exist) {
				$error = $this->getErrorMessage(self::Error_ReplayDetected,$balance);
				throw new Exception(json_encode($error));
			}

			if(!$transactionExist){
				if(abs($result['Amount']) > 0){
					$success = false;
					if($debit){
						if ($difference < 0) {
							$error = $this->getErrorMessage(self::Error_UserBalanceNotEnough,$balance);
							throw new Exception(json_encode($error));
						}
						$is_settled = self::SETTLED_DEBIT;
						$success = $this->subtract_amount($playerId,abs($result['Amount']));
					} else {
						$is_settled = self::SETTLED_CREDIT;
						$success = $this->add_amount($playerId,abs($result['Amount']));
					}
				} else {
					$is_settled = ($debit) ? self::SETTLED_DEBIT : self::SETTLED_CREDIT;
				}
			} 
			//check balance after
			$afterBalanceResult = $this->game_api->queryPlayerBalance($playerName);
			$afterBalance = isset($afterBalanceResult['success']) && $afterBalanceResult['success'] ? $afterBalanceResult['balance'] : 0;

			if($success){
				$transaction = array(
					"force_debit" => isset($result['ForceDebit']) ? $result['ForceDebit'] : NULL,
					"request_id" => isset($result['RequestId']) ? $result['RequestId'] : NULL,
					"transaction_id" => isset($result['TransactionId']) ? $result['TransactionId'] : NULL,
					"user_id" => isset($result['UserId']) ? $result['UserId'] : NULL,
					"bet_id" => isset($result['BetId']) ? $result['BetId'] : NULL,
					"reason" => isset($result['Reason']) ? $result['Reason'] : NULL,
					"amount" => isset($result['Amount']) ? $result['Amount'] : NULL,
					"currency" => isset($result['Currency']) ? $result['Currency'] : NULL,
					"description" => isset($result['Description']) ? $result['Description'] : NULL,
					"settle_status" => isset($result['SettleStatus']) ? $result['SettleStatus'] : NULL,
					"created_at" => date('Y-m-d H:i:s'),
					"is_settled" => (!$transactionExist) ? $is_settled : self::DUPLICATE_TRANSACTION,
					"after_balance" => $afterBalance,
					"before_balance" => $balance
				);

				$this->utils->debug_log('>>>>>>>>>>>>>> RWB transaction monitor', $transaction);
				
				if(!$exist) {
					$this->CI->rwb_game_transactions->insertRow($transaction);
				}

				$response = array(
					"Code" 		=> self::Success,
					"Balance"	=> $afterBalance
				);
			} else{
				$response = array(
					"Code" 		=> self::Error_System,
					//"Balance"	=> $afterBalance remove balance on error 
				);
			}
		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response = json_decode($e->getMessage());
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($response));
		return;
	}

	private function subtract_amount($player_id, $amount) {
		$controller = $this;
		$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount) {
			return $controller->wallet_model->decSubWallet($player_id, $controller->game_api->getPlatformCode(), $amount);
		});
		return $success;
	}

	private function add_amount($player_id, $amount) {
		$controller = $this;
		$success = $this->lockAndTransForPlayerBalance($player_id, function() use($controller, $player_id, $amount) {
			return $controller->wallet_model->incSubWallet($player_id, $controller->game_api->getPlatformCode(), $amount);
		});
		return $success;
	}

	public function getErrorMessage($statusCode,$balance){
		$data = array(
				"Code" => $statusCode,
				// "Balance" => $balance, remove balance on error
		);

		if($statusCode == self::Error_UserBalanceNotEnough){//alow this error code to pass balance
			$data['Balance'] = $balance;
		}
		return $data;
	}

}

///END OF FILE////////////