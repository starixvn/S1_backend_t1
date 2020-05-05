<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Ultraplay_service_api extends BaseController {

	public function __construct() {
		parent::__construct();
		$this->load->model(array('wallet_model','ultraplay_game_logs'));
		$this->game_api = $this->utils->loadExternalSystemLibObject(ULTRAPLAY_API);
	}

	public function index($methodName) {
		$response = array();
		$reqString = file_get_contents('php://input');
		$reqArray = json_decode($reqString, true);

		try {

			if ( ! $reqArray && isset($reqArray['RequestID'])) {
				throw new Exception('Error Processing Request', 1);
			}

			$this->request_id = $reqArray['RequestID'];

			$this->db->insert('response_results', array(
				'note' 				=> $this->request_id,
				'system_type_id' 	=> $this->game_api->getPlatformCode(),
				'request_api' 		=> $methodName,
				'request_params' 	=> $reqString,
				'created_at' 		=> date('Y-m-d H:i:s'),
			));

			$this->response_result_id = $this->db->insert_id();

			if ($this->game_api->get_api_version() != $reqArray['ApiVersion']) {
				throw new Exception('Error Processing Request', 1);
			}

			$this->utils->debug_log('>>>>>>>>>>>>>> ultraplay request monitor', $this->request_id, $methodName, $reqArray);

			$response = $this->{$methodName}($reqArray);

			$this->utils->debug_log('>>>>>>>>>>>>>> ultra_play response monitor', $this->request_id, $methodName, $response);

			$response['Status'] = 'OK';
		} catch (Exception $e) {
			$this->utils->debug_log('error', $this->request_id, $methodName,  $e->getMessage());
			$response['ErrorDescription'] = $e->getMessage();
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($response));
		return;

	}

	public function tokenLogin($params) {

		$token = isset($params['Token']) ? $params['Token'] : NULL;
		$player = $this->game_api->getPlayerInfoByToken($token);

		if ( ! $player) throw new Exception("Invalid Token", 1);

		$player_id = $player['playerId'];
		$player_username = $player['username'];
		$game_username = $this->game_api->getGameUsernameByPlayerId($player_id);
		$balance_result = $this->game_api->queryPlayerBalance($player_username);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;
		$currency = $this->game_api->get_currency();

		$data = array(
			'AccountID' => $game_username,
			'Balance' => $balance,
			'Currency' => $currency,
		);

		return $data;

	}

	public function refreshToken($params) {

		$token = isset($params['Token']) ? $params['Token'] : NULL;
		$player_id = $this->game_api->getPlayerIdByToken($token);

		if ( ! $player_id) throw new Exception("Invalid Token", 1);

		$new_token = $this->game_api->getPlayerToken($player_id);

		$data = array(
			'Token' => $new_token,
		);

		return $data;

	}

	public function getBalance($params) {

		$token = isset($params['Token']) ? $params['Token'] : NULL;
		$player = $this->game_api->getPlayerInfoByToken($token);

		if ( ! $player) throw new Exception("Invalid Token", 1);

		$player_username = $player['username'];
		$balance_result = $this->game_api->queryPlayerBalance($player_username);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;

		$data = array(
			'Balance' => $balance,
		);

		return $data;

	}

	public function placeBet($params) {

		$this->load->model('game_logs');

		$token = isset($params['Token']) ? $params['Token'] : NULL;
		$player = $this->game_api->getPlayerInfoByToken($token);

		if ( ! $player) throw new Exception("Invalid Token", 1);

		$player_id = $player['playerId'];
		$player_username = $player['username'];

		$description = isset($params['Description']) ? $params['Description'] : NULL;
		$deviceType = isset($params['DeviceType']) ? $params['DeviceType'] : NULL;
		$formattedOdds = isset($params['FormattedOdds']) ? $params['FormattedOdds'] : NULL;
		$groupTicketID = isset($params['GroupTicketID']) ? $params['GroupTicketID'] : NULL;
		$isAccepted = isset($params['IsAccepted']) ? $params['IsAccepted'] : NULL;
		$isCombo = isset($params['IsCombo']) ? $params['IsCombo'] : NULL;
		$odds = isset($params['Odds']) ? $params['Odds'] : NULL;
		$oddsFormat = isset($params['OddsFormat']) ? $params['OddsFormat'] : NULL;
		$selectionsDetails = isset($params['SelectionsDetails']) ? json_encode($params['SelectionsDetails']) : NULL;
		$ticketID = isset($params['TicketID']) ? $params['TicketID'] : NULL;

        if(isset($params['SelectionsDetails'])){
            $gameCode = count($params['SelectionsDetails']) >= 1 ? $params['SelectionsDetails'][0]['Sport']['Name']: "Combo Parlay";
        }else{
            $gameCode = null;
        }

        #prepare game description
        $this->load->model('game_description_model');
        $game_description_data = $this->game_description_model->getGameDescriptionListByGamePlatformIdAndGameCodeArray(ULTRAPLAY_API,$gameCode);
        $unknownGame = $this->CI->game_description_model->getUnknownGame(ULTRAPLAY_API);

        $game_description_id    = null;
        $game_type_id           = NULL;
        $gamereference          = NULL;

        if(!empty($game_description_data[$gameCode]->id)){
            $game_description_id = $game_description_data[$gameCode]->id;
        }

        if(!empty($game_description_data[$gameCode]->game_type_id)){
            $game_type_id = $game_description_data[$gameCode]->game_type_id;
        }else{
            $game_type_id = $unknownGame->game_type_id;
        }
        #end

		$stake = isset($params['Stake']) ? $params['Stake'] : 0;

		$this->utils->debug_log('>>>>>>>>>>>>>> ultraplay placeBet monitor', $params);

		$this->ultraplay_game_logs->insertGameLogs(array(
			'PlayerId' => $player_id,
			'IsAccepted' => $isAccepted,
			'Stake' => $stake,
			'TicketID' => $ticketID,
			'GroupTicketID' => $groupTicketID,
			'Odds' => $odds,
			'OddsFormat' => $oddsFormat,
			'FormattedOdds' => $formattedOdds,
			'IsCombo' => $isCombo,
			'DeviceType' => $deviceType,
			'SelectionsDetails' => $selectionsDetails,
			'Description' => $description,
            'uniqueid' => $ticketID,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
            'game_code' => $gameCode,
		));

		$balance_result = $this->game_api->queryPlayerBalance($player_username);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;

		if (($balance - $stake) < 0) {
			throw new Exception('Player has insufficient funds.', 6503);
		}

        $this->subtract_amount($player_id, $stake);

		$balance_result = $this->game_api->queryPlayerBalance($player_username);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;

		$id = $this->ultraplay_game_logs->getIdByUniqueid($ticketID);
		$record = $this->ultraplay_game_logs->getGameRecord($id);

		$external_uniqueid 	= $record['TicketID'];
		$bet_amount 		= $record['Stake'];
		$result_amount 		= - $bet_amount;
		$after_balance 		= $balance;
		$has_both_side 		= 0;
		$start_at 			= $record['created_at'];
		$end_at 			= $record['created_at'];

		list($game_description_id, $game_type_id) = $this->game_api->processUnknownGame($game_description_id, $game_type_id, $gameCode, NULL, NULL);

		$betDetails = $this->utils->encodeJson(array('bet' => $bet_amount, 'rate' => $record['Odds'], 'bet_detail' => ""));
		// $extra['status'] = Game_logs::STATUS_PENDING;
		$extra = array(
			'status' => Game_logs::STATUS_PENDING,
			'trans_amount'	=> 	$bet_amount,
			'odds'	=> 	$record['Odds'],
			'note' => $betDetails,
		);

		$this->utils->debug_log('placeBet records monitor =====================>', $record);
		$this->utils->debug_log('placeBet extra monitor =====================>', $extra);

		$this->game_api->syncGameLogs(
			$game_type_id,  			# game_type_id
			$game_description_id,		# game_description_id
			$gamereference, 			# game_code
			$game_type_id, 				# game_type
			$gamereference, 			# game
			$player_id, 				# player_id
			$player_username, 			# player_username
			$bet_amount, 				# bet_amount
			$result_amount, 			# result_amount
			null,						# win_amount
			null,						# loss_amount
			$after_balance,				# after_balance
			$has_both_side, 			# has_both_side
			$external_uniqueid, 		# external_uniqueid
			$start_at,					# start_at
			$end_at,					# end_at
			$this->response_result_id,	# response_result_id
			Game_logs::FLAG_GAME,		# flag
			$extra
		);

		$data = array(
			'Balance' => $balance,
		);

		return $data;
	}

	public function acceptBet($params) {

		$this->load->model('game_logs');

		$token = isset($params['Token']) ? $params['Token'] : NULL;
		$player = $this->game_api->getPlayerInfoByToken($token);

		if ( ! $player) throw new Exception("Invalid Token", 1);

		$player_id = $player['playerId'];
		$player_username = $player['username'];

		$ticketID = isset($params['TicketID']) ? $params['TicketID'] : NULL;

		$id = $this->ultraplay_game_logs->getIdByUniqueid($ticketID);

		if (empty($id)) {
			throw new Exception("TicketID not found", 1);
		}

		$record = $this->ultraplay_game_logs->getGameRecord($id);

		$this->ultraplay_game_logs->updateGameLog($id, array(
			'IsAccepted' => true,
			'updated_at' => date('Y-m-d H:i:s'),
		));

		$balance_result = $this->game_api->queryPlayerBalance($player_username);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;

		$record = $this->ultraplay_game_logs->getGameRecord($id);

        #prepare game description
        $this->load->model('game_description_model');
        $game_description_data = $this->game_description_model->getGameDescriptionListByGamePlatformIdAndGameCodeArray(ULTRAPLAY_API,$record['game_code']);
        $unknownGame = $this->CI->game_description_model->getUnknownGame(ULTRAPLAY_API);

        $game_description_id    = null;
        $game_type_id           = NULL;
        $gamereference          = NULL;

        if(!empty($game_description_data[$record['game_code']]->id)){
            $game_description_id = $game_description_data[$record['game_code']]->id;
        }

        if(!empty($game_description_data[$record['game_code']]->game_type_id)){
            $game_type_id = $game_description_data[$record['game_code']]->game_type_id;
        }else{
            $game_type_id = $unknownGame->game_type_id;
        }
        #end

		$external_uniqueid 	= $record['TicketID'];
		$bet_amount 		= $record['Stake'];
		$result_amount 		= - $bet_amount;
		$after_balance 		= $balance;
		$has_both_side 		= 0;
		$start_at 			= $record['created_at'];
		$end_at 			= $record['created_at'];

        list($game_description_id, $game_type_id) = $this->game_api->processUnknownGame($game_description_id, $game_type_id, $gameCode, NULL, NULL);

		$betDetails = $this->utils->encodeJson(array('bet' => $bet_amount, 'rate' => $record['Odds'], 'bet_detail' => ""));
		// $extra['status'] = Game_logs::STATUS_ACCEPTED;
		$extra = array(
			'status' => Game_logs::STATUS_ACCEPTED,
			'trans_amount'	=> 	$bet_amount,
			'odds'	=> 	$record['Odds'],
			'bet_details' => $betDetails,
			'odds_type' => 'eu',
		);

		$this->utils->debug_log('acceptBet records monitor =====================>', $record);
		$this->utils->debug_log('acceptBet extra monitor =====================>', $extra);

		$this->game_api->syncGameLogs(
			$game_type_id,  			# game_type_id
			$game_description_id,		# game_description_id
			$gamereference, 			# game_code
			$game_type_id, 				# game_type
			$gamereference, 			# game
			$player_id, 				# player_id
			$player_username, 			# player_username
			$bet_amount, 				# bet_amount
			$result_amount, 			# result_amount
			null,						# win_amount
			null,						# loss_amount
			$after_balance,				# after_balance
			$has_both_side, 			# has_both_side
			$external_uniqueid, 		# external_uniqueid
			$start_at,					# start_at
			$end_at,					# end_at
			$this->response_result_id,	# response_result_id
			Game_logs::FLAG_GAME,		# flag
			$extra
		);

		$data = array(
			'Balance' => $balance,
		);

		return $data;
	}

	public function declineBet($params) {

		$this->load->model('game_logs');

		$token = isset($params['Token']) ? $params['Token'] : NULL;
		$player = $this->game_api->getPlayerInfoByToken($token);

		if ( ! $player) throw new Exception("Invalid Token", 1);

		$player_id = $player['playerId'];
		$player_username = $player['username'];

		$ticketID = isset($params['TicketID']) ? $params['TicketID'] : NULL;

		$id = $this->ultraplay_game_logs->getIdByUniqueid($ticketID);

		if (empty($id)) {
			throw new Exception("TicketID not found", 1);
		}

		$record = $this->ultraplay_game_logs->getGameRecord($id);

		$stake = $record['Stake'];
		$this->add_amount($player_id, $stake);

		$this->ultraplay_game_logs->updateGameLog($id, array(
			'IsAccepted' => false,
			'updated_at' => date('Y-m-d H:i:s'),
		));

		$balance_result = $this->game_api->queryPlayerBalance($player_username);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;

		$record = $this->ultraplay_game_logs->getGameRecord($id);

        #prepare game description
        $this->load->model('game_description_model');
        $game_description_data = $this->game_description_model->getGameDescriptionListByGamePlatformIdAndGameCodeArray(ULTRAPLAY_API,$record['game_code']);
        $unknownGame = $this->CI->game_description_model->getUnknownGame(ULTRAPLAY_API);

        $game_description_id    = null;
        $game_type_id           = NULL;
        $gamereference          = NULL;

        if(!empty($game_description_data[$record['game_code']]->id)){
            $game_description_id = $game_description_data[$record['game_code']]->id;
        }

        if(!empty($game_description_data[$record['game_code']]->game_type_id)){
            $game_type_id = $game_description_data[$record['game_code']]->game_type_id;
        }else{
            $game_type_id = $unknownGame->game_type_id;
        }
        #end

		$external_uniqueid 	= $record['TicketID'];
		$bet_amount 		= $record['Stake'];
		$result_amount 		= - $bet_amount;
		$after_balance 		= $balance;
		$has_both_side 		= 0;
		$start_at 			= $record['created_at'];
		$end_at 			= $record['created_at'];

        list($game_description_id, $game_type_id) = $this->game_api->processUnknownGame($game_description_id, $game_type_id, $gameCode, NULL, NULL);

		$betDetails = $this->utils->encodeJson(array('bet' => $bet_amount, 'rate' => $record['Odds'], 'bet_detail' => ""));
		//$extra['status'] = Game_logs::STATUS_REJECTED;
		$extra = array(
			'status' => Game_logs::STATUS_REJECTED,
			'trans_amount'	=> 	$bet_amount,
			'odds'	=> 	$record['Odds'],
			'bet_details' => $betDetails,
		);

		$this->utils->debug_log('declineBet records monitor =====================>', $record);
		$this->utils->debug_log('declineBet extra monitor =====================>', $extra);

		$this->game_api->syncGameLogs(
			$game_type_id,  			# game_type_id
			$game_description_id,		# game_description_id
			$gamereference, 			# game_code
			$game_type_id, 				# game_type
			$gamereference, 			# game
			$player_id, 				# player_id
			$player_username, 			# player_username
			$bet_amount, 				# bet_amount
			$result_amount, 			# result_amount
			null,						# win_amount
			null,						# loss_amount
			$after_balance,				# after_balance
			$has_both_side, 			# has_both_side
			$external_uniqueid, 		# external_uniqueid
			$start_at,					# start_at
			$end_at,					# end_at
			$this->response_result_id,	# response_result_id
			Game_logs::FLAG_GAME,		# flag
			$extra
		);

		$data = array(
			'Balance' => $balance,
		);

		return $data;
	}

	public function cashoutBet($params) {

		$token = isset($params['Token']) ? $params['Token'] : NULL;
		$player = $this->game_api->getPlayerInfoByToken($token);

		if ( ! $player) throw new Exception("Invalid Token", 1);

		$player_id = $player['playerId'];
		$player_username = $player['username'];

		$ticketID = isset($params['TicketID']) ? $params['TicketID'] : NULL;

		$id = $this->ultraplay_game_logs->getIdByUniqueid($ticketID);

		if (empty($id)) {
			throw new Exception("TicketID not found", 1);
		}

		$cashoutAmount = isset($params['CashoutAmount']) ? $params['CashoutAmount'] : 0;

		$this->add_amount($player_id, $cashoutAmount);

		$cashoutOdds = isset($params['CashoutOdds']) ? $params['CashoutOdds'] : NULL;
		$formattedCashoutOdds = isset($params['FormattedCashoutOdds']) ? $params['FormattedCashoutOdds'] : NULL;
		$activeStake = isset($params['ActiveStake']) ? $params['ActiveStake'] : NULL;

		$this->ultraplay_game_logs->updateGameLog($id, array(
			'CashoutAmount' => $cashoutAmount,
			'CashoutOdds' => $cashoutOdds,
			'FormattedCashoutOdds' => $formattedCashoutOdds,
			'ActiveStake' => $activeStake,
			'updated_at' => date('Y-m-d H:i:s'),
		));

		$balance_result = $this->game_api->queryPlayerBalance($player_username);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;

		$data = array(
			'Balance' => $balance,
		);

		return $data;
	}

	public function settleBet($params) {

		$this->load->model('game_logs');

		$token = isset($params['Token']) ? $params['Token'] : NULL;
		$player = $this->game_api->getPlayerInfoByOldToken($token);

		if ( ! $player) throw new Exception("Invalid Token", 1);

		$player_id = $player['playerId'];
		$player_username = $player['username'];

		$ticketID = isset($params['TicketID']) ? $params['TicketID'] : NULL;

		$id = $this->ultraplay_game_logs->getIdByUniqueid($ticketID);

		if (empty($id)) {
			throw new Exception("TicketID not found", 1);
		}

		$record = $this->ultraplay_game_logs->getGameRecord($id);

		$status = isset($params['Status']) ? $params['Status'] : NULL;

		$extra['status'] = Game_logs::STATUS_SETTLED;

		if ($status == 'Cancelled') { # Cancelled by the admin. In this case the stake should also be refunded.
			$extra['status'] = Game_logs::STATUS_CANCELLED;
		} else if ($status == 'Refund') {
			$extra['status'] = Game_logs::STATUS_REFUND;
		}

		$payout = isset($params['Payout']) ? $params['Payout'] : 0;

		$this->add_amount($player_id, $payout);

		$commitDate = isset($params['CommitDate']) ? $params['CommitDate'] : NULL;
		$selectionsStatus = isset($params['SelectionsStatus']) ? json_encode($params['SelectionsStatus']) : NULL;

		$this->ultraplay_game_logs->updateGameLog($id, array(
			'Payout' => $payout,
			'CommitDate' => $commitDate,
			'SelectionsStatus' => $selectionsStatus,
			'Status' => $status,
			'updated_at' => date('Y-m-d H:i:s'),
		));

		$balance_result = $this->game_api->queryPlayerBalance($player_username);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;

		$record = $this->ultraplay_game_logs->getGameRecord($id);

		$game_id 				= NULL;
		$game_type_id 			= NULL;
		$game_description_id 	= NULL;
		$gamereference 			= NULL;

        #prepare game description
        $this->load->model('game_description_model');
        $game_description_data = $this->game_description_model->getGameDescriptionListByGamePlatformIdAndGameCodeArray(ULTRAPLAY_API,$record['game_code']);
        $unknownGame = $this->CI->game_description_model->getUnknownGame(ULTRAPLAY_API);

        $game_description_id    = null;
        $game_type_id           = NULL;
        $gamereference          = NULL;

        if(!empty($game_description_data[$record['game_code']]->id)){
            $game_description_id = $game_description_data[$record['game_code']]->id;
        }

        if(!empty($game_description_data[$record['game_code']]->game_type_id)){
            $game_type_id = $game_description_data[$record['game_code']]->game_type_id;
        }else{
            $game_type_id = $unknownGame->game_type_id;
        }
        #end

		$external_uniqueid 	= $record['TicketID'];
		$bet_amount 		= $record['Stake'];
		$result_amount 		= $record['Payout'] - $bet_amount;
		$after_balance 		= $balance;
		$has_both_side 		= $bet_amount >= $result_amount && $result_amount > 0 ? 1 : 0;
		$start_at 			= $record['created_at'];
		$end_at 			= $record['CommitDate'];

        list($game_description_id, $game_type_id) = $this->game_api->processUnknownGame($game_description_id, $game_type_id, $gameCode, NULL, NULL);

		$betDetails = $this->utils->encodeJson(array('bet' => $bet_amount, 'rate' => $record['Odds'], 'sports_bet' => $this->setBetDetails($record) ));
		//$extra['status'] = Game_logs::STATUS_SETTLED;

        $extra = array(
			'status' => Game_logs::STATUS_SETTLED,
			'trans_amount'	=> 	$bet_amount,
			'odds'	=> 	$record['Odds'],
			'note' => '',
            'bet_details' => $betDetails,
            'table' => $record['GroupTicketID']
		);

		$this->utils->debug_log('settleBet records monitor =====================>', $record);
		$this->utils->debug_log('settleBet extra monitor =====================>', $extra);

        $this->utils->debug_log('==============> SelectionsDetails value', $record['SelectionsDetails']);
        $sectDetails = json_decode($record['SelectionsDetails'], true);
        /*----- just get the first array for the display */
        $sportsGameFields = array(
            'match_details'     => $sectDetails[0]['Event']['Name'],
            'match_type'        => $sectDetails[0]['Odd']['IsLive'],
            'bet_info'          => $sectDetails[0]['Odd']['Name'],
            'handicap'          => '',
            'bet_type'          => $record['IsCombo'] == '1' ? 'Combo Bets' : 'Single Bets'
        );
        $this->utils->debug_log('==============> Ultraplay Sport Game Fields Value', $sportsGameFields);

		$this->game_api->syncGameLogs(
			$game_type_id,  			# game_type_id
			$game_description_id,		# game_description_id
			$gamereference, 			# game_code
			$game_type_id, 				# game_type
			$gamereference, 			# game
			$player_id, 				# player_id
			$player_username, 			# player_username
			$bet_amount, 				# bet_amount
			$result_amount, 			# result_amount
			null,						# win_amount
			null,						# loss_amount
			$after_balance,				# after_balance
			$has_both_side, 			# has_both_side
			$external_uniqueid, 		# external_uniqueid
			$start_at,					# start_at
			date('Y-m-d H:i:s', strtotime($end_at . ' +9 hours')),					# end_at
			$this->response_result_id,	# response_result_id
			Game_logs::FLAG_GAME,		# flag
			$extra,
            $sportsGameFields
		);

		$data = array(
			'Balance' => $balance,
		);

		return $data;
	}

    public function setBetDetails($field){
        $data = json_decode($field['SelectionsDetails'],true);
        $set = array();
        if(!empty($data)){
            foreach ($data as $key => $game) {
                $set[$key] = array(
                    'yourBet' => $game['Odd']['Name'],
                    'isLive' => $game['Odd']['IsLive'],
                    'odd' => $game['Odd']['Value'],
                    'hdp'=> 'N/A',
                    'htScore'=> '',
                    'eventName' => $game['Event']['Name'],
                    'league' => $game['Tournament']['Name'],
                );
            }
        }
        return $set;
    }

	public function unsettleBet($params) {

		$this->load->model('game_logs');

		$token = isset($params['Token']) ? $params['Token'] : NULL;
		$player = $this->game_api->getPlayerInfoByOldToken($token);
		if ( ! $player) throw new Exception("Invalid Token", 1);

		$player_id = $player['playerId'];
		$player_username = $player['username'];

		$ticketID = isset($params['TicketID']) ? $params['TicketID'] : NULL;

		$id = $this->ultraplay_game_logs->getIdByUniqueid($ticketID);

		if (empty($id)) {
			throw new Exception("TicketID not found", 1);
		}

		$record = $this->ultraplay_game_logs->getGameRecord($id);

		$this->subtract_amount($player_id, $record['Payout']);

		$selectionsStatus = isset($params['SelectionsStatus']) ? json_encode($params['SelectionsStatus']) : NULL;

		$this->ultraplay_game_logs->updateGameLog($id, array(
			'Payout' => NULL,
			'CommitDate' => NULL,
			'SelectionsStatus' => $selectionsStatus,
			'Status' => 'Pending',
			'updated_at' => date('Y-m-d H:i:s'),
		));

		$balance_result = $this->game_api->queryPlayerBalance($player_username);
		$balance = isset($balance_result['success']) && $balance_result['success'] ? $balance_result['balance'] : 0;

		$record = $this->ultraplay_game_logs->getGameRecord($id);

		$game_id 				= NULL;
		$game_type_id 			= NULL;
		$game_description_id 	= NULL;
		$gamereference 			= NULL;

        #prepare game description
        $this->load->model('game_description_model');
        $game_description_data = $this->game_description_model->getGameDescriptionListByGamePlatformIdAndGameCodeArray(ULTRAPLAY_API,$record['game_code']);
        $unknownGame = $this->CI->game_description_model->getUnknownGame(ULTRAPLAY_API);

        $game_description_id    = null;
        $game_type_id           = NULL;
        $gamereference          = NULL;

        if(!empty($game_description_data[$record['game_code']]->id)){
            $game_description_id = $game_description_data[$record['game_code']]->id;
        }

        if(!empty($game_description_data[$record['game_code']]->game_type_id)){
            $game_type_id = $game_description_data[$record['game_code']]->game_type_id;
        }else{
            $game_type_id = $unknownGame->game_type_id;
        }
        #end

		$external_uniqueid 	= $record['TicketID'];
		$bet_amount 		= $record['Stake'];
		$result_amount 		= $record['Payout'] - $record['Stake'];
		$after_balance 		= $balance;
		$has_both_side 		= $bet_amount >= $result_amount && $result_amount > 0 ? 1 : 0;
		$start_at 			= $record['created_at'];
		$end_at 			= $record['CommitDate'];

        list($game_description_id, $game_type_id) = $this->game_api->processUnknownGame($game_description_id, $game_type_id, $gameCode, NULL, NULL);

		$betDetails = $this->utils->encodeJson(array('bet' => $bet_amount, 'rate' => $record['Odds'], 'sports_bet' => $this->setBetDetails($record) ));
		// $extra['status'] = Game_logs::STATUS_PENDING;
		$extra = array(
			'status' => Game_logs::STATUS_PENDING,
			'trans_amount'	=> 	$bet_amount,
			'odds'	=> 	$record['Odds'],
			'note' => '',
            'bet_details' => $betDetails,
            'table' => $record['GroupTicketID']
		);

		$this->utils->debug_log('unsettleBet records monitor =====================>', $record);
		$this->utils->debug_log('unsettleBet extra monitor =====================>', $extra);


		$this->game_api->syncGameLogs(
			$game_type_id,  			# game_type_id
			$game_description_id,		# game_description_id
			$gamereference, 			# game_code
			$game_type_id, 				# game_type
			$gamereference, 			# game
			$player_id, 				# player_id
			$player_username, 			# player_username
			$bet_amount, 				# bet_amount
			$result_amount, 			# result_amount
			null,						# win_amount
			null,						# loss_amount
			$after_balance,				# after_balance
			$has_both_side, 			# has_both_side
			$external_uniqueid, 		# external_uniqueid
			$start_at,					# start_at
			date('Y-m-d H:i:s', strtotime($end_at . ' +9 hours')),					# end_at
			$this->response_result_id,	# response_result_id
			Game_logs::FLAG_GAME,		# flag
			$extra
		);

		$data = array(
			'Balance' => $balance,
		);

		return $data;
	}

	private function subtract_amount($playerId, $amount) {
		$controller = $this;
		$success = $this->lockAndTransForPlayerBalance($playerId, function() use($controller, $playerId, $amount) {
			return $controller->wallet_model->decSubWallet($playerId, $controller->game_api->getPlatformCode(), $amount);
		});
	}

	private function add_amount($playerId, $amount) {
		$controller = $this;
		$success = $this->lockAndTransForPlayerBalance($playerId, function() use($controller, $playerId, $amount) {
			return $controller->wallet_model->incSubWallet($playerId, $controller->game_api->getPlatformCode(), $amount);
		});
	}
}

///END OF FILE////////////