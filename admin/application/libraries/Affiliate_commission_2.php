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
class Affiliate_commission_2 {

	private $affiliates_settings = array();

	public function __construct() {
		$this->CI = &get_instance();
		$use_total_hour = $this->CI->utils->getConfig('use_total_hour');
		$this->CI->load->model(array('affiliatemodel','affiliate_earnings','external_system','player_model'));
		$this->CI->load->model(($use_total_hour ? 'total_player_game_hour' : 'game_logs'), 'game_model');
		$this->game_platforms = $this->CI->external_system->getAllActiveSytemGameApi();
	}

	public function generate_daily_earnings_for_all($date = NULL, $affiliate_username = NULL) {

		$date = empty($date) ? date('Y-m-d') : date('Y-m-d', strtotime($date));

		$this->date 			 	 = $date; 
		$this->start_date 			 = $date . ' 00:00:00'; 
		$this->end_date 			 = $date . ' 23:59:59';
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

		array_walk($affiliates, array($this, 'generate_daily_earnings_for_one'), $date);

		// TODO: UPDATE
		// if (isset($this->default_settings['autoTransferToWallet']) && $this->default_settings['autoTransferToWallet']) {
		// 	$this->CI->affiliate_earnings->transferAllEarningsToWallet($this->date, $this->default_settings['minimumPayAmount']);
		// }

		return TRUE;
	}

	public function generate_daily_earnings_for_one($affiliate = NULL, $index = NULL, $date = NULL) {

		$affiliate_id = $affiliate['affiliateId'];

		$is_paid = $this->CI->db
				 ->where('affiliate_id', $affiliate_id)
				 ->where('date', $this->date)
				 ->where('paid_flag', Affiliate_earnings::DB_TRUE)
				 ->count_all_results('aff_daily_earnings');
				 
		if ($is_paid) {
			echo "Error: affiliate_id {$affiliate_id} is paid already for the date of " . $this->date . "\n";
			return;
		}

		$affiliate_settings	= $this->get_affiliate_settings($affiliate_id);
		$affiliate = $this->generate_affiliate_commission_record($affiliate_settings, $affiliate_id);

		if ($affiliate) {

			$commission_from_sub_affiliates = 0;
			$total_commission = $affiliate['commission_amount'] + $commission_from_sub_affiliates;

			$current_timestamp = date('Y-m-d H:i:s');

			$data = array(
				'date'								=> $this->date,
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
				'commission_percentage_breakdown'	=> json_encode($affiliate['commission_percentage_breakdown']),
				'total_commission'					=> $total_commission,
				'type'								=> Affiliate_earnings::TYPE_INIT,
				'paid_flag'							=> Affiliate_earnings::DB_FALSE,
				'manual_flag'						=> Affiliate_earnings::DB_FALSE,
				'note'								=> 'calc date ' . $this->date . ' at ' . $current_timestamp,
				'updated_by'						=> $this->user_id,
				'updated_at'						=> $current_timestamp,
			);

			if ($total_commission) {
				var_dump($data);
			}

			$this->CI->db->replace('aff_daily_earnings', $data);

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

	public function get_levels($affiliate) {
		$level = 0;
		$levels = array($level => [$affiliate]);
		do {
			$affiliates = $levels[$level++];
			foreach ($affiliates as $affiliate) {
				if (isset($affiliate['sub_affiliates']) && ! empty($affiliate['sub_affiliates'])) {
					$levels[$level] = ! isset($levels[$level]) ? $affiliate['sub_affiliates'] : array_merge($levels[$level], $affiliate['sub_affiliates']);
				}
			}
		} while (isset($affiliate['sub_affiliates']) && ! empty($affiliate['sub_affiliates']));
		return $levels;
	}

	public function get_all_affiliates($affiliate_id = NULL) {
		$this->CI->db->select('affiliateId');
		$this->CI->db->select('parentId');
		$this->CI->db->select('username');
		$this->CI->db->from('affiliates');
		$query = $this->CI->db->get();
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

		list($gross_revenue, $net_revenue, $net_commission, $platform_fee, $admin_fee, $commission_percentage_breakdown) = $this->get_gross_revenue_and_platform_fee($players_id, $affiliate_settings, $total_fee, $this->start_date, $this->end_date);

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
			'commission_percentage_breakdown' => $commission_percentage_breakdown,
			'commission_amount' => $net_commission,
		);

	}

	public function get_players_id_active_players_and_total_players($affiliate_id, $affiliate_settings, $start_date, $end_date) {
		$players_id = array(); $active_players = 0; $total_players = 0;
		try {
			$players_id = $this->CI->affiliatemodel->getAllPlayersUnderAffiliateId($affiliate_id);
			if ( ! empty($players_id)) {
				$total_players = count($players_id);
				$active_players_id = $this->CI->transactions->get_players_with_minimum_deposit($affiliate_settings['minimumDeposit'], $start_date, $end_date, $players_id);
				$active_players_id = $this->CI->affiliatemodel->filterActivePlayersById($affiliate_settings, $players_id, $start_date, $end_date, 'day');
				$game_platform_ids  = $this->CI->utils->filterActiveGameApi($affiliate_settings['provider']);
				if ( ! empty($game_platform_ids)) {
					$active_players_id = $this->CI->affiliatemodel->filterActivePlayersByIdByProvider($affiliate_settings, $active_players_id, $start_date, $end_date, $game_platform_ids, $affiliate_settings['totalactiveplayer']);
				}
				$active_players = count($active_players_id);
			}
		} catch (Exception $e) {
			$this->CI->utils->error_log($e->getMessage());
		}
		return array($players_id, $active_players, $total_players);
	}

	public function get_gross_revenue_and_platform_fee($players_id, $affiliate_settings, $total_fee, $start_date, $end_date) {

		$gross_revenue = 0; $net_revenue = 0; $net_commission = 0; $total_platform_fee = 0; $total_admin_fee = 0; $commission_percentage_breakdown = array();
		
		try {
			if ( ! empty($players_id)) {
				switch ($affiliate_settings['baseIncomeConfig']) {
					case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN:
						list($total_bet) = $this->CI->game_model->getTotalBetsWinsLossByPlayers($players_id, $start_date, $end_date);
						foreach ($this->game_platforms as $game_platform) {
							$game_platform_id 		  = $game_platform['id'];
							$game_platform_share 	  = (isset($affiliate_settings['platform_shares'][$game_platform_id]) ? $affiliate_settings['platform_shares'][$game_platform_id] : $affiliate_settings['level_master']) / 100;
							$commission_percentage_breakdown[$game_platform_id] = $game_platform_share * 100;
							$game_platform_rate 	  = isset($game_platform['game_platform_rate']) ? (100 - $game_platform['game_platform_rate']) / 100 : 0;
							list($bet, $win, $loss)   = $this->CI->game_model->getTotalBetsWinsLossByPlayers($players_id, $start_date, $end_date, $game_platform_id);
							$bet_result 			  = $loss - $win;
							$game_platform_fee 		  = $bet_result * $game_platform_rate;
							$game_platform_gross      = $bet_result - $game_platform_fee;

							$admin_fee = $game_platform_gross * $this->admin_fee_rate;
							if ($this->CI->utils->isEnabledFeature('aff_no_admin_fee_for_negative_revenue') && $game_platform_gross < 0) { # OGP-6368
								var_dump('aff_no_admin_fee_for_negative_revenue');
								$admin_fee = 0;
							}

							$bet_ratio 				  = $total_bet != 0 ? ($bet / $total_bet) : 0;
							$game_platform_net  	  = $game_platform_gross - $admin_fee - ($total_fee * $bet_ratio);
							$game_platform_commission = $game_platform_net * $game_platform_share;

							$gross_revenue 		     += $game_platform_gross;
							$net_revenue 		     += $game_platform_net;
							$net_commission 	     += $game_platform_commission;
							$total_platform_fee      += $game_platform_fee;
							$total_admin_fee         += $admin_fee;
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

		return array($gross_revenue, $net_revenue, $net_commission, $total_platform_fee, $total_admin_fee, $commission_percentage_breakdown);
	}

}