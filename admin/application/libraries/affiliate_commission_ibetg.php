<?php

## MODELS
#  affiliatemodel
#  affiliate_earnings
#  external_system
#  player_model
#  transactions
#  total_player_game_hour or game_logs
#
## CHECK
#  $this->CI->affiliatemodel->getAllPlayersUnderAffiliateId();
#  $this->CI->affiliatemodel->filterActivePlayersById();
#  $this->CI->affiliatemodel->filterActivePlayersByIdByProvider();
#  $this->CI->affiliatemodel->getAllAffiliateTerm();
#  $this->CI->affiliate_earnings->get_deposit_withdraw_income();
#  $this->CI->affiliate_earnings->transferAllEarningsToWallet();
#  $this->CI->external_system->getAllActiveSytemGameApi();
#  $this->CI->transactions->sumTransactionFee();
#  $this->CI->transactions->get_players_with_minimum_deposit();
#  $this->CI->player_model->getPlayersTotalBonus();
#  $this->CI->player_model->getPlayersTotalCashback();
#  $this->CI->total_player_game_hour->getTotalBetsWinsLossByPlayers();
#  $this->CI->game_logs->getTotalBetsWinsLossByPlayers();
#
## UTILS
#  $this->CI->users->getSuperAdminId();
#  $this->CI->utils->error_log();
#  $this->CI->utils->getMonthRange();
#  $this->CI->utils->filterActiveGameApi();
#  $this->CI->utils->getLastYearMonth();
class Affiliate_commission_ibetg {

	private $affiliates_settings = array();

	public function __construct() {
		$this->CI = &get_instance();
		$use_total_hour = $this->CI->utils->getConfig('use_total_hour');
		$this->CI->load->model(array('affiliatemodel','affiliate_earnings','external_system','player_model', 'operatorglobalsettings', 'game_type_model'));
		$this->CI->load->model('game_logs', 'game_model');
		$this->game_platforms = $this->CI->external_system->getAllActiveSytemGameApi();
	}

	public function generate_monthly_earnings_for_all($yearmonth = NULL, $affiliate_username = NULL) {

		$yearmonth 					 = empty($yearmonth) ? $this->CI->utils->getLastYearMonth() : $yearmonth;
		list($start_date, $end_date) = $this->CI->utils->getMonthRange($yearmonth);
		$this->yearmonth 			 = $yearmonth; 
		$this->start_date 			 = $start_date; 
		$this->end_date 			 = $end_date;
		$this->user_id 				 = $this->CI->users->getSuperAdminId();
		if ($affiliate_username) {
			$affiliate_id = $this->CI->affiliatemodel->getAffiliateIdByUsername($affiliate_username); 
		}

		if (isset($affiliate_id)) {
			var_dump('COMPUTING AFFILIATE COMMISSION FOR ' . $affiliate_username);
			$affiliates = $this->get_all_affiliates($affiliate_id);
		} else {
			$affiliates = $this->get_all_affiliates();
		}

		array_walk($affiliates, array($this, 'generate_monthly_earnings_for_one'), $yearmonth);

		//6. 只进行结算和生成报表。客户自行决定发放与否
		//if (isset($this->default_settings['autoTransferToWallet']) && $this->default_settings['autoTransferToWallet']) {
		//	$this->CI->affiliate_earnings->transferAllEarningsToWallet($this->yearmonth, $this->default_settings['minimumPayAmount']);
		//}

		return TRUE;
	}

	public function generate_monthly_earnings_for_one($affiliates = NULL, $index = NULL, $yearmonth = NULL) {

		$affiliate_id = $affiliates['affiliateId'];
		$affiliate_settings	= $this->get_affiliate_settings($affiliate_id);
		//var_export($affiliate_settings);
		$affiliate = $this->generate_affiliate_commission_record($affiliate_settings, $affiliate_id);
		$commission_from_sub_affiliates = $this->generate_sub_affiliate_commission_record($affiliate_settings, $affiliates);
		$total_commission = $affiliate['commission_amount'] + $commission_from_sub_affiliates;

		//get last 3 month commission not include this year month
		// $totalactiveplayer = 0;
		// $last_commissions = array();
		// for ($i = 1; $i <= 3; $i++) {
		// 	// $i = 1;
		// 	$lastMonth = new DateTime($this->yearmonth);
		// 	$lastMonth->sub(new DateInterval('P'.$i.'M'));
		// 	$report_where = Array('year_month' => $lastMonth->format('Ym'), 'affiliate_id' => $affiliate_id, 'paid_flag' => Affiliate_earnings::DB_FALSE);
		//  	$query = $this->CI->db->select('total_commission')->from('aff_monthly_earnings')->where($report_where)->get();
		// 	if ($query->num_rows > 0) {
		// 		$totalactiveplayer += (int)$query->row()->active_players;
		// 		$last_commissions[$i]['active_players'] = (int)$query->row()->active_players;
		// 		$last_commissions[$i]['total_commission'] = (int)$query->row()->total_commission;
		// 	}
		// }
		// //if total active player grater then minimum total active players
		// if ($totalactiveplayer >= $affiliate_settings['totalactiveplayer']) {
		// 	foreach ($last_commissions as $commission) {
		// 		$total_commission += $commission['total_commission'];
		// 	}
		// }

		$current_timestamp = date('Y-m-d H:i:s');

		$data = array(
			'year_month'						=> $this->yearmonth,
			'affiliate_id'						=> $affiliate_id,
			'active_players'					=> $affiliate['active_players'],
			'total_players'						=> $affiliate['total_players'],
			'gross_revenue'						=> $affiliate['gross_revenue'],
			'platform_fee'						=> $affiliate['platform_fee'],
			'bonus_fee'							=> $affiliate['bonus_fee'],
			'cashback_fee'						=> $affiliate['cashback_fee'],
			'transaction_fee'					=> $affiliate['transaction_fee'],
			'admin_fee'							=> $affiliate['admin_fee'],
			'total_fee'							=> $affiliate['total_fee'],
			'net_revenue'						=> $affiliate['net_revenue'],
			'commission_percentage'				=> $affiliate['commission_rate'],
			'commission_amount'					=> $affiliate['commission_amount'],
			'commission_from_sub_affiliates'	=> $commission_from_sub_affiliates,
			'total_commission'					=> $total_commission,
			'type'								=> Affiliate_earnings::TYPE_INIT,
			'paid_flag'							=> Affiliate_earnings::DB_FALSE,
			'manual_flag'						=> Affiliate_earnings::DB_FALSE,
			'note'								=> 'calc year month ' . $this->yearmonth . ' at ' . $current_timestamp,
			'updated_by'						=> $this->user_id,
			'updated_at'						=> $current_timestamp,
		);

		// if ($total_commission) {
		// 	var_dump($data);
		// }
		//where condition
		$where = Array('year_month' => $data['year_month'], 'affiliate_id' => $data['affiliate_id']);
		//query same record first
		$query = $this->CI->db->get_where('aff_monthly_earnings', $where);
		//if no record found then insert else update
		if ($query->num_rows <= 0) {
			$this->CI->db->insert('aff_monthly_earnings', $data);
		} else {
			$this->CI->db->update('aff_monthly_earnings', $data, $where);
		}
	}

	public function get_affiliate_settings($affiliate_id) {

		if (empty($this->affiliates_settings)) {
			$this->affiliates_settings 	= $this->CI->affiliatemodel->getAllAffiliateTerm();
			$this->default_settings 	= $this->affiliates_settings[-1];
		}

		$affiliate_settings = isset($this->affiliates_settings[$affiliate_id]) ? $this->affiliates_settings[$affiliate_id] : $this->default_settings;

		array_unshift($affiliate_settings['sub_levels'], $affiliate_settings['level_master']);

		return $affiliate_settings;

	}

	public function get_all_affiliates($affiliate_id = NULL) {
		$query = $this->CI->db->query("SELECT affiliateId, parentId, username FROM affiliates");
		$affiliates = array_column($query->result_array(), null, 'affiliateId');
		foreach ($affiliates as $affiliateId => &$affiliate) {
			$parentId = $affiliate['parentId'];
			if ($parentId) {
				$affiliates[$parentId]['sub_affiliates'][] = &$affiliate;
			}
		}
		return $affiliate_id ? [$affiliates[$affiliate_id]] : $affiliates;
	}

	public function generate_affiliate_commission_record($affiliate_settings, $affiliate_id) {

		$bonus_fee_rate 		= isset($affiliate_settings['bonus_fee']) ? $affiliate_settings['bonus_fee'] / 100 : 0;
		$cashback_fee_rate 		= isset($affiliate_settings['cashback_fee']) ? $affiliate_settings['cashback_fee'] / 100 : 0;
		$transaction_fee_rate 	= isset($affiliate_settings['transaction_fee']) ? $affiliate_settings['transaction_fee'] / 100 : 0;
		$this->admin_fee_rate 	= isset($affiliate_settings['admin_fee']) ? $affiliate_settings['admin_fee'] / 100 : 0;

		list($players_id, $active_players, $total_players) = $this->get_players_id_active_players_and_total_players($affiliate_id, $affiliate_settings, $this->start_date, $this->end_date);
		$bonus_fee = 0;
		if ($bonus_fee_rate > 0) {
			$total_bonus = $this->CI->player_model->getPlayersTotalBonus($players_id, $this->start_date, $this->end_date);
			$bonus_fee = $total_bonus * $bonus_fee_rate;
		}

		$cashback_fee = 0;
		if ($cashback_fee_rate > 0) {
			$total_cashback = $this->CI->player_model->getPlayersTotalCashback($players_id, $this->start_date, $this->end_date);
			$cashback_fee = $total_cashback * $cashback_fee_rate;
		}

		$transaction_fee = 0;
		if ($transaction_fee_rate > 0) {
			$total_transaction = $this->CI->transactions->sumTransactionFee($players_id, $this->start_date, $this->end_date);
			$transaction_fee = $total_transaction * $transaction_fee_rate;
		}

		$total_fee = $bonus_fee + $cashback_fee + $transaction_fee;

		list($gross_revenue, $net_revenue, $net_commission, $platform_fee, $admin_fee, $total_bet_result) = $this->get_gross_revenue_and_platform_fee($players_id, $active_players, $affiliate_settings, $total_fee, $this->start_date, $this->end_date);

		$total_fee += $admin_fee;

		return array(
			'active_players' => $active_players,
			'total_players' => $total_players,
			'gross_revenue' => $gross_revenue,
			'platform_fee' => $platform_fee,
			'bonus_fee' => $bonus_fee,
			'cashback_fee' => $cashback_fee,
			'transaction_fee' => $transaction_fee,
			'admin_fee' => $admin_fee,
			'total_fee' => $total_fee,
			'net_revenue' => $net_revenue,
			'commission_rate' => 0,
			'commission_amount' => $net_commission,
			'total_bet_result' => $total_bet_result
		);

	}

	public function get_players_id_active_players_and_total_players($affiliate_id, $affiliate_settings, $start_date, $end_date) {
		$players_id = array(); $active_players = 0; $total_players = 0;
		try {
			$players_id = $this->CI->affiliatemodel->getAllPlayersUnderAffiliateId($affiliate_id);
			if ( ! empty($players_id)) {
				$total_players = count($players_id);
				//活跃会员定义：月结区间内进行过最少五次有效下注且投注总额不低于500RMB。
				$active_players_id = $this->CI->game_model->filterActivePlayersByPlayersId($players_id, $affiliate_settings['minimumBetting'], $affiliate_settings['minimumBettingTimes'], $start_date, $end_date);
				//var_dump("active players id : " . var_export($active_players_id, true));
				//$active_players_id = $this->CI->transactions->get_players_with_minimum_deposit_and_minimum_deposit_count($affiliate_settings['minimumDeposit'], 5, $start_date, $end_date, $players_id);
				// $active_players_id = $this->CI->affiliatemodel->filterActivePlayersById($affiliate_settings, $players_id, $start_date, $end_date, 'day');
				// $game_platform_ids  = $this->CI->utils->filterActiveGameApi($affiliate_settings['provider']);
				// if ( ! empty($game_platform_ids)) {
				// 	$active_players_id = $this->CI->affiliatemodel->filterActivePlayersByIdByProvider($affiliate_settings, $active_players_id, $start_date, $end_date, $game_platform_ids, $affiliate_settings['totalactiveplayer']);
				// }
				$active_players = count($active_players_id);
			}
		} catch (Exception $e) {
			$this->CI->utils->error_log($e->getMessage());
		}
		return array($players_id, $active_players, $total_players);
	}

	public function get_gross_revenue_and_platform_fee($players_id, $active_players, $affiliate_settings, $total_fee, $start_date, $end_date) {

		$gross_revenue = 0; $net_revenue = 0; $net_commission = 0; $total_platform_fee = 0; $total_admin_fee = 0; $total_bet_result = 0;
		
		try {
			if ( ! empty($players_id)) {
				switch ($affiliate_settings['baseIncomeConfig']) {
					case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN:
					case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN_TOTALCOMMISSION:
					case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN_ACTIVEPLAYERCOMMISSION:
					case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN_ACTIVEPLAYERCOMMISSIONBYGAMEPLATFORM:
						list($total_bet) = $this->CI->game_model->getTotalBetsWinsLossByPlayersForce($players_id, $start_date, $end_date);
						//var_dump('total_bet ' . $total_bet);
						//getTotalBetsWinsLossByPlayers($players_id, $start_date, $end_date);
						//get affilicate level setting
						$affilicateLevels = json_decode($this->CI->operatorglobalsettings->getSettingJson('affilliate_level_settings', 'template'), true);
						
						$setting = $this->getAffilicateLevelSetting($affilicateLevels, $total_bet, $active_players);

						$result = Array();
						//var_dump("selected_game_tree" . var_export($setting['selected_game_tree'], true));
						foreach ($setting['selected_game_tree'] as $game_id => $game_share) {
							if (strpos($game_id, '_') > 0) {
								if (!empty($game_share)) {
									$game_platform_id = substr($game_id, 3, strlen($game_id) - 3);
									list($bet, $win, $loss)   = $this->CI->game_model->getTotalBetsWinsLossByPlayersForce($players_id, $start_date, $end_date, $game_platform_id);
									$game_platform_rate = isset($this->game_platforms[$game_platform_id]['game_platform_rate']) ? (100 - $this->game_platforms[$game_platform_id]['game_platform_rate']) / 100 : 0;
									$result[$game_platform_id]['bet'] = $bet;
									$result[$game_platform_id]['win'] = $win;
									$result[$game_platform_id]['loss'] = $loss;
									$bet_result = $loss - $win;
									$game_platform_fee = $bet_result * $game_platform_rate;
									$game_platform_gross = $bet_result - $game_platform_fee;
									$admin_fee = $game_platform_gross * $this->admin_fee_rate;
									$bet_ratio = $total_bet != 0 ? ($bet / $total_bet) : 0;
									$game_platform_net = $game_platform_gross - $admin_fee - ($total_fee * $bet_ratio);
									$result[$game_platform_id]['bet_result'] = $bet_result;
									$result[$game_platform_id]['game_platform_fee'] = $game_platform_fee;
									$result[$game_platform_id]['game_platform_gross'] = $game_platform_gross;
									$result[$game_platform_id]['admin_fee'] = $admin_fee;
									$result[$game_platform_id]['bet_ratio'] = $bet_ratio;
									$result[$game_platform_id]['game_platform_net'] = $game_platform_net;
									$result[$game_platform_id]['game_platform_commission'] = round($result[$game_platform_id]['game_platform_net'] * ($game_share /100) * 100, 0) / 100;
								} else {
									$result[$game_platform_id]['bet'] = 0;
									$result[$game_platform_id]['win'] = 0;
									$result[$game_platform_id]['loss'] = 0;
									$result[$game_platform_id]['bet_result'] = 0;
									$result[$game_platform_id]['game_platform_fee'] = 0;
									$result[$game_platform_id]['game_platform_gross'] = 0;
									$result[$game_platform_id]['admin_fee'] = 0;
									$result[$game_platform_id]['bet_ratio'] = 0;
									$result[$game_platform_id]['game_platform_net'] = 0;
									$result[$game_platform_id]['game_platform_commission'] = 0;
								}
							} else {
								if (!empty($game_share)) {
									var_dump('game_share : ' . $game_share);
									$gameTypeInfo = $this->CI->game_type_model->getGameTypeById($game_id);
									$game_platform_id = $gameTypeInfo->game_platform_id;
									list($bet, $win, $loss) = $this->CI->game_model->getTotalBetsWinsLossByPlayersByGameType($players_id, $start_date, $end_date, $game_platform_id, $game_id);
									$result[$game_platform_id]['game_type'][$game_id]['bet'] = $bet;
									$result[$game_platform_id]['game_type'][$game_id]['win'] = $win;
									$result[$game_platform_id]['game_type'][$game_id]['loss'] = $loss;
									$bet_result = $loss - $win;
									$game_platform_fee = $bet_result * $game_platform_rate;
									$game_platform_gross = $bet_result - $game_platform_fee;
									$admin_fee = $game_platform_gross * $this->admin_fee_rate;
									$bet_ratio = $total_bet != 0 ? ($bet / $total_bet) : 0;
									$game_platform_net = $game_platform_gross - $admin_fee - ($total_fee * $bet_ratio);
									$result[$game_platform_id]['game_type'][$game_id]['bet_result'] = $bet_result;
									$result[$game_platform_id]['game_type'][$game_id]['game_platform_fee'] = $game_platform_fee;
									$result[$game_platform_id]['game_type'][$game_id]['game_platform_gross'] = $game_platform_gross;
									$result[$game_platform_id]['game_type'][$game_id]['admin_fee'] = $admin_fee;
									$result[$game_platform_id]['game_type'][$game_id]['bet_ratio'] = $bet_ratio;
									$result[$game_platform_id]['game_type'][$game_id]['game_platform_net'] = $game_platform_net;
									$result[$game_platform_id]['game_type'][$game_id]['game_platform_commission'] = round($result[$game_platform_id]['game_type'][$game_id]['game_platform_net'] * ($game_share /100) * 100, 0) / 100;
								} else {
									$result[$game_platform_id]['game_type'][$game_id]['bet'] = 0;
									$result[$game_platform_id]['game_type'][$game_id]['win'] = 0;
									$result[$game_platform_id]['game_type'][$game_id]['loss'] = 0;
									$result[$game_platform_id]['game_type'][$game_id]['bet_result'] = 0;
									$result[$game_platform_id]['game_type'][$game_id]['game_platform_fee'] = 0;
									$result[$game_platform_id]['game_type'][$game_id]['game_platform_gross'] = 0;
									$result[$game_platform_id]['game_type'][$game_id]['admin_fee'] = 0;
									$result[$game_platform_id]['game_type'][$game_id]['bet_ratio'] = 0;
									$result[$game_platform_id]['game_type'][$game_id]['game_platform_net'] = 0;
									$result[$game_platform_id]['game_type'][$game_id]['game_platform_commission'] = 0;
								}
							}
						}
						//game type commission first
						foreach ($result as $game_platform_id => $game_platform) {
							$commission = 0;
							if (!empty($game_platform['game_type']))
							{
								foreach($game_platform['game_type'] as $game_type_id => $row) {
									$commission += $row['game_platform_commission'];
									$gross_revenue += $row['game_platform_gross'];
									$net_revenue += $row['game_platform_net'];
									$net_commission += $row['game_platform_commission'];
									$total_platform_fee += $row['game_platform_fee'];
									$total_admin_fee += $row['admin_fee'];
									$total_bet_result += $row['bet_result'];
								}
							}
							if ($commission == 0) {
								$gross_revenue += $game_platform['game_platform_gross'];
								$net_revenue += $game_platform['game_platform_net'];
								$net_commission += $game_platform['game_platform_commission'];
								$total_platform_fee += $game_platform['game_platform_fee'];
								$total_admin_fee += $game_platform['admin_fee'];
								$total_bet_result += $game_platform['bet_result'];
							}
						}
						break;

					case Affiliatemodel::INCOME_CONFIG_TYPE_DEPOSIT_WITHDRAWAL:
						$gross_revenue = $this->CI->affiliate_earnings->get_deposit_withdraw_income($players_id, $start_date, $end_date);
						break;
				}
			}
		} catch (Exception $e) {
			$this->CI->utils->error_log($e->getMessage());
		}

		return array($gross_revenue, $net_revenue, $net_commission, $total_platform_fee, $total_admin_fee, $total_bet_result);
	}

	public function getAffilicateLevelSetting($affilicateLevels, $total_bet, $total_players) {
		$index = 0;
		$level = Array();
		foreach ($affilicateLevels as $setting) 
		{
			if (($total_bet >= $setting['min_profits'] && $total_bet <= $setting['max_profits']) || ($total_bet >= $setting['min_profits'] && $setting['max_profits'] == 0)) {
				$level[] = $index;
			} else if (($total_players >= $setting['min_valid_player'] && $total_players <= $setting['max_valid_player']) || ($total_players >= $setting['min_valid_player'] && $setting['max_valid_player'] == 0)) {
				$level[] = $index;
			}
			$index++;
		}
		$setting = $affilicateLevels[0];
		if (!empty($level))
			$setting = $affilicateLevels[min($level)];
		//fix key
		$selected_game_tree = array();
		foreach ($setting['selected_game_tree'] as $key=>$value) {
			$selected_game_tree[$key] = $value;
		}
		$setting['selected_game_tree'] = $selected_game_tree;
		return $setting;
	}

	public function generate_sub_affiliate_commission_record($affiliate_settings, $affiliates) {
		//get all sub affiliates
		$total_sub_affiliate_commission = 0;
		if (isset($affiliates['sub_affiliates'])) {
			foreach ($affiliates['sub_affiliates'] as $sub_affiliate) {
				$sub_affiliate_id = $sub_affiliate['affiliateId'];
				$affiliate = $this->generate_affiliate_commission_record($affiliate_settings, $sub_affiliate_id);

				switch($affiliate_settings['baseIncomeConfig']) {
				 	case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN_TOTALCOMMISSION:
						$total_sub_affiliate_commission += round($affiliate['commission_amount'] * ($affiliate_settings['level_master'] / 100 * 100), 0) / 100;
				 		break;
				 	case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN_ACTIVEPLAYERCOMMISSION:
						$total_sub_affiliate_commission += round($affiliate['total_bet_result'] * ($affiliate_settings['level_master'] / 100 * 100), 0) / 100;
				 		break;
				 	case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN_ACTIVEPLAYERCOMMISSIONBYGAMEPLATFORM:
						$total_sub_affiliate_commission += round($affiliate['total_bet_result'] * ($affiliate_settings['level_master'] / 100 * 100), 0) / 100;
				 		break;
				}
			}
		}
		return $total_sub_affiliate_commission;
	}

}