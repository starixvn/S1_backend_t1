<?php

/**
 * General behaviors include
 * * generate report logs in every scheduler based from batch_transaction_id from GlobaCom (Pagcor)
 * * Send data to GlobalCom (Pagcor) with the given date range of schedule
 *
 * @category kingrich_send_data_scheduler
 * @version 1.01.01
 * @copyright 2013-2019 TripleOneTech
 */

trait kingrich_send_data_scheduler_generate {

	public function generate_kingrich_send_data_schedule() {
		$response = [];
        
		$this->utils->debug_log('<<====== generate_kingrich_send_data_schedule gathering ongoing_schedule ======>>');
		$this->load->library('kingrich_library');
		$this->load->model(array('game_logs','kingrich_send_data_scheduler','kingrich_api_logs','kingrich_scheduler_logs'));
		$ongoing_schedule = $this->kingrich_send_data_scheduler->getOngoingSchedule();
		$this->utils->info_log('<<====== generate_kingrich_send_data_schedule ongoing_schedule ======>>', $ongoing_schedule);

		if (!empty($ongoing_schedule)) {
			foreach ($ongoing_schedule as $key => $value) {
				$get_gamelogs_response = $this->get_gamelogs($value);
				if (!empty($get_gamelogs_response)) {
						$response = $this->data_sendout($value['scheduler_id'], $get_gamelogs_response);
				} else {
					$this->set_scheduler_done($value['scheduler_id']);
					$response = [
						'status'	=> lang('Success') ,
						'msg'		=> "Done data to send for this schedule." ,
					];
				}
			}
		}

		return $response;
	}

	public function get_test_player_id() {
		$this->utils->debug_log('<<====== generate_kingrich_send_data_schedule gathering get_test_player_id ======>>');
		$player_tag_test_account = $this->utils->getConfig('player_tag_test_account');

		if ( ! is_array($player_tag_test_account)) {
			$player_tag_test_account = [$player_tag_test_account];
		}

		foreach ($player_tag_test_account as &$tag) {
			$tag = "'{$tag}'";
		}

		$sql = "SELECT DISTINCT p.playerId FROM playertag AS pt JOIN tag AS t ON t.tagId = pt.tagId JOIN player AS p ON p.playerId = pt.playerId WHERE p.deleted_at IS NULL AND t.tagName IN (" . implode(',', $player_tag_test_account) . ")";
		$query = $this->db->query($sql);
		$rows = $query->result_array();

		$this->utils->info_log('<<====== generate_kingrich_send_data_schedule get_test_player_id response ======>>', $rows);
		return array_column($rows, 'playerId');
	}

	public function get_gamelogs($value = null){
		$response = [];
		if(!empty($value)){
			$this->utils->debug_log('<<====== generate_kingrich_send_data_schedule gathering get_gamelogs ======>>');
				
			$this->db->select("pl.username as player_name, pl.playerId as player_id, gl.end_at as transaction_date, gl.external_uniqueid as transaction_id, gl.end_at as settlement_date, pl.currency as player_currency, gd.game_code as game_code, gl.flag as flag, gd.game_name as game_name, gl.game_platform_id as game_platform_id, gt.game_tag_id as game_tag_id, gl.trans_amount as real_bet_amount, IF(gl.win_amount > 0, gl.win_amount + gl.trans_amount, 0) as credit_amount, gl.trans_amount - ( IF(gl.win_amount > 0, gl.win_amount + gl.trans_amount, 0) ) as net_amount",FALSE);

		    $this->db->from('game_logs gl');
		    $this->db->join('player pl', 'pl.playerId = gl.player_id', 'left');
		    $this->db->join('game_description gd', 'gd.id = gl.game_description_id', 'left');
		    $this->db->join('game_type gt', 'gt.id = gd.game_type_id', 'left');
		    $this->db->where('gl.end_at <= ', $value['date_to']);
		    $this->db->where('gl.end_at >= ', $value['date_from']);
		    $this->db->where('gl.flag', Game_logs::FLAG_GAME);
		    $this->db->where('gl.trans_amount >=', Kingrich_library::MIN_DEBIT_AMOUNT);
		    $this->db->where('(gl.win_amount + gl.trans_amount) >=', Kingrich_library::MIN_CREDIT_AMOUNT);
		    $this->db->where('gl.room IS NULL');
		    $this->db->limit(Kingrich_library::LIMIT);
		    $get_test_player_id = $this->get_test_player_id();

		    if(!empty($get_test_player_id)) {
		    	$this->db->where("pl.playerId NOT IN (" . implode(',', $get_test_player_id) . ")");
		    }
		
		    if( !empty($value['currency']) && $this->utils->getConfig('multiple_currency_enabled') ) {
				$this->db->where("pl.currency",$value['currency']);
		    }

			$response = $this->runMultipleRowArray();

			$this->utils->info_log('<<====== generate_kingrich_send_data_schedule get_gamelogs response ======>>', $response);
		}
		
		return $response;
	}

	public function data_sendout($scheduler_id, $values = null) {
		$response = [];
		if (!empty($values)) {
			$this->utils->debug_log('<<====== generate_kingrich_send_data_schedule data_sendout ======>>', $values);
			
			$kingrich_currency_branding = $this->config->item('kingrich_currency_branding');
			$brand_name = $this->utils->getConfig('brand_name');
			$brand_url = $this->utils->getConfig('brand_url');

			$game_transactions = array_map( function($value) use ($kingrich_currency_branding ,$brand_name ,$brand_url) {

						$bet_type = lang('N/A',1);

						if($this->kingrich_library->get_game_type($value['game_tag_id'],$value['game_platform_id']) == Kingrich_library::GAME_TYPE_SPORTSBOOK){
								if ($value['real_bet_amount'] > 0 ) {
									$bet_type = lang("Cash",1);
								} else if ($value['real_bet_amount'] <= 0) {
									$bet_type = lang("Credit",1);
								} else {
									$bet_type = lang("Credit",1);
								}
							}
						return array(
							'player_name' 		=> $value['player_name'],
							'player_no' 		=> $value['player_id'],
							'player_type' 		=> Kingrich_library::PLAYER_TYPE_REAL,
							'transaction_date' 	=> date('Y-m-d\TH:i:s.000', strtotime($value['transaction_date'])),
							'transaction_id' 	=> $value['transaction_id'],
							'settlement_date' 	=> date('Y-m-d\TH:i:s.000', strtotime($value['settlement_date'])),
							'brand' 			=> isset($kingrich_currency_branding[$value['player_currency']]['brand_name'])? $kingrich_currency_branding[$value['player_currency']]['brand_name'] :$brand_name,
							'brand_url' 		=> isset($kingrich_currency_branding[$value['player_currency']]['brand_url'])? $kingrich_currency_branding[$value['player_currency']]['brand_url'] :$brand_url,
							'game_code' 		=> $value['game_code'],
							'game_name' 		=> ($value['flag'] == Game_logs::FLAG_GAME)?lang($value['game_name'],1) : lang('N/A',1),
							'game_type' 		=> ( $this->kingrich_library->get_game_type($value['game_tag_id'],$value['game_platform_id']) ) ? : lang('N/A',1),
							'currency_code' 	=> $value['player_currency'],
							'bet_type' 			=> $bet_type,
							'bet_amount' 		=> floatval($value['real_bet_amount']),
							'debit_amount' 		=> floatval($value['real_bet_amount']),
							'credit_amount' 	=> floatval($value['credit_amount']),
							'net_amount' 		=> floatval($value['net_amount']),
						);

					}, $values);

			$this->utils->info_log('<<====== generate_kingrich_send_data_schedule data_sendout data info count======>>', count($game_transactions));
			$this->utils->info_log('<<====== generate_kingrich_send_data_schedule data_sendout data info ======>>', var_dump($game_transactions));

			$this->utils->debug_log('<<====== generate_kingrich_send_data_schedule submit_game_records  ======>>');

			$submit_result = $this->kingrich_library->submit_game_records($game_transactions);

			$this->utils->info_log('<<====== generate_kingrich_send_data_schedule submit_game_records response ======>>', $submit_result);

			if(!empty($submit_result)){
				if(isset($submit_result['batch_transaction_id'])){
					$submit_result_data = array(
						'batch_transaction_id' => $submit_result['batch_transaction_id'],
						'api_created_date' => $submit_result['created_date'],
						'created_at' => $this->utils->getNowForMysql(),
						'status' => $submit_result['status'],
					);

					$game_logs_update = array();
					foreach ($game_transactions as $key => $value) {
						array_push($game_logs_update,
							array(
								'external_uniqueid' => $value['transaction_id'],
								'room'  => $submit_result['batch_transaction_id'],
							)
						);
					}
					
					$this->db->update_batch("game_logs", $game_logs_update, 'external_uniqueid');

					$this->kingrich_api_logs->insertRecord($submit_result_data);
					$this->utils->info_log('<<====== generate_kingrich_send_data_schedule kingrich_api_logs insertRecord  ======>>',  $submit_result_data);

					$scheduler_logs_data = array(
						'batch_transaction_id' => $submit_result['batch_transaction_id'],
						'scheduler_id' => $scheduler_id,
						'total' => count($game_transactions),
						'created_at' => $this->utils->getNowForMysql(),
					);

					$this->kingrich_scheduler_logs->insertRecord($scheduler_logs_data);
					$this->utils->info_log('<<====== generate_kingrich_send_data_schedule kingrich_scheduler_logs insertRecord ======>>',  $scheduler_logs_data);
				}
				$response = $submit_result;
			}
		}
		return $response;
	}

	public function set_scheduler_done($scheduler_id){
		$this->load->model(array('kingrich_send_data_scheduler'));

		$data = [
			'id'			=> $scheduler_id ,
			'status'		=> self::DONE ,
		];

		$response = $this->kingrich_send_data_scheduler->updateStatus($data);
	}
}
