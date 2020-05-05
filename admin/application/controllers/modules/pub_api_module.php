<?php

trait pub_api_module{

	/**
	 * public telephone callback url:
	 *
	 * /async/call_player_tele/{phone number}
	 * /pub/call_player_tele/{phone number}
	 *
	 * @param  string $playerTelephone phone number
	 * @return redirect url
	 */
	public function call_player_tele($playerTelephone = ''){

		try{

			if( empty( $playerTelephone ) ) throw new Exception(lang('Player Telephone missing'));

			$this->utils->debug_log("Player telephone: ".$playerTelephone);

			$error=null;
			$url=$this->utils->call_tele_api($playerTelephone, $error);
			if(empty($url)){

				if(empty($error)){
					$error='Call Telephone System Failed';
				}

				throw new Exception(lang($error));
			}

			redirect($url);

			// $callerId = $this->config->item('default_tele_id');

			// # load the first available teleSystem
			// $apiIds = $this->utils->getAllCurrentTeleSystemList();

			// if( empty( $apiIds ) ) throw new Exception(lang('No any telephone marketing system'));

			// $api = $this->utils->loadExternalSystemLibObject($apiIds[0]);
			// $url = $api->getCallUrl($playerTelephone, $callerId);

			// $this->utils->debug_log("Using url [$url] to make a call");

			// redirect($url);

		}catch(Exception $e){

			$this->utils->error_log("No TeleSystem API configured.", $e);
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $e->getMessage());
			redirect('/'); # redirect to home page

		}

	}

	public function data($usernameFormat = "format_keep_3_char_hide__last_2") {

		$this->load->model('transactions');
		$this->load->model('game_logs');
		$this->load->model('total_player_game_minute');
		
		$end_date = date('Y-m-d 23:59:59');
		$start_date = date('Y-m-d 00:00:00', strtotime('-7 days'));
		$min_amount = 20000000;
		
		$transactions = $this->transactions->getLastTransactions(10);
		$deposits = $this->transactions->getTopDeposits($start_date, $end_date);
		$withdraws = $this->transactions->getTopWithdraws($start_date, $end_date);
		$winners = $this->total_player_game_minute->getWinners($min_amount, $start_date, $end_date);

		array_walk($transactions, function(&$row) use ($usernameFormat) {
			// amount is already formatted like this " ₩ 2,013,213 "
			$row['amount'] = $this->utils->formatCurrencyWithSymNoDecimal($row['amount']);

			//format date  2017/10/05 15:04:17
			$date = date_create($row['date']);

			$row['username'] = $this->usernameDisplayFormat($usernameFormat,$row['username']);

			$row['date'] = $this->utils->formatDatetimeForDisplay($date);

			$row['type'] = $row['type'] == Transactions::DEPOSIT ? 'deposit' : 'withdraw';
		});
		
		array_walk($deposits, function(&$row) {
			$row['username'] = $this->usernameDisplayFormat($usernameFormat,$row['username']);
		});
		
		array_walk($withdraws, function(&$row) {
			$row['username'] = $this->usernameDisplayFormat($usernameFormat,$row['username']);
		});

		array_walk($winners, function(&$row) {
			$row['username'] = $this->usernameDisplayFormat($usernameFormat,$row['username']);
		});

		$data = array(
			'transactions' 	=> $transactions,
			'deposits' 		=> $deposits,
			'withdraws' 	=> $withdraws,
			'winners' 		=> $winners,
		);

		return $this->returnJsonResult($data);

	}

	private function usernameDisplayFormat($usernameFormat, $username) {
		switch ($usernameFormat) {
				case "format_keep_3_char_hide__last_2":// sample display test002 = tes** / format_keep_3_char_hide__last_2
					return substr($username, 0, 3)."**";
					break;
				case "format_hide_last_4_char": // * the last 4 characters of username / test002 = tes**** / format_hide_last_4_char
					return substr($username, 0, (strlen($username) - 4)) . str_repeat("*", strlen(substr($username, -4)));
					break;
				case "format_hide_middle_3_char":// sample display test002 = te***02 / format_hide_middle_3_char
					return substr($username, 0, 2) . '***' . substr($username, -2);
					break;
				default:// sample display test002 = tes**  / format_keep_3_char_hide__last_2
					return substr($username, 0, 3)."**";
					break;
			}
	}

	public function get_all_news($is_jsonp='false') {

		$this->load->model(['player_model']);
		$data=$this->player_model->getAllPubNews('date desc');

		if($is_jsonp){
			return $this->returnJsonpResult($data);
		}else{
			return $this->returnJsonResult($data);
		}
	}

}