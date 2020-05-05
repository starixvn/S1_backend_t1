<?php

/**
 * Class onetime_command_module
 *
 * General behaviors include :
 *
 * * only one-time task
 *
 *
 * @category Command Line
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait onetime_command_module {

	/**
	 * overview : game adder
	 * @param date $start
	 * @param date $end
	 */
	public function game_adder($start = null, $end = null) {

		$this->utils->markProfilerStart('game_adder');

		$this->load->model('game_logs');
		$this->load->model('game_type_model');
		$this->load->model('game_description_model');

		$this->db->trans_start(); # ========================================================================================

		$unknown_games = $this->find_unknown_in_game_logs($start, $end);

		foreach ($unknown_games as &$unknown_game) {

			$unknown_game->game_code = str_replace(' ', '', strtolower($unknown_game->external_game_id));
			$unknown_game->game_name = strtolower($unknown_game->system_code . '.' . str_replace(' ', '', $unknown_game->external_game_id));
			$unknown_game->game_type_lang = strtolower($unknown_game->system_code . '_' . str_replace(' ', '_', $unknown_game->type));

			# CREATE GAME TYPE IF NO GAME TYPE ID RETURNED
			$unknown_game->game_type_id = $this->game_type_model->getGameTypeId($unknown_game->type, $unknown_game->game_platform_id);
			if (!$unknown_game->game_type_id) {
				$unknown_game->game_type_id = $this->game_type_model->create(array(
					'game_platform_id' => $unknown_game->game_platform_id,
					'game_type' => $unknown_game->type,
					'game_type_lang' => $unknown_game->game_type_lang,
					'note' => 'SYSTEM GENERATED GAME TYPE',
				));
			}

			# CREATE GAME DESCRIPTION IF NO GAME DESCRIPTION ID RETURNED
			$unknown_game->game_description_id = $this->game_description_model->getGameDescriptionId($unknown_game->game_code);
			if (!$unknown_game->game_description_id) {

				$new_game = array(
					'game_platform_id' => $unknown_game->game_platform_id,
					'external_game_id' => $unknown_game->external_game_id,
					'english_name' => $unknown_game->english_name,
					'clientid' => $unknown_game->clientid,
					'moduleid' => $unknown_game->moduleid,
					'game_type_id' => $unknown_game->game_type_id,
					'game_code' => $unknown_game->game_code,
					'game_name' => $unknown_game->game_name,
					'note' => 'SYSTEM GENERATED GAME DESCRIPTION',
				);

				$this->db->insert('game_description', $new_game);

				$game_description_id = $this->db->insert_id();
				$unknown_game->game_description_id = $game_description_id;
				$new_game['id'] = $game_description_id;
				$new_game_list[] = $new_game;

			} else {

				# UPDATE GAME DESCRIPTION's GAME TYPE ID IF GAME TYPE IS NOT THE SAME
				$game_type_id = $this->game_description_model->getGameTypeIdByGameCode($unknown_game->game_code);
				if ($game_type_id != $unknown_game->game_type_id) {
					$this->game_description_model->update($unknown_game->game_description_id, array(
						'game_type_id' => $unknown_game->game_type_id,
					));
				}

			}

			# UPDATE GAME_LOG
			$this->game_logs->update($unknown_game->id, array(
				'game_platform_id' => $unknown_game->game_platform_id,
				'game_type_id' => $unknown_game->game_type_id,
				'game_description_id' => $unknown_game->game_description_id,
				'game' => $unknown_game->english_name,
				'game_type' => $unknown_game->type,
				'game_code' => $unknown_game->game_code,
				'note' => 'SYSTEM UPDATED GAME LOGS',
			));

		}

		$this->db->trans_complete(); # =====================================================================================

		if ($new_game_list) {
			$this->load->library('email_setting');
			$this->email_setting->sendEmail($this->config->item('email_new_games'), array(
				'subject' => 'New Game Description' . $this->utils->getNowForMysql(),
				'body' => var_export($new_game_list, true),
			));
		}

		$this->utils->markProfilerEnd('game_adder');
		$this->utils->printProfilerLog('game_adder');

		echo "done";
	}

	/**
	 * overview : find unknown in game logs
	 *
	 * @param date $start
	 * @param date $end
	 * @return array
	 */
	private function find_unknown_in_game_logs($start = null, $end = null) {

		$this->benchmark->mark('start');
		$this->db->select('game_logs.id');
		$this->db->select('game_logs.game_platform_id');
		$this->db->select('external_system.system_code');
		$this->db->from('game_logs');
		$this->db->join('game_type', 'game_type.id = game_logs.game_type_id', 'left');
		$this->db->join('game_description', 'game_description.id = game_logs.game_description_id', 'left');
		$this->db->join('external_system', 'external_system.id = game_logs.game_platform_id AND external_system.system_type = 1', 'left');
		$this->db->where('game_logs.external_uniqueid IS NOT NULL', null, false);
		if (isset($start, $end)) {
			$this->db->where('(game_logs.end_at =>', $start);
			$this->db->where('(game_logs.end_at =<', $end);
		}
		$this->db->where('(game_description.game_code', 'unknown');
		$this->db->where('game_type.game_type', 'unknown');
		$this->db->or_where('game_description.id IS NULL)', null, false);

		$this->find_row_in_origin();
		$this->guess_game_info();

		$query = $this->db->get();
		$this->benchmark->mark('end');

		$this->utils->debug_log($this->benchmark->elapsed_time('start', 'end'));

		return $query->result();
	}

	/**
	 * overview : find row in origin
	 */
	private function find_row_in_origin() {
		$this->load->model('external_system');
		$platforms = $this->external_system->getAllSytemGameApi();

		# find_row_in_origin
		foreach ($platforms as $platform) {
			$table = strtolower($platform['system_code']) . '_game_logs';
			$game_platform_id = $platform['id'];
			$this->db->join($table, "game_logs.game_platform_id = {$game_platform_id} AND {$table}.external_uniqueid = game_logs.external_uniqueid", 'left');
		}
	}

	/**
	 * overview : guess game information
	 */
	private function guess_game_info() {
		$this->load->model('external_system');
		$platforms = $this->external_system->getAllSytemGameApi();

		# guess_game_info - external_game_id
		$select_code = '(CASE ';
		foreach ($platforms as $platform) {
			$table = strtolower($platform['system_code']) . '_game_logs';
			$game_platform_id = $platform['id'];
			if ($game_platform_id == 1) {
				$select_code .= "WHEN game_logs.game_platform_id = {$game_platform_id} THEN {$table}.gameshortcode ";
			} else if ($game_platform_id == 6) {
				$select_code .= "WHEN game_logs.game_platform_id = {$game_platform_id} THEN {$table}.display_name ";
			}
		}
		$select_code .= ' ELSE null END) external_game_id';
		$this->db->select($select_code, null, false);

		# guess_game_info - english_name
		$select_name = '(CASE ';
		foreach ($platforms as $platform) {
			$table = strtolower($platform['system_code']) . '_game_logs';
			$game_platform_id = $platform['id'];
			if ($game_platform_id == 1) {
				# PT
				$select_name .= "WHEN game_logs.game_platform_id = {$game_platform_id} THEN {$table}.gamename ";
			} else if ($game_platform_id == 6) {
				# MG
				$select_name .= "WHEN game_logs.game_platform_id = {$game_platform_id} THEN {$table}.display_name ";
			}
		}
		$select_name .= ' ELSE null END) english_name';
		$this->db->select($select_name, null, false);

		# guess_game_info - type
		$select_type = '(CASE ';
		foreach ($platforms as $platform) {
			$table = strtolower($platform['system_code']) . '_game_logs';
			$game_platform_id = $platform['id'];
			if ($game_platform_id == 1) {
				# PT
				$select_type .= "WHEN game_logs.game_platform_id = {$game_platform_id} THEN {$table}.gametype ";
			} else if ($game_platform_id == 6) {
				# MG
				$select_type .= "WHEN game_logs.game_platform_id = {$game_platform_id} THEN {$table}.display_game_category ";
			}
		}
		$select_type .= ' ELSE null END) type';
		$this->db->select($select_type, null, false);

		# guess_game_info - clientid
		$select_client = '(CASE ';
		foreach ($platforms as $platform) {
			$table = strtolower($platform['system_code']) . '_game_logs';
			$game_platform_id = $platform['id'];
			if ($game_platform_id == 6) {
				# MG
				$select_client .= "WHEN game_logs.game_platform_id = {$game_platform_id} THEN {$table}.client_id ";
			}
		}
		$select_client .= ' ELSE null END) clientid';
		$this->db->select($select_client, null, false);

		# guess_game_info - moduleid
		$select_module = '(CASE ';
		foreach ($platforms as $platform) {
			$table = strtolower($platform['system_code']) . '_game_logs';
			$game_platform_id = $platform['id'];
			if ($game_platform_id == 6) {
				# MG
				$select_module .= "WHEN game_logs.game_platform_id = {$game_platform_id} THEN {$table}.module_id ";
			}
		}
		$select_module .= ' ELSE null END) moduleid';
		$this->db->select($select_module, null, false);

	}

	/**
	 * overview : get all pt balance
	 *
	 * @param string $username
	 */
	// public function query_pt_balance($username) {
	// 	$this->load->model(array('game_provider_auth', 'player_model'));
	// 	$api = $this->loadApi(PT_API);
	// 	$playerId = $this->player_model->getPlayerIdByUsername($username);
	// 	if (!empty($playerId)) {
	// 		$isRegisteredFlag = $this->game_provider_auth->isRegisterd($playerId, PT_API);
	// 		if ($isRegisteredFlag) {
	// 			$rlt = $api->queryPlayerBalance($username);
	// 			$this->utils->debug_log('rlt', $rlt);
	// 			$this->output->append_output(var_export($rlt, true) . "\n");
	// 		}

	// 	}
	// }

	/**
	 * overview : fix merge mg game logs
	 *
	 * @param string $fromStr
	 * @param string $toStr
	 * @param string $playerName
	 */
	public function fix_merge_mg_game_logs($fromStr, $toStr, $playerName = null) {
		$rlt = array('success' => false);
		$api = $this->loadApi(MG_API);
		if ($api) {
			$token = random_string('unique');
			//set from to
			$dateTimeFrom = null;
			$dateTimeTo = null;
			if (!empty($fromStr)) {
				$dateTimeFrom = new \DateTime($fromStr);
			}
			if (!empty($toStr)) {
				$dateTimeTo = new \DateTime($toStr);
			}
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName);

			$rlt = $api->syncMergeToGameLogs($token);
		}

		$this->output->append_output(var_export($rlt, true) . "\n");
	}

	/**
	 * overview : show affiliate password
	 *
	 * @param string $username
	 */
	public function show_aff_password($username) {
		$this->db->from('affiliates')->where('username', $username);
		$qry = $this->db->get();
		$row = $qry->row();
		$password = '';
		if ($row) {
			$this->load->library(array('salt'));
			$password = $row->password;
			$password = $this->salt->decrypt($password, $this->getDeskeyOG());
		}

		$this->output->append_output("password:" . $password . "\n");
	}

	/**
	 * overview : change auto increment
	 *
	 * @param int $number
	 */
	public function change_auto_increment($number = 16877) {
		$tables = [
			'sale_orders',
			'transactions',
			'player',
			'adminusers',
			'affiliates',
			'promorules',
			'promocmssetting',
			'vipsetting',
			'walletaccount',
			'playeraccount',
		];

		foreach ($tables as $table) {
			$this->db->query("ALTER TABLE {$table} AUTO_INCREMENT {$number}");
		}
		$msg = $this->utils->debug_log($tables, 'change to ', $number);
		$this->returnText($msg);
	}

	/**
	 * overview : show
	 * @param $username
	 */
	public function show_player_password($username) {
		$this->db->from('player')->where('username', $username);
		$qry = $this->db->get();
		$row = $qry->row();
		$password = '';
		if ($row) {
			$this->load->library(array('salt'));
			$password = $row->password;
			$password = $this->salt->decrypt($password, $this->getDeskeyOG());
		}

		$this->utils->debug_log("password:" . $password . "\n");
	}

	/**
	 * overview : sync IPS to transaction
	 */
// 	public function sync_ips_to_trans() {
// 		$this->load->library(array('authentication'));
// 		$this->load->model(array('sale_order'));
// 		$sql = <<<EOD
// select * from sale_orders where external_order_id like 'NT%' and transaction_id is null and status=5
// EOD;

// 		$qry = $this->db->query($sql);
// 		foreach ($qry->result() as $row) {
// 			$orderId = $row->id;
// 			$this->utils->debug_log('update ', $orderId, 'to processing', Sale_order::STATUS_PROCESSING);
// 			$this->db->where('id', $orderId)->set('status', Sale_order::STATUS_PROCESSING)
// 				->update('sale_orders');
// 			$rlt = $this->sale_order->approveSaleOrder($orderId);
// 			$this->utils->debug_log('approveSaleOrder', $orderId, 'result', $rlt);
// 		}

// 	}


	/**
	 * overview : sync game table
	 */
	public function sync_game_table() {
		//sync 'game' table
		//delete game
		//insert from external_system
		$this->load->model(array('external_system'));
		$this->external_system->startTrans();
		$this->external_system->syncGameTable();
		$rlt = $this->external_system->endTransWithSucc();
		$msg = $this->utils->debug_log('sync game table result', $rlt);

		$this->returnText($msg);
	}

	public function test_send_message($anytext){

		$ignore=<<<EOD
{
    "from": "2016-11-18 13:51:14",
    "to": "2016-11-18 14:21:14",
    "result": {
        "success": true,
        "sync_result": {
            "syncOriginalGameLogs": {
                "20": {
                    "success": true,
                    "response_result_id": 7401326
                },
                "22": {
                    "success": true,
                    "count": 24,
                    "sum": 83.7,
                    "real_count": 1074
                },
                "29": {
                    "success": true
                },
                "53": {
                    "success": true
                },
                "66": {
                    "success": true
                },
                "69": {
                    "success": true
                },
                "70": true,
                "71": {
                    "success": false,
                    "lastRowId": null
                },
                "72": {
                    "success": true
                },
                "73": null,
                "84": {
                    "success": false,
                    "response_result_id": 7401673
                },
                "85": {
                    "success": true,
                    "response_result_id": 7401675
                }
            },
            "syncMergeToGameLogs": {
                "20": {
                    "success": true,
                    "count": 0
                },
                "22": {
                    "success": true
                },
                "29": {
                    "success": true
                },
                "53": {
                    "success": true
                },
                "66": {
                    "success": true
                },
                "69": null,
                "70": {
                    "success": false
                },
                "71": {
                    "success": true
                },
                "72": {
                    "success": true
                },
                "73": {
                    "success": false
                },
                "84": {
                    "success": true,
                    "count": 0
                },
                "85": {
                    "success": true
                }
            }
        }
    }
}
EOD;

		$json=json_decode( $anytext, true );

		$this->utils->debug_log('json', $json);

		$this->utils->sendMessageService($json );

	}

	public function add_ezugi_gamelogs(){

		$this->load->dbforge();

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'PlayerId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'Username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'BetTypeID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'ezugiID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'ezugiID4' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'RoundID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'ServerID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'TableID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'UID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'UID2' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'OperatorID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'OperatorID2' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'SessionCurrency' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'SkinID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'BetSequenceID' => array(
				'type' => 'VARCHAR',

				'constraint' => '11',
				'null' => true,
			),
			'Bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'Win' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'Bankroll' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'GameString' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'GameString2' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'SeatID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'BetStatusID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'BrandID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'RoundDateTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'ActionID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'BetType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'PlatformID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'DateInserted' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'GameTypeID' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'BFTransactionFound' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'GameTypeName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'DealerID' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'ErrorCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'originalErrorCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'TransactionID' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			)
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('ezugi_game_logs');
		$this->load->model(['player_model']);
		$this->player_model->addIndex('ezugi_game_logs', 'idx_UID', 'UID');
		$this->player_model->addIndex('ezugi_game_logs', 'idx_RoundDateTime', 'RoundDateTime');
		$this->player_model->addIndex('ezugi_game_logs', 'idx_GameTypeName', 'GameTypeName');

	}

	public function batch_adjust_cashback_flag($onlyPrintLog='false'){

		$onlyPrintLog= $onlyPrintLog == 'true';

		$this->load->model(['player_promo', 'player_model', 'transactions']);

		$rows=$this->player_promo->getDisableCashbackPlayers();

		if(empty($rows)){
			$now=$this->utils->getNowForMysql();
			foreach ($rows as $row) {
				$playerId=$row['playerId'];
				$startDate=$row['dateProcessed'];

				$this->utils->debug_log('query deposit/withdraw :'.$playerId, $startDate, $now);

				list($totalDeposit, $totalWithdrawal, $totalBonus, $totalCashback)=
					$this->transactions->getTotalDepositWithdrawalBonusCashbackByPlayers(
						$playerId, $startDate, $now);

				if($totalDeposit<=0 && $totalWithdrawal<=0){
					$rlt=false;
					$this->utils->debug_log('disableCashbackByPlayerId player:'.$playerId);
					if(!$onlyPrintLog){
						$rlt=$this->player_model->disableCashbackByPlayerId($playerId);
					}

					$this->utils->debug_log('disableCashbackByPlayerId', $rlt);
				}
			}
		}
	}

    public function reset_0_game_shares_on_aff(){
    	//load all affiliate , if game shares is 0, set it empty
    	$this->load->model(['affiliatemodel']);
    	$this->affiliatemodel->reset_game_shares();

    }

 //    public function batch_delete_aff_for_kgvipcn($dry_run='true'){

	// 	$dry_run=$dry_run=='true';
	// 	$lastLoginDate='2017-10-30 23:59:59';

	// 	$this->utils->debug_log('batch_delete_aff_for_kgvipcn dry run', $dry_run);

	// 	$this->utils->loopRawRows(false, function() use ($lastLoginDate){

	// 		$this->db->select('affiliateId, username, lastLogin')->from('affiliates')->where('lastLogin <=', $lastLoginDate)
	// 			->where('deleted_at is null', null, false);

	// 		return null;

	// 	}, function($row) use($dry_run){

	// 		$success=false;
	// 		$id=$row['affiliateId'];
	// 		$sql="update affiliates set deleted_at='".$this->utils->getNowForMysql()."' where affiliateId=".$id;

	// 		if(!$dry_run){
	// 			//update deleted_at
	// 			$success=$this->utils->executeSqlOnRaw($sql);

	// 			$this->utils->debug_log($sql, $success);
	// 		}else{
	// 			$this->utils->debug_log('dry run', $sql);
	// 			$success=true;
	// 		}

	// 		return $success;

	// 	});

	// }

	public function show_player_password_history($username) {
		$this->db->select('player_password_history.*')->from('player_password_history')
			->join('player', 'player.playerId=player_password_history.player_id')
		    ->where('player.username', $username);
		$qry = $this->db->get();
		$rows = $qry->result_array();
		// $password = '';
		if (!empty($rows)) {
			$this->load->library(array('salt'));
			foreach ($rows as $row) {
				$current_password = $this->salt->decrypt($row['current_password'], $this->getDeskeyOG());
				$new_password = $this->salt->decrypt($row['new_password'], $this->getDeskeyOG());
				$this->utils->debug_log($row['updated_at']." change password: " . $current_password . " to ".$new_password);
			}

		}else{

			$this->utils->debug_log("not found " . $username);

		}

	}

	public function query_api_balance($username, $apiId) {
		$this->load->model(array('game_provider_auth', 'player_model'));
		$api = $this->loadApi($apiId);
		$playerId = $this->player_model->getPlayerIdByUsername($username);
		if (!empty($playerId)) {
			// $isRegisteredFlag = $this->game_provider_auth->isRegisterd($playerId, PT_API);
			// if ($isRegisteredFlag) {
				$rlt = $api->queryPlayerBalance($username);
				$this->utils->debug_log('rlt of '.$username.' in '.$apiId, $rlt);
			// }
		}
	}


	public function updateTransactionSecureIdsPerDay($fromdate=null,$todate=null){
		$begin = new DateTime($fromdate);
		$end   = new DateTime($todate);
		$this->load->model(array('transactions', 'sale_order','wallet_model'));
		$transactionTypes = [
			transactions::DEPOSIT,
			transactions::WITHDRAWAL,
			transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET,
			transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET
		];
		$fields = ['id','transaction_type','external_transaction_id'];
		if(!empty($fromdate) && !empty($todate) ){
			for($i = $begin; $i <= $end; $i->modify('+1 day')){
				$day = $i->format("Y-m-d");
				$transactions = $this->transactions->getTransactionsByTransactionTypesAndDay($transactionTypes,$fields,$day);
				foreach ($transactions as  $transaction) {
					$secure_request_id = null;
					switch ((int)$transaction['transaction_type']) {
						case transactions::DEPOSIT:
						if(isset($transaction['id'])){
							$secure_request_id= $this->sale_order->getSecureIdByTransactionId($transaction['id']);
						}
						break;
						case transactions::WITHDRAWAL:
						if(isset($transaction['id'])){
							$secure_request_id = $this->wallet_model->getTransactionCodeByTransactionId($transaction['id']);
						}
						break;
						case transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET:
						case transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET:
						if(isset($transaction['external_transaction_id'])){
							$secure_request_id = $this->wallet_model->getSecureIdByExternalTransactionId($transaction['external_transaction_id']);
						}
						break;
						default:
							# code...
						break;
					}
					$this->transactions->updateRequestSecureId($transaction['id'], $secure_request_id);
					$this->utils->debug_log('updateRequestSecureId','day', $day, 'secure_id', $secure_request_id, 'transaction_type', $transaction['transaction_type'],'transaction_id', $transaction['id']);
				}
			}
		}
	}

	public function reset_player_password_without_game($playerId, $newPassword){
		$this->load->model(array('player_model'));
		$this->load->library('player_manager');
		$player = $this->player_manager->getPlayerById($playerId);
		$data = array(
			'password' => $newPassword,
		);
		// save player password history
		$this->player_model->insertPasswordHistory($playerId, Player_model::RESET_PASSWORD, $this->utils->encodePassword($newPassword));
		$this->player_manager->resetPassword($data, $playerId);
		$this->utils->debug_log('rlt of player:'.$player['username'].' and new password: '.$newPassword);
	}

    public function decode_vr($merchant_key) {
    	// $merchant_key='';
    	$base64_data="S9lE+agYCALfvvlIHh2CrLOpS+0SbuWTf5f850WwK0zqA1rHNn62rk2lIuCJjfx6JGyv4XNnh4LFFKeO8GX5WZEu28Ib1/IVYrjhXT2uK/fBPWjXTjFtborQqkpI117Evn4Il0iowaXqLV0VnRg5kIkJvgPkig6z+MYxc8v5cLeOYxllLv5AqtrX4YG0OkwdZ2ooVwa7ORruBjOyLqp5uw==";
        $data = base64_decode($base64_data);

        //Decrypt data
        $plain_data = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $merchant_key, $data, MCRYPT_MODE_ECB);

        //Remove US-ASCII control character
        $plain_data = trim($plain_data, "\x00..\x1F");

        $this->utils->debug_log($plain_data);
    }

	public function batch_sub_balance($balance_csv_file, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){

		$this->load->model(['player_model', 'transactions']);

		$debug_info=[
			'ignore_first_row'=>$ignore_first_row=='true',
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];
		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>$ignore_first_row=='true'];

		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failed_username'=>[], // username=>failed message
			'username'=>[],
		];

		$balance_csv_file='/home/vagrant/Code/'.$balance_csv_file;

		$controller=$this;
		$cnt=0;
		$message=null;
		$result_info['success']=$this->utils->loopCSV($balance_csv_file, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false)
			use($controller, $config, &$result_info){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$this->utils->debug_log('processing', $tmpData);
			}

			$next=true;

			$username=$tmpData[0];
			$minus_amount=round(doubleval($tmpData[1]), 2);

			if(empty($username) || $minus_amount<=0){

				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$username;
				return $next;
			}

			$playerId=$this->player_model->getPlayerIdByUsername($username);

			if(!empty($playerId)){
				if (!@$debug_info['dry_run']) {

					$success=$controller->lockAndTransForPlayerBalance($playerId, function()
							use($minus_amount, $playerId, $controller, $username){

						$action_name='Minus balance';
						$wallet_name='';
						$adminUserId=1;
						$adjustment_type=Transactions::MANUAL_SUBTRACT_BALANCE;
						$note = sprintf('%s <b>%s</b> balance to <b>%s</b>',
									$action_name, number_format($minus_amount, 2), $username);

						$success=$controller->transactions->createAdjustmentTransaction($adjustment_type, $adminUserId, $playerId,
							$minus_amount, null, $note);

						return $success;
					});

				}else{

					$this->utils->debug_log('dry run...');
					$success=true;
				}
			}else{

				$success=false;

			}

			$result_info['cnt']++;
			if($success){

				$result_info['successCnt']++;
				// $result_info['username'][]=$username;
			}else{
				$result_info['failedCnt']++;
				$result_info['failed_username'][]=['username'=>$username, 'amount'=>$minus_amount];
			}

			return $next;

		});

		$this->utils->debug_log('minus balance',$result_info);

	}


	public function regenerate_gamelogs_transaction($game_platform_id='NULL', $fromdate='NULL', $todate='NULL', $step='1', $dry_run='false'){
		$fromdate = ($fromdate == 'NULL') ? null : $fromdate;
		$todate = ($todate == 'NULL') ? null : $todate;
		$game_platform_id = ($game_platform_id == 'NULL') ? null : $game_platform_id;
		if(empty($fromdate) || empty($todate) || empty($game_platform_id)){
			$this->utils->debug_log('Please provide some date!');
			return;
		}
		$dry_run=$dry_run=='true';
		$this->load->model(array('game_provider_auth','transactions','game_logs'));
		$transactions_model = $this->transactions;
		$game_logs_model = $this->game_logs;
		$game_provider_auth_model = $this->game_provider_auth;
		$step = '+'.$step.' hours';

		$this->utils->loopDateTimeStartEnd($fromdate, $todate, $step, function($from, $to, $step)
			use($game_platform_id, $transactions_model, $game_logs_model,$game_provider_auth_model, $dry_run){

				$from_str = $this->utils->formatDateTimeForMysql($from);
				$to_str = $this->utils->formatDateTimeForMysql($to);
				$rows = $transactions_model->getGamelogsTransactions($game_platform_id,$from_str,$to_str);
				$this->utils->debug_log('Found gamelogs trans :', count($rows), 'from', $from_str,'to', $to_str);
				if(!empty($rows)){
					foreach ($rows as $row) {
						$row->response_result_id = empty($response_result_id) ? '' : $response_result_id;
						$row->username = $game_provider_auth_model->getGameUsernameByPlayerId($row->player_id, $game_platform_id);
						$row->note= 'player ' . $row->player_id . ' ' . $row->username . ' change balance to ' . $row->after_balance . ' amount:' . $row->trans_amount . ' type:' . $row->trans_type ;
						$response_result_id = $transactions_model->getResponseResultIdBySecureId($row->request_secure_id);
						if(!empty($response_result_id )){
							if(!$game_logs_model->isResponseResultIdIfExist($response_result_id)){
	 			 	           //insert
								if(!$dry_run){
									//unset($row['request_secure_id']);
									$game_logs_model->insertGameTransaction( $game_platform_id, $row->player_id, $row->username, $row->after_balance, $row->trans_amount, $response_result_id, $row->trans_type, $row->start_at);
									$this->utils->debug_log('gamelogs trans inserted:',@$row);
								}else{
									$this->utils->debug_log('dry run only: ',@$row);
								}
							}
						}else{
							$this->utils->debug_log('response_result_id not found:  request_secure_id: ',@$row->request_secure_id, 'row', $row);
						}
					}
				}
				return true;
			});
	}

    public function regenerate_affiliate_static_report($aff_userName='NULL', $firstDate='NULL', $secondDate='NULL', $dry_run='false'){
        $this->load->model('affiliate_statistics_model');

        $firstDate = (strtoupper($firstDate) == 'NULL') ? null : $firstDate;
        $secondDate = (strtoupper($secondDate) == 'NULL') ? null : $secondDate;
        $aff_userName = (strtoupper($aff_userName) == 'NULL') ? null : $aff_userName;

        if(empty($firstDate) && empty($secondDate)){
            return $this->utils->debug_log('Please provide some date!');
        }

        $next_day = date("Y-m-d", time() + 86400);
        $dateTo = $next_day;
        if($firstDate == 'this_month'){
            $dateFrom = date('Y-m').'-01';
        }elseif($firstDate == 'last_month'){
            $dateFrom = date('Y-m', strtotime('-1 month')).'-01';
            $dateTo = date("Y-m".'-01');
        }elseif($firstDate == 'today'){
            $dateFrom = date('Y-m-d');
        }else if($firstDate=='yesterday'){
            $dateFrom = date('Y-m-d', strtotime('yesterday'));
            $dateTo = date('Y-m-d');
        }else{
            $dateFrom = $firstDate;
            $dateTo = date('Y-m-d', strtotime($secondDate.'+1 day'));
        }


        if(strtotime($dateFrom) <= strtotime($dateTo)){
            $date_start = new DateTime($dateFrom);
            $date_end = new DateTime($dateTo);

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($date_start, $interval, $date_end);

            foreach ($period as $dt) {
                $dateTime_start = $dt->format("Y-m-d").' ' . '00:00:00';
                $dateTime_end = $dt->format("Y-m-d").' ' .  '23:59:59';

                $this->affiliate_statistics_model->generateStatistics($dateTime_start, $dateTime_end, $aff_userName);
            }
        }
        elseif(strtotime($dateFrom) < strtotime('2015-01-01') /*---minimum date---*/){
            $this->utils->debug_log('Invalid, minimum date 2015-01-01');
        }
        else{
            $this->utils->debug_log('Invalid dates!');
        }

        return true;
    }

	/**
	 * overview : show affiliate password
	 *
	 * @param string $username
	 */
	public function show_agency_password($username) {
		$this->db->from('agency_agents')->where('agent_name', $username);
		$qry = $this->db->get();
		$row = $qry->row();
		$password = '';
		if ($row) {
			$this->load->library(array('salt'));
			$password = $row->password;
			$password = $this->salt->decrypt($password, $this->getDeskeyOG());
		}

		$this->output->append_output("password:" . $password . "\n");
	}

    public function generateUpdateAffiliatePassword($length=8, $saveToCsv='FALSE', $onlyUpdateNull='TRUE'){

    	$this->load->model(array('affiliatemodel'));
    	$this->load->library('salt');

    	$onlyUpdateNull= ($onlyUpdateNull == 'TRUE') ?  true : false;
    	$saveToCsv= ($saveToCsv == 'TRUE') ?  true : false;
    	$d = new DateTime();
    	$directory = '/home/vagrant/Code/';
    	$csv_filepath =  $directory.(__FUNCTION__).'-'.$this->_app_prefix.'-'.$d->format('Y_m_d_H_i_s').'.csv' ;
    	$affMap = $this->affiliatemodel->getUsernameMap();
    	$passList = [];

    	if($saveToCsv){

    		$header_data = ['affiliateId','username','password'];

    		$fp = fopen($csv_filepath, 'w');
    		if ($fp) {
    			$BOM = "\xEF\xBB\xBF";
                fwrite($fp, $BOM); //
            } else {
                //create report failed
            	$this->utils->error_log('create csv file failed', $csv_filepath);
            	return;
            }

            fputcsv($fp, $header_data, ',', '"');
        }


        $count = 0;

        foreach ($affMap as $username => $affiliateId ) {

        	$password = $this->utils->generate_password_no_special_char($length);
        	$hash = $this->salt->encrypt($password, $this->getDeskeyOG());
        	$data = array('affiliateId'=>$affiliateId, 'username'=>$username, 'password'=>$password);
        	$this->utils->debug_log("Affiliate password loop at " , $data);

        	$this->affiliatemodel->startTrans();

        	if($onlyUpdateNull){
        		$affiliate= $this->affiliatemodel->getAffPassword($affiliateId);
        		if(empty($affiliate['password']) || $affiliate['password'] == ''){
        			$this->affiliatemodel->editAffiliates(array('password'=>$hash), $affiliateId);
        			$this->utils->debug_log("Affiliate password update row only null " , $data);
        			if($saveToCsv) {
        				fputcsv($fp, $data, ',', '"');
        			}else{
        				array_push($passList, $data);
        			}
        			$count++;
        		}
	       	}else{//update all
	       		$this->affiliatemodel->editAffiliates(array('password'=>$hash), $affiliateId);
	       		$this->utils->debug_log("Affiliate password update row all affiliates" , $data);
	       		if($saveToCsv) {
	       			fputcsv($fp, $data, ',', '"');
	       		}else{
	       			array_push($passList, $data);
	       		}
	       		$count++;
	       	}

	       	if ($this->affiliatemodel->isErrorInTrans()){
	       		$this->utils->error_log("Failed update aff password" , $affiliateId);
	       	}

       	$this->affiliatemodel->endTrans();

       }//loop

       if($saveToCsv) {
       	fclose($fp);
       	$this->utils->info_log("CSV Affiliate password is here " , $csv_filepath, 'Update count', $count);
       }else{
       	$this->utils->info_log("Affiliate passlist password is here " , @$passList , 'Update count', $count);
       }
   }


   public function setOneWorksAgentPlayersBetlimit($agent_name='_null',$playerId='_null',$dry_run='true'){

   	if(empty($agent_name) || $agent_name=='_null' ){
   		$this->utils->error_log('Please provide agent_name');
   		return;
   	}

   	$dry_run = $dry_run == 'true' ? true : false;
   	$playerId = $playerId != "_null" ? $playerId : null;

   	$this->load->model(['agency_model','player_model','game_provider_auth']);
   	/*
   	$config['oneworks_bet_limit_settings'] = [
        [
            'sport_type' => 1,
            'min_bet' => 10,
            'max_bet' => 50000,
            'max_bet_per_match' => 100000
        ],
        [
            'sport_type' => 2,
            'min_bet' => 10,
            'max_bet' => 50000,
            'max_bet_per_match' => 100000
        ],
        [
            'sport_type' => 3,
            'min_bet' => 10,
            'max_bet' => 50000,
            'max_bet_per_match' => 100000
        ]
     ];
     */
     $oneworks_bet_limit_settings = $this->utils->getConfig('oneworks_bet_limit_settings');

     $api = $this->utils->loadExternalSystemLibObject(ONEWORKS_API);
     $playerMap = $this->player_model->getPlayerIdUsernameMap();
     $agent_id = $this->agency_model->getAgentIdByUsername($agent_name);
     if(empty($agent_id)){
     	$this->utils->error_log('agent id not is empty with agent_name: '.$agent_name);
     	return;
     }

     $playerIds = null;
     if(empty($playerId)){
     	$playerIds = $this->agency_model->get_players_by_agent_id($agent_id);
     }else{
     	$playerIds = [$playerId];
     }

     $failCount = 0;
     $totalCount = count($playerIds);
     $failedList = [];
     $no_of_registered = 0;
     $successList=[];

     if($api){
     	if(!empty($playerIds)){
     		foreach ($playerIds as $playerId) {
     			if($this->game_provider_auth->isRegisterd($playerId,ONEWORKS_API)){
     				$playerName = $playerMap[$playerId];
     				$rlt=[];
     				if(isset($playerName)){
     					if(!empty($oneworks_bet_limit_settings)){
     						if($dry_run){
     							$this->utils->info_log('Dry run only for ', 'playerId', $playerId, 'playerName',$playerName);
     						}else{
     							$rlt = $api->setMemberBetSetting($playerName,$oneworks_bet_limit_settings);
     							if(!$rlt['success']){
     								array_push($failedList,['playerid'=>$playerId, 'reason'=>'Api request failed']);
     								$failCount++;
     							}else{
     								array_push($successList, ['playerid'=>$playerId, 'playerName'=>$playerName]);
     							}
     						}
     					}else{
     						$this->utils->error_log('oneworks_assigned_bet_limit config , Please set it!');
     						break;
     					}
     				}else{
     					array_push($failedList,['playerid'=>$playerId, 'reason'=>'Player name not exist in sbe']);
     					$failCount++;
     					$this->utils->error_log('Playername not exist', $playerId);
     				}
     				$no_of_registered++;
     			}
     		}
     	}else{
     		$this->utils->error_log('Empty playerids!');
     	}

     }else{
     	$this->utils->error_log(' ONEWORKS_API not set!');
   	}//if
   	$successCount = $totalCount - $failCount;
   	$this->utils->info_log("Oneworks set betting limit done, [$successCount] out of [$totalCount] succeed. no_of_registered = [$no_of_registered]", "failedList", $failedList, 'dry_run', $dry_run);
   	$this->utils->info_log("successList",$successList);
   	$this->utils->debug_log("oneworks_bet_limit_settings", @$oneworks_bet_limit_settings);
   }

   	public function delete_gameplay_original_gamelogs_duplicate($startDate=null,$endDate=null){
   		$this->utils->info_log('Now Query duplicate map for Gameplay gamelogs duplicate');
   		$query = "SELECT MAX(id) as max_id,external_uniqueid FROM gameplay_game_logs GROUP BY external_uniqueid HAVING COUNT(external_uniqueid) > 1";
   		$duplicates = [];

   		$query = $this->db->query($query);
   		if ($query && $query->num_rows() > 0) {
			$duplicates=json_decode(json_encode($query->result()),true);
			$query->free_result();
		}

   		$this->utils->debug_log('duplicates',$duplicates);
		$external_uniqueid_map = array_column($duplicates, "max_id", "external_uniqueid");
		$dateTimeFrom = new \DateTime($startDate);
		$dateTimeTo = new \DateTime($endDate);
		$interval = new DateInterval('P1D');
		$daterange = new DatePeriod($dateTimeFrom, $interval ,$dateTimeTo);

		# QUERY GAMELOGS PER DAY
		foreach($daterange as $date){
			$gameRecords = [];
			$dataStartDate = $date->format("Y-m-d 00:00:00");
			$dataEndDate = $date->format("Y-m-d 23:59:59");
			$dataSql = <<<EOD
			SELECT
			  id,external_uniqueid
			FROM
			  gameplay_game_logs
			WHERE (
			    trans_date BETWEEN ?
			    AND ?
			  )
			  OR (
			    timeBet BETWEEN ?
			    AND ?
			  )
EOD;
			$query2 = $this->db->query($dataSql, array(
				$dataStartDate,
				$dataEndDate,
				$dataStartDate,
				$dataEndDate,
			));

	   		if ($query2 && $query2->num_rows() > 0) {
				$gameRecords=json_decode(json_encode($query2->result()),true);
				$query2->free_result();
			}

			foreach ($gameRecords as $record) {
				$maxid = @$external_uniqueid_map[$record['external_uniqueid']];
				# check if latest record if not delete
				if($maxid&&$maxid!=$record['id']){
			        $this->db->where('id', $record['id']);
			        $this->db->delete('gameplay_game_logs');
					$this->utils->debug_log("Gameplay Duplicate record delete id = " , $record['id']);
				}
			}
		}
    }

    public function delete_gameplay_original_gamelogs_duplicate_via_temp_table(){
   		$this->utils->info_log('Now DELETING DUPLICATE Gamelogs for Gameplay');
   		$query = "SELECT id FROM gameplay_game_logs_temp";
   		$duplicates = [];

   		$query = $this->db->query($query);
   		if ($query && $query->num_rows() > 0) {
			$duplicates=json_decode(json_encode($query->result()),true);
			$query->free_result();
		}
		foreach ($duplicates as $record) {
			# check if latest record if not delete
	        $this->db->where('id', $record['id']);
	        $this->db->delete('gameplay_game_logs');
			$this->utils->debug_log("Gameplay Duplicate record delete id = " , $record['id']);
		}
    }

    public function batch_update_gameplatform_set_member_bet_settings($game_platform_id, $is_setmemberbetsetting = true){
   		$this->utils->info_log('Now Query Game Accounts to Update');
   		$SQL = <<<EOD
   		SELECT
		  plyr.username
		FROM
		  `game_provider_auth` AS auth
		  LEFT JOIN player AS plyr
		    ON auth.player_id = plyr.playerId
		WHERE auth.game_provider_id = ?
		  AND auth.register = 1
EOD;
		$players = [];
   		$query = $this->db->query($SQL,array($game_platform_id));
   		if ($query && $query->num_rows() > 0) {
			$players=json_decode(json_encode($query->result()),true);
			$query->free_result();
		}

		# LOAD GAME API
		$this->api = $this->utils->loadExternalSystemLibObject($game_platform_id);
		if(!empty($players)){
			foreach ($players as $key => $player) {
				if($is_setmemberbetsetting){
					$updateMember = $this->api->setMemberBetSetting($player['username']);
				} else {
					$updateMember = $this->api->updateMemberSetting($player['username']);
				}
				if($updateMember['success']){
					$this->utils->debug_log('Player ', $player['username'], ' Updated!');
				}else{
					$this->utils->error_log('Player ', $player['username'], ' Update Failed!');
				}
			}
		}

		$this->utils->debug_log('Done Updating player member settings!');
	}

	/**
	 * overview : update player bet settings, only for debug
	 *
	 * @param int	 $gamePlatformId
	 * @param string $username
	 * @param int $sport_type
	 * @param int $market_type
	 * @param int $min_bet
	 * @param int $max_bet
	 * @param int $maxPerMatch
	 * @return mixed
	 */

	public function update_player_bet_settings($username, $sport_type, $market_type, $min_bet, $max_bet, $maxPerMatch) {


		$api=$this->utils->loadExternalSystemLibObject(SBOBET_API);
		$gamePlatformId = SBOBET_API;
		if(!empty($api)){
			$rlt=$api->updatePlayerBetSettingsBySportTypeAndMarketType($username, $sport_type, $market_type, $min_bet, $max_bet, $maxPerMatch);
			$this->utils->debug_log('result of updatePlayerBetSettingsBySportTypeAndMarketType', $rlt);

		}else{
			$this->utils->error_log('NOT FOUND API', $gamePlatformId);
		}

		$this->utils->debug_log('========= end update_player_bet_settings ============================');

		$this->utils->debug_log('gamePlatformId', $gamePlatformId, 'username', $username,
			'sport_type', $sport_type,'market_type', $market_type,
			'min_bet', $min_bet, 'max_bet', $max_bet,
			'maxPerMatch', $maxPerMatch, 'result', $rlt);
	}

	/**
	 * overview : batch update all players bet settings
	 * @param int $gamePlatformId
	 * @return mixed
	 */

	public function update_all_players_bet_settings($gamePlatformId, $gameUsername = _COMMAND_LINE_NULL) {
		$this->utils->debug_log("COMMAND: (update_all_players_bet_settings)");
		$this->CI->load->model('game_provider_auth');

		$cnt = 0;
		$gamePlatformId = isset($gamePlatformId)?$gamePlatformId:SBOBET_API;
		$api=$this->utils->loadExternalSystemLibObject($gamePlatformId);

		if(!empty($api)){

			if(isset($gameUsername) && $gameUsername==_COMMAND_LINE_NULL){
				$gameUsername = $this->CI->game_provider_auth->getAllGameUsernames($api->getPlatformCode());
			}else{
				$gameUsername = array($gameUsername);
			}
			
			foreach($gameUsername as $username) {			
				$result = $api->updatePlayerBetSettings($username);				
				$this->utils->debug_log("COMMAND: (update_all_players_bet_settings) username: ". $username, $result);
				$cnt ++;
			}
		}else{
			$this->utils->error_log('NOT FOUND API', $gamePlatformId);
		}		

		$this->utils->info_log("COMMAND: (update_all_players_bet_settings) Update count: " . $cnt);
		return;
	}

	/**
	 * overview : update player bet settings for DG Game, only for debug
	 *
	 * @param string $limit_group
	 * @return mixed
	 */

	public function update_player_bet_limit_group($limit_group) {

		$api=$this->utils->loadExternalSystemLibObject(DG_API);
		if(!empty($api)){
			$rlt=$api->updatePlayerBetLimitGroup($limit_group);
			$this->utils->debug_log('result of updatePlayerBetLimitGroup', $rlt);

		}else{
			$this->utils->error_log('NOT FOUND API', $gamePlatformId);
		}

		$this->utils->debug_log('========= end update_player_bet_limit_group ============================');

		$this->utils->debug_log('update_player_bet_limit_group result', $rlt);
	}

	/**
	 * overview : update player bet settings for MTECH OG, only for debug
	 *
	 * @param string $gameprovider $video_roulette_limit_id $type
	 * @return mixed
	 */

	public function update_player_bet_limit_id($gameprovider,$video_roulette_limit_id,$type) {

		$api=$this->utils->loadExternalSystemLibObject(MTECH_OG_API);
		if(!empty($api)){
			$rlt=$api->updatePlayerBetLimitGroup($gameprovider,$video_roulette_limit_id,$type);
			$this->utils->debug_log('result of updatePlayerBetLimitGroup', $rlt);

		}else{
			$this->utils->error_log('NOT FOUND API', $gamePlatformId);
		}

		$this->utils->debug_log('========= end update_player_bet_limit_group ============================');

		$this->utils->debug_log('update_player_bet_limit_group result', $rlt);
	}

	public function delete_duplicate_system_feature(){
		$this->load->model(['system_feature']);
		$this->system_feature->clearDuplicateName();
	}

	public function export_player_password(){

		$this->load->model(array('player_model'));
		$rows = $this->player_model->getPlayerIdUsernameMap();

		$d = new DateTime();
		$csv_file =  './players_'.$d->format('Y_m_d').'.csv' ;
		$fp = fopen($csv_file, 'w');

		fputcsv($fp, ['PlayerID', 'Username', 'Password'], ',', '"');

		if($fp){
			$BOM = "\xEF\xBB\xBF";
			fwrite($fp, $BOM); //
		}else{
			$this->utils->error_log("error occured try again");
			return;
		}

		foreach($rows as $playerId => $username) {
			$player = [];
			$player['playerId'] = $playerId;
			$player['username'] = $username;
			$player['password'] = $this->player_model->getPasswordByUsername($username);
			fputcsv($fp, $player, ',', '"');
		}
	}

	public function batch_reset_agent_key_password($filename){
		$filename='/home/vagrant/Code/'.$filename;
		$this->utils->debug_log('try load file', $filename);
		$agentNameString=trim(file_get_contents($filename));
		if(empty($agentNameString)){
			$this->utils->error_log('empty agent name', $agentNameString);
			$this->utils->info_log('usage: batch_reset_agent_key_password <agent_name_file>');
			return;
		}
		$this->utils->debug_log('get agent name from file', $agentNameString);

		$agentNameList=explode(',', $agentNameString);
		if(empty($agentNameList)){
			$this->utils->error_log('empty agentNameList', $agentNameList);
			$this->utils->info_log('usage: batch_reset_agent_key_password <agent_name_file>');
			return;
		}
		$this->load->model(['agency_model']);
		$agentIdList=$this->agency_model->getIdListByAgentNameList($agentNameList);
		$this->utils->debug_log('get agent id list', $agentIdList);
		if(count($agentIdList)!=count($agentNameList)){
			$this->utils->error_log('miss something on agentIdList', count($agentIdList), count($agentNameList));
			$this->utils->info_log('usage: batch_reset_agent_key_password <agentNameList>');
			return;
		}

		$this->agency_model->resetKeyAndPasswordByIdList($agentIdList);

		$this->utils->info_log('result of reset', $agentIdList);
	}

	public function batch_hard_delete_players_info($config_key,$dbKey=_COMMAND_LINE_NULL, $dry_run = 'TRUE'){
         //specify dbkey if config->game_logs_group has keys ex. idr, cny if not let _null value
		$tables = $this->utils->getConfig('delete_player_info_settings');
		$player_ids = $this->utils->getConfig($config_key);
		if(empty($player_ids)){
			return $this->utils->error_log('No player Ids supplied');
		}
		$d = new DateTime();     
		$date_str = $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999);  
		$token=$config_key.'_'.$date_str ;
		if($dbKey !=_COMMAND_LINE_NULL){
			$token=$dbKey.'-'.$token; 
		}
		$csv_log_headers = [];
		foreach ($tables as $table => $value) {
			array_push($csv_log_headers,$table);
		}
		$filepath=null;
		$dry_run = (strtolower($dry_run) == 'true') ? true : false;
		foreach ($player_ids as  $player_id) {
			$this->player_model->startTrans();
			$this->hard_delete_player_info($tables, $player_id, __FUNCTION__, $dbKey, $filepath, $dry_run, $token);
			$this->player_model->endTrans();
		}
		$this->utils->info_log(__FUNCTION__.'_log file '.$filepath,'dry_run:'.$dry_run);

	}


	public function hard_delete_players_by_agent_id($agent_id,$dbKey=_COMMAND_LINE_NULL,$dry_run='TRUE'){
        //specify dbkey if config->game_logs_group has keys ex. idr, cny if not let _null value
		$tables = $this->utils->getConfig('delete_player_info_settings');
		$this->load->model(['player_model']);
		$dry_run = (strtolower($dry_run) == 'true') ? true : false;
		$player_count=0;
		$total_count=0;
		$player_map= $this->player_model->getAgentPlayerIdUsernameMap($agent_id);
		if(empty($player_map)){
			return $this->utils->error_log('Empty player map');
		}else{
			$total_count =  count($player_map);
		}
		$d = new DateTime(); 
		$date_str = $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999);   
		$token=$agent_id.'_'.$date_str;    
		if($dbKey !=_COMMAND_LINE_NULL){
			$token=$dbKey.'-'.'agent_id_'.$agent_id.'-'.$date_str; 
		}
		$csv_log_headers = [];
		foreach ($tables as $table => $value) {
			array_push($csv_log_headers,$table);
		}
		$filepath=null;
		$this->utils->_appendSaveDetailedResultToRemoteLog($token, __FUNCTION__, [], $filepath, true, $csv_log_headers);
		
		foreach ($player_map as $player_id => $player) {
			$this->player_model->startTrans();
			$this->hard_delete_player_info($tables, $player_id, __FUNCTION__, $dbKey, $filepath, $dry_run, $token);
			$this->player_model->endTrans();
			$player_count++;
			$this->utils->info_log('processed  '.$player_count.' out of '.$total_count.' at player_id:'.$player_id.' at agent_id:'.$agent_id);

		}
		$this->utils->info_log(__FUNCTION__.'_log file '.$filepath,'dry_run:'.$dry_run);

	}


	protected function hard_delete_player_info($tables, $playerId, $func, $dbKey, $filepath, $dry_run, $token){ 

		$table_as_columns_row = [];

		foreach ($tables as $table => &$details) {

			$all_pages_table_data = [];
			$affected_rows_arr=[];
			$affected_rows = 1;//trigger while loop
			$limit=500;//default
			$offset=0;
			$page=0;		
			$save_info_to_csv = (isset($details['save_info_to_csv']) && $details['save_info_to_csv'] === true) ? true : false;


			$game_logs_details =[];

			if($table == 'game_logs_group'){

				if(isset($details[$dbKey]['game_logs'])){
					$details = $details[$dbKey]['game_logs'];
					$game_logs_details = $details;
				}else{
					$game_logs_details = $details['game_logs'];
				}
			}
			
			while($affected_rows > 0){

				if($table == 'game_logs_group'){

					$player_id_field = $game_logs_details['player_id_field'];
					$limit = $game_logs_details['sql_delete_limit'];			
					$game_logs_original_tbls = $game_logs_details['game_logs_originals'];
					$game_logs_originals_str_for_select = implode(".external_uniqueid,", $game_logs_original_tbls).'.external_uniqueid';
					$game_logs_originals_str_for_delete = implode(",", $game_logs_original_tbls);
					$left_joins = [];

					foreach ($game_logs_original_tbls as $original_log_tbl) {
						$join_str = 'LEFT JOIN '. $original_log_tbl.' ON '. $original_log_tbl.'.external_uniqueid = '. 'game_logs.external_uniqueid COLLATE utf8_unicode_ci';
						array_push($left_joins, $join_str);
					}
					$where = 'game_logs.'.$player_id_field.'= '.$playerId;

					if($dry_run){
						$sql = 'SELECT '. $game_logs_originals_str_for_select. ' FROM game_logs '.implode(" ", $left_joins).' WHERE '.$where.' LIMIT '.$limit.'  OFFSET '.$offset.' ';
						$is_dry_run = 'true';
					}else{
						$sql = 'DELETE '. $game_logs_originals_str_for_delete. ' FROM game_logs '.implode(" ", $left_joins).' WHERE '.$where;
						$is_dry_run = 'false';
					}

				}else if($table == 'transactions_from_type'){
					$player_id_field = $details['player_id_field'];
					$limit = $details['sql_delete_limit'];
					if($dry_run){
						$sql = 'SELECT  '.$player_id_field.' FROM transactions where from_type = '.Transactions::PLAYER.' and from_id = "'.$playerId.'"  LIMIT '.$limit.'  OFFSET '.$offset.' ';
						$is_dry_run = 'true';
					}else{
						$sql = 'DELETE FROM transactions where from_type = '.Transactions::PLAYER.' and from_id = "'.$playerId.'" LIMIT '.$limit;
						$is_dry_run = 'false';
					}

				}else if($table == 'transactions_to_type'){
					$player_id_field = $details['player_id_field'];
					$limit = $details['sql_delete_limit'];
					if($dry_run){
						$sql = 'SELECT  '.$player_id_field.' FROM transactions where to_type = '.Transactions::PLAYER.'  and to_id = "'.$playerId.'"  LIMIT '.$limit.'  OFFSET '.$offset.' ';
						$is_dry_run = 'true';
					}else{
						$sql = 'DELETE  FROM transactions where to_type = '.Transactions::PLAYER.'  and to_id = "'.$playerId.'" LIMIT '.$limit;
						$is_dry_run = 'false';
					}

				}else{
					$player_id_field = $details['player_id_field'];
					$limit = $details['sql_delete_limit'];
					
					if($dry_run){
						$sql_for_saving =  'SELECT * FROM '.$table.' WHERE '.$player_id_field.' = "'.$playerId.'"  LIMIT '.$limit.'  OFFSET '.$offset.' ';
						$sql = 'SELECT '.$player_id_field.' FROM '.$table.' WHERE '.$player_id_field.' = "'.$playerId.'"  LIMIT '.$limit.'  OFFSET '.$offset.' ';
						$is_dry_run = 'true';
					}else{
						$sql_for_saving =  'SELECT * FROM '.$table.' WHERE '.$player_id_field.' = "'.$playerId.'"  LIMIT '.$limit;
						$sql = 'DELETE  FROM '.$table.' WHERE '.$player_id_field.' = '.$playerId.' LIMIT '.$limit;
						$is_dry_run = 'false';
					}
				}

				$page++;
				$for_save_rlt=[];
				if($save_info_to_csv){
					$q = $this->db->query($sql_for_saving);
					$for_save_rlt = $q->result_array();
				}

				$qry = $this->db->query($sql);
				$affected_rows = $this->db->affected_rows();
				//save important info
				if($save_info_to_csv){
					foreach ($for_save_rlt as &$value) {
						if($table == 'player'){
							$value['big_wallet'] = json_decode($value['big_wallet']);
						}
					}
					array_push($affected_rows_arr,$for_save_rlt);
				}
				else{
					if($affected_rows > 0){
						array_push($affected_rows_arr, $affected_rows);
					}	
				}
				if($dry_run){
					$this->utils->debug_log('table',$table, 'affected count:'.$affected_rows , 'dry_run:'.$is_dry_run, 'playerId:'.$playerId, 'page_no:'.$page,'LIMIT '.$limit.'  OFFSET '.$offset);
					$offset = $offset + $limit;
				}else{
					$this->utils->debug_log($table, 'affected count:'.$affected_rows , 'dry_run:'.$is_dry_run, 'playerId:'.$playerId, 'page_no:'.$page);
				}			

            	if($affected_rows < $limit){ //prevent another offset or last page
            		$affected_rows=0;//stop while loop
            	}

            }//while loop
            array_push($all_pages_table_data, $affected_rows_arr);
            array_push($table_as_columns_row , serialize($all_pages_table_data));             

		}//foreach table
		$this->utils->_appendSaveDetailedResultToRemoteLog($token, $func, $table_as_columns_row, $filepath, true, []);
		
	}


	public function batch_hard_delete_agency_agents_info($config_key,$dry_run = 'TRUE'){
		
		$agent_ids = $this->utils->getConfig($config_key);
		$this->load->model(['player_model']);

		if(empty($agent_ids)){
			return $this->utils->error_log('No agent Ids supplied');
		}
		$d = new DateTime(); 
		$token=$config_key.'_'.$d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999);
		$tables_details = $this->utils->getConfig('delete_agent_info_settings');
		$csv_log_headers = array_keys($tables_details);
		$filepath=null;
		$dry_run = (strtolower($dry_run) == 'true') ? true : false;
		$this->utils->_appendSaveDetailedResultToRemoteLog($token, __FUNCTION__, [], $filepath, true, $csv_log_headers);
		foreach ($agent_ids as $agentId) {
			$this->player_model->startTrans();
			$this->hard_delete_agency_agent_info($agentId,__FUNCTION__,$filepath, $dry_run, $token);
			$this->player_model->endTrans();
		}
		$this->utils->info_log(__FUNCTION__.'_log file '.$filepath,'dry_run:'.$dry_run);		
	}


	protected function hard_delete_agency_agent_info($agentId, $func, $filepath, $dry_run, $token){

		if(empty($agentId)){
			return $this->utils->error_log('No agent Id supplied');
		}
		$tables_details = $this->utils->getConfig('delete_agent_info_settings');
		$table_as_columns_row = [];

		foreach ($tables_details as $table => &$details) {

			$all_pages_table_data = [];
			$affected_rows_arr=[];
			$affected_rows = 1;//trigger while loop
			$limit = $details['sql_delete_limit'];

			$offset=0;
			$page=0;
			$agent_id_field = $details['agent_id_field'];
			$save_info_to_csv = (isset($details['save_info_to_csv']) && $details['save_info_to_csv'] === true) ? true : false;

			while($affected_rows > 0){

				if($table == 'transactions_from_type'){
					if($dry_run){
						$sql = 'SELECT  '.$agent_id_field.' FROM transactions where from_type = '.Transactions::AGENT.' and from_id = "'.$agentId.'"  LIMIT '.$limit.'  OFFSET '.$offset.' ';
						$is_dry_run = 'true';
					}else{
						$sql = 'DELETE FROM transactions where from_type = '.Transactions::AGENT.' and from_id = "'.$agentId.'" LIMIT '.$limit;
						$is_dry_run = 'false';
					}

				}else if($table == 'transactions_to_type'){
					if($dry_run){
						$sql = 'SELECT  '.$agent_id_field.' FROM transactions where to_type = '.Transactions::AGENT.'  and to_id = "'.$agentId.'"  LIMIT '.$limit.'  OFFSET '.$offset.' ';
						$is_dry_run = 'true';
					}else{
						$sql = 'DELETE  FROM transactions where to_type = '.Transactions::AGENT.'  and to_id = "'.$agentId.'" LIMIT '.$limit;
						$is_dry_run = 'false';
					}

				}else{	
					
					if($dry_run){
						$sql = 'SELECT '.$agent_id_field.' FROM '.$table.' WHERE '.$agent_id_field.' = "'.$agentId.'"  LIMIT '.$limit.'  OFFSET '.$offset.' ';
						$sql_for_saving =  'SELECT * FROM '.$table.' WHERE '.$agent_id_field.' = "'.$agentId.'"  LIMIT '.$limit.'  OFFSET '.$offset.' ';
						$is_dry_run = 'true';
					}else{
						$sql_for_saving =  'SELECT * FROM '.$table.' WHERE '.$agent_id_field.' = "'.$agentId.'"  LIMIT '.$limit ;
						$sql = 'DELETE  FROM '.$table.' WHERE '.$agent_id_field.' = '.$agentId.' LIMIT '.$limit;
						$is_dry_run = 'false';
					}
				}

				$page++;
				$for_save_rlt=[];
				if($save_info_to_csv){
					$q = $this->db->query($sql_for_saving);
					$for_save_rlt = $q->result_array();

				}
				if($dry_run === false){
                	//start trans
				}
				$qry = $this->db->query($sql);

				$affected_rows = $this->db->affected_rows();
				//save important info
				if($save_info_to_csv){		
					array_push($affected_rows_arr,$for_save_rlt);
				}
				else{
					if($affected_rows > 0){
						array_push($affected_rows_arr, $affected_rows);
					}	
				}
				if($dry_run){
					$this->utils->debug_log('table',$table, 'affected count:'.$affected_rows , 'dry_run:'.$is_dry_run, 'agentId:'.$agentId, 'page_no:'.$page,'LIMIT '.$limit.'  OFFSET '.$offset);
					$offset = $offset + $limit;
				}else{
					$this->utils->debug_log($table, 'affected count:'.$affected_rows , 'dry_run:'.$is_dry_run, 'agentId:'.$agentId, 'page_no:'.$page);
				}		

            	if($affected_rows < $limit){ //prevent another offset or last page
            		$affected_rows=0;//stop while loop
            	}

    		}//while loop
    		array_push($all_pages_table_data, $affected_rows_arr);
    		array_push($table_as_columns_row , serialize($all_pages_table_data)); 
    	}// foreach
    	$this->utils->_appendSaveDetailedResultToRemoteLog($token, $func, $table_as_columns_row, $filepath, true, []);       
    	
    }

    /**
	 * overview : Get total rake of player 
	 * @param array or int $playerIds
	 * @param string date $dateTimeFrom
	 * @param string date $dateTimeTo
	 * @param array or int $gamePlatformId
	 * @return sum of total rake
	 */

    public function get_total_rake($playerIds, $dateTimeFrom = null, $dateTimeTo = null, $gamePlatformId = null){
    	$this->CI->load->model(array('game_logs'));
    	$total_rake   = $this->CI->game_logs->getTotalRakeByPlayers($playerIds, $dateTimeFrom, $dateTimeTo, $gamePlatformId);
    	$this->utils->debug_log('total_rake =========>'.$total_rake);
    }

    public function create_agent_on_api($game_platform_id){
    	$api = $this->utils->loadExternalSystemLibObject($game_platform_id);
		if(!empty($api)){
			$rlt=$api->createAgent();
			$this->utils->debug_log('result of create_agent_on_api', $rlt);

		}else{
			$this->utils->error_log('NOT FOUND API', $game_platform_id);
		}
		$this->utils->debug_log('========= end create_agent_on_api ============================');
    }

    public function create_test_player($username=null,$password=_COMMAND_LINE_NULL,$pass_length=_COMMAND_LINE_NULL,$levelId=_COMMAND_LINE_NULL,$registered_by=_COMMAND_LINE_NULL,$mm_key=_COMMAND_LINE_NULL,$tag=_COMMAND_LINE_NULL){ 
     //sudo ./command.sh create_test_player aris1007  aris1001 _null 9 _null test_mattermost_notif local

    	if(empty($username)){
    		$this->utils->error_log('missing username param');
    		return;
    	}
    	$this->load->library('salt');
    	$this->load->model(['group_level','player_model']);

    	if($levelId == _COMMAND_LINE_NULL){
    		$levelId = '1';
    	}
    	if($pass_length == _COMMAND_LINE_NULL){
    		$pass_length = 7;
    	}
    	if($password == _COMMAND_LINE_NULL){
    		$password = $this->utils->generate_password_no_special_char($pass_length);
    	}
    	$rand_chars=$this->utils->generate_password_no_special_char(7);
    	$first_name = 'testplayer'.$rand_chars;
    	$last_name = 'testplayer'.$rand_chars;

    	if($registered_by == _COMMAND_LINE_NULL){
    		$registered_by = 'T1';
    	}

    	$level=$this->group_level->getLevelById($levelId);
    	$levelName=$level['vipLevelName'];
    	$group=$this->group_level->getGroupById($level['vipSettingId']);
    	$groupName = $group['groupName'];
    	$username = trim($username);
    	if(!$this->player_model->usernameExist($username)){
    		$player_basic_info = array(
    			'levelId' => $levelId,
    			'levelName' => $levelName,
    			'groupName' => $groupName,
    			'password' => $this->salt->encrypt($password, $this->getDeskeyOG()),
    			'username' => $username,
    			'registered_by' => $registered_by,
    			'email' => $first_name.'@tripleonetech.net',
    			'createdOn' => $this->utils->getNowForMysql()
    		);
    		$player_id = $this->player_model->insertData('player', $player_basic_info);
            $player_basic_info['password']=$password;//override hash
            $player_details = array(
            	'playerId' => $player_id,
            	'firstName' => $first_name,
            	'lastName' => $last_name,
            );
            $this->player_model->insertData('playerdetails',  $player_details);
            //required to view on new userinfo
            $playeraccount = array(
            	'playerId' => $player_id,
            	'currency' =>  $this->player_model->getActiveCurrencyCode(),
            	'type' => Wallet_model::TYPE_MAINWALLET,
            	'typeOfPlayer' => Wallet_model::TYPE_OF_PLAYER_REAL,
            	'totalBalanceAmount' => 0,
            	'typeId' => Wallet_model::MAIN_WALLET_ID ,
            	'status' => Player_model::DEFAULT_PLAYERACCOUNT_STATUS
            );
            $this->player_model->insertData('playeraccount',  $playeraccount);
            $this->utils->info_log('testplayer created',$player_basic_info);

            if($mm_key != _COMMAND_LINE_NULL){
            	$this->load->helper('mattermost_notification_helper');
            	$channel =  $mm_key;
            	$texts_and_tags ='#'.__FUNCTION__.(new Datetime())->format('Ymd').' #year_mo_'.(new Datetime())->format('Ym');
            	if($tag != _COMMAND_LINE_NULL){
            		$texts_and_tags ='#'.__FUNCTION__.(new Datetime())->format('Ymd').' #year_mo_'.(new Datetime())->format('Ym').' #'.$tag;
            	}	
            	$notif_message = array(array('text' => "```json\n".json_encode($player_basic_info,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n```",'type' => 'info'));
            	sendNotificationToMattermost(__FUNCTION__, $channel,$notif_message,$texts_and_tags);
            }
        }else{
        	$this->utils->error_log($username.' already exist');
        }		
    }


    public function batch_create_test_players($config_key){

	// $config['sample']=
	// "password_length" =>null,
	// 'mm_key' => 'test_mattermost_notif',
	// 'tag' => 'local'
	// 'players' =>[
	// 	"username" => "testplayer",
	// 	"password"=> "4AOYNG" // null,
	// 	"registered_by" => "t1",
	// ],
	// []...
	//    ];

    	$config = $this->utils->getConfig($config_key);
    	if(empty($config)){
    		$this->utils->error_log('config key not found');
    		return;
    	}
    	if(!isset($config['test_players'])){
    		$this->utils->error_log('players not set in config');
    		return;
    	}
    	$test_players = $config['test_players'];
    	if(empty($test_players)){
    		$this->utils->error_log('config key not found');
    		return;
    	}
    	$mm_key=$pass_length=$tag= _COMMAND_LINE_NULL;
    	if(isset($config['pass_length'])){
    		$pass_length=$config['pass_length'];
    	}
    	if(isset($config['mm_key'])){
    		$mm_key=$config['mm_key'];
    	}
    	if(isset($config['tag'])){
    		$tag=$config['tag'];
    	}

    	foreach ($test_players as $test_player) {

    		if(isset($test_player['username'])){
    			$parts = preg_split('/\s+/', $test_player['username']);
    			if(count($parts) > 1){
    				$this->utils->error_log('please check your config at ',$test_player);
    				continue;
    			}
    		}else{
    			$this->utils->error_log('please check your config at ',$test_player);
    			continue;
    		}
    		$username=preg_replace('/\s+/', '', $test_player['username']);
    		$password=$levelId=$first_name=$last_name=$last_name=$registered_by=_COMMAND_LINE_NULL;

    		if(isset($test_player['password'])){
    			$password=$test_player['password'];
    		}
    		if(isset($test_player['levelId'])){
    			$levelId=$test_player['levelId'];
    		}
    		if(isset($test_player['registered_by'])){
    			$registered_by=$test_player['registered_by'];
    		}
    		$this->create_test_player($username,$password,$pass_length,$levelId,$registered_by,$mm_key,$tag);
    	}
    }

}


