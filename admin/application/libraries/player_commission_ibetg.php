<?php
class Player_commission_ibetg {
	public function __construct() 
	{
		$this->CI = &get_instance();
		$this->CI->load->model(array('player', 'player_model', 'player_friend_referral', 'friend_referral_level', 'external_system', 'game_type_model', 'affiliatemodel'));
		$this->CI->load->model('game_logs', 'game_model');
		$this->game_platforms = $this->CI->external_system->getAllActiveSytemGameApi();
	}

	public function generate_player_friend_refferred_logs_for_all($yearmonthdate = null, $player_username = null) {
		$previous_date = new DateTime();
		$previous_date->sub(new DateInterval('P1D'));
		$yearmonthdate = !empty($yearmonthdate) ? $yearmonthdate : $previous_date->format('Ymd');

		$this->user_id = $this->CI->users->getSuperAdminId();
		if ($player_username) {
			$player_info = $this->CI->player->getPlayerByUsername($player_username); 
			$player_id = $player_info['player_id'];
		}
		if (isset($player_id)) {
			$players = $this->get_all_referrial_players($player_id);
		} else {
			$players = $this->get_all_referrial_players();
		}
		array_walk($players, array($this, 'generate_player_friend_refferred_logs_for_one'), $yearmonthdate);
		return TRUE;
	}

	public function generate_player_friend_refferred_logs_for_one($player_id = null, $index = null, $yearmonthdate = null) {
		$previous_date = new DateTime();
		$previous_date->sub(new DateInterval('P1D'));
		$yearmonthdate = !empty($yearmonthdate) ? $yearmonthdate : $previous_date->format('Ymd');
		$execute_date = new DateTime($yearmonthdate);
		$execute_datetime_range = array(
			'start_date' => $execute_date->format('Y-m-d 00:00:00'),
			'end_date' => $execute_date->format('Y-m-d 23:59:59')
		);
		$user_id = $this->CI->users->getSuperAdminId();
		list($referred_count, $total_bets) = $this->CI->player_friend_referral->getPlayerFriendRefferedCountAndTotalBets($player_id, $execute_datetime_range['start_date'], $execute_datetime_range['end_date']);
		$current_timestamp = date('Y-m-d H:i:s');
		$data = Array(
			'player_id' => $player_id,
			'year_month_date' => $yearmonthdate,
			'referred_count' => $referred_count,
			'total_bets' => $total_bets,
			'note' => 'calc year month date' . $yearmonthdate . ' at ' . $current_timestamp,
			'updated_by' => $user_id,
			'updated_at' => $current_timestamp
		);

		$query = $this->CI->db->get_where('player_friend_referrial_logs', array('year_month_date'=> $yearmonthdate, 'player_id'=>$player_id));
		//if no record found then insert else update
		if ($query->num_rows <= 0) {
			$data['created_at'] = $current_timestamp;
			$this->CI->db->insert('player_friend_referrial_logs', $data);
		} else {
			$this->CI->db->update('player_friend_referrial_logs', $data, array('year_month_date'=> $yearmonthdate, 'player_id'=>$player_id));
		}
	}

	public function generate_monthly_earnings_for_all($yearmonth = NULL, $player_username = NULL) 
	{
		$yearmonth = empty($yearmonth) ? $this->CI->utils->getLastYearMonth() : $yearmonth;
		list($start_date, $end_date) = $this->CI->utils->getMonthRange($yearmonth);
		$this->yearmonth 			 = $yearmonth; 
		$this->start_date 			 = $start_date; 
		$this->end_date 			 = $end_date;
		//get refferial player setting
		$result = $this->CI->player->getFriendReferralSettings();
		$affiliateDefaultSetting = $this->CI->affiliatemodel->getDefaultAffSettings();
		$this->minimumBetting    = $affiliateDefaultSetting['minimumBetting'];
		$this->minimumBettingTimes = $affiliateDefaultSetting['minimumBettingTimes'];
		$this->user_id 				 = $this->CI->users->getSuperAdminId();
		if ($player_username) {
			$player_info = $this->CI->player->getPlayerByUsername($player_username); 
			$player_id = $player_info['player_id'];
		}
		if (isset($player_id)) {
			var_dump('COMPUTING PLAYER COMMISSION FOR ' . $player_username);
			$players = $this->get_all_referrial_players($player_id);
		} else {
			$players = $this->get_all_referrial_players();
		}
		//get player referrial setting
		array_walk($players, array($this, 'generate_monthly_earnings_for_one'), $yearmonth);
		return TRUE;
	}

	public function generate_monthly_earnings_for_one($player_id = NULL, $index = NULL, $yearmonth = NULL) 
	{
		$friend_referral_level = $this->CI->friend_referral_level->getAllFriendReferralLevel();
		$referral = $this->generate_player_commission_record($friend_referral_level, $player_id);
		
		$total_commission = $referral['commission_amount'];
		//get last 3 month commission not include this year month
		// for ($i = 3; $i >= 1; $i--) {
		// 	$lastMonth = new DateTime($yearmonth);
		// 	$lastMonth->sub(new DateInterval('P'.$i.'M'));
		// 	$report_where = Array('year_month' => $lastMonth->format('Ym'), 'player_id' => $player_id, 'paid_flag' => 0);
		//  	$query = $this->CI->db->select('total_commission')->from('friend_referrial_monthly_earnings')->where($report_where)->get();
		// 	if ($query->num_rows > 0) {
		// 		$total_commission += (float)$query->row()->total_commission;
		// 	}
		// }

		$current_timestamp = date('Y-m-d H:i:s');

		$data = Array(
			'year_month'						=> $this->yearmonth,
			'player_id'							=> $player_id,
			'active_players'					=> $referral['active_players'],
			'total_players'						=> $referral['total_players'],
			'total_bets'						=> $referral['total_bets'],
			'total_commission'					=> $referral['commission_amount'],
			'type'								=> 1,
			'paid_flag'							=> 0,
			'manual_flag'						=> 0,
			'note'								=> 'calc year month ' . $this->yearmonth . ' at ' . $current_timestamp,
			'updated_by'						=> $this->user_id,
			'updated_at'						=> $current_timestamp,

		);

		//where condition
		$where = Array('year_month' => $data['year_month'], 'player_id' => $data['player_id']);
		//query same record first
		$query = $this->CI->db->get_where('friend_referrial_monthly_earnings', $where);
		//if no record found then insert else update
		if ($query->num_rows <= 0) {
			$this->CI->db->insert('friend_referrial_monthly_earnings', $data);
		} else {
			$this->CI->db->update('friend_referrial_monthly_earnings', $data, $where);
		}
	}

	public function generate_player_commission_record($friend_referral_level, $player_id) 
	{
		list($players_id, $active_players, $total_players) = $this->get_players_id_active_players_and_total_players($player_id, $friend_referral_level, $this->start_date, $this->end_date);
		list($total_bets, $commission) = $this->get_gross_revenue($players_id, $active_players, $friend_referral_level, $this->start_date, $this->end_date);
		return array(
			'active_players' => $active_players,
			'total_players' => $total_players,
			'total_bets' => $total_bets,
			'commission_amount' => $commission
		);

	}

	public function get_players_id_active_players_and_total_players($player_id, $friend_referral_level, $start_date, $end_date) {
		$referred_players_id = array(); 
		$total_players = 0;

		try {
			$referred_players_id = $this->get_all_player_referred($player_id);
			if ( ! empty($referred_players_id)) {
				$total_players = count($referred_players_id);
				//活跃会员定义：月结区间内投注总额不低于500RMB。
				$active_players_id = $this->CI->game_model->filterActivePlayersByPlayersId($referred_players_id, $this->minimumBetting, $this->minimumBettingTimes, $start_date, $end_date);
				$active_players = count($active_players_id);
			}
		} catch (Exception $e) {
			$this->CI->utils->error_log($e->getMessage());
		}
		return array($referred_players_id, $active_players, $total_players);
	}

	public function get_gross_revenue($players_id, $total_players, $friend_referral_level, $start_date, $end_date)
	{
		//get this player total bet
		list($total_bets) = $this->CI->game_model->getTotalBetsWinsLossByPlayersForce($players_id, $start_date, $end_date);
		$minimumSettingIndex = $this->getFriendReferialLevelSetting($friend_referral_level, $total_bets, $total_players);
		$current_betting_calulated = 0;
		$commission = 0;
		//get game bets by refered player
		// foreach($friend_referral_level as $index => $setting)
		// {
		// 	if ($index > $minimumSettingIndex) break;
			$level_setting = $this->fix_setting($friend_referral_level[$minimumSettingIndex]['selected_game_tree']);
			$platFormCommission = Array();
			foreach ($level_setting as $game_id => $game_share) {
				if (strpos($game_id, '_') > 0) {
					if (!empty($game_share)) {
						$game_id = substr($game_id, 3, strlen($game_id) - 3);
						list($game_bets) = $this->CI->game_model->getTotalBetsWinsLossByPlayersForce($players_id, $start_date, $end_date, $game_id);
						if ($game_bets > 0){
							$platFormCommission[$game_id]['total_bets'] = round($game_bets * ($game_share/ 100) * 100, 0) / 100;
						}
						else{
							$platFormCommission[$game_id]['total_bets'] = 0;
						}
					} else {
						$platFormCommission[$game_id]['total_bets'] = 0;
					}
				} else {
					if (!empty($game_share)) {
						$gameTypeInfo = $this->CI->game_type_model->getGameTypeById($game_id);
						$game_platform_id = $gameTypeInfo->game_platform_id;
						list($game_bets) = $this->CI->game_model->getTotalBetsWinsLossByPlayersByGameType($players_id, $start_date, $end_date, $game_platform_id, $game_id);
						if ($game_bets > 0){
							$platFormCommission[(int)$game_platform_id][$game_id]['total_bets'] = round($game_bets * ($game_share/ 100) * 100, 0) / 100;
						}
						else
						{
							$platFormCommission[(int)$game_platform_id][$game_id]['total_bets'] = 0;
						}
					} else {
						$platFormCommission[(int)$game_platform_id][$game_id]['total_bets'] = 0;
					}
				}
			}
			//$game_bets -= $setting['max_betting'];
		//}
		//var_dump($platFormCommission);
		//check for game type commission
		foreach($platFormCommission as $game_platform_id => $game_type) {
			if (is_array($game_type)){
				foreach ($game_type as $game_type_id => $game_type_commission) {
					$commission += $game_type_commission['total_bets'];
				}
			}
		}
		//if no game type commission get game platform commission
		if ($commission == 0) {
			foreach($platFormCommission as $game_platform) {
				$commission += $game_platform['total_bets'];
			}
		}
		return Array($total_bets, $commission);
	}

	public function get_all_referrial_players($player_id = null) {
		$sql = "SELECT DISTINCT p.* FROM player AS p
			INNER JOIN playerfriendreferral AS pfr ON p.playerId = pfr.playerId
			INNER JOIN playeraccount as pa on p.playerId = pa.playerId where pa.type = 'wallet'";

		if (!empty($player_id))
			$sql .= " AND p.playerId = ? ";

		$query = $this->CI->db->query($sql, array($player_id));

		$players = array_column($query->result_array(), 'playerId');
		return $player_id ? [$player_id] : $players;
	}

	public function get_all_player_referred($player_id = null) {
		$sql = "SELECT p.* FROM player AS p
			INNER JOIN playerfriendreferral AS pfr ON p.playerId = pfr.invitedPlayerId
			INNER JOIN playeraccount as pa on p.playerId = pa.playerId where pa.type = 'wallet'";

		if (!empty($player_id))
			$sql .= " AND pfr.playerId = ? ";

		$query = $this->CI->db->query($sql, array($player_id));

		$players = array_column($query->result_array(), 'playerId');
		return $players;
	}

	public function getFriendReferialLevelSetting($friend_referral_level, $total_bets, $total_players) {
		$index = 0;
		$level = Array();
		foreach ($friend_referral_level as $setting) 
		{
			if (($total_bets >= $setting['min_betting'] && $total_bets <= $setting['max_betting']) || ($total_bets >= $setting['min_betting'] && $setting['max_betting'] == 0)) {
				$level[] = $index;
			} else if (($total_players >= $setting['min_volid_player'] && $total_players <= $setting['max_volid_player']) || ($total_players >= $setting['min_volid_player'] && $setting['max_volid_player'] == 0)) {
				$level[] = $index;
			}
			$index++;
		}
		if (empty($level))
			$level[] = 0;
		return min($level);
	}

	public function fix_setting($level_game_setting) {
		$setting = json_decode($level_game_setting, true);
		$result = Array();
		foreach($setting as $key => $value) {
			// if (substr($key, 0, 3) == "gp_")
			// 	$key = substr($key, 3, strlen($key) - 3);
			$result[$key] = $value;
		}
		return $result;
	}
}