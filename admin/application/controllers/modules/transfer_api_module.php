<?php

/**
 *
 * api for transfer balance
 */
trait transfer_api_module{


	/**
	 *
	 * player.xxx/async/call_transfer/98
	 *
	 * @param string $payment_api_id
	 *
	 * @return json
	 */
	public function call_transfer($payment_api_id){

		if(empty($payment_api_id)){
			return $this->returnErrorStatus();
		}

		$success=false;

		//get post
		$fields=$this->getInputGetAndPost();

		$api=$this->utils->loadExternalSystemLibObject($payment_api_id);
		//validate parameters
		//only allow Abstract_payment_api_external_deposit
		$result=$api->validateCallbackParameters($fields);

		if($result['success'] && $result['process_by_api']){

			$fields['processed_info']=$result['processed_info'];
			$playerId=$fields['processed_info'][Abstract_payment_api_external_deposit::FIELD_PLAYER_ID];
			$success = $this->lockAndTransForPlayerBalance($playerId, function ()
				 use ($fields, $api, &$result) {

				$result=$api->callbackFromServer(null, $fields);

				$success=true;
				return $success;
			});

			if(!$success){
				$result['return_result']['success']=$success;
				$result['return_result']['errorNum']=50;
				$result['return_result']['info']='internal error';
			}
		}

		$this->utils->debug_log('input parameters',$fields, 'result', $result);

		if($result['return_type']=='json'){

			$this->returnJsonResult($result['return_result']);

		}else{

			$this->returnText($result['return_result']);

		}

	}

    public function checkGameApiCurrencyRateWithConvertedAmount(){
        $this->load->model(array('wallet_model', 'daily_currency','player_model'));

        $game_with_fixed_currency = $this->config->item('game_with_fixed_currency');

        if (!$this->authentication->isLoggedIn()) {
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('Not Login'), NULL);
        }
        $player_id = $this->authentication->getPlayerId();

        $currency = $this->utils->getCurrentCurrency();

        $transfer_from = $this->input->get_post('transfer_from');
        $transfer_to = $this->input->get_post('transfer_to');
        $amount = $this->input->get_post('amount');

        $subwallet=null;
        $success=$this->wallet_model->lockAndTransForPlayerBalance($player_id, function () use (
            $player_id, &$subwallet) {

            $subwallet = $this->wallet_model->getAllPlayerAccountByPlayerId($player_id);
            return !empty($subwallet);
        });

        $game_from_name = lang("Main Wallet");
        $game_to_name = lang("Main Wallet");
        if($transfer_from == Wallet_model::MAIN_WALLET_ID){
            foreach($subwallet as $wallet){
                if($wallet['typeId'] == $transfer_to){
                    $game_to_name = lang($wallet['game']);
                }
            }
            $game_api = $transfer_to;
        }else{
            $game_api = $transfer_from;

            foreach($subwallet as $wallet){
                if($wallet['typeId'] == $transfer_from){
                    $game_from_name = lang($wallet['game']);
                }
                if($wallet['typeId'] == $transfer_to){
                    $game_to_name = lang($wallet['game']);
                }
            }
        }

        $result = [
            'status' => FALSE,
            'amount' => 0,
            'converted_amount' => 0,
            'rate' => 1,
            'message' => NULL
        ];

        if(!isset($game_with_fixed_currency[$game_api])){
            return $this->returnCommon(BaseController::MESSAGE_TYPE_SUCCESS, NULL, $result);
        }

        $target_currency = $game_with_fixed_currency[$game_api];
        $rate = $this->daily_currency->getCurrentCurrencyRateWithNoGenerate($this->utils->getTodayForMysql(), $currency['currency_code'], $target_currency);

        if(empty($rate)){
            return $this->returnCommon(BaseController::MESSAGE_TYPE_SUCCESS, NULL, $result);
        }

        $converted_amount  = round($amount * $rate, 2);

        if($transfer_from == Wallet_model::MAIN_WALLET_ID) {
            $message = sprintf(lang('currency_modal_confirmation_message_deposit %s %s %s'), $amount, $converted_amount, $game_to_name);
        } else {
            $message = sprintf(lang('currency_modal_confirmation_message_withdraw %s %s %s %s'), $converted_amount, $game_from_name, $amount, $game_to_name);
        }

        $result['status'] = TRUE;
        $result['converted_amount'] = $converted_amount;
        $result['amount'] = round($amount, 2);
        $result['rate'] = $rate;
        $result['message'] = $message;

        return $this->returnCommon(BaseController::MESSAGE_TYPE_SUCCESS, NULL, $result);
    }
}
