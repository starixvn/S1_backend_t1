<?php
require_once dirname(__FILE__) . '/base_model.php';

/**
 * get game logs
 * external_log_id is a version field
 *
 *
 * @property  Utils utils
 */
class Game_logs extends BaseModel {

	protected $tableName = 'game_logs';

	const FLAG_GAME = 1;
	const FLAG_TRANSACTION = 2;

	const TRANS_TYPE_MAIN_WALLET_TO_SUB_WALLET = 1;
	const TRANS_TYPE_SUB_WALLET_TO_MAIN_WALLET = 2;
	//like tip, player pay to game platform
	const TRANS_TYPE_SUB_WALLET_TO_GAME_PLATFORM = 3;
	const TRANS_TYPE_GAME_PLATFORM_TO_SUB_WALLET = 4;

	const BETINFO_TOPTEN_BET = 1;
	const BETINFO_TOPTEN_WIN = 2;
	const BETINFO_TOPTEN_LOSS = 3;

	const STATUS_SETTLED = 1; //never change, should in game_logs
	const STATUS_PENDING = 2; //waiting settle
	const STATUS_ACCEPTED = 3; //accepted bet
	const STATUS_REJECTED = 4; //rejected bet
	const STATUS_CANCELLED = 5; //cancel
	const STATUS_VOID = 6;
	const STATUS_REFUND = 7;

	const MAXIMUM_ODDS = 1.5;
	const BET_DETAILS_INVALID = "Invalid";

    const BET_TYPE_MULTI_BET='Multi Bet';
    const BET_TYPE_SINGLE_BET='Single Bet';
    const BET_TYPE_OLD_SINGLE_BET='Single Bets';

	const IS_GAMELOGS = 1;
	const IS_GAMELOGS_UNSETTLE = 2;

    const MD5_FIELDS_FOR_GAME_LOGS=[
        'external_uniqueid', 'bet_amount', 'result_amount', 'real_betting_amount', 'trans_amount',
        'player_id', 'bet_at', 'game_platform_id', 'game_description_id', 'end_at', 'status','rent','bet_details'];

    const MD5_FLOAT_AMOUNT_FIELDS=['bet_amount', 'result_amount', 'real_betting_amount', 'trans_amount','rent'];

    const DATE_TYPES = [
    	'default' => 1,
        'settled' => 2,
        'bet' => 3,
        'updated' => 4,
    ];

	function __construct() {
		parent::__construct();
	}

	/**
	 * @param string loginName
	 * @param datetime dateFrom
	 * @param datetime dateTo
	 * @param int platformCode
	 *
	 * @return array
	 */
	function queryGameRecords($dateFrom, $dateTo, $loginName, $platformCode) {
		$qry = $this->db->get_where($this->tableName, array('player_username' => $loginName, "start_at >=" => $dateFrom->format('Y-m-d H:i:s'), "end_at <=" => $dateTo->format('Y-m-d H:i:s'), "game_platform_id" => $platformCode));
		return $this->getMultipleRow($qry);
	}

	/**
	 * @param string loginName
	 * @param datetime dateFrom
	 * @param datetime dateTo
	 *
	 * @return array
	 */
	function getGameLogStatistics($dateFrom, $dateTo, $loginName, $platformCode) {
		$qry = $this->db->get_where($this->tableName, array('player_username' => $loginName, "start_at >=" => $dateFrom->format('Y-m-d H:i:s'), "end_at <=" => $dateTo->format('Y-m-d H:i:s'), "game_platform_id" => $platformCode));
		return $this->getMultipleRow($qry);
	}

	/**
	 *
	 * sync data to game logs, will check md5_sum to update
	 *
	 * @param string $data
	 *
	 * @return array
	 */
	function syncToGameLogs($data, &$execType) {
		if(!isset($data['external_uniqueid'])){
			$this->utils->error_log('lost external_uniqueid', $data);
			return false;
		}
		if(!isset($data['game_platform_id'])){
			$this->utils->error_log('lost game_platform_id', $data);
			return false;
		}

		$status = self::STATUS_SETTLED;
		if (isset($data['status'])) {
			$status = $data['status'];
			unset($data['status']);
		}

		$game_platform_id = $data['game_platform_id'];
		$external_uniqueid = $data['external_uniqueid'];

		//try fill bet_at
		if(!isset($data['bet_at'])){
			$data['bet_at']=$data['end_at'];
		}
		// if(!isset($data['updated_at'])){
		// 	$data['updated_at']=$data['end_at'];
		// }
		$data['updated_at']=$this->utils->getNowForMysql();
		if(!isset($data['flag']) || empty($data['flag'])){
			$data['flag']=self::FLAG_GAME;
		}
		if(!isset($data['rent'])){
	        $data['rent']=0;
	    }
		if(!isset($data['trans_amount'])){
	        $data['trans_amount']=$data['bet_amount'];
	    }

		//auto make md5_sum
		if(empty($data['md5_sum'])){

			$data['status']=$status;

			// if($external_uniqueid=='180807011638749'){
			// 	$this->utils->debug_log($data);

		 //        $arr=[];
		 //        foreach (self::MD5_FIELDS_FOR_GAME_LOGS as $key) {
		 //            if(isset($data[$key])){
		 //            	if(in_array($key, self::MD5_FLOAT_AMOUNT_FIELDS)){
		 //            		$arr[]=sprintf('%.02F', doubleval($data[$key]));
		 //            	}else{
		 //                	$arr[]=$data[$key];
		 //            	}
		 //            }
		 //        }

			// 	$this->utils->debug_log($arr, implode('', $arr));
			// }
			//make one if empty
			$data['md5_sum']=$this->generateMD5SumOneRow($data, self::MD5_FIELDS_FOR_GAME_LOGS, self::MD5_FLOAT_AMOUNT_FIELDS);

			unset($data['status']);
		}

		$updatedOrInserted=false;
		$success=true;

		$execType='ignore';
		if ($status == self::STATUS_SETTLED) {

			$this->db->where('external_uniqueid', $external_uniqueid)
				->where('game_platform_id', $game_platform_id);
			$delUnsettleSucc=$this->runRealDelete('game_logs_unsettle');
			if(!$delUnsettleSucc){
				$this->utils->error_log('delete game_logs_unsettle failed', $external_uniqueid, $game_platform_id, $delUnsettleSucc);
			}

			list($id, $md5_sum) = $this->getIdMD5ByExternalUniqueid($game_platform_id, $external_uniqueid,
				'game_logs');

			//doesn't exist
			if (isset($data['status'])) {
				unset($data['status']);
			}

			if (!empty($id)) {
				// if($is_update_game_logs){
					//compare md5
					if(empty($md5_sum) || $md5_sum!=$data['md5_sum']){
						$this->db->where('id', $id);
						$this->db->set($data);
						$success=$this->runAnyUpdate('game_logs');
						$execType='update';
						$updatedOrInserted=true;
					}else{
						$success=true;
					}
				// }else{
				// 	$success=true;
				// }
			} else {
				if($this->utils->getConfig('enabled_generate_game_logs_id_from_redis')){
					//get id first
					$data['id']=$this->generateGameLogsId();
				}
				$success=$this->insertData('game_logs', $data);
				$execType='insert';
				$updatedOrInserted=true;
			}
		} else {

			if ($status == self::STATUS_REJECTED || $status == self::STATUS_ACCEPTED || $status == self::STATUS_REFUND || $status == self::STATUS_VOID || $status == self::STATUS_CANCELLED) {
				$this->db->where('external_uniqueid', $external_uniqueid)
					->where('game_platform_id', $game_platform_id);
				$delGameLogsSucc=$this->runRealDelete('game_logs');
				if(!$delGameLogsSucc){
					$this->utils->error_log('delete game_logs_unsettle failed', $external_uniqueid, $game_platform_id, $delUnsettleSucc);
				}
			}

			$data['status'] = $status;
			//game platform id with external unqiueid
			list($id, $md5_sum) = $this->getIdMD5ByExternalUniqueid($game_platform_id, $external_uniqueid,
				'game_logs_unsettle');

			if (!empty($id)) {
				// if($is_update_game_logs){
					//compare md5
					if(empty($md5_sum) || $md5_sum!=$data['md5_sum']){
						$this->db->where('id', $id);
						$this->db->set($data);
						$success=$this->runAnyUpdate('game_logs_unsettle');
						$execType='update';
						$updatedOrInserted=true;
					}else{
						$success=true;
					}
				// }else{
				// 	$success= true;
				// }

			} else {
				if($this->utils->getConfig('enabled_generate_game_logs_unsettled_id_from_redis')){
					//get id first
					$data['id']=$this->generateGameLogsUnsettledId();
				}
				$success= $this->insertData('game_logs_unsettle', $data);
				$execType='insert';
				$updatedOrInserted=true;
			}

		}

	    if($updatedOrInserted && $success && $this->utils->isEnabledFeature('enabled_sync_game_logs_stream')){
	        $insertStreamSuccess=$this->insertRowToStream($data);
	        if(!$insertStreamSuccess){
	        	$this->utils->error_log('insert stream failed', $data);
	        }
	    }

	    if(!$success){
	    	$this->utils->error_log('update/insert failed', $data);
	    }

	    return $success;
	}

	public function getIdByExternalUniqueid($uniqueid, $tableName, $date_from=null, $date_to=null) {
		$uniqueid=strval($uniqueid);
		$this->db->select('id')->from($tableName)->where('external_uniqueid', $uniqueid);

		if(!empty($date_from) && !empty($date_to)){
			$this->db->where('end_at >=', $date_from)->where('end_at <=', $date_to);
		}

		return $this->runOneRowOneField('id');
	}

    public function getBetDetailsByUniqueid($uniqueid, $field){
        $data = $this->db->select($field)->from($this->tableName)->where('external_uniqueid', $uniqueid)->get();
        return $data->result_array();
    }

	function update($id, $data) {
		return $this->db->update($this->tableName, $data, array('id' => $id));
	}

	/**
	 * @param string $data
	 *
	 * @return boolean
	 */
	function isItemAlreadyExists($tableName, $uniqueId, $date_from=null, $date_to=null) {

		$id = $this->getIdByExternalUniqueid($uniqueId, $tableName, $date_from, $date_to);

		return !empty($id);
	}

	/**
	 * @param string $data
	 *
	 * @return boolean
	 */
	// function updateToGameLogs($data) {
	// 	$this->db->where('external_uniqueid', $data['external_uniqueid']);
	// 	return $this->db->update($this->tableName, $data);
	// }

	/**
	 * @param datetime dateFrom
	 * @param datetime dateTo
	 * @param int hour
	 *
	 * @return boolean
	 */
	function getAllRecordPerHourOfAllPlayer($dateFrom, $dateTo) {
		// $qry = $this->db->query("SELECT * FROM $this->tableName
		// 							WHERE start_at >= '" . $dateFrom . "'
		// 							AND end_at <= '" . $dateTo . "'
		// 						");
		$this->db->select("player_id, game_platform_id, game_type_id, game_description_id,sum(bet_amount) as betting_amount, sum(result_amount) as result_amount,DATE_FORMAT(end_at,'%Y-%m-%d') as game_date,DATE_FORMAT(end_at,'%H') as hour", false);
		$this->db->where("end_at >=", $dateFrom);
		$this->db->where("end_at <=", $dateTo);
		$this->db->group_by(array("player_id", "game_platform_id", "game_type_id", "game_description_id", "DATE_FORMAT(end_at,'%Y%m%d%H')"));
		$qry = $this->db->get($this->tableName);

		// $this->utils->printLastSQL();

		return $this->getMultipleRow($qry);
	}

	/**
	 * @param string player_id
	 * @param datetime dateFrom
	 * @param datetime dateTo
	 * @param int hour
	 *
	 * @return boolean
	 */
	function getAllRecordPerHourOfPlayer($dateFrom, $dateTo, $player_id) {
		$this->db->select("player_id, game_platform_id, game_type_id, game_description_id,sum(bet_amount) as betting_amount, sum(result_amount) as result_amount,DATE_FORMAT(end_at,'%Y-%m-%d') as game_date,DATE_FORMAT(end_at,'%H') as hour", false);
		$this->db->where("end_at >=", $dateFrom);
		$this->db->where("end_at <=", $dateTo);
		$this->db->where("player_id", $player_id);
		$this->db->group_by(array("player_id", "game_platform_id", "game_type_id", "game_description_id", "DATE_FORMAT(end_at,'%Y%m%d%H')"));
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
	}

	/**
	 * @param datetime dateFrom
	 * @param datetime dateTo
	 * @param int hour
	 *
	 * @return boolean
	 */
	// function getOperatorRecordPerHour($dateFrom, $dateTo) {

	// 	$this->db->where("start_at >=", $dateFrom);
	// 	$this->db->where("end_at <=", $dateTo);
	// 	$qry = $this->db->get($this->tableName);

	// 	// $qry = $this->db->query("SELECT * FROM $this->tableName
	// 	// 							WHERE start_at >= '" . $dateFrom . "'
	// 	// 							AND end_at <= '" . $dateTo . "'
	// 	// 						");
	// 	return $this->getMultipleRow($qry);
	// }

	/**
	 *
	 * @return datetime
	 */
	public function getFirstRecordDateTime() {
		$this->db->order_by('start_at asc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'start_at');
	}

	/**
	 *
	 * @return datetime
	 */
	public function getLastRecordDateTime() {
		$this->db->order_by('end_at desc');
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'end_at');
	}

	// get_agent_bet_info {{{2
	/**
	 *  get bet info according given array of player ids
	 *
	 *  @param  array player ids
	 *  @return array for total bets, lost bets, bets except tie bets
	 */
	private $agent_bet_info;

	public function get_agent_bet_info($agent_id, $player_ids, $game_platform_id = null, $game_type_id = null, $start_date = null, $end_date = null) {

		if ( ! empty($player_ids)) {

			$key = md5(json_encode([
				'agent_id' => $agent_id,
				'player_ids' => $player_ids,
				'start_date' => $start_date,
				'end_date' => $end_date,
			]));

			if ( ! isset($this->agent_bet_info[$key])) {

				$this->db->select('game_platform_id');
				$this->db->select('game_type_id');
				$this->db->select_sum('bet_amount', 'total_bets');
				$this->db->select_sum('trans_amount', 'total_real_bets');
				$this->db->select_sum('(CASE WHEN result_amount > 0 THEN bet_amount ELSE 0 END)', 'winning_bets');
				$this->db->select_sum('(CASE WHEN result_amount < 0 THEN bet_amount ELSE 0 END)', 'lost_bets');
				$this->db->select_sum('(CASE WHEN result_amount = 0 THEN bet_amount ELSE 0 END)', 'tie_bets');
				$this->db->select_sum('(CASE WHEN result_amount >= 0 THEN result_amount ELSE 0 END)', 'gain_sum');
				$this->db->select_sum('(CASE WHEN result_amount < 0 THEN (0 - result_amount) ELSE 0 END)', 'loss_sum');
				$this->db->select_sum('result_amount', 'gain_loss_sum');
				$this->db->from('game_logs');
				$this->db->where_in('player_id', $player_ids);
				$this->db->group_by('game_platform_id');
				$this->db->group_by('game_type_id');

				if ($start_date) {
					$this->db->where('end_at >=', $start_date);
				}

				if ($end_date) {
					$this->db->where('end_at <= ', $end_date);
				}

				$query = $this->db->get();

				$result = $query->result_array();

				$agent_bet_info = array(
					'total_bets' => 0,
					'winning_bets' => 0,
					'lost_bets' => 0,
					'tie_bets' => 0,
					'gain_sum' => 0,
					'loss_sum' => 0,
					'gain_loss_sum' => 0,
					'bet_percentage' => 0,
					'game_types' => array()
				);

				foreach ($result as $row) {

					$row_game_platform_id = $row['game_platform_id'];
					$row_game_type_id = $row['game_type_id'];

					$agent_bet_info['total_bets'] += $row['total_bets'];
					$agent_bet_info['winning_bets'] += $row['winning_bets'];
					$agent_bet_info['lost_bets'] += $row['lost_bets'];
					$agent_bet_info['tie_bets'] += $row['tie_bets'];
					$agent_bet_info['gain_sum'] += $row['gain_sum'];
					$agent_bet_info['loss_sum'] += $row['loss_sum'];
					$agent_bet_info['gain_loss_sum'] += $row['gain_loss_sum'];

					if ($row['total_bets'] > 0) {

						unset($row['game_platform_id'], $row['game_type_id']);

						$agent_bet_info['game_types'][$row_game_type_id] = $row;
					}

				}

				$this->agent_bet_info[$key] = $agent_bet_info;

			}

			if (isset($game_type_id)) {

				if (isset($this->agent_bet_info[$key]['game_types'][$game_type_id])) {

					$game_type_bet_info = $this->agent_bet_info[$key]['game_types'][$game_type_id];
					$game_type_bet_info['bet_percentage'] = round($game_type_bet_info['total_bets'] / $this->agent_bet_info[$key]['total_bets'], 2);

					return $game_type_bet_info;
				}

			} else {
				return $this->agent_bet_info[$key];
			}

		}


		return array(
			'total_bets' => 0,
			'winning_bets' => 0,
			'lost_bets' => 0,
			'tie_bets' => 0,
			'gain_sum' => 0,
			'loss_sum' => 0,
			'gain_loss_sum' => 0,
			'bet_percentage' => 0,
		);

	} // get_agent_bet_info  }}}2

	/**
	 * Used when calculating agency commission, determines the 'percentage' for each game platform and game type
	 * based on bet amount. This percentage is used when calculating corresponding fees.
	 *
	 * @param  String $agent_id   Agent's ID.
	 * @param  Array $player_ids This agent's players' IDs.
	 * @param  String $start_date Commission calculation start date. SQL Formatted DateTime string.
	 * @param  String $end_date   Commission calculation end date. SQL Formatted DateTime string.
	 * @return Array Percentage by game platform ID. $game_platform_id => $game_type_id => $percentage. Percentage values sum up to 1.
	 */
	public function get_bet_percentage_by_platform_and_type($agent_id, $player_ids, $start_date, $end_date) {
		$this->load->model(array('total_player_game_hour'));
		$result = $this->total_player_game_hour->get_bet_percentage_by_platform_and_type($player_ids, $start_date, $end_date);
		$this->utils->debug_log("For agent [$agent_id], between [$start_date] and [$end_date], betting percentage by platform, type, and player: ", $result);
		return $result;
	}

	public function get_players_bet_info($player_ids, $start_date = null, $end_date = null) {

		$times_odds = $this->utils->isEnabledFeature('adjust_rolling_for_low_odds');

		$players_bet_info = array(
			'total_bets' => 0,
			'winning_bets' => 0,
			'lost_bets' => 0,
			'tie_bets' => 0,
			'gain_sum' => 0,
			'loss_sum' => 0,
			'gain_loss_sum' => 0,
			'game_types' => array()
		);

		if ( ! empty($player_ids)) {

			$this->db->select('game_platform_id');
			$this->db->select('game_type_id');
			$this->db->select('player_id');
			if(!$times_odds) {
				$this->db->select_sum('bet_amount', 'total_bets');
				$this->db->select_sum('trans_amount', 'total_real_bets');
				$this->db->select_sum('(CASE WHEN result_amount > 0 THEN bet_amount ELSE 0 END)', 'winning_bets');
				$this->db->select_sum('(CASE WHEN result_amount < 0 THEN bet_amount ELSE 0 END)', 'lost_bets');
				$this->db->select_sum('(CASE WHEN result_amount = 0 THEN bet_amount ELSE 0 END)', 'tie_bets');
				$this->db->select_sum('(CASE WHEN result_amount >= 0 THEN result_amount ELSE 0 END)', 'gain_sum');
				$this->db->select_sum('(CASE WHEN result_amount < 0 THEN (0 - result_amount) ELSE 0 END)', 'loss_sum');
				$this->db->select_sum('result_amount', 'gain_loss_sum');
			} else { # OGP-3365, special algo to prevent small odds bet for rolling
				$this->db->select_sum('(CASE WHEN (ISNULL(odds) OR odds = 0 OR odds > 2) THEN bet_amount ELSE bet_amount * (odds-1) END)', 'total_bets');
				$this->db->select_sum('(CASE WHEN (ISNULL(odds) OR odds = 0 OR odds > 2) THEN trans_amount ELSE trans_amount * (odds-1) END)', 'total_real_bets');
				$this->db->select_sum('(CASE WHEN result_amount > 0 THEN (CASE WHEN (ISNULL(odds) OR odds = 0 OR odds > 2) THEN bet_amount ELSE bet_amount * (odds-1) END) ELSE 0 END)', 'winning_bets');
				$this->db->select_sum('(CASE WHEN result_amount < 0 THEN (CASE WHEN (ISNULL(odds) OR odds = 0 OR odds > 2) THEN bet_amount ELSE bet_amount * (odds-1) END) ELSE 0 END)', 'lost_bets');
				$this->db->select_sum('(CASE WHEN result_amount = 0 THEN (CASE WHEN (ISNULL(odds) OR odds = 0 OR odds > 2) THEN bet_amount ELSE bet_amount * (odds-1) END) ELSE 0 END)', 'tie_bets');
				$this->db->select_sum('(CASE WHEN result_amount >= 0 THEN result_amount ELSE 0 END)', 'gain_sum');
				$this->db->select_sum('(CASE WHEN result_amount < 0 THEN (0 - result_amount) ELSE 0 END)', 'loss_sum');
				$this->db->select_sum('result_amount', 'gain_loss_sum');
			}
			$this->db->select_sum('bet_amount', 'total_bets_display');
			$this->db->select_sum('(CASE WHEN result_amount = 0 THEN bet_amount ELSE 0 END)', 'total_tie_bets_display');

			$this->db->select_sum('(CASE WHEN match_type = 1 THEN bet_amount ELSE 0 END)', 'total_live_bets');
			$this->db->select_sum('(CASE WHEN match_type = 1 THEN trans_amount ELSE 0 END)', 'total_live_real_bets');
			$this->db->select_sum('(CASE WHEN match_type = 1 AND result_amount > 0 THEN bet_amount ELSE 0 END)', 'live_winning_bets');
			$this->db->select_sum('(CASE WHEN match_type = 1 AND result_amount < 0 THEN bet_amount ELSE 0 END)', 'live_lost_bets');
			$this->db->select_sum('(CASE WHEN match_type = 1 AND result_amount = 0 THEN bet_amount ELSE 0 END)', 'live_tie_bets');
			$this->db->select_sum('(CASE WHEN match_type = 1 AND result_amount >= 0 THEN result_amount ELSE 0 END)', 'live_gain_sum');
			$this->db->select_sum('(CASE WHEN match_type = 1 AND result_amount < 0 THEN (0 - result_amount) ELSE 0 END)', 'live_loss_sum');
			$this->db->select_sum('(CASE WHEN match_type = 1 THEN result_amount ELSE 0 END)', 'live_gain_loss_sum');

			$this->db->from('game_logs');
			$this->db->where_in('player_id', $player_ids);
			$this->db->group_by('game_platform_id');
			$this->db->group_by('game_type_id');
			$this->db->group_by('player_id');

			if ($start_date) {
				$this->db->where('end_at >=', $start_date);
			}

			if ($end_date) {
				$this->db->where('end_at <= ', $end_date);
			}

			$query = $this->db->get();

			$result = $query->result_array();

			foreach ($result as $row) {

				$row_game_platform_id = $row['game_platform_id'];
				$row_game_type_id = $row['game_type_id'];
				$row_player_id = $row['player_id'];

				if ($row['total_bets'] > 0 || $row['gain_loss_sum'] > 0) {
					// $row['gain_loss_sum'] > 0 is to cater for the case of free bets

					# UPDATE BET INFO
					$players_bet_info['total_bets'] += $row['total_bets'];
					$players_bet_info['winning_bets'] += $row['winning_bets'];
					$players_bet_info['lost_bets'] += $row['lost_bets'];
					$players_bet_info['tie_bets'] += $row['tie_bets'];
					$players_bet_info['gain_sum'] += $row['gain_sum'];
					$players_bet_info['loss_sum'] += $row['loss_sum'];
					$players_bet_info['gain_loss_sum'] += $row['gain_loss_sum'];

					if ( ! isset($players_bet_info['game_types'][$row_game_type_id])) {
						$players_bet_info['game_types'][$row_game_type_id] = array(
							'game_platform_id' => $row_game_platform_id,
							'total_bets' => 0,
							'winning_bets' => 0,
							'lost_bets' => 0,
							'tie_bets' => 0,
							'gain_sum' => 0,
							'loss_sum' => 0,
							'gain_loss_sum' => 0,
							'players' => array()
						);
					}

					# UPDATE GAME TYPE
					$players_bet_info['game_types'][$row_game_type_id]['total_bets'] += $row['total_bets'];
					$players_bet_info['game_types'][$row_game_type_id]['winning_bets'] += $row['winning_bets'];
					$players_bet_info['game_types'][$row_game_type_id]['lost_bets'] += $row['lost_bets'];
					$players_bet_info['game_types'][$row_game_type_id]['tie_bets'] += $row['tie_bets'];
					$players_bet_info['game_types'][$row_game_type_id]['gain_sum'] += $row['gain_sum'];
					$players_bet_info['game_types'][$row_game_type_id]['loss_sum'] += $row['loss_sum'];
					$players_bet_info['game_types'][$row_game_type_id]['gain_loss_sum'] += $row['gain_loss_sum'];

					# UPDATE PLAYER
					$players_bet_info['game_types'][$row_game_type_id]['players'][$row_player_id] = $row;

				}

			}

		}

		return $players_bet_info;

	} // get_agent_bet_info  }}}2

	// get_player_bet_info {{{2
	/**
	 *  get bet info according given array of player ids
	 *
	 *  @param  array player ids
	 *  @return array for total bets, lost bets, bets except tie bets
	 */
    public function get_player_bet_info($player_id, $start_date = null, $end_date = null) {
    	if($this->utils->getConfig('use_total_minute')){
    		$this->load->model(['total_player_game_minute']);

    		return $this->total_player_game_minute->get_player_bet_info($player_id, $start_date, $end_date);
    	}

		$this->db->select_sum('bet_amount', 'total_bets');
		$this->db->select_sum('trans_amount', 'total_real_bets');
		$this->db->select_sum('(CASE WHEN result_amount < 0 THEN bet_amount ELSE 0 END)', 'lost_bets');
		$this->db->select_sum('(CASE WHEN result_amount = 0 THEN bet_amount ELSE 0 END)', 'tie_bets');
		$this->db->select_sum('(CASE WHEN result_amount > 0 THEN bet_amount ELSE 0 END)', 'win_bets');
		$this->db->from('game_logs');

		$this->db->where('player_id', $player_id);
		if ($start_date) {
			$this->db->where('end_at >=', $start_date);
		}
		if ($end_date) {
			$this->db->where('end_at <= ', $end_date);
		}
		$this->db->where('flag', self::FLAG_GAME);
		// $query = $this->db->get();
		// $result = $query->result_array();

        return $this->runOneRowArray();
    } // get_player_bet_info  }}}2

	public function get_last_activity_date($player_id) {
		$this->db->select('game_platform_id id');
		$this->db->select_max('end_at', 'last_activity_date');
		$this->db->from('game_logs');
		$this->db->where('player_id', $player_id);
		$this->db->group_by('game_platform_id');
		$query = $this->db->get();
		return array_column($query->result_array(), 'last_activity_date', 'id');
	}

	public function getSummary($player_id) {
		if ($this->utils->getConfig('use_total_hour')) {
			$this->load->model(array('total_player_game_hour'));
			return $this->total_player_game_hour->getSummary($player_id);
		}

		$this->db->select('game_platform_id id');
		$this->db->select_sum('bet_amount', 'bet_sum');
		$this->db->select('count(bet_amount) bet_count');
		$this->db->select_sum('(CASE WHEN result_amount >= 0 THEN result_amount ELSE 0 END)', 'gain_sum');
		$this->db->select_sum('(result_amount >= 0)', 'gain_count');
		$this->db->select_sum('(CASE WHEN result_amount < 0 THEN (0 - result_amount) ELSE 0 END)', 'loss_sum');
		$this->db->select_sum('(result_amount < 0)', 'loss_count');
		$this->db->select_sum('result_amount', 'gain_loss_sum');
		$this->db->select('count(result_amount) gain_loss_count');
		$this->db->from('game_logs');
		$this->db->where('player_id', $player_id);
		$this->db->where('flag', self::FLAG_GAME);
		$this->db->group_by('game_platform_id');
		$query = $this->db->get();

		$result = $query->result_array();
		$list = array();
		foreach ($result as $row) {
			$list[$row['id']] = array(
				'bet' => array(
					'sum' => $row['bet_sum'],
					'count' => $row['bet_count'],
					'ave' => $row['bet_count'] ? $row['bet_sum'] / $row['bet_count'] : 0,
				),
				'gain' => array(
					'sum' => $row['gain_sum'],
					'count' => $row['gain_count'],
					'percent' => $row['bet_count'] ? (($row['gain_count'] / $row['bet_count']) * 100) : 0,
					'ave' => $row['gain_count'] ? $row['gain_sum'] / $row['gain_count'] : 0,
				),
				'loss' => array(
					'sum' => $row['loss_sum'],
					'count' => $row['loss_count'],
					'percent' => $row['bet_count'] ? (($row['loss_count'] / $row['bet_count']) * 100) : 0,
					'ave' => $row['loss_count'] ? $row['loss_sum'] / $row['loss_count'] : 0,

				),
				'gain_loss' => array(
					'sum' => $row['gain_loss_sum'],
					'count' => $row['gain_loss_count'],
				),
			);
		}
		return $list ?: false;
	}

	/**
	 * gets specific game log data based on date
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function getSpecificGameLogData($username = null, $game = null, $start_date = null, $end_date = null, $game_code = null) {

		if (strlen($start_date) <= 11) {
			//add time
			$start_date = $start_date . ' 00:00:00';
		}

		if (strlen($end_date) <= 11) {
			//add time
			$end_date = $end_date . ' 23:59:59';
		}

		$this->db->select('game_logs.id, external_system.system_code as game, ifnull(game_description.game_name, game_logs.game) as game_name, ifnull(game_type.game_type_lang, game_logs.game_type) as game_type_lang, game_logs.bet_amount, game_logs.result_amount, game_logs.end_at, game_logs.after_balance, game_logs.player_username', false);
		$this->db->from('game_logs');
		$this->db->join('external_system', 'game_logs.game_platform_id=external_system.id');
		$this->db->join('game_description', 'game_logs.game_description_id=game_description.id', 'left');
		$this->db->join('game_type', 'game_description.game_type_id=game_type.id', 'left');
		$this->db->order_by('end_at', 'DESC');

		if ($start_date) {
			$this->db->where('end_at >=', $start_date);
		}

		if ($end_date) {
			$this->db->where('end_at <=', $end_date);
		}

		if ($username) {
			$this->db->where('player_username', $username);
		}

		if ($game) {
			$this->db->where('game_logs.game_platform_id', $game);
		}

		if ($game_code) {
			$this->db->where('game_logs.game_code', $game_code);
		}

		$query = $this->db->get();

		// $this->utils->printLastSQL();
		return $query->result_array();
	}

	public function getGameLogs_api($player_id, $time_from, $time_to, $flag = 0, $game_provider = null, $game_code = null, $limit = null, $offset = null) {

		$this->db
			->where("end_at BETWEEN '{$time_from}' AND '{$time_to}'", null)
			->where("L.player_id", $player_id)
			->from('game_logs AS L')
			->join('external_system AS S', 'L.game_platform_id = S.id')
			->select('count(*) AS count')
		;

		if (!empty($flag)) { $this->db->where([ 'flag' => $flag ]); }
		if (!empty($game_provider)) { $this->db->where([ 'S.system_code' => $game_provider ]); }
		if (!empty($game_code)) { $this->db->where([ 'L.game_code' => $game_code ]); }

		$count = intval($this->runOneRowOneField('count'));

		// $this->utils->printLastSQL();

		$this->db
			->where("end_at BETWEEN '{$time_from}' AND '{$time_to}'", null)
			->where("L.player_id", $player_id)
			->from('game_logs AS L')
			->join('external_system AS S', 'L.game_platform_id = S.id')
			->join('game_description AS D', 'L.game_description_id = D.id', 'left')
			->join('game_type AS T', 'D.game_type_id = T.id', 'left')
			->join('player AS P', 'L.player_id = P.playerId', 'left')
			->join('affiliates AS AF', 'P.affiliateId = AF.affiliateId', 'left')
			->select([ // 'L.id',
				// 'S.system_code as game', 'D.game_code',
				'L.end_at AS payout_date',
				'L.start_at AS bet_date',
				'L.player_username',
				'AF.username AS affiliate_username',
				'P.groupName',
				'P.levelName',
				'S.system_code AS game_provider',
				'ifnull(T.game_type_lang, L.game_type) as game_type',
				'ifnull(D.game_name, L.game) as game_name',
				'L.game_code',
				'L.trans_amount	AS real_bet',
				'L.bet_amount	AS valid_bet',
				'L.result_amount',
				'L.bet_amount + L.result_amount AS bet_plus_result_amount',
				'L.win_amount',
				'L.loss_amount',
				'L.after_balance',
				'L.trans_amount',
				'L.table AS round_no',
				'L.note',
				'L.bet_details',
				'L.bet_type',
				'L.flag',
				// 'S.id AS gameproviderId',
				// 'L.external_uniqueid AS unique_id',
				// 'L.match_type', 'L.match_details', 'L.handicap',
				// 'L.odds_type', 'L.odds', 'L.rent'
			])
			->order_by('end_at', 'DESC')
		;

		// selects
		if (!empty($flag))	{ $this->db->where([ 'flag' => $flag ]); }
		if (!empty($game_provider)) { $this->db->where([ 'S.system_code' => $game_provider ]); }
		if (!empty($game_code)) { $this->db->where([ 'L.game_code' => $game_code ]); }

		// limits
		if (!empty($limit))	{ $this->db->limit($limit, $offset);  }

		$rows = $this->runMultipleRowArray();

		$this->utils->printLastSQL();

		$flag_lang = [ '1' => 'Game', '2' => 'Transaction' ];
		if (!empty($rows)) {
			foreach ($rows as & $row) {
				$row['game_type'] = lang($row['game_type']);
				$row['game_name'] = lang($row['game_name']);
				$row['flag'] = isset($flag_lang[$row['flag']]) ? $flag_lang[$row['flag']] : $row['flag'];
				$row['player_level'] = sprintf("%s - %s", lang($row['groupName']), lang($row['levelName']));
				unset($row['levelName']);
				unset($row['groupName']);
			}
		}

		return [ 'row_count_total' => $count, 'limit' => $limit, 'offset' => $offset, 'rows' => $rows ];
	}

	public function getPlayerCurrentBetByGamePlatformId($playerId, $gamePlatformId, $dateTimeFrom, $dateTimeTo = null, $promoId = null) {
		if ($this->utils->getConfig('use_total_minute')) {
			$this->load->model(['total_player_game_minute']);
			//call total minute
			return $this->total_player_game_minute->getPlayerCurrentBetByGamePlatformId($playerId, $gamePlatformId, $dateTimeFrom, $dateTimeTo, $promoId);
		}

		$totalBetAmount = 0;
		if ($dateTimeFrom != null) {

			if ($dateTimeTo == null) {
				$dateTimeTo = $this->utils->getNowForMysql();
			}

			$this->db->select('sum(bet_amount) as totalBetAmount', false)
				->from('game_logs')
				->where('game_platform_id', $gamePlatformId)
				->where('end_at >=', $dateTimeFrom)
				->where('end_at <=', $dateTimeTo)
				->where('player_id', $playerId)
			;
			if ($promoId) {
				$playerGames = $this->getPlayerGames($promoId);
				$this->db->where_in('game_description_id', $playerGames);
			}

			$totalBetAmount = $this->runOneRowOneField('totalBetAmount');

			// $this->utils->printLastSQL();

		}
		return $totalBetAmount;

		// $qry = $this->db->get();
		// $rows = array(array('totalBetAmount' => 0));
		// if ($qry->num_rows() > 0) {
		// 	$rows = $qry->result_array();
		// }

		// return $rows;
	}

	public function getPlayerCurrentBet($playerId, $dateTimeFrom, $dateTimeTo = null, $promoId = null) {
		if ($this->utils->getConfig('use_total_minute')) {
			$this->load->model(['total_player_game_minute']);
			//call total minute
			return $this->total_player_game_minute->getPlayerCurrentBet($playerId, $dateTimeFrom, $dateTimeTo, $promoId);
		}

		$totalBetAmount = 0;
		if ($dateTimeFrom != null) {

			if ($dateTimeTo == null) {
				$dateTimeTo = $this->utils->getNowForMysql();
			}

			$this->db->select('sum(bet_amount) as totalBetAmount', false)
				->from('game_logs')
				->where('end_at >=', $dateTimeFrom)
				->where('end_at <=', $dateTimeTo)
				->where('player_id', $playerId)
			;
			if ($promoId) {
				$playerGames = $this->getPlayerGames($promoId);
				$this->db->where_in('game_description_id', $playerGames);
			}

			$totalBetAmount = $this->runOneRowOneField('totalBetAmount');

			// $this->utils->printLastSQL();

		}
		return $totalBetAmount;

		// $qry = $this->db->get();
		// $rows = array(array('totalBetAmount' => 0));
		// if ($qry->num_rows() > 0) {
		// 	$rows = $qry->result_array();
		// }

		// return $rows;
	}

	public function getPlayerCurrentBetByPlatform($playerId, $dateTimeFrom, $dateTimeTo = null, $promoId = null, $game_platform = null){
        if ($this->utils->getConfig('use_total_minute')) {
            $this->load->model(['total_player_game_minute']);
            //call total minute
            return $this->total_player_game_minute->getPlayerCurrentBetByPlatform($playerId, $dateTimeFrom, $dateTimeTo, $promoId, $game_platform);
        }

        $totalBetAmount = 0;
        if ($dateTimeFrom != null) {

            if ($dateTimeTo == null) {
                $dateTimeTo = $this->utils->getNowForMysql();
            }

            $this->db->select('sum(bet_amount) as totalBetAmount', false)
                ->from('game_logs')
                ->where('end_at >=', $dateTimeFrom)
                ->where('end_at <=', $dateTimeTo)
                ->where('player_id', $playerId)
            ;
            if ($promoId) {
                $playerGames = $this->getPlayerGames($promoId);
                $this->db->where_in('game_description_id', $playerGames);
            }
            if ($game_platform) {
                $this->db->where_in('game_platform_id', (is_array($game_platform)) ? implode(',', $game_platform) : $game_platform);
            }

            $totalBetAmount = $this->runOneRowOneField('totalBetAmount');

            // $this->utils->printLastSQL();

        }
        return $totalBetAmount;

    }

	private function extractBalance($balanceStr) {
		//extract balance
		$arr = explode('|', $balanceStr);
		$balance = null; // $arr[0];
		if (count($arr) >= 3) {
			$balance = floatval($arr[2]);
		}
		return $balance;
	}

	/**
	 *
	 * add lock
	 *
	 * @param  [type]         $gamePlatformId from external system and constants.php
	 * @param  [type]         $playerId
	 * @param  \DateTime|null $dateTimeFrom
	 * @param  \DateTime|null $dateTimeTo
	 * @return bool
	 */
	public function convertGameLogsToSubWallet($gamePlatformId, $playerId = null,
		\DateTime $dateTimeFrom = null, \DateTime $dateTimeTo = null) {

		$mark = 'benchConvertGameLogsToSubWallet' . $gamePlatformId;
		$this->utils->markProfilerStart($mark);
		$this->load->model(array('wallet_model'));
		//game_logs to daily_balance
		$this->db->select("player_id, " .
			"MAX(CONCAT(end_at, '|', id ,'|', after_balance)) as balance", false);

		// if ($dateTimeFrom && $dateTimeTo) {
		// 	$fromStr = $this->utils->formatDateTimeForMysql($dateTimeFrom);
		// 	$toStr = $this->utils->formatDateTimeForMysql($dateTimeTo);
		// 	$this->db->where("end_at >=", $fromStr);
		// 	$this->db->where("end_at <=", $toStr);
		// }
		if ($dateTimeFrom) {
			$fromStr = $this->utils->formatDateTimeForMysql($dateTimeFrom);
			$this->db->where("end_at >=", $fromStr);
		}
		if (!empty($playerId)) {
			$this->db->where('player_id', $playerId);
		}
		$this->db->where('game_platform_id', $gamePlatformId);
		$this->db->group_by(array("player_id"));
		$qry = $this->db->get('game_logs');

		// $this->utils->printLastSQL();
		// $this->db->last_query();
		$rows = $this->getMultipleRow($qry);
		if ($rows) {
			$this->utils->debug_log('start convert game logs ' . $gamePlatformId);
			$cnt = 0;
			$cntFailed = 0;
			$sum = 0;
			$self = $this;
			foreach ($rows as $row) {
				$cnt++;
				$balance = $this->extractBalance($row->balance);
				if ($balance !== null) {
					$sum += $balance;
					$playerId = $row->player_id;
					$success = $this->CI->wallet_model->lockAndTransForPlayerBalance($playerId, function ()
						 use ($self, $playerId, $row, $gamePlatformId, $balance) {
							// $this->utils->debug_log('balance', $balance, $row->player_id);
							return $self->wallet_model->syncSubWallet($row->player_id, $gamePlatformId, $balance);
						});

					if (!$success) {
						$cntFailed++;
					}
				}
				// $this->syncBalanceInfo($row->player_id, $row->game_platform_id, $row->game_date, $balance);
				// //main + sub
				// $totalBal = $this->calcTotalBalance($row->player_id, $row->game_date);
				// $this->syncMainBalance($row->player_id, $row->game_date, $totalBal);
			}
			$this->utils->debug_log('convert game logs ' . $gamePlatformId, 'convert game logs', $cnt, 'sum', $sum, 'failed', $cntFailed);
		}

		$this->utils->markProfilerEndAndPrint($mark);

		return true;
	}

	public function insertGameTransaction($gamePlatformId, $playerId, $gameUsername, $afterBalance, $amount, $response_result_id, $transType, $created_at = null) {
		$this->load->model(array('game_description_model'));

		$gameInfo = $this->game_description_model->getUnknownGame($gamePlatformId);
		$unknowGameDescId = isset($gameInfo->id)?$gameInfo->id:0;
		$unknowGameTypeId = isset($gameInfo->game_type_id)?$gameInfo->game_type_id:0;

		if (empty($created_at)) {
			$created_at = $this->utils->getNowForMysql();
		}

		if (empty($unknowGameDescId)) {
			$unknowGameDescId = 0;
		}
		if (empty($unknowGameTypeId)) {
			$unknowGameTypeId = 0;
		}

		$this->db->set('bet_amount', 0)
			->set('result_amount', 0)
			->set('response_result_id', $response_result_id)
			->set('player_id', $playerId)
			->set('player_username', $gameUsername)
			->set('game', 'game transaction')
			->set('game_description_id', $unknowGameDescId)
			->set('game_type_id', $unknowGameTypeId)
			->set('game_platform_id', $gamePlatformId)
			->set('start_at', $created_at)
			->set('end_at', $created_at)
			->set('trans_amount', $amount)
			->set('after_balance', $afterBalance)
			->set('note', 'player ' . $playerId . ' ' . $gameUsername . ' change balance to ' . $afterBalance . ' amount:' . $amount . ' type:' . $transType)
			->set('flag', self::FLAG_TRANSACTION)
			->set('trans_type', $transType)
			->insert($this->tableName);

		return $this->db->insert_id();
	}

	public function selectNewestGameLogs($rows) {
		$today = date("Y-m-d H:i:s");
		$sql = 'SELECT * FROM game_logs  WHERE end_at <= ? order by end_at DESC LIMIT ? ';
		$query = $this->db->query($sql, array($today, $rows));
		return array(
			'total' => $query->num_rows(),
			'data' => $query->result_array(),
		);
	}

	public function getTotalBetsWinsLossByPlayers($playerIds, $dateTimeFrom = null, $dateTimeTo = null, $gamePlatformId = null, $promoruleId = null) {
        $this->utils->debug_log("use_total_minute" . $this->utils->getConfig('use_total_minute'));
		if ($this->utils->getConfig('use_total_minute')) {
			$this->load->model(array('total_player_game_minute'));
			return $this->total_player_game_minute->getTotalBetsWinsLossByPlayers($playerIds, $dateTimeFrom, $dateTimeTo, $gamePlatformId, $promoruleId);

		} else {
			return $this->getTotalBetsWinsLossByPlayersForce($playerIds, $dateTimeFrom, $dateTimeTo, $gamePlatformId, $promoruleId);

			// $totalBet = 0;
			// $totalWin = 0;
			// $totalLoss = 0;

			// if (!empty($playerIds)) {
			// 	$this->db->select_sum('game_logs.bet_amount', 'total_bet')
			// 		->select_sum('IF(game_logs.result_amount < 0, ABS(game_logs.result_amount) , 0)', 'total_loss')
			// 		->select_sum('IF(game_logs.result_amount > 0, game_logs.result_amount , 0)', 'total_win')
			// 		->from('game_logs')
			// 		->where('flag', self::FLAG_GAME)
			// 	;

			// 	if (count($playerIds) == 1) {
			// 		$this->db->where('player_id', $playerIds[0]);
			// 	} else {
			// 		$this->db->where_in('player_id', $playerIds);
			// 	}

			// 	if (!empty($gamePlatformId)) {
			// 		if (is_array($gamePlatformId)) {
			// 			$this->db->where_in('game_platform_id', $gamePlatformId);
			// 		} else {
			// 			$this->db->where('game_platform_id', $gamePlatformId);
			// 		}
			// 	}
			// 	if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
			// 		$this->db->where('end_at >=', $dateTimeFrom);
			// 		$this->db->where('end_at <=', $dateTimeTo);
			// 	}
			// 	if (!empty($promorulesId)) {
			// 		$this->db->join('promorulesgamebetrule', 'promorulesgamebetrule.game_description_id=game_logs.game_description_id');
			// 		$this->db->where('promorulesgamebetrule.promoruleId', $promorulesId);
			// 	}

			// 	$row = $this->runOneRow();
			// 	$totalBet = $row->total_bet;
			// 	$totalWin = $row->total_win;
			// 	$totalLoss = $row->total_loss;
			// 	// $this->utils->printLastSQL();
			// }
			// return array($totalBet, $totalWin, $totalLoss);
		}
	}

    public function getCloseLossByPlayer($playerId, $dateTimeFrom, $dateTimeTo){
        $totalBetsWinsLoss = $this->getTotalBetsWinsLossByPlayers($playerId, $dateTimeFrom, $dateTimeTo);
        $totalWin = $totalBetsWinsLoss['1'];
        $totalLoss = $totalBetsWinsLoss['2'];

        return $totalWin - $totalLoss;
    }

    public function getTotalRakeByPlayers($playerIds, $dateTimeFrom = null, $dateTimeTo = null, $gamePlatformId = null){
        $this->load->model(['game_logs']);

		$rake_table='total_rake_player_ids_tmp_' . time() . random_string('alnum');
		$this->utils->debug_log('create rake table '.$rake_table, strtotime($dateTimeFrom), strtotime($dateTimeTo));

		// //drop table
		// $sql='drop table if exists '.$rake_table;
		// $this->runRawUpdateInsertSQL($sql);

		//create again
		// $sql='create table '.$rake_table.' select player_id from game_logs limit 0';//this will give a GTID consistency violation.
		// $sql='create table '.$rake_table.' like game_logs';
		$sql = 'CREATE TEMPORARY TABLE '.$rake_table.' select player_id FROM game_logs LIMIT 0';
		$this->runRawUpdateInsertSQL($sql);

		//inserting data
		$data = array();
		if(is_array($playerIds)){
			$playerIds = array_unique($playerIds);//filter duplicate id's
			if(!empty($playerIds)){
				foreach ($playerIds as $key => $id) {
					$data[] = array("player_id" => $id);
				}
			}
		} else {
			$data[] = array("player_id" => $playerIds);
		}
		if(!empty($data)){
			$sql = $this->db->insert_batch($rake_table, $data);
		}


		//selecting data
		$sqlTime = '';
		if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
            $sqlTime='gl.end_at >= ? and gl.end_at <= ?';
        }

        $sqlPlatform = '';
        if(!empty($gamePlatformId)){
        	if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
	            $sqlPlatform .= ' and ';
	        }

	        if (is_array($gamePlatformId)) {
	        	$gamePlatformId = implode(',', $gamePlatformId);
	        	$sqlPlatform .= 'gl.game_platform_id IN (?)';
            } else {
                $sqlPlatform .= 'gl.game_platform_id = ?';
            }
        }

$sql = <<<EOD
SELECT sum(gl.rent) as total_rake
FROM {$this->tableName} as gl
JOIN {$rake_table} as rk ON gl.player_id = rk.player_id 
WHERE

{$sqlTime}
{$sqlPlatform}
EOD;

		$params=[$dateTimeFrom,$dateTimeTo,$gamePlatformId];
        $result = $this->runRawSelectSQLArray($sql, $params);
        $totalRake = 0;
        if(isset($result[0]['total_rake'])){
        	$totalRake = $result[0]['total_rake'];
        }


        //drop table after process
		$sql='drop table if exists '.$rake_table;
		$this->runRawUpdateInsertSQL($sql);

        return $totalRake;
    	/*
        $totalRake = 0;
        if(!empty($playerIds)) {
            $this->db->select_sum('game_logs.rent', 'total_rake')
                ->from($this->tableName)
                ->where('flag', self::FLAG_GAME);

            if (count($playerIds) == 1) {
                $this->db->where('player_id', $playerIds[0]);
            } else {
                $this->db->where_in('player_id', $playerIds);
            }

            if (!empty($gamePlatformId)) {
                if (is_array($gamePlatformId)) {
                    $this->db->where_in('game_platform_id', $gamePlatformId);
                } else {
                    $this->db->where('game_platform_id', $gamePlatformId);
                }
            }
            if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
                $this->db->where('end_at >=', $dateTimeFrom);
                $this->db->where('end_at <=', $dateTimeTo);
            }
            $row = $this->runOneRow();
            $totalRake = $row->total_rake;
            if (empty($totalRake)) {
                $totalRake = 0;
            }
        }
        return $totalRake;
        */
    }

	public function getTotalBetsWinsLossByPlayersForce($playerIds, $dateTimeFrom = null, $dateTimeTo = null, $gamePlatformId = null, $promoruleId = null) {
		$totalBet = 0;
		$totalWin = 0;
		$totalLoss = 0;
		$totalTransAmount = 0;

		// $this->utils->debug_log('playerIds', $playerIds, 'dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo);

		if (!empty($playerIds)) {
			$this->db->select_sum('game_logs.bet_amount', 'total_bet')
				->select_sum('IF(game_logs.result_amount < 0, ABS(game_logs.result_amount) , 0)', 'total_loss')
				->select_sum('IF(game_logs.result_amount > 0, game_logs.result_amount , 0)', 'total_win')
				->select_sum('game_logs.trans_amount', 'total_trans_amount')
				->from('game_logs')
				->where('flag', self::FLAG_GAME)
			;

			if (count($playerIds) == 1) {
				$this->db->where('player_id', $playerIds[0]);
			} else {
				$this->db->where_in('player_id', $playerIds);
			}

			if (!empty($gamePlatformId)) {
				if (is_array($gamePlatformId)) {
					$this->db->where_in('game_platform_id', $gamePlatformId);
				} else {
					$this->db->where('game_platform_id', $gamePlatformId);
				}
			}
			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$this->db->where('end_at >=', $dateTimeFrom);
				$this->db->where('end_at <=', $dateTimeTo);
			}
			if (!empty($promorulesId)) {
				$this->db->join('promorulesgamebetrule', 'promorulesgamebetrule.game_description_id=game_logs.game_description_id');
				$this->db->where('promorulesgamebetrule.promoruleId', $promorulesId);
			}
			$row = $this->runOneRow();

			// $this->utils->printLastSQL();

			$totalBet = $row->total_bet;
			$totalWin = $row->total_win;
			$totalLoss = $row->total_loss;
			$totalTransAmount = $row->total_trans_amount;

			if (empty($totalBet)) {
				$totalBet = 0;
			}
			if (empty($totalWin)) {
				$totalWin = 0;
			}
			if (empty($totalLoss)) {
				$totalLoss = 0;
			}
			if (empty($totalTransAmount)) {
				$totalTransAmount = 0;
			}
			// $this->utils->printLastSQL();
		}
		return array($totalBet, $totalWin, $totalLoss, $totalTransAmount);
	}

	/**
	 * add by spencer.kuo 2017.05.15
	*/
	public function getTotalBetsWinsLossByPlayersByGameType($playerIds, $dateTimeFrom = null, $dateTimeTo = null, $gamePlatformId, $gameTypeId = null, $promoruleId = null) {
		$totalBet = 0;
		$totalWin = 0;
		$totalLoss = 0;
		$totalTransAmount = 0;

		// $this->utils->debug_log('playerIds', $playerIds, 'dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo);

		if (!empty($playerIds)) {
			$this->db->select_sum('game_logs.bet_amount', 'total_bet')
				->select_sum('IF(game_logs.result_amount < 0, ABS(game_logs.result_amount) , 0)', 'total_loss')
				->select_sum('IF(game_logs.result_amount > 0, game_logs.result_amount , 0)', 'total_win')
				->select_sum('game_logs.trans_amount', 'total_trans_amount')
				->from('game_logs')
				->where('flag', self::FLAG_GAME)
			;

			if (count($playerIds) == 1) {
				$this->db->where('player_id', $playerIds[0]);
			} else {
				$this->db->where_in('player_id', $playerIds);
			}

			if (!empty($gamePlatformId)) {
				if (is_array($gamePlatformId)) {
					$this->db->where_in('game_platform_id', $gamePlatformId);
				} else {
					$this->db->where('game_platform_id', $gamePlatformId);
				}
			}

			if (!empty($gameTypeId)) {
				if (is_array($gameTypeId)) {
					$this->db->where_in('game_type_id', $gameTypeId);
				} else {
					$this->db->where('game_type_id', $gameTypeId);
				}
			}

			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$this->db->where('end_at >=', $dateTimeFrom);
				$this->db->where('end_at <=', $dateTimeTo);
			}
			if (!empty($promorulesId)) {
				$this->db->join('promorulesgamebetrule', 'promorulesgamebetrule.game_description_id=game_logs.game_description_id');
				$this->db->where('promorulesgamebetrule.promoruleId', $promorulesId);
			}
			$row = $this->runOneRow();

			// $this->utils->printLastSQL();

			$totalBet = $row->total_bet;
			$totalWin = $row->total_win;
			$totalLoss = $row->total_loss;
			$totalTransAmount = $row->total_trans_amount;

			if (empty($totalBet)) {
				$totalBet = 0;
			}
			if (empty($totalWin)) {
				$totalWin = 0;
			}
			if (empty($totalLoss)) {
				$totalLoss = 0;
			}
			if (empty($totalTransAmount)) {
				$totalTransAmount = 0;
			}
			// $this->utils->printLastSQL();
		}
		return array($totalBet, $totalWin, $totalLoss, $totalTransAmount);
	}

	/**
	 * add by spencer.kuo 2017.05.15
	 */
	public function filterActivePlayersByPlayersId($playerIds, $minimumBets, $minimumBetsTimes, $dateTimeFrom = null, $dateTimeTo = null) {

		if (!empty($playerIds)) {
			$this->db
				->select('game_logs.player_id')
				->select('count(game_logs.player_id) AS total_betcount')
				->select_sum('game_logs.bet_amount', 'total_bet')
				->from('game_logs')
				->where('game_logs.flag', self::FLAG_GAME)
			;

			if (count($playerIds) == 1) {
				$this->db->where('game_logs.player_id', $playerIds[0]);
			} else {
				$this->db->where_in('game_logs.player_id', $playerIds);
			}

			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$this->db->where('game_logs.end_at >=', $dateTimeFrom);
				$this->db->where('game_logs.end_at <=', $dateTimeTo);
			}

			$this->db->group_by('game_logs.player_id');
			$this->db->having('total_bet >=', $minimumBets);
			$this->db->having('total_betcount >= ', $minimumBetsTimes);

			$rows = $this->runMultipleRowArray() ?: array();
			return array_column($rows, 'player_id');
		}
		return array();
	}

	public function getTotalBetsWinsLossByAgents($agentIds, $dateTimeFrom = null, $dateTimeTo = null, $gamePlatformId = null, $promoruleId = null) {
		if ($this->utils->getConfig('use_total_minute')) {
			$this->load->model(array('total_player_game_minute'));
			return $this->total_player_game_minute->getTotalBetsWinsLossByAgents($agentIds, $dateTimeFrom, $dateTimeTo, $gamePlatformId, $promoruleId);

		} else {

			$db=$this->getReadOnlyDB();

			$totalBet = 0;
			$totalWin = 0;
			$totalLoss = 0;

			if (!empty($agentIds)) {
				$db->select_sum('game_logs.bet_amount', 'total_bet')
					->select_sum('IF(game_logs.result_amount < 0, ABS(game_logs.result_amount) , 0)', 'total_loss')
					->select_sum('IF(game_logs.result_amount > 0, game_logs.result_amount , 0)', 'total_win')
					->from('game_logs')
					->join('player', 'player.playerId = game_logs.player_id')
					->where('flag', self::FLAG_GAME)
				;

				if (count($agentIds) == 1) {
					$this->utils->debug_log(['agentIds' => $agentIds, 'agentId' => $agentIds[0]]);
					$db->where('agent_id', $agentIds[0]);
				} else {
					$this->utils->debug_log('GAMELOGSWHEREINAGENTID:', $agentIds);
					$db->where_in('agent_id', $agentIds);
				}

				if (!empty($gamePlatformId)) {
					if (is_array($gamePlatformId)) {
						$db->where_in('game_platform_id', $gamePlatformId);
					} else {
						$db->where('game_platform_id', $gamePlatformId);
					}
				}
				if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
					$db->where('end_at >=', $dateTimeFrom);
					$db->where('end_at <=', $dateTimeTo);
				}
				if (!empty($promorulesId)) {
					$db->join('promorulesgamebetrule', 'promorulesgamebetrule.game_description_id=game_logs.game_description_id');
					$db->where('promorulesgamebetrule.promoruleId', $promorulesId);
				}

				$row = $this->runOneRow($db);
				$totalBet = $row->total_bet;
				$totalWin = $row->total_win;
				$totalLoss = $row->total_loss;
				$this->utils->debug_log($db->last_query());
				// $this->utils->printLastSQL();
			}
			return array($totalBet, $totalWin, $totalLoss);
		}
	}

	public function getGameBetInfo($type, $dateTimeFrom, $dateTimeTo, $gamePlatformId, $gameDescriptionId) {
		$this->db->select('bet_amount,player_id,start_at,end_at,game_description_id,result_amount');

		if ($type == self::BETINFO_TOPTEN_WIN) {
			$this->db->where('result_amount > 0');
		} elseif ($type == self::BETINFO_TOPTEN_LOSS) {
			$this->db->where('result_amount < 0');
		}
		if (!empty($gamePlatformId)) {
			$this->db->where('game_platform_id', $gamePlatformId);
		}
		if (!empty($gameDescriptionId)) {
			$this->db->where('game_description_id', $gameDescriptionId);
		}
		if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
			$this->db->where('end_at >=', $dateTimeFrom);
			$this->db->where('end_at <=', $dateTimeTo);
		}
		$this->db->order_by('end_at', 'desc');
		$this->db->limit(10);
		$qry = $this->db->get($this->tableName);
		return $this->getMultipleRow($qry);
	}

	const DEFAULT_MIN_AMOUNT_FOR_TOPTEN = 900;
	public function getTopTenWinPlayers($gameDescriptionId) {
		$this->load->model('player_model');
		$dateFrom = new DateTime('7 days ago');
		$dateTimeFrom = $dateFrom->format('YmdH');
		$dateTo = new DateTime();
		$dateTimeTo = $dateTo->format('YmdH');

		$min_win_amount = $this->utils->getConfig('min_win_amount_for_newest') == 0 || $this->utils->getConfig('min_win_amount_for_newest') == null ? self::DEFAULT_MIN_AMOUNT_FOR_TOPTEN : $this->utils->getConfig('min_win_amount_for_newest');
		$this->db->select('player_id as username');
		$this->db->select_sum('IF(win_amount >= 900, win_amount , 0)', 'total_win_amount');
		$this->db->group_by('player_id');
		$this->db->where('game_description_id', $gameDescriptionId);
		$this->db->having('total_win_amount >=', $min_win_amount);

		if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
			$this->db->where('date_hour >=', $dateTimeFrom);
			$this->db->where('date_hour <=', $dateTimeTo);
		}

		$this->db->order_by('date_hour', 'desc');
		$this->db->limit(10);
		$qry = $this->db->get('total_player_game_hour');
		if ($qry && $qry->num_rows() > 0) {
			foreach ($qry->result_array() as $row) {
				$row['username'] = $this->player_model->getUsernameById($row['username']);
				$data[] = $row;
			}
			return $data;
		}
	}

	public function getNewestTenWinPlayers($gameDescriptionId) {
		$this->load->model('player_model');
		$this->db->select('player_id as username,win_amount');
		$this->db->where('win_amount >', 0);
		$this->db->where('flag', self::FLAG_GAME);
		$this->db->order_by('end_at', 'desc');
		$this->db->limit(10);
		$this->db->where('game_description_id', $gameDescriptionId);

		$qry = $this->db->get($this->tableName);
		if ($qry && $qry->num_rows() > 0) {
			foreach ($qry->result_array() as $row) {
				$row['username'] = $this->player_model->getUsernameById($row['username']);
				$data[] = $row;
			}
			return $data;
		}
	}

	public function getPlayerTotalBetsWinsLossByDatetime($playerId, $dateTimeFrom, $dateTimeTo, $gamePlatformId = null) {
		return $this->getTotalBetsWinsLossByPlayers(array($playerId), $dateTimeFrom, $dateTimeTo, $gamePlatformId);

		// $totalBet = 0;
		// $totalWin = 0;
		// $totalLoss = 0;

		// $this->db->select_sum('bet_amount', 'total_bet')
		// 	->select_sum('IF(result_amount < 0, (0 - result_amount) , 0)', 'total_loss')
		// 	->select_sum('IF(result_amount > 0, result_amount , 0)', 'total_win')
		// 	->from('game_logs')
		// 	->where('player_id', $playerId)
		// ;
		// if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
		// 	$this->db->where('end_at >=', $dateTimeFrom);
		// 	$this->db->where('end_at <=', $dateTimeTo);
		// }

		// $row = $this->runOneRow();
		// $totalBet = $row->total_bet;
		// $totalWin = $row->total_win;
		// $totalLoss = $row->total_loss;
		// // $this->utils->printLastSQL();
		// return array($totalBet, $totalWin, $totalLoss);
	}

	/**
	 * Will get player promo current bet
	 *
	 * @param 	playerName str
	 * @param 	dateJoined datetime
	 * @return	array
	 */
	public function getPlayerCurrentBetByPlayerName($playerName, $dateJoined, $promoId = null, $playerId = null) {

		$playerGames = null;
		if ($promoId) {
			$playerGames = $this->getPlayerGames($promoId);
		}

		$this->db->select('sum(bet_amount) as totalBetAmount')
			->from('game_logs')
			->where('end_at >=', $dateJoined)
			->where('end_at <=', $this->utils->getNowForMysql())
			->where('player_id', $playerId)
		;
		if ($promoId) {
			// $playerGames = $this->getPlayerGames($promoId);
			$this->db->where_in('game_description_id', $playerGames);
		}

		$qry = $this->db->get();
		$rows = array(array('totalBetAmount' => 0));
		if ($qry->num_rows() > 0) {
			$rows = $qry->result_array();
		}

		return $rows;

	}

	public function getFirstDatetime() {
		$this->db->select('end_at')->from($this->tableName)->order_by('end_at');
		$this->limitOneRow();
		return $this->runOneRowOneField('end_at');
	}

	public function sumAmount($start, $to) {
		$this->db->select_sum('bet_amount')->select_sum('result_amount')
			->select_sum('loss_amount')->select_sum('win_amount')
			->from($this->tableName)
			->where('end_at >=', $start)
			->where('end_at <=', $to);

		$row = $this->runOneRow();
		$bet_amount = 0;
		$result_amount = 0;
		$win_amount = 0;
		$loss_amount = 0;
		if (!empty($row)) {
			$bet_amount = $row->bet_amount;
			$result_amount = $row->result_amount;
			$win_amount = $row->win_amount;
			$loss_amount = $row->loss_amount;
		}
		return array(floatval($bet_amount), floatval($result_amount), floatval($win_amount), floatval($loss_amount));
	}

	public function getPlayerTotalGameLogsByDate($playerId, $dateTimeFrom, $dateTimeTo = null, $gameDescIdArr = null)
    {
        if ($this->utils->getConfig('use_total_minute')) {
            $this->load->model(array('total_player_game_minute'));
            return $this->total_player_game_minute->getPlayerTotalGameLogsByDate($playerId, $dateTimeFrom, $dateTimeTo, $gameDescIdArr);
        } else {
            $result = [];
            $betting_amount = 0;
            $date_minute = null;

            $this->db->select('*')
                ->from('game_logs')
                ->where('player_id', $playerId);

            if (empty($dateTimeTo)) {
                $dateTimeTo = $this->utils->getNowForMysql();
            }

            $this->db->where('end_at >=', $dateTimeFrom);
            $this->db->where('end_at <=', $dateTimeTo);

            if (!empty($gameDescIdArr)) {
                $this->db->where_in('game_description_id', $gameDescIdArr);
            }

            $rows = $this->runMultipleRow();
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $betting_amount = $row->bet_amount;
                    $date_minute = $row->updated_at;
                    $result[] = [
                        'game_description_id'=> $row->game_description_id,
                        'betting_amount' => $betting_amount,
                        'date_minute' => $date_minute,
                        'used' => false,
                    ];
                }
            }
            return $result;
        }
    }

    # Note: Bet amount calculated by this function has been adjusted based on odd (bet_for_cashback), if configured
	public function totalPlayerBettingAmountWithLimitByVIP($playerId, $dateTimeFrom, $dateTimeTo = null, $gameDescIdArr = null) {
		if ($this->utils->getConfig('use_total_minute')) {
			$this->load->model(array('total_player_game_minute'));
			return $this->total_player_game_minute->totalPlayerBettingAmountWithLimitByVIP($playerId, $dateTimeFrom, $dateTimeTo, $gameDescIdArr);
		} else {

			// $playerGames = null;
			// if ($promoId) {
			// 	$this->load->model(array('promorules'));
			// 	$playerGames = $this->promorules->getPlayerGames($promoId);
			// }

			$this->db->select_sum('bet_for_cashback', 'totalBetAmount')
				->from('game_logs')
				->where('player_id', $playerId);

			if (empty($dateTimeTo)) {
				$dateTimeTo = $this->utils->getNowForMysql();
			}
			// $fromDateHourStr = $this->utils->formatDateHourForMysql(new DateTime($dateTimeFrom));
			// $toDateHourStr = $this->utils->formatDateHourForMysql(new DateTime($dateTimeTo));

			// $this->utils->debug_log('dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo);
			$this->db->where('end_at >=', $dateTimeFrom);
			$this->db->where('end_at <=', $dateTimeTo);

			// $this->db->where('date_hour >=', $fromDateHourStr)->where('date_hour <=', $toDateHourStr);

			if (!empty($gameDescIdArr)) {
				$this->db->where_in('game_description_id', $gameDescIdArr);
			}

			$qry = $this->db->get();

			// $this->utils->printLastSQL();
			$betting_amount=0;
			// $rows = array(array('totalBetAmount' => 0));
			if ($qry->num_rows() > 0) {
				// $rows = $qry->result_array();
				$row=$qry->row_array();
				$betting_amount=$row['totalBetAmount'];
			}

			// return $rows;
			return $betting_amount;

			// $rows = array(array('totalBetAmount' => 0));
			// if ($qry->num_rows() > 0) {
			// 	$rows = $qry->result_array();
			// }

			// return $rows;
		}

	}

	public function sumBetsWinsLossByDatetime($playerId, $dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $promoruleId = null) {
		return $this->sumBetsWinsLossByDatetimePlayers(array($playerId), $dateTimeFrom, $dateTimeTo, $gamePlatformId);
	}

	public function sumBetsWinsLossByDatetimePlayers($playerIdArr, $dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $promoruleId = null) {
		//TODO by promorule id
		$use_total_minute = $this->utils->getConfig('use_total_minute');
		if ($use_total_minute) {
			$this->load->model(array('total_player_game_minute'));
			list($totalBet, $totalWin, $totalLoss) = $this->total_player_game_minute->getTotalBetsWinsLossByPlayers($playerIdArr, $dateTimeFrom, $dateTimeTo, $gamePlatformId, $promoruleId);
		} else {
			list($totalBet, $totalWin, $totalLoss) = $this->getTotalBetsWinsLossByPlayers($playerIdArr, $dateTimeFrom, $dateTimeTo, $gamePlatformId, $promoruleId);
		}
		return array($totalBet, $totalWin, $totalLoss);
	}

	public function sumBetsWinsLossByDatetimeAgents($agentIdArr, $dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $promoruleId = null) {
		//TODO by promorule id
		$use_total_minute = $this->utils->getConfig('use_total_minute');
		if ($use_total_minute) {
			$this->load->model(array('total_player_game_minute'));
			$this->utils->debug_log('AGENT_ID', $agentIdArr);
			list($totalBet, $totalWin, $totalLoss) = $this->total_player_game_minute->getTotalBetsWinsLossByAgents($agentIdArr, $dateTimeFrom, $dateTimeTo, $gamePlatformId, $promoruleId);
		} else {
			$this->utils->debug_log('AGENT_ID', $agentIdArr);
			list($totalBet, $totalWin, $totalLoss) = $this->getTotalBetsWinsLossByAgents($agentIdArr, $dateTimeFrom, $dateTimeTo, $gamePlatformId, $promoruleId);
		}
		return array($totalBet, $totalWin, $totalLoss);
	}

	public function getTotalBetsWinsLossGroupByGamePlatformByPlayers($playerIds, $dateTimeFrom, $dateTimeTo) {
		$result = array();
		if ($this->utils->getConfig('use_total_minute')) {
			$this->load->model(array('total_player_game_minute'));
			return $this->total_player_game_minute->getTotalBetsWinsLossGroupByGamePlatformByPlayers($playerIds, $dateTimeFrom, $dateTimeTo);

		} else {

			$totalBet = 0;
			$totalWin = 0;
			$totalLoss = 0;

			if (!empty($playerIds)) {
				$this->db->select_sum('bet_amount', 'total_bet')
					->select_sum('IF(result_amount < 0, ABS(result_amount) , 0)', 'total_loss')
					->select_sum('IF(result_amount > 0, result_amount , 0)', 'total_win')
					->select('game_platform_id')
					->from('game_logs')
					->where('flag', self::FLAG_GAME)
					->group_by('game_platform_id')
				;

				if (count($playerIds) == 1) {
					$this->db->where('player_id', $playerIds[0]);
				} else {
					$this->db->where_in('player_id', $playerIds);
				}

				if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
					$this->db->where('end_at >=', $dateTimeFrom);
					$this->db->where('end_at <=', $dateTimeTo);
				}

				$rows = $this->runMultipleRow();
				if (!empty($rows)) {

					foreach ($rows as $row) {
						$totalBet = $row->total_bet;
						$totalWin = $row->total_win;
						$totalLoss = $row->total_loss;
						$result[$row->game_platform_id] = array($totalBet, $totalWin, $totalLoss);
					}
				}
				// $this->utils->printLastSQL();
			}
		}
		return $result;
	}

	public function sumOperatorBetsWinsLossByDatetime($dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $promoruleId = null, $db = null) {

		list($totalBet, $totalWin, $totalLoss) = $this->getTotalBetsWinsLoss($dateTimeFrom, $dateTimeTo, $gamePlatformId, $db);

		return array($totalBet, $totalWin, $totalLoss);

	}

	public function getTotalBetsWinsLoss($dateTimeFrom = null, $dateTimeTo = null, $gamePlatformId = null, $db = null) {
		if ($this->utils->getConfig('use_total_minute')) {
			$this->load->model(array('total_player_game_minute'));
			return $this->total_player_game_minute->getTotalBetsWinsLoss($dateTimeFrom, $dateTimeTo, $gamePlatformId, $db);
		} else {
			if ($db == null) {
				$db = $this->db;
			}

			$this->utils->debug_log('dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'gamePlatformId', $gamePlatformId);

			$totalBet = 0;
			$totalWin = 0;
			$totalLoss = 0;

			// if (!empty($playerIds)) {
			$db->select_sum('bet_amount', 'total_bet')
				->select_sum('IF(result_amount < 0, ABS(result_amount) , 0)', 'total_loss')
				->select_sum('IF(result_amount > 0, result_amount , 0)', 'total_win')
				->from('game_logs')
				->join('player', 'player.playerId = game_logs.player_id')
				->where('player.deleted_at IS NULL')
				->where('flag', self::FLAG_GAME)
			;

			// if (count($playerIds) == 1) {
			// 	$this->db->where('player_id', $playerIds[0]);
			// } else {
			// 	$this->db->where_in('player_id', $playerIds);
			// }

			if (!empty($gamePlatformId)) {
				if (is_array($gamePlatformId)) {
					$db->where_in('game_platform_id', $gamePlatformId);
				} else {
					$db->where('game_platform_id', $gamePlatformId);
				}
			}
			if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
				$db->where('end_at >=', $dateTimeFrom);
				$db->where('end_at <=', $dateTimeTo);
			}
			//get one row
			$qry = $db->get();
			$row = null;
			if ($qry && $qry->num_rows() > 0) {
				$row = $qry->row();
			}

			if ($row) {
				// $row = $this->runOneRow();
				$totalBet = $row->total_bet;
				$totalWin = $row->total_win;
				$totalLoss = $row->total_loss;
				// $this->utils->printLastSQL();
				// }
			}
			return array($totalBet, $totalWin, $totalLoss);
		}
	}

	public function sumBettingAmountBySubWallet($playerId, $dateTimeFrom, $dateTimeTo = null, $promoId = null, $subWallet = null) {

		if ($this->utils->getConfig('use_total_minute')) {
			$this->load->model(['total_player_game_minute']);
			//call total minute
			return $this->total_player_game_minute->sumBettingAmountBySubWallet($playerId, $dateTimeFrom, $dateTimeTo, $promoId, $subWallet);
		}

		$totalBetAmount = 0;
		if ($dateTimeFrom != null) {

			if ($dateTimeTo == null) {
				$dateTimeTo = $this->utils->getNowForMysql();
			}

			$this->db->select('sum(bet_amount) as totalBetAmount', false)
				->from('game_logs')
				->where('end_at >=', $dateTimeFrom)
				->where('end_at <=', $dateTimeTo)
				->where('player_id', $playerId)
			;

			if ($promoId) {
				$playerGames = $this->getPlayerGames($promoId);
				$this->db->where_in('game_description_id', $playerGames);
			}

			if ($subWallet) {
				$this->db->where('game_platform_id', $subWallet);
			}

			$totalBetAmount = $this->runOneRowOneField('totalBetAmount');

			// $this->utils->printLastSQL();

		}
		return $totalBetAmount;

		// $qry = $this->db->get();
		// $rows = array(array('totalBetAmount' => 0));
		// if ($qry->num_rows() > 0) {
		// 	$rows = $qry->result_array();
		// }

		// return $rows;
	}

	public function getPlayerListLastHour($platformId){

		if ($this->utils->getConfig('use_total_minute')) {
			$this->load->model(['total_player_game_minute']);
			//call total minute
			return $this->total_player_game_minute->getPlayerListLastHour($platformId);
		}

	}

	public function getPlayerTotalGamesHistoryByDay($player_id, $from) {
		$today = date("Y-m-d");
		$sql = "
			SELECT
				 DATE(game_logs.end_at) AS end_at
				,external_system.system_code AS game
			    ,SUM(game_logs.trans_amount) AS real_bet_amount
			    ,SUM(game_logs.bet_amount) AS bet_amount
			    ,SUM(game_logs.result_amount) AS result_amount
			    ,SUM(game_logs.bet_amount + game_logs.result_amount) AS bet_plus_result_amount
			    ,SUM(game_logs.win_amount) AS win_amount
			    ,SUM(game_logs.loss_amount) AS loss_amount
			    ,SUM(game_logs.after_balance) AS after_balance
			    ,SUM(game_logs.trans_amount) AS trans_amount
			FROM game_logs
			LEFT JOIN player
				ON player.playerId = game_logs.player_id
			LEFT JOIN affiliates
				ON affiliates.affiliateId = player.affiliateId
			LEFT JOIN game_description
				ON game_description.id = game_logs.game_description_id
			LEFT JOIN game_type
				ON game_type.id = game_description.game_type_id
			LEFT JOIN external_system
				ON game_logs.game_platform_id = external_system.id
			WHERE
				player.playerId = ? AND flag = ? AND
				DATE(game_logs.end_at) >= ? AND DATE(game_logs.end_at) <= ?
			GROUP BY DATE(game_logs.end_at), external_system.system_code
		";

		$query = $this->db->query($sql, array($player_id, self::FLAG_GAME, $from, $today));

		return array(
			'total' => $query->num_rows(),
			'data' => $query->result_array(),
		);
	}

	public function getUnsettledBets($status, $dateTimeFrom = null, $dateTimeTo = null, $gamePlatformId = null, $db = null) {
		if ($db == null) {
			$db = $this->db;
		}

		$this->utils->debug_log('dateTimeFrom', $dateTimeFrom, 'dateTimeTo', $dateTimeTo, 'gamePlatformId', $gamePlatformId);

		$db->select_sum('bet_amount', 'total_bet')
			->from('game_logs_unsettle')
			->where_in('status', $status)
		;

		if (!empty($gamePlatformId)) {
			if (is_array($gamePlatformId)) {
				$db->where_in('game_platform_id', $gamePlatformId);
			} else {
				$db->where('game_platform_id', $gamePlatformId);
			}
		}
		if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
			$db->where('end_at >=', $dateTimeFrom);
			$db->where('end_at <=', $dateTimeTo);
		}
		//get one row
		$qry = $db->get();
		$row = null;
		if ($qry && $qry->num_rows() > 0) {
			$row = $qry->row();
		}
		if ($row) {
			$totalUnsettledBet = $row->total_bet;
		}
		return $totalUnsettledBet;
	}


	public function playerGamesHistoryWLimit($playerId, $limit, $offset, $search, $is_count = false) {

		if ($is_count) {
			$this->db->select('count(game_logs.id) as cnt');
		} else {
			$this->db->select("
	            game_logs.id as id,
	            game_type.game_type as game_type,
	            game_description.game_code as game_code,
	            game_logs.end_at as end_at,
	            external_system.system_code as game,
	            game_logs.trans_amount as real_bet_amount,
	            game_logs.bet_amount as bet_amount,
	            game_logs.result_amount as result_amount,
	            (game_logs.bet_amount + game_logs.result_amount) as bet_plus_result_amount,
	            game_logs.win_amount as win_amount,
	            game_logs.loss_amount as loss_amount,
	            game_logs.after_balance as after_balance,
	            game_logs.trans_amount as trans_amount,
	            game_logs.table as roundno,
	            game_logs.note as betDetails,
	            game_logs.flag as flag"
	        );
		}

		if (isset($search['from'], $search['to'])) {
			$this->db->where("game_logs.end_at >=", $search['from']);
			$this->db->where("game_logs.end_at <=", $search['to']);
		}

		if (isset($limit, $offset)) {
			$this->db->limit($limit, $offset);
		}


        $this->db->from("game_logs");

        $this->db->join('player', 'player.playerId = game_logs.player_id', 'left');
        $this->db->join('affiliates', 'affiliates.affiliateId = player.affiliateId', 'left');
        $this->db->join('game_description', 'game_description.id = game_logs.game_description_id', 'left');
        $this->db->join('game_type', 'game_type.id = game_description.game_type_id', 'left');
        $this->db->join('external_system', 'game_logs.game_platform_id = external_system.id', 'left');

        $this->db->where('game_logs.player_id', $playerId);
        $this->db->where('game_logs.flag', self::FLAG_GAME);
        $this->db->order_by('game_logs.end_at', 'desc');
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			if ($is_count) {
				return $this->getOneRowOneField($query, 'cnt');
			} else {
				return $query->result_array();
			}
		}

		return null;
	}

	public function queryPagination($from, $to, $game_platform_id, $playerId, $page_number, $size_per_page, $agent, $game_status, $tableName,$date_mode){
		//always from 1
		if($page_number<1){
			$page_number=1;
		}
		$result=[[], 0, 0, 0];

        $gpidSQL=null;
        //required
		$gpidSQL=' and game_logs.game_platform_id='.$game_platform_id.' ';

		$playerSQL=null;
		$playerId=intval($playerId);
		if(!empty($playerId)){
			$playerSQL=' and game_logs.player_id='.$playerId.' ';
		}

        $merchant_code=$agent['agent_name'];
        $agent_id=intval($agent['agent_id']);

        $limit_count=$size_per_page;
		$offset=($page_number-1)*$size_per_page;

		// $game_status = $tableName=='game_logs'?'settled':'unsettle';
		$date_filter_by='game_logs.updated_at';
		$use_index='use index(idx_updated_at)';
		switch ($date_mode) {
			case 'by_bet_time':
				$date_filter_by='game_logs.bet_at';
				$use_index='use index(idx_bet_at)';
				break;

			case 'by_last_update_time':
				$date_filter_by='game_logs.updated_at';
				$use_index='use index(idx_updated_at)';
				break;
			case 'by_payout_time':
				$date_filter_by='game_logs.end_at';
				$use_index='use index(idx_end_at)';
				break;
		}

		$statusSQL=self::STATUS_SETTLED.' as detail_status, ';
		if($tableName=='game_logs_unsettle'){
			$statusSQL=' game_logs.status as detail_status,';
		}

		$sql=<<<EOD
select game_logs.id as uniqueid,
CONCAT(game_logs.game_platform_id,game_logs.external_uniqueid)  as external_uniqueid,
game_logs.external_uniqueid as game_external_uniqueid,
game_provider_auth.login_name as username,
'{$merchant_code}' as merchant_code,
game_logs.game_platform_id as game_platform_id,
game_description.external_game_id as game_code,
game_description.game_name as game_name,
game_logs.end_at game_finish_time,
game_logs.note game_details,
game_logs.bet_at as bet_time,
game_logs.end_at as payout_time,
game_logs.`table` as round_number,
ifnull(game_logs.trans_amount,game_logs.bet_amount)  as real_bet_amount,
game_logs.bet_amount as effective_bet_amount,
game_logs.result_amount as result_amount,
game_logs.result_amount+ifnull(game_logs.trans_amount,game_logs.bet_amount) as payout_amount,
game_logs.after_balance as after_balance,
game_logs.bet_details as bet_details,
game_logs.md5_sum,
game_logs.ip_address,
game_logs.bet_type,
game_logs.odds_type,
game_logs.odds,
game_logs.response_result_id,
game_logs.external_log_id as update_version,
'{$game_status}' as game_status,
{$statusSQL}
game_logs.updated_at as updated_at
from {$tableName} as game_logs {$use_index}
join game_provider_auth on game_logs.player_id=game_provider_auth.player_id and game_logs.game_platform_id=game_provider_auth.game_provider_id
join game_description on game_description.id=game_logs.game_description_id
where
game_logs.flag=? and {$date_filter_by} >= ? and {$date_filter_by} <= ?
and game_provider_auth.agent_id=?
{$playerSQL}
{$gpidSQL}
limit {$offset}, {$limit_count}
EOD;

		$params=[self::FLAG_GAME, $from, $to, $agent_id];

		$this->utils->debug_log('query game history', $sql, $params);
		$rows=$this->runRawSelectSQLArray($sql, $params);

		if(empty($rows)){
			$rows=[];
		}

		$countSql=<<<EOD
select count(game_logs.id) cnt
from {$tableName} as game_logs {$use_index}
join game_provider_auth on game_logs.player_id=game_provider_auth.player_id and game_logs.game_platform_id=game_provider_auth.game_provider_id
join game_description on game_description.id=game_logs.game_description_id
where
game_logs.flag=? and {$date_filter_by} >= ? and {$date_filter_by} <= ?
and game_provider_auth.agent_id=?
{$playerSQL}
{$gpidSQL}
EOD;

		$this->utils->debug_log('count game history', $countSql, $params);
		$cntRows=$this->runRawSelectSQLArray($countSql, $params);
		$total_pages=0;
		if(!empty($cntRows)){
			$totalCnt=$cntRows[0]['cnt'];
			$total_pages= intval($totalCnt / $size_per_page) + ($totalCnt % $size_per_page > 0 ? 1 : 0);
		}

		$total_rows_current_page=count($rows);
		$current_page=$page_number;
        if($total_pages<$current_page){
            $current_page=$total_pages;
        }

        $result=[$rows, $total_pages, $current_page, $total_rows_current_page];

		return $result;
	}

    /**
     * query pagination
     *
     * @param string $from
     * @param string $to
     * @param $game_platform_id
     * @param $playerId
     * @param $page_number
     * @param $size_per_page
     * @param $agent
     * @return array rows
     * @internal param $merchant_code
     */
	public function queryMultiplePagination($from, $to, $multiple_game_platform, $playerId, $page_number, $size_per_page,
		$agent, $game_status, $tableName, $date_mode, &$sql=null, &$countSql=null){

	    //lock query

		//always from 1
		if($page_number<1){
			$page_number=1;
		}
		$result=[[], 0, 0, 0];

        $gpidSQL=null;
        //required
        // $game_platform_id=intval($game_platform_id);
		if(!empty($multiple_game_platform)){
			//filter id
			$apiIdArr=[];
			foreach ($multiple_game_platform as $val) {
				$val=intval($val);
				if($val>0){
					$apiIdArr[]=$val;
				}
			}

			if(!empty($apiIdArr)){
				if(count($apiIdArr)==1){
		            $gpidSQL=' and game_logs.game_platform_id='.$apiIdArr[0].' ';
				}else{
		            $gpidSQL=' and game_logs.game_platform_id in ('.implode(',', $apiIdArr).') ';
				}

	        }else{
	        	//empty
	        	$this->utils->error_log('empty apiIdArr', $multiple_game_platform, $apiIdArr);
	        	return $result;
	        }
        }else{
        	//empty
        	$this->utils->error_log('empty multiple_game_platform', $multiple_game_platform);
        	return $result;
        }

		$playerSQL=null;
		$playerId=intval($playerId);
		if(!empty($playerId)){
			$playerSQL=' and game_logs.player_id='.$playerId.' ';
		}

        $merchant_code=$agent['agent_name'];
        $agent_id=intval($agent['agent_id']);

        $limit_count=$size_per_page;
		$offset=($page_number-1)*$size_per_page;

		// $game_status = $tableName=='game_logs'?'settled':'unsettle';
		$date_filter_by='game_logs.updated_at';
		$use_index='use index(idx_updated_at)';
		switch ($date_mode) {
			case 'by_bet_time':
				$date_filter_by='game_logs.bet_at';
				$use_index='use index(idx_bet_at)';
				break;

			case 'by_last_update_time':
				$date_filter_by='game_logs.updated_at';
				$use_index='use index(idx_updated_at)';
				break;
			case 'by_payout_time':
				$date_filter_by='game_logs.end_at';
				$use_index='use index(idx_end_at)';
				break;
		}

		$statusSQL=self::STATUS_SETTLED.' as detail_status, ';
		if($tableName=='game_logs_unsettle'){
			$statusSQL=' game_logs.status as detail_status,';
		}

		$sql=<<<EOD
select game_logs.id as uniqueid,
CONCAT(game_logs.game_platform_id,game_logs.external_uniqueid)  as external_uniqueid,
game_logs.external_uniqueid as game_external_uniqueid,
game_provider_auth.login_name as username,
'{$merchant_code}' as merchant_code,
game_logs.game_platform_id as game_platform_id,
game_description.external_game_id as game_code,
game_description.game_name as game_name,
game_logs.end_at game_finish_time,
game_logs.note game_details,
game_logs.bet_at as bet_time,
game_logs.end_at as payout_time,
game_logs.`table` as round_number,
ROUND(ifnull(game_logs.trans_amount, game_logs.bet_amount), 2)  as real_bet_amount,
ROUND(game_logs.bet_amount, 2) as effective_bet_amount,
ROUND(game_logs.result_amount, 2) as result_amount,
ROUND(game_logs.result_amount+ifnull(game_logs.trans_amount,game_logs.bet_amount), 2) as payout_amount,
ROUND(game_logs.after_balance, 2) as after_balance,
game_logs.bet_details as bet_details,
game_logs.md5_sum,
game_logs.ip_address,
game_logs.bet_type,
game_logs.odds_type,
game_logs.odds,
ROUND(game_logs.rent, 2) as rent,
game_logs.response_result_id,
game_logs.external_log_id as update_version,
'{$game_status}' as game_status,
{$statusSQL}
game_logs.updated_at as updated_at
from {$tableName} as game_logs {$use_index}
join game_provider_auth on game_logs.player_id=game_provider_auth.player_id and game_logs.game_platform_id=game_provider_auth.game_provider_id
join game_description on game_description.id=game_logs.game_description_id
where
game_logs.flag=? and {$date_filter_by} >= ? and {$date_filter_by} <= ?
and game_provider_auth.agent_id=?
{$playerSQL}
{$gpidSQL}
limit {$offset}, {$limit_count}
EOD;

		$params=[self::FLAG_GAME, $from, $to, $agent_id];

		$this->utils->debug_log('query game history', $sql, $params);
		$rows=$this->runRawSelectSQLArray($sql, $params);

		if(empty($rows)){
			$rows=[];
		}

		$countSql=<<<EOD
select count(game_logs.id) cnt
from {$tableName} as game_logs {$use_index}
join game_provider_auth on game_logs.player_id=game_provider_auth.player_id and game_logs.game_platform_id=game_provider_auth.game_provider_id
join game_description on game_description.id=game_logs.game_description_id
where
game_logs.flag=? and {$date_filter_by} >= ? and {$date_filter_by} <= ?
and game_provider_auth.agent_id=?
{$playerSQL}
{$gpidSQL}
EOD;

		$this->utils->debug_log('count game history', $countSql, $params);
		$cntRows=$this->runRawSelectSQLArray($countSql, $params);
		$total_pages=0;
		if(!empty($cntRows)){
			$totalCnt=$cntRows[0]['cnt'];
			$total_pages= intval($totalCnt / $size_per_page) + ($totalCnt % $size_per_page > 0 ? 1 : 0);
		}

		$total_rows_current_page=count($rows);
		$current_page=$page_number;
        if($total_pages<$current_page){
            $current_page=$total_pages;
        }

        $result=[$rows, $total_pages, $current_page, $total_rows_current_page];

		return $result;

	}

    /**
     * @param $total_type
     * @param $dateFrom
     * @param $dateTo
     * @param $game_platform_id
     * @param $playerId
     * @param $page_number
     * @param $size_per_page
     * @param $agent
     * @return array $rows
     * @internal param $merchant_code
     */
    public function queryTotalPagination($total_type, $dateFrom, $dateTo, $game_platform_id, $playerId, $page_number, $size_per_page, $agent){

		$table='total_player_game_minute';
		$dateField='date_minute';
		$fromDateStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateFrom));
		$toDateStr = $this->utils->formatDateMinuteForMysql(new DateTime($dateTo));
		switch ($total_type) {
			// default
			// case 'minute':
			// 	break;

			case 'hourly':
				$table='total_player_game_hour';
				$dateField='date_hour';
				$fromDateStr = $this->utils->formatDateHourForMysql(new DateTime($dateFrom));
				$toDateStr = $this->utils->formatDateHourForMysql(new DateTime($dateTo));
				break;

			case 'daily':
				$table='total_player_game_day';
				$dateField='`date`';
				$fromDateStr = $this->utils->formatDateForMysql(new DateTime($dateFrom));
				$toDateStr = $this->utils->formatDateForMysql(new DateTime($dateTo));
				break;

			case 'monthly':
				$table='total_player_game_month';
				$dateField='`month`';
				$fromDateStr = $this->utils->formatYearMonthForMysql(new DateTime($dateFrom));
				$toDateStr = $this->utils->formatYearMonthForMysql(new DateTime($dateTo));
				break;

			case 'yearly':
				$table='total_player_game_year';
				$dateField='`year`';
				$fromDateStr = $this->utils->formatYearForMysql(new DateTime($dateFrom));
				$toDateStr = $this->utils->formatYearForMysql(new DateTime($dateTo));
				break;
		}

		//always from 1
		if($page_number<1){
			$page_number=1;
		}

        $gpidSQL=null;
        $game_platform_id=intval($game_platform_id);
        if(!empty($game_platform_id)){
            $gpidSQL=' and '.$table.'.game_platform_id='.$game_platform_id;
        }

        $playerSQL=null;
		$playerId=intval($playerId);
		if(!empty($playerId)){
			$playerSQL=' and '.$table.'.player_id='.$playerId;
		}

        $merchant_code=$agent['agent_name'];
        $agent_id=$agent['agent_id'];

        $limit_count=$size_per_page;
		$offset=($page_number-1)*$size_per_page;

		$sql=<<<EOD
select {$table}.id as uniqueid, game_provider_auth.login_name as username,
'{$merchant_code}' as merchant_code, {$dateField} as total_time,
{$table}.game_platform_id as game_platform_id, game_description.external_game_id as game_code, game_description.game_name as game_name,
{$table}.real_betting_amount as real_bet_amount, {$table}.betting_amount as effective_bet_amount,
{$table}.result_amount as result_amount, {$table}.result_amount+{$table}.real_betting_amount as payout_amount
from {$table}
join game_provider_auth on {$table}.player_id=game_provider_auth.player_id and {$table}.game_platform_id=game_provider_auth.game_provider_id
join game_description on game_description.id={$table}.game_description_id
where
{$dateField} >= ? and {$dateField} <= ?
and game_provider_auth.agent_id=?
{$playerSQL}
{$gpidSQL}
order by {$table}.{$dateField} desc
limit {$offset}, {$limit_count}
EOD;

		$params=[$fromDateStr, $toDateStr, $agent_id];

		$this->utils->debug_log('query total game history', $sql, $params);
		$rows=$this->runRawSelectSQLArray($sql, $params);

		if(empty($rows)){
			$rows=[];
		}

		$countSql=<<<EOD
select count({$table}.id) cnt
from {$table}
join game_provider_auth on {$table}.player_id=game_provider_auth.player_id and {$table}.game_platform_id=game_provider_auth.game_provider_id
join game_description on game_description.id={$table}.game_description_id
where
{$dateField} >= ? and {$dateField} <= ?
and game_provider_auth.agent_id=?
{$playerSQL}
{$gpidSQL}
EOD;

		$this->utils->debug_log('count total game history', $countSql, $params);
		$cntRows=$this->runRawSelectSQLArray($countSql, $params);
		$total_pages=0;
		if(!empty($cntRows)){
			$totalCnt=$cntRows[0]['cnt'];
			$total_pages= intval($totalCnt / $size_per_page) + ($totalCnt % $size_per_page > 0 ? 1 : 0);
		}

		$total_rows_current_page=count($rows);
		$current_page=$page_number;
        if($total_pages<$current_page){
            $current_page=$total_pages;
        }

		return [$rows, $total_pages, $current_page, $total_rows_current_page];

	}

	public function countGameLogsByTime($gamePlatformId, $startDate, $endDate, $flag = self::FLAG_GAME) {
		$this->db->select(array("count(id) as cnt"));
		$this->db->from("game_logs");
		$this->db->where("game_logs.game_platform_id", $gamePlatformId);
		$this->db->where("game_logs.end_at >=", date("Y-m-d H:i:s", $startDate));
		$this->db->where("game_logs.end_at <", date("Y-m-d H:i:s", $endDate));
		$this->db->where("game_logs.flag", $flag);
		$query = $this->db->get();
		return $this->getOneRowOneField($query, 'cnt');
	}

    public function getGamelogsBetDetailsByExternalUniqueId($external_uniqueid){
        $this->db->where("external_uniqueid", $external_uniqueid);
        $query = $this->db->get('game_logs');
        $row = $query->row_array();
        // print_r($row['bet_details']);exit();
    	if(!empty($row)){
            return $row['bet_details'];
        }
    }

	public function getUnsettleUniqueExternalId($uniqueid, $field) {
		$data = $this->db->select($field)->from('game_logs_unsettle')->where('external_uniqueid', $uniqueid)->get();
		return $data->result_array();
	}

    public function getPlayerTotalBetCount($playerIds, $dateTimeFrom, $dateTimeTo, $gamePlatformId = null, $gameType = null, $bet_limit = 0){
        $totalBetCount = 0;

        if (empty($playerIds)) {
            return $totalBetCount;
        }

        $this->db->select('count(id) total_bet_count');
        $this->db->from('game_logs');

        if (is_array($playerIds)) {
            $this->db->where_in('player_id', $playerIds);
        } else {
            $this->db->where('player_id', $playerIds);
        }

        if (!empty($gamePlatformId)) {
            if (is_array($gamePlatformId)) {
                $this->db->where_in('game_platform_id', $gamePlatformId);
            } else {
                $this->db->where('game_platform_id', $gamePlatformId);
            }
        }

        if (!empty($gameType)) {
            if (is_array($gamePlatformId)) {
                $this->db->where_in('game_type_id', $gameType);
            } else {
                $this->db->where('game_type_id', $gameType);
            }
        }

        if(!empty($bet_limit)){
            $this->db->where('bet_amount >=', $bet_limit);
        }

        if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
            $fromDateTime = $this->utils->formatDateTimeForMysql(new DateTime($dateTimeFrom));
            $toDateTime = $this->utils->formatDateTimeForMysql(new DateTime($dateTimeTo));

            $this->db->where('start_at >=', $fromDateTime);
            $this->db->where('start_at <=', $toDateTime);
        }

        if(empty($row = $this->runOneRow())){
            return $totalBetCount;
        }

        $totalBetCount = $row->total_bet_count;
        return $totalBetCount;
    }

    public function isResponseResultIdIfExist($response_result_id){
    	$this->db->select('response_result_id');
    	$this->db->where('response_result_id', $response_result_id);
    	$this->db->where('flag', self::FLAG_TRANSACTION);
    	$this->db->from('game_logs');
    	$result = $this->runOneRowOneField('response_result_id');
    	if(empty($result)){
    		return false;
    	}
    	return true;
    }

    const GAME_LOGS_ID_KEY_REDIS='_GAME_LOGS_ID_KEY_REDIS';
    const GAME_LOGS_UNSETTLED_ID_KEY_REDIS='_GAME_LOGS_UNSETTLED_ID_KEY_REDIS';

    public function generateGameLogsId(){
    	return $this->utils->generateUniqueIdFromRedis(self::GAME_LOGS_ID_KEY_REDIS);
    }

    public function generateGameLogsUnsettledId(){
    	return $this->utils->generateUniqueIdFromRedis(self::GAME_LOGS_UNSETTLED_ID_KEY_REDIS);
    }

	public function insertRowToGameLogs(array $data) {
		unset($data['status']);
		//always is 0 when insert
		$data['external_log_id']=0;
		$data['updated_at']=$this->utils->getNowForMysql();
		if($this->utils->getConfig('enabled_generate_game_logs_id_from_redis')){
			//get id
			$data['id']=$this->generateGameLogsId();
		}
		// return $this->db->insert($this->tableName, $data);
		return $this->insertData($this->tableName, $data);
	}

	/**
	 * always update by primary key
	 * @param  array $data
	 * @param  string $primaryKeyFieldInDB
	 * @return boolean
	 */
	public function updateRowToGameLogs(array $data, $primaryKeyFieldInDB='id') {
		$id=$data[$primaryKeyFieldInDB];
		unset($data[$primaryKeyFieldInDB]);
		unset($data['status']);
		unset($data['external_log_id']);
		$data['updated_at']=$this->utils->getNowForMysql();
	    $this->db->where($primaryKeyFieldInDB, $id);
	    $this->db->set($data);
	    //update version external_log_id=ifnull(external_log_id,0)+1
	    $this->db->set('external_log_id', 'ifnull(external_log_id,0)+1', false);
	    return $this->runAnyUpdate($this->tableName);
	}

	public function insertRowToStream(array $data){
		//remove note
		unset($data['note']);
		unset($data['id']);
		// return true;
		return $this->insertData('game_logs_stream', $data);
	}

	public function insertRowToGameLogsUnsettle($data) {
		//always is 0 when insert
		$data['external_log_id']=0;
		if($this->utils->getConfig('enabled_generate_game_logs_unsettled_id_from_redis')){
			//get id first
			$data['id']=$this->generateGameLogsUnsettledId();
		}
		return $this->insertData('game_logs_unsettle', $data);
	}

	public function updateRowToGameLogsUnsettle(array $data, $primaryKeyFieldInDB='id') {
		$id=$data[$primaryKeyFieldInDB];
		unset($data[$primaryKeyFieldInDB]);
	    $this->db->where($primaryKeyFieldInDB, $id);
	    $this->db->set($data);
	    //update version external_log_id=ifnull(external_log_id,0)+1
	    $this->db->set('external_log_id', 'ifnull(external_log_id,0)+1', false);
	    return $this->runAnyUpdate('game_logs_unsettle');
	}

	public function queryExistsUnsettleMap(array $uniqueidValues){
		$this->db->select('md5_sum, id, external_uniqueid')
		  ->from('game_logs_unsettle')->where_in('external_uniqueid', $uniqueidValues);

        $existsRows = $this->runMultipleRowArray();
        $result=[];
        if(!empty($existsRows)){
        	foreach ($existsRows as $row) {
        		$result[$row['external_uniqueid']]=[$row['md5_sum'], $row['id']];
        	}
        }
        unset($existsRows);

        return $result;
	}

    const SETTLED_STATUS_BUT_INVALID_BET=[self::STATUS_REJECTED, self::STATUS_ACCEPTED, self::STATUS_REFUND, self::STATUS_VOID, self::STATUS_CANCELLED];

    /**
     *
     * will remove other status rows
     *
     * require external_uniqueid and status in insertRows and updateRows
     * require game_logs_id in updateRows
     *
     * @param  object $api
     * @param  callable makeParamsForInsertOrUpdateGameLogsRow
     * @param  array $insertRows  only for game_logs
     * @param  array $updateRows  only for game_logs
     * @return boolean
     */
	public function processUnsettleGameLogs($api, callable $makeParamsForInsertOrUpdateGameLogsRow, &$insertRows, &$updateRows){

    	if(!is_object($api) || $api==null){
    		return false;
    	}

		$success=true;

		$uniqueidValues=[];
        if(!empty($insertRows)){
        	$uniqueidValues=array_column($insertRows, 'external_uniqueid');
    	}
        if(!empty($updateRows)){
        	$uniqueidValues=array_merge($uniqueidValues, array_column($updateRows, 'external_uniqueid'));
    	}
        $existsRowUnsettleMap=$this->queryExistsUnsettleMap($uniqueidValues);
        unset($uniqueidValues);

        // $this->utils->debug_log('after queryExistsUnsettleMap', $existsRowUnsettleMap);

        $updateUnsettleCount=0;
        $insertUnsettleCount=0;
        $deleteGameLogsCount=0;
        $deleteGameLogsUnsettleCount=0;

        //try delete it on game_logs_unsettle or game_logs if md5 changed
        //if status is settled, then delete game_logs_unsettle
        //if status is other, then delete game_logs, insert or update game_logs_unsettle
        $cntOfInsert=count($insertRows);
        if($cntOfInsert>0){
        	// $deleteUniqueidArr=[];
        	for($i=0; $i<$cntOfInsert; $i++) {
        		$external_uniqueid=$insertRows[$i]['external_uniqueid'];
        		// $row=$insertRows[$i];
        		if($insertRows[$i]['status']==self::STATUS_SETTLED){
        			//remove from unsettle if exists
        			if(isset($existsRowUnsettleMap[$external_uniqueid])){
        				//delete unsettle
        				$this->db->where('id', $existsRowUnsettleMap[$external_uniqueid][1]);
        				$deleteSuccess=$this->runRealDelete('game_logs_unsettle');
        				if(!$deleteSuccess){
        					$this->utils->error_log('delete game_logs_unsettle failed', $existsRowUnsettleMap[$insertRows[$i]['external_uniqueid']]);
        				}else{
        					$deleteGameLogsUnsettleCount++;
        					$this->utils->debug_log('delete game_logs_unsettle', $external_uniqueid, $existsRowUnsettleMap[$external_uniqueid][1]);
        				}

        			}else{
        				//nothing
        			}
        			//can't insert/update game_logs_unsettle
        		}else{
        			//don't need to delete game_logs, because it's insertRows which means doesn't exist on game_logs
        			//others, exists and md5 is diff
        			if(isset($existsRowUnsettleMap[$external_uniqueid])){
        				if($insertRows[$i]['md5_sum']!=$existsRowUnsettleMap[$external_uniqueid][0]){
	        				$row=$insertRows[$i];

	        				$row['game_logs_unsettle_id']=$existsRowUnsettleMap[$external_uniqueid][1];
	        				//exists then update
							$params=$makeParamsForInsertOrUpdateGameLogsRow($row);

							// $this->utils->debug_log($external_uniqueid, $existsRowUnsettleMap[$external_uniqueid], $row, $params);

	        				$this->commonInsertOrUpdateGameLogsOrUnsettleRow($api, $params, 'update');
	        				unset($params);
	        				unset($row['game_logs_unsettle_id']);

	        				$updateUnsettleCount++;
	        			}else{
	        				$this->utils->debug_log('ignore this row', $external_uniqueid);
	        			}
        			}else{
        				//insert
						$params=$makeParamsForInsertOrUpdateGameLogsRow($insertRows[$i]);
        				$this->commonInsertOrUpdateGameLogsOrUnsettleRow($api, $params, 'insert');
        				unset($params);
        				$insertUnsettleCount++;
        			}
        			unset($insertRows[$i]);
        		}
        	}
        }

        $cntOfUpdate=count($updateRows);
        if($cntOfUpdate>0){
        	// $deleteUniqueidArr=[];
        	for($i=0; $i<$cntOfUpdate; $i++) {

        		$external_uniqueid=$updateRows[$i]['external_uniqueid'];
        		// $row=$updateRows[$i];
        		if($updateRows[$i]['status']==self::STATUS_SETTLED){
        			//remove from unsettle if exists
        			if(isset($existsRowUnsettleMap[$external_uniqueid])){
        				//delete unsettle
        				$this->db->where('id', $existsRowUnsettleMap[$external_uniqueid][1]);
        				$deleteSuccess=$this->runRealDelete('game_logs_unsettle');
        				if(!$deleteSuccess){
        					$this->utils->error_log('delete game_logs_unsettle failed', $existsRowUnsettleMap[$updateRows[$i]['external_uniqueid']]);
        				}else{
        					$deleteGameLogsUnsettleCount++;
        					$this->utils->debug_log('delete game_logs_unsettle', $external_uniqueid, $existsRowUnsettleMap[$external_uniqueid][1]);
        				}

        			}else{
        				//nothing
        			}
        			//can't insert/update game_logs_unsettle
        		}else{
    				//delete game_logs, we only keep settled to game_logs
    				$this->db->where('id', $updateRows[$i]['game_logs_id']);
    				$deleteSuccess=$this->runRealDelete('game_logs');
    				if(!$deleteSuccess){
    					$this->utils->error_log('delete game_logs failed', $updateRows[$i]['game_logs_id']);
    				}else{
    					$deleteGameLogsCount++;
    					$this->utils->debug_log('delete game_logs', $updateRows[$i]['game_logs_id']);
    				}

        			//others
        			if(isset($existsRowUnsettleMap[$external_uniqueid])){
        				if($updateRows[$i]['md5_sum']!=$existsRowUnsettleMap[$external_uniqueid][0]){

	        				$updateRows[$i]['game_logs_unsettle_id']=$existsRowUnsettleMap[$external_uniqueid][1];
	        				//exists then update, game_logs_unsettle_id to $params
							$params=$makeParamsForInsertOrUpdateGameLogsRow($updateRows[$i]);
							//game_logs_unsettle_id to id
	        				$this->commonInsertOrUpdateGameLogsOrUnsettleRow($api, $params, 'update');
	        				unset($params);
	        				unset($updateRows[$i]['game_logs_unsettle_id']);
	        				$updateUnsettleCount++;
	        			}else{
	        				$this->utils->debug_log('ignore this row', $external_uniqueid);
	        			}
        			}else{
        				//insert
						$params=$makeParamsForInsertOrUpdateGameLogsRow($updateRows[$i]);
        				$this->commonInsertOrUpdateGameLogsOrUnsettleRow($api, $params, 'insert');
        				unset($params);
        				$insertUnsettleCount++;
        			}
        			//delete from updateRows
        			unset($updateRows[$i]);
        		}
        	}
        }

        $this->utils->info_log('after process unsettle', count($insertRows), count($updateRows),
        	'insertUnsettleCount:'.$insertUnsettleCount, 'updateUnsettleCount:'.$updateUnsettleCount,
        	'deleteGameLogsUnsettleCount:'.$deleteGameLogsUnsettleCount, 'deleteGameLogsCount:'.$deleteGameLogsCount);

        unset($existsRowUnsettleMap);

        return $success;
	}

	/**
	 *
	 * @param  object $api
	 * @param  array $params
	 * 	       		game_info [int $game_type_id, int $game_description_id, $game_code, $game_type, $game]
	 *          	player_info [int $player_id, $player_username]
	 *           	amount_info [double $bet_amount, double $result_amount, $bet_for_cashback, double $real_betting_amount, $win_amount, $loss_amount, $after_balance]
	 *            	date_info [string $start_at, string $end_at, string $bet_at,updated_at]
	 *             	int flag
	 *              int status
	 *              additional_info [$has_both_side, string $external_uniqueid, string $round_number, string $md5_sum, int $response_result_id, int $sync_index, string bet_type]
	 *              bet_details
	 *              extra
	 *              game_logs_id, game_logs_unsettle_id
	 * @return array
	 */
	public function preprocessGameLogs($api, $params){

    	if(!is_object($api) || $api==null){
    		return false;
    	}

        //game_info
		$game_type_id=intval($params['game_info']['game_type_id']);
		$game_description_id=intval($params['game_info']['game_description_id']);
		$game_code=$params['game_info']['game_code'];
		$game_type=$params['game_info']['game_type'];
		$game=$params['game_info']['game'];
		//player_info
		$player_id=intval($params['player_info']['player_id']);
		$player_username=strval($params['player_info']['player_username']);
		//amount_info
		$amount_info=$params['amount_info'];
		$bet_amount=doubleval($amount_info['bet_amount']);
		$result_amount=doubleval($amount_info['result_amount']);
		$bet_for_cashback=doubleval($amount_info['bet_for_cashback']);
		$real_betting_amount=doubleval($amount_info['real_betting_amount']);
		$win_amount=doubleval($amount_info['win_amount']);
		$loss_amount=doubleval($amount_info['loss_amount']);
		$after_balance=doubleval($amount_info['after_balance']);
		//date_info
		$start_at=strval($params['date_info']['start_at']);
		$end_at=strval($params['date_info']['end_at']);
		$bet_at=strval($params['date_info']['bet_at']);
		// $updated_at=strval($params['date_info']['updated_at']);
		//always use current time for updated_at
		$updated_at=$this->utils->getNowForMysql();
		//additional_info
		$has_both_side=intval($params['additional_info']['has_both_side']);
		$external_uniqueid=strval($params['additional_info']['external_uniqueid']);
		$round_number=strval($params['additional_info']['round_number']);
		$md5_sum=strval($params['additional_info']['md5_sum']);
		$response_result_id=$params['additional_info']['response_result_id'];
		$sync_index=$params['additional_info']['sync_index'];
		$bet_type=$params['additional_info']['bet_type'];

		$flag=$params['flag'];
		$status=$params['status'];
		$bet_details=$params['bet_details'];
		$extra=$params['extra'];

        if (empty($player_id)) {
            $this->utils->error_log('lost player id', $player_id, $player_username);
            return false;
        }
        if ($amount_info['bet_amount']===null) {
            $this->utils->error_log('lost bet_amount', $amount_info);
            return false;
        }
        if ($amount_info['real_betting_amount']===null) {
            $this->utils->error_log('lost real_betting_amount', $amount_info);
            return false;
        }
        if ($amount_info['result_amount']===null) {
            $this->utils->error_log('lost result_amount', $amount_info);
            return false;
        }
        if (empty($game_description_id)) {
            $this->utils->error_log('lost game_description_id', $game_description_id, $game_code, $game);
            return false;
        }
        if (empty($game_type_id)) {
            $this->utils->error_log('lost game_type_id', $game_type_id, $game_type);
            return false;
        }
        if (empty($end_at)) {
            $this->utils->error_log('lost end_at', $params['date_info']);
            return false;
        }
        if (empty($bet_at)) {
            $this->utils->error_log('lost bet_at', $params['date_info']);
            return false;
        }
        // if (empty($updated_at)) {
        //     $this->utils->error_log('lost updated_at', $params['date_info']);
        //     return false;
        // }

        if (empty($win_amount)) {
            $win_amount = $result_amount > 0 ? $result_amount : 0;
        }
        if (empty($loss_amount)) {
            $loss_amount = $result_amount < 0 ? abs($result_amount) : 0;
        }
        if (empty($has_both_side)) {
            $has_both_side = 0;
        }
        if (empty($after_balance)) {
            $after_balance = 0;
        }

        //overwrite from extra
        // $real_betting_amount=$bet_amount;
        if(isset($extra['trans_amount'])){
            $real_betting_amount=$extra['trans_amount'];
            unset($extra['trans_amount']);
        }else if(isset($extra['real_betting_amount'])){
            $real_betting_amount=$extra['real_betting_amount'];
            unset($extra['real_betting_amount']);
        }

        // $bet_for_cashback=$bet_amount;
        // overwrite by extra
        if(isset($extra['bet_for_cashback'])){
            $bet_for_cashback=$extra['bet_for_cashback'];
            unset($extra['bet_for_cashback']);
        }

        // $this->CI->utils->debug_log('bet_for_cashback', $bet_for_cashback);

        if(isset($extra['running_platform'])){
            $running_platform=$extra['running_platform'];
            unset($extra['running_platform']);
        }else{
            $running_platform=$api->getCommonRunningPlatform();
        }

        $odds=0;
        if(isset($extra['odds'])){
            $odds=$extra['odds'];
            unset($extra['odds']);
        // }elseif(isset($extra['note'])){
        //     $betDetail=$this->utils->decodeJson($extra['note']);
        //     if(!empty($betDetail) && isset($betDetail['rate'])){
        //         $odds=$betDetail['rate'];
        //     }
        }

        # Specify the type of odds used in this data, possible value: eu, hk
        # This (for now) will not be recorded in game logs DB but will be used to determine bet_for_cashback
        $odds_type = null;
        if(isset($extra['odds_type'])) {
            $odds_type = $extra['odds_type'];
            unset($extra['odds_type']);
        }

        $date_from=null;
        if(isset($extra['query_date_from'])) {
            $date_from = $extra['query_date_from'];
            unset($extra['query_date_from']);
        }
        $date_to=null;
        if(isset($extra['query_date_to'])) {
            $date_to = $extra['query_date_to'];
            unset($extra['query_date_to']);
        }

        $game_platform_id = $api->getPlatformCode();
        if(isset($extra['game_platform_id'])) {
            $game_platform_id = $extra['game_platform_id'];
            unset($extra['game_platform_id']);
        }
        if(isset($extra['t1_game_platform_id'])) {
            $game_platform_id = $extra['t1_game_platform_id'];
            unset($extra['t1_game_platform_id']);
        }

        $data = array(
            'game_platform_id' => $game_platform_id,
            'game_type_id' => $game_type_id,
            'game_description_id' => $game_description_id,
            'game_code' => $game_code,
            'game_type' => $game_type,
            'game' => $game,
            'player_id' => $player_id,
            'player_username' => $player_username,
            'bet_amount' => $bet_amount,
            'real_betting_amount' => $real_betting_amount,
            'bet_for_cashback' => $bet_for_cashback,
            'result_amount' => $result_amount,
            'win_amount' => $win_amount,
            'loss_amount' => $loss_amount,
            'after_balance' => $after_balance,
            'trans_amount' => $real_betting_amount,
            'has_both_side' => $has_both_side,
            'external_uniqueid' => $external_uniqueid,
            'response_result_id' => $response_result_id,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'bet_at' => $bet_at,
            'updated_at' => $updated_at,
            'flag' => $flag,
            'running_platform' => $running_platform ,
            'odds' => $odds,
            'odds_type' => $odds_type,
            'table' => $round_number,
            'md5_sum' => $md5_sum,
            'bet_type'=>$bet_type,
            'sync_index'=>$sync_index,
            'status'=>$status,
        );

        if (!empty($extra)) {
            $data = array_merge($data, $extra);
        }

        //add created at
        // if (!empty($bet_details)) {
            // $bet_details = is_array($data['bet_details']) ? $data['bet_details'] : json_decode($data['bet_details'], true);
            // $betDetailsExistInGamelogs = json_decode($this->getGamelogsBetDetailsByExternalUniqueId($data['external_uniqueid']), true);
            // $bet_details['Created At'] = $data['updated_at'];
            // $bet_details['Last Sync Time'] = $this->utils->getNowForMysql();
            // if (!empty($betDetailsExistInGamelogs) && isset($betDetailsExistInGamelogs['Created At'])) {
            //     $bet_details['Created At'] = $betDetailsExistInGamelogs['Created At'];
            // }
            // $data['bet_details'] = json_encode($bet_details);
        // } else {
        	// $bet_details=['Created At' => $data['updated_at']];
        	// $bet_details=['Last Sync Time' => $this->utils->getNowForMysql()];
            // $data['bet_details'] = json_encode(array("Created At" => $this->utils->getNowForMysql()));
        // }

        //process odds type adn bet details
        # Invalidate / adjust bets
        # odds_type is defined in actual API implementation, can be hk or eu
        # If this is defined, means there will be odds and we need to apply the adjustment
        if (!empty($data['odds_type'])) {
            $odds_type = $data['odds_type'];
            // unset($data['odds_type']);//will save odds type

            # Sample config
            # $config['adjust_bet_by_odds'] = array(
            # IBC_API => array(
            #     'hk' => 0.5,
            #     'eu' => 1.5,
            #     'method' => 'invalidate',
            #     'invalid-game-types' => [134,135]
            # ))
            $adjust_bet_by_odds = $this->utils->getConfig('adjust_bet_by_odds');
            $api_id = $data['game_platform_id'];
            if(!empty($adjust_bet_by_odds) && array_key_exists($api_id, $adjust_bet_by_odds)) {
                $adjust_params = $adjust_bet_by_odds[$api_id];
                $odd_threshold = $adjust_params[$odds_type];
                $bet_adjust_method = $adjust_params['method'];

                if($data['odds'] < $odd_threshold) {
                    if($bet_adjust_method == 'invalidate') {
                        $data['bet_for_cashback'] = 0;
                        $bet_details["Status"] = self::BET_DETAILS_INVALID;
                    } elseif($bet_adjust_method == 'adjust') {
                        $odds = $data['odds'] - ($odds_type == 'eu' ? 1 : 0);
                        $data['bet_for_cashback'] *= $odds;
                    }
                    $this->utils->debug_log("adjust_bet_by_odds: Record's odds [$data[odds]] less than threshold [$odd_threshold], based on method [$bet_adjust_method], adjusted bet_for_cashback as [$data[bet_for_cashback]]");
                }

                # Invalidate tie bet
                if (isset($data['result_amount']) && abs($data['result_amount']) < 0.0001) {
                    $data['bet_for_cashback'] = 0;
                    $bet_details["Status"] = self::BET_DETAILS_INVALID;
                }

                # Invalidate configured game types, e.g. horse racing from IBC
                if (array_key_exists('invalid-game-types', $adjust_params)){
                    $invalid_game_types = $adjust_params['invalid-game-types'];
                    if(is_array($invalid_game_types) && in_array($data['game_type_id'], $invalid_game_types)) {
                        # Detected current game log is from an invalid game type
                        $data['bet_for_cashback'] = 0;
                        $bet_details["Status"] = self::BET_DETAILS_INVALID;
                    }
                }
            }
        }

        $data['bet_details']=json_encode($bet_details, JSON_UNESCAPED_UNICODE);
    	if($status==self::STATUS_SETTLED){
	        //for update game_logs
	        if(isset($params['game_logs_id'])){
	        	$data['id']=$params['game_logs_id'];
	        }
    	}else{
	        //for update game_logs
	        if(isset($params['game_logs_unsettle_id'])){
	        	$data['id']=$params['game_logs_unsettle_id'];
	        }
    	}
        return $data;
	}

	/**
	 *
	 * insert or update game_logs or unsettle
	 *
	 * @param  object $api
	 * @param  array $params
	 * 	       		game_info [int $game_type_id, int $game_description_id, $game_code, $game_type, $game]
	 *          	player_info [int $player_id, $player_username]
	 *           	amount_info [double $bet_amount, double $result_amount, $bet_for_cashback, double $real_betting_amount, $win_amount, $loss_amount, $after_balance]
	 *            	date_info [string $start_at, string $end_at, string $bet_at,updated_at]
	 *             	int flag
	 *              int status
	 *              additional_info [$has_both_side, string $external_uniqueid, string $round_number, string $md5_sum, int $response_result_id, int $sync_index, string bet_type]
	 *              bet_details
	 *              extra
	 *              game_logs_id, game_logs_unsettle_id
	 * @param  string $update_type
	 * @return
	 */
    public function commonInsertOrUpdateGameLogsOrUnsettleRow(
    	$api,
    	array $params,
        $update_type='update') {

    	if(!is_object($api) || $api==null){
    		return false;
    	}
    	//game_logs_unsettle_id or game_logs_id to id, depends status
    	$data=$this->preprocessGameLogs($api, $params);

    	if($data===false){
    		return false;
    	}

    	$status=$params['status'];

        $success=true;
		if ($status == self::STATUS_SETTLED) {
	        if($update_type=='insert'){
	        	$success=$this->insertRowToGameLogs($data);
	        }else{
	        	$success=$this->updateRowToGameLogs($data);
	        }
	    }else{
	        if($update_type=='insert'){
	        	$success=$this->insertRowToGameLogsUnsettle($data);
	        }else{
	        	$success=$this->updateRowToGameLogsUnsettle($data);
	        }
	    }

	    if($success && $this->utils->isEnabledFeature('enabled_sync_game_logs_stream')){
	        $insertStreamSuccess=$this->insertRowToStream($data);
	        if(!$insertStreamSuccess){
	        	$this->utils->error_log('insert stream failed', $data);
	        }
	    }

	    if(!$success){
	    	$this->utils->error_log($update_type.' failed , status:'.$status, $data);
	    }

        return $success;

    }

    /**
     * getIdMD5ByExternalUniqueid
     *
     * @param  string $uniqueid
     * @param  string $tableName
     * @param  string $date_from
     * @param  string $date_to
     * @return array [id, md5_sum]
     */
	public function getIdMD5ByExternalUniqueid($game_platform_id, $external_uniqueid, $tableName) {
		$external_uniqueid=strval($external_uniqueid);
		$this->db->select('id, md5_sum')->from($tableName)
		    ->where('external_uniqueid', $external_uniqueid)
		    ->where('game_platform_id', $game_platform_id);

		$row=$this->runOneRowArray();
		
		if($row) {
			$rlt=[$row['id'], $row['md5_sum']];
		}else {
			$rlt = [null,null];
		}
		
		unset($row);
		return $rlt;
	}

	public function build_daily_wagerdata_for_ole777($time_start, $time_end) {
		$date_start = date('Ymd', strtotime($time_end));
		$this->db->from("{$this->tableName} AS L")
			->join('player as P', 'L.player_id = P.playerId', 'left')
			->join('game_type as T', 'L.game_platform_id = T.game_platform_id AND L.game_type_id = T.id', 'left')
			->join('game_tags as G', 'T.game_tag_id = G.id', 'left')
			->where("end_at BETWEEN '{$time_start}' AND '{$time_end}'", null, false)
			->where(['flag' => 1])
			->select("'{$date_start}' AS date", false)
			->select([
				'P.username AS userCode' ,
				'L.game_platform_id AS ProductID' ,
				'L.game_type_id as GameTypeID' ,
				'G.tag_code AS game_tag' ,
				'COUNT(L.id) AS WagerCount' ,
				'SUM(bet_amount) as BetAmount' ,
				'IFNULL(SUM(trans_amount), SUM(bet_amount)) AS EffectiveAmount' ,
				'SUM(result_amount) AS WinLoss'
			])
			->group_by(['L.game_platform_id', 'G.tag_code', 'L.player_id'])
			->order_by('L.game_platform_id, G.tag_code, L.player_id')
		;

		$res = $this->runMultipleRowArray();

		$this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());
		$this->utils->debug_log(__METHOD__, 'res', $res);

		return $res;
	}

	/**
	 * Get game logs date column based on date type
	 * Default(end_at) or Settled(updated_at)
	 * @param  int $date_type
	 * @return string
	 * Created by Frans Eric Dela Cruz (frans.php.ph) 2018-11-06
	 */
	public function getGameLogsDateColumn($date_type)
	{
		$column = 'game_logs.end_at';
		switch ($date_type) {

			case self::DATE_TYPES['default']:
				$column = 'game_logs.end_at';
                break;
            case self::DATE_TYPES['settled']:
                $column = 'game_logs.end_at';
                break;
            case self::DATE_TYPES['bet']:
                $column = 'game_logs.bet_at';
                break;
            case self::DATE_TYPES['updated']:
                $column = 'game_logs.updated_at';
                break;
            default:
                $column = 'game_logs.end_at';
                break;
		}

		return $column;
	}

	/**
	 * @param $set_date_limit
	 * @param $game_platform_id
	 * @return null
	 */
	public function getUnsettledGameLogsByPlatformId($set_date_limit, $game_platform_id) {
		$this->db->select('bet_at, external_uniqueid, player_username');
		$this->db->where('game_platform_id', $game_platform_id);
		if($set_date_limit) {
			$this->db->where('bet_at BETWEEN NOW() - INTERVAL 60 DAY AND NOW()', "", false);
		}
		$this->db->from('game_logs_unsettle');
		return  $this->runMultipleRowArray();
	}

	public function batchGetFieldsByExternalUniqueid($fields,$uniqueids, $tableName, $date_from=null, $date_to=null) {
		$fields = implode(',',$fields);
		$this->db->select($fields)->from($tableName)->where_in('external_uniqueid', $uniqueids);

		if(!empty($date_from) && !empty($date_to)){
			$this->db->where('end_at >=', $date_from)->where('end_at <=', $date_to);
		}
		return $this->runRawSelectSQLArray();
	}

	/**
	 * always update by primary key
	 * @param  array $data
	 * @param  string $primaryKeyFieldInDB
	 * @return boolean
	 */
	public function batchUpdateRowToGameLogs(array $data , $primaryKeyFieldInDB = 'id') {
	    $this->db->update_batch('game_logs',$data,'id');
	    return  $this->db->affected_rows();
	}

    /**
     * query pagination
     *
     * @param string $start
     * @param $game_platform_id
     * @param $playerId
     * @param $size_per_page
     * @param $agent
     * @return array [$rows, $total_count, $last_datetime]
     */
	public function queryStreamByStartTime($start, $multiple_game_platform, $playerId, $min_size,
		$agent, $game_status, $tableName, $max_minutes_limit, &$sqlInfo=[]){

		$result=[[], 0, null, null];

		$gamegateway_stream_query_max_limit_seconds=$this->utils->getConfig('gamegateway_stream_query_max_limit_seconds');
		$from=$start;
		$now=new DateTime();
		//less than now-60s
		$now->modify('-'.$gamegateway_stream_query_max_limit_seconds.' seconds');
		$to=$now->format('Y-m-d H:i:s');
		if($max_minutes_limit>0){
			$fromDatetime=new DateTime($from);
			$fromDatetime->modify('+'.$max_minutes_limit.' minutes');
			$newTo=$fromDatetime->format('Y-m-d H:i:s');
			if($newTo<$to){
				$this->utils->debug_log('change to time', $newTo, 'old $to', $to);
				$to=$newTo;
			}
		}

        $gpidSQL=null;
        //required
        // $game_platform_id=intval($game_platform_id);
		if(!empty($multiple_game_platform)){
			//filter id
			$apiIdArr=[];
			foreach ($multiple_game_platform as $val) {
				$val=intval($val);
				if($val>0){
					$apiIdArr[]=$val;
				}
			}

			if(!empty($apiIdArr)){
				if(count($apiIdArr)==1){
		            $gpidSQL=' and game_logs.game_platform_id='.$apiIdArr[0].' ';
				}else{
		            $gpidSQL=' and game_logs.game_platform_id in ('.implode(',', $apiIdArr).') ';
				}

	        }else{
	        	//empty
	        	$this->utils->error_log('empty apiIdArr', $multiple_game_platform, $apiIdArr);
	        	return $result;
	        }
        }else{
        	//empty
        	$this->utils->error_log('empty multiple_game_platform', $multiple_game_platform);
        	return $result;
        }

		$playerSQL=null;
		$playerId=intval($playerId);
		if(!empty($playerId)){
			$playerSQL=' and game_logs.player_id='.$playerId.' ';
		}

        $merchant_code=$agent['agent_name'];
        $agent_id=intval($agent['agent_id']);

        $limit_count=$min_size;

		// $game_status = $tableName=='game_logs'?'settled':'unsettle';
		$date_filter_by='game_logs.updated_at';
		$use_index='use index(idx_updated_at)';

		$statusSQL=self::STATUS_SETTLED.' as detail_status, ';
		if($tableName=='game_logs_unsettle'){
			$statusSQL=' game_logs.status as detail_status,';
		}
		$dateSQL=' and '.$date_filter_by.' >= ? and '.$date_filter_by.' <= ?';
		$limitSQL=' limit '.$limit_count;
		$sql=$this->buildQueryGameLogStreamSQL($merchant_code, $game_status, $statusSQL,
			$tableName, $use_index, $date_filter_by, $playerSQL, $gpidSQL, $limitSQL, $dateSQL);

		// $isChangedNewTo=false;
		$params=[self::FLAG_GAME, $from, $to, $agent_id];
		$enabled_explain_function_for_query_stream=$this->utils->getConfig('enabled_explain_function_for_query_stream');
		if($max_minutes_limit<=0 && $enabled_explain_function_for_query_stream){
			//no limit
	        $countOfExplain=$this->queryExplainRows($sql, $params, 'game_logs');
	        $max_limit_of_query_stream=$this->utils->getConfig('max_limit_of_query_stream');
	        $max_minutes_limit_for_query_stream=$this->utils->getConfig('max_minutes_limit_for_query_stream');
	        $this->utils->debug_log('compare countOfExplain', $countOfExplain, 'max_limit_of_query_stream', $max_limit_of_query_stream, 'max_minutes_limit_for_query_stream', $max_minutes_limit_for_query_stream);
	        //if >500000 then 60 minutes
	        if($countOfExplain>$max_limit_of_query_stream){
	        	$this->utils->debug_log('reached max_limit_of_query_stream, will change to time', $countOfExplain, $max_limit_of_query_stream, $from, $to);
				$fromDatetime=new DateTime($from);
				$fromDatetime->modify('+'.$max_minutes_limit_for_query_stream.' minutes');
				$newTo=$fromDatetime->format('Y-m-d H:i:s');
				$this->utils->debug_log('try set new to', $newTo, $to);
				if($newTo<$to){
					$to=$newTo;
					// $isChangedNewTo=true;
					$this->utils->debug_log('set new to', $to);
				}
	        }
		}else{
			$this->utils->debug_log('still keep to', $to);
		}

		if($to > $now->format('Y-m-d H:i:s')){
			//means some code is wrong
			$this->utils->error_log('wrong $to, reset to $now', $to, $now);
			$to=$now->format('Y-m-d H:i:s');
		}

		$params=[self::FLAG_GAME, $from, $to, $agent_id];
		$sqlInfo=['sql'=>$sql, 'params'=>$params];
		$t=time();
		$this->utils->debug_log('query game history', $sql, $params);
		$rows=$this->runRawSelectSQLArray($sql, $params);
		$sqlInfo['sqlTime']=time()-$t;
		$this->utils->debug_log('result of query game history', $params, count($rows), $sqlInfo['sqlTime']);

		$last_datetime=null;
		$next_datetime=null;
		$total_count=0;
		if(empty($rows)){
			$rows=[];
			$next_datetime=$to;
		}else{
			$total_count=count($rows);
			//get last time from last row and query again
			$lastRow=$rows[count($rows)-1];// last row
			$last_datetime=$lastRow['updated_at'];
			$lastDT=new DateTime($last_datetime);
			$lastDT->modify('+1 second');
			$next_datetime=$lastDT->format('Y-m-d H:i:s');
			if($total_count>=$min_size){
				$lastParams=[self::FLAG_GAME, $last_datetime, $agent_id];
				$sqlInfo['lastParams']=$lastParams;
				$dateSQL=' and '.$date_filter_by.' = ? ';
				$limitSQL='';
				$lastSQL=$this->buildQueryGameLogStreamSQL($merchant_code, $game_status, $statusSQL,
					$tableName, $use_index, $date_filter_by, $playerSQL, $gpidSQL, $limitSQL, $dateSQL);
				$t=time();
				$sqlInfo['lastSQL']=$lastSQL;
				$rowsForLastTime=$this->runRawSelectSQLArray($lastSQL, $lastParams);
				$sqlInfo['lastSqlTime']=time()-$t;
				//merge $rowsForLastTime and $row
				$this->utils->debug_log('rowsForLastTime', $lastSQL, $lastParams, count($rowsForLastTime), $sqlInfo['lastSqlTime']);
				//remove same date time
				for ($i=$total_count-1; $i >= 0 ; $i--) {
					if($rows[$i]['updated_at']==$last_datetime){
						unset($rows[$i]);
					}else{
						break;
					}
				}
				$this->utils->debug_log('after delete last time', count($rows));
				$rows=array_merge($rows, $rowsForLastTime);
				$this->utils->debug_log('after merge', count($rows));
				$total_count=count($rows);
			}else{
				$this->utils->debug_log('ignore query last time,no enough rows', $total_count, $min_size);
			}
		}

        $result=[$rows, $total_count, $last_datetime, $next_datetime];

		return $result;

	}

	public function buildQueryGameLogStreamSQL($merchant_code, $game_status, $statusSQL,
			$tableName, $use_index, $date_filter_by, $playerSQL, $gpidSQL, $limitSQL, $dateSQL){
		$sql=<<<EOD
select game_logs.id as uniqueid,
CONCAT(game_logs.game_platform_id,game_logs.external_uniqueid)  as external_uniqueid,
game_logs.external_uniqueid as game_external_uniqueid,
game_provider_auth.login_name as username,
'{$merchant_code}' as merchant_code,
game_logs.game_platform_id as game_platform_id,
game_description.external_game_id as game_code,
game_description.game_name as game_name,
game_logs.end_at game_finish_time,
game_logs.note game_details,
game_logs.bet_at as bet_time,
game_logs.end_at as payout_time,
game_logs.`table` as round_number,
ROUND(ifnull(game_logs.trans_amount, game_logs.bet_amount), 2)  as real_bet_amount,
ROUND(game_logs.bet_amount, 2) as effective_bet_amount,
ROUND(game_logs.result_amount, 2) as result_amount,
ROUND(game_logs.result_amount+ifnull(game_logs.trans_amount,game_logs.bet_amount), 2) as payout_amount,
ROUND(game_logs.after_balance, 2) as after_balance,
game_logs.bet_details as bet_details,
game_logs.md5_sum,
game_logs.ip_address,
game_logs.bet_type,
game_logs.odds_type,
game_logs.odds,
ROUND(game_logs.rent, 2) as rent,
game_logs.response_result_id,
game_logs.external_log_id as update_version,
'{$game_status}' as game_status,
{$statusSQL}
game_logs.updated_at as updated_at
from {$tableName} as game_logs {$use_index}
join game_provider_auth on game_logs.player_id=game_provider_auth.player_id and game_logs.game_platform_id=game_provider_auth.game_provider_id
join game_description on game_description.id=game_logs.game_description_id
where
game_logs.flag=?
{$dateSQL}
and game_provider_auth.agent_id=?
{$playerSQL}
{$gpidSQL}
order by {$date_filter_by}
{$limitSQL}
EOD;

		return $sql;
	}

	public function queryPlayerGameReportPagination($from_date, $to_date, array $gamePlatformIdArray,
		$page_number, $size_per_page, $playerId=null, $game_platform_id=null, $agentId=null){
		//always from 1
		if($page_number<1){
			$page_number=1;
		}
        $limit_count=$size_per_page;
		$offset=($page_number-1)*$size_per_page;

		$result=[[], 0, 0, 0, null];
		if($size_per_page<=0){
			return $result;
		}

		$params=[$from_date, $to_date];
		$gamePlatformSQL='';
		if(!empty($game_platform_id)){
			$gamePlatformSQL='and game_platform_id=?';
			$params[]=$game_platform_id;
		}
		$playerSQL='';
		if(!empty($playerId)){
			$playerSQL='and player_id=?';
			$params[]=$playerId;
		}
		$agentSQL='';
		if(!empty($agentId)){
			$agentSQL='and agent_id=?';
			$params[]=$agentId;
		}

		$selectGP='';
		foreach ($gamePlatformIdArray as $gpId) {
			$selectGP.=' round(sum(if(game_platform_id='.$gpId.',betting_amount,0)),2) as turnover_'.$gpId.", \n".
				' round(sum(if(game_platform_id='.$gpId.', -result_amount,0)),2) as ggr_'.$gpId.", \n";
		}
		//query from game report
		$sql=<<<EOD
select
{$selectGP}
username,
game_username
from player_report_simple_game_daily
where
total_date>=? and total_date<=?
{$gamePlatformSQL}
{$playerSQL}
{$agentSQL}
group by username
order by username
limit {$offset}, {$limit_count}

EOD;

		$rows=$this->runRawSelectSQLArray($sql, $params);
		$this->utils->debug_log('query player_report_simple_game_daily', $sql, $params);
		if(empty($rows)){
			$rows=[];
		}

		$countSql=<<<EOD
select count(distinct username) cnt
from player_report_simple_game_daily
where
total_date>=? and total_date<=?
{$gamePlatformSQL}
{$playerSQL}
{$agentSQL}
EOD;

		$this->utils->debug_log('count player_report_simple_game_daily', $countSql, $params);
		$cntRows=$this->runRawSelectSQLArray($countSql, $params);
		$total_pages=0;
		if(!empty($cntRows)){
			$totalCnt=$cntRows[0]['cnt'];
			$total_pages= intval($totalCnt / $size_per_page) + ($totalCnt % $size_per_page > 0 ? 1 : 0);
		}

		$total_rows_current_page=count($rows);
		$current_page=$page_number;
        if($total_pages<$current_page){
            $current_page=$total_pages;
        }

		$sqlInfo=['sql'=>$sql, 'countSql'=>$countSql, 'params'=>$params];

        $result=[$rows, $total_pages, $current_page, $total_rows_current_page, $sqlInfo];

		return $result;
	}

	public function resetGameLogsIdOnRedis($id, &$error=null){
		//validate id from
		$this->db->select('min(id) as minid,max(id) as maxid')->from('game_logs');
		$row=$this->runOneRowArray();
		if($row['minid']<=$id && $id<=$row['maxid']){
			//wrong id, can't reset
			$error='cannot reset wrong id, min is '.$row['minid'].', max is '.$row['maxid'];
			$this->utils->error_log($error);
			return false;
		}
		$this->utils->debug_log('try reset '.self::GAME_LOGS_ID_KEY_REDIS, $id, $row);
    	return $this->utils->resetUniqueIdOnRedis(self::GAME_LOGS_ID_KEY_REDIS, $id);
	}

	public function resetGameLogsUnsettledIdOnRedis($id, &$error=null){
		//validate id from
		$this->db->select('min(id) as minid,max(id) as maxid')->from('game_logs_unsettle');
		$row=$this->runOneRowArray();
		if($row['minid']<=$id && $id<=$row['maxid']){
			//wrong id, can't reset
			$error='cannot reset wrong id, min is '.$row['minid'].', max is '.$row['maxid'];
			$this->utils->error_log($error);
			return false;
		}
		$this->utils->debug_log('try reset '.self::GAME_LOGS_UNSETTLED_ID_KEY_REDIS, $id, $row);
    	return $this->utils->resetUniqueIdOnRedis(self::GAME_LOGS_UNSETTLED_ID_KEY_REDIS, $id);
	}

}

/////end of file///////