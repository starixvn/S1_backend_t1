<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
#  $this->CI->affiliatemodel->getAffiliateHierarchy();
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
class Affiliate_commission {

	private $affiliates_settings = array();

	public function __construct() {
		$this->CI = &get_instance();
		$use_total_hour = $this->CI->utils->getConfig('use_total_hour');
		$this->CI->load->model(array('affiliatemodel','affiliate_earnings','external_system','player_model','affiliatemodel','transactions'));
		$this->CI->load->model(($use_total_hour ? 'total_player_game_hour' : 'game_logs'), 'game_model');
		$this->game_platforms = $this->CI->external_system->getAllSytemGameApi();
	}

    /**
     * @param null $yearmonth
     * @param null $affiliate_username
     * @param null $specific_start_date
     * @param null $specific_end_date : this specific dates used, if client want to merge other month records
     * @return bool
     */
	public function generate_monthly_earnings_for_all($yearmonth = NULL, $affiliate_username = NULL, $specific_start_date = NULL, $specific_end_date = NULL, $for_fix = 0) {

		$yearmonth 					 = empty($yearmonth) ? $this->CI->utils->getLastYearMonth() : $yearmonth;
		list($start_date, $end_date) = $this->CI->utils->getMonthRange($yearmonth);

		$this->yearmonth 			 = $yearmonth;
		$this->start_date 			 = $start_date; 
		$this->end_date 			 = $end_date;
		$this->for_fix				 = $for_fix;
        if( !empty($specific_start_date) && !empty($specific_end_date) ){
            $this->start_date = $specific_start_date . ' 00:00:00';
            $this->end_date = $specific_end_date . ' 23:59:59';
        }
		$this->user_id 				 = $this->CI->users->getSuperAdminId();
        $this->CI->utils->debug_log('the dates -----------> ', 'start : '. $this->start_date. ' end: '. $this->end_date);
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
		
		if (isset($this->default_settings['autoTransferToWallet']) && $this->default_settings['autoTransferToWallet']) {
			$this->CI->affiliate_earnings->transferAllEarningsToWallet($this->yearmonth, $this->default_settings['minimumPayAmount']);
		}

		return TRUE;
	}

	public function generate_affiliate_level($affiliate, &$affiliate_levels, $level = 0) {
		if (isset($affiliate['sub_affiliates'])) {
			$level++;
			foreach ($affiliate['sub_affiliates'] as $sub_affiliate) {
				$affiliate_levels[$level-1][] = $sub_affiliate['affiliateId'];
				$this->generate_affiliate_level($sub_affiliate, $affiliate_levels, $level);
			}
		}
	}

	public function generate_monthly_earnings_for_one($affiliate = NULL, $index = NULL, $yearmonth = NULL) {

		$affiliate_id = $affiliate['affiliateId'];

		$is_paid = $this->CI->db
				 ->where('affiliate_id', $affiliate_id)
				 ->where('year_month', $this->yearmonth)
				 ->where('paid_flag', Affiliate_earnings::DB_TRUE)
				 ->count_all_results('aff_monthly_earnings');
				 
        if ($is_paid) {
            if ($this->for_fix==0) {
                echo "Error: affiliate_id {$affiliate_id} is paid already for the year month of " . $this->yearmonth . "\n";

            } else {
                $all_players_id = $this->CI->affiliatemodel->getAllPlayersUnderAffiliateId($affiliate_id, null, $this->end_date);
                $data = array(
                    'year_month'						=> $this->yearmonth,
                    'affiliate_id'						=> $affiliate_id,
                    'total_players'						=> count($all_players_id),
                    'updated_at'						=> date('Y-m-d H:i:s'),
                );
                $where = array('year_month' => $data['year_month'], 'affiliate_id' => $data['affiliate_id']);
                $this->CI->db->update('aff_monthly_earnings', $data, $where);
			}
			return;
		}

		$affiliate_settings = $this->get_affiliate_settings($affiliate_id);

		$affiliate_commission = $this->generate_affiliate_commission_record($affiliate_id);
		$commonSettings = $this->CI->affiliatemodel->getDefaultAffSettings();

		$details = json_encode($this->earnings_details);

		if ($affiliate_commission) {

			$commission_from_sub_affiliates = 0;
			$subaffiliate_commissions = array();

			if ($this->CI->utils->isEnabledFeature('enable_commission_from_subaffiliate')) {

				$subaffiliate_levels = array();
				$affiliate_tree = $this->CI->affiliatemodel->getAffiliateHierarchy($affiliate_id);
				$this->generate_affiliate_level($affiliate_tree, $subaffiliate_levels);

				foreach ($subaffiliate_levels as $level => $subaffiliate_ids) {
					if (isset($affiliate_settings['sub_levels'][$level]) && $affiliate_settings['sub_levels'][$level] > 0) {
						foreach ($subaffiliate_ids as $subaffiliate_id) {
							$subaffiliate_commission = $this->generate_affiliate_commission_record($subaffiliate_id);
							$subaffiliate_commission = array(
								'subaffiliate_id' => $subaffiliate_id,
								'level' => $level + 1,
								'net_revenue' => $subaffiliate_commission['net_revenue'],
								'commission_rate' => $affiliate_settings['sub_levels'][$level],
								'commission_amount' => $subaffiliate_commission['commission_amount'],
								'commission_amount_by_tier' => $subaffiliate_commission['commission_amount_by_tier'],
								'commission_from_sub_affiliate' => round(($affiliate_settings['sub_levels'][$level] / 100) * $subaffiliate_commission['commission_amount'], 2),
							);
							if(isset($commonSettings['enable_commission_by_tier']) && $commonSettings['enable_commission_by_tier'] == true){
								$subaffiliate_commission['commission_from_sub_affiliate'] = round(($affiliate_settings['sub_levels'][$level] / 100) * $subaffiliate_commission['commission_amount_by_tier'], 2);
							}

							if ($this->CI->utils->isEnabledFeature('ignore_subaffiliates_with_negative_commission')) { # OGP-6695 neglect sub-affiliate's commission when it is negative.
								if ($subaffiliate_commission['commission_from_sub_affiliate'] > 0) {
									$subaffiliate_commissions[] = $subaffiliate_commission;
								}
							} else {
								$subaffiliate_commissions[] = $subaffiliate_commission;
							}

						}
					}
				}
				$this->CI->utils->debug_log('affiliate $subaffiliate_commissions -------->', $subaffiliate_commissions);

				$commission_from_sub_affiliates = array_sum(array_column($subaffiliate_commissions, 'commission_from_sub_affiliate'));

			}

			$total_commission = $affiliate_commission['commission_amount'] + $commission_from_sub_affiliates;
			if(isset($commonSettings['enable_commission_by_tier']) && $commonSettings['enable_commission_by_tier'] == true){
				$total_commission = $affiliate_commission['commission_amount_by_tier'] + $commission_from_sub_affiliates;
			}

			$current_timestamp = date('Y-m-d H:i:s');

			$note = 'calc year month ' . $this->yearmonth . ' at ' . $current_timestamp;

			if ($affiliate_settings['totalactiveplayer'] > $affiliate_commission['active_players'] && !isset($commonSettings['enable_commission_by_tier']) && $commonSettings['enable_commission_by_tier'] == false) {
				$note .= ' | the affiliate did not meet the required minimum numbers of active players.';
			}
			$data = array(
				'year_month'						=> $this->yearmonth,
				'affiliate_id'						=> $affiliate_id,
				'active_players'					=> $affiliate_commission['active_players'],
				'total_players'						=> $affiliate_commission['total_players'],
				'gross_revenue'						=> $affiliate_commission['gross_revenue'],
				'platform_fee'						=> $affiliate_commission['platform_fee'],
				'bonus_fee'							=> $affiliate_commission['bonus_fee'],
				'cashback_fee'						=> $affiliate_commission['cashback_fee'],
				'transaction_fee'					=> $affiliate_commission['transaction_fee'],
				'admin_fee'							=> $affiliate_commission['admin_fee'],
				'total_fee'							=> $affiliate_commission['total_fee'],
				'net_revenue'						=> $affiliate_commission['net_revenue'],
				'commission_percentage'				=> $affiliate_commission['commission_rate'],
				'commission_amount'					=> $affiliate_commission['commission_amount'],
				'commission_from_sub_affiliates'	=> $commission_from_sub_affiliates,
				'commission_percentage_breakdown'	=> json_encode($affiliate_commission['commission_percentage_breakdown']),
				'total_commission'				    => $total_commission,
				'type'								=> Affiliate_earnings::TYPE_INIT,
				'paid_flag'							=> Affiliate_earnings::DB_FALSE,
				'manual_flag'						=> Affiliate_earnings::DB_FALSE,
				'note'								=> $note,
				'updated_by'						=> $this->user_id,
				'updated_at'						=> $current_timestamp,
				'details'							=> $details,
				'negative_net_revenue'              => $affiliate_commission['negative_net_revenue'],
				'total_net_revenue'                 => $affiliate_commission['total_net_revenue'],
				'commission_amount_by_tier'         => $affiliate_commission['commission_amount_by_tier'],
				'commission_amount_breakdown'       => json_encode($affiliate_commission['commission_amount_breakdown']),
				'sub_aff_commission_breakdown'      => json_encode($subaffiliate_commissions),
			);

			if ($total_commission) {
				var_dump($data);
			}

			$this->CI->db->replace('aff_monthly_earnings', $data);
		}

	}

	public function get_affiliate_settings($affiliate_id) {

		if (empty($this->affiliates_settings)) {
			$this->affiliates_settings 	= $this->CI->affiliatemodel->getAllAffiliateTerm();
			$this->default_settings 	= $this->affiliates_settings[-1];
		}

		$affiliate_settings = isset($this->affiliates_settings[$affiliate_id]) ? $this->affiliates_settings[$affiliate_id] : $this->default_settings;

		return $affiliate_settings;
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

	public function generate_affiliate_commission_record($affiliate_id) {

		$affiliate_settings	= $this->get_affiliate_settings($affiliate_id);

		$this->earnings_details = array('settings' => $affiliate_settings);

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
        if(isset($affiliate_settings['split_transaction_fee']) && $affiliate_settings['split_transaction_fee'] == true){
            $trans_deposit_fee_rate = isset($affiliate_settings['transaction_deposit_fee']) ? $affiliate_settings['transaction_deposit_fee'] / 100 : 0;
            $trans_withdrawal_fee_rate = isset($affiliate_settings['transaction_withdrawal_fee']) ? $affiliate_settings['transaction_withdrawal_fee'] / 100 : 0;
            $deposit_fee = 0;
            $withdrawal_fee = 0;
            if($trans_deposit_fee_rate > 0){
                $deposit_transaction = $this->CI->transactions->sumTransactionsDepositOrWithdrawal($players_id, $this->start_date, $this->end_date, Transactions::DEPOSIT);
                $deposit_fee = $deposit_transaction * $trans_deposit_fee_rate;
            }
            if($trans_withdrawal_fee_rate > 0){
                $withdrawal_transaction = $this->CI->transactions->sumTransactionsDepositOrWithdrawal($players_id, $this->start_date, $this->end_date, Transactions::WITHDRAWAL);
                $withdrawal_fee = $withdrawal_transaction * $trans_withdrawal_fee_rate;
            }
            $transaction_fee = $deposit_fee + $withdrawal_fee;
        }else{
            if ($transaction_fee_rate > 0) {
                $total_transaction = $this->CI->transactions->sumTransactionFee($players_id, $this->start_date, $this->end_date);
                $transaction_fee = $total_transaction * $transaction_fee_rate;
            }
        }

		$total_fee = $bonus_fee + $cashback_fee + $transaction_fee;

		list($gross_revenue, $net_revenue, $net_commission, $platform_fee, $admin_fee, $commission_percentage_breakdown) = $this->get_gross_revenue_and_platform_fee($players_id, $affiliate_settings, $total_fee, $this->start_date, $this->end_date);

		$total_fee += $admin_fee;

		$this->earnings_details['other_fees']['bonus'] = $bonus_fee;
		$this->earnings_details['other_fees']['cashback'] = $cashback_fee;
		$this->earnings_details['other_fees']['transaction'] = $transaction_fee;
		$this->earnings_details['other_fees']['total'] = $total_fee;

        /* *
         * Implement affiliate commission by tier
         * */
//         $net_revenue = 1000000; //sample
//         $active_players = 10; //sample

        $tier_settings = $this->CI->affiliatemodel->getAffCommissionTierSettings();

        $previous_year_month = date('Ym', strtotime($this->yearmonth.'01'." -1 month") );
        $previous_negative_net_revenue = $this->CI->affiliatemodel->getPreviousNegativeNetRevenue($affiliate_id, $previous_year_month); // get the last month negative net revenue

        $negative_net_revenue = 0;
        $total_net_revenue = $previous_negative_net_revenue + $net_revenue;
        if($total_net_revenue < 0){
            $negative_net_revenue = $total_net_revenue;
        }

        $comm_tier_amount = 0;
        $comm_amount_breakdown = [];
        if($total_net_revenue > 0){
            $current_net_revenue = $total_net_revenue;
            $starting_revenue = 0; $net_revenue_scope=0;

            foreach($tier_settings as $key => $setting) {
                if($current_net_revenue <= 0){
                    continue;
                }

                if ($setting['active_members'] <= $active_players && $setting['min_net_revenue'] <= $total_net_revenue){
                    $net_revenue_scope = $setting['max_net_revenue'] - $starting_revenue;
                    if($net_revenue_scope > $current_net_revenue){
                        $net_revenue_scope = $current_net_revenue;
                    }

                    $comm_amount_breakdown[] = [
                        'amount' => $net_revenue_scope,
                        'level' => $setting['level'],
                        'min_net_rev' => $setting['min_net_revenue'],
                        'max_net_rev' => $setting['max_net_revenue'],
                        'rate' => $setting['commission_rates'] / 100
                    ];

                    $current_net_revenue = (floatval($current_net_revenue) - floatval($net_revenue_scope));
                    $starting_revenue = $starting_revenue +  $net_revenue_scope;
                }
                elseif($setting['active_members'] <= $active_players && $current_net_revenue > 0 && !empty($comm_amount_breakdown)){
                    $comm_amount_breakdown[$key-1]['amount'] = $comm_amount_breakdown[$key-1]['amount'] + $current_net_revenue;

                    $current_net_revenue = $current_net_revenue - $net_revenue_scope;
                    $starting_revenue = $starting_revenue +  $net_revenue_scope;
                }

                /*$current_net_revenue = $current_net_revenue - $net_revenue_scope;
                $starting_revenue = $starting_revenue +  $net_revenue_scope;*/
            }

//          // this will add to the last tier setting
            $count = count($comm_amount_breakdown);
            if($count > 0 && $current_net_revenue > 0){
                $comm_amount_breakdown[$count-1]['amount'] = $comm_amount_breakdown[$count-1]['amount'] + $current_net_revenue;
            }

            foreach($comm_amount_breakdown as $breakdown){
                $comm_tier_amount += $breakdown['amount'] * $breakdown['rate'];
            }
//            $this->CI->utils->debug_log('the $comm_tier_amount ------>', $comm_tier_amount);
//            $this->CI->utils->debug_log('the $comm_amount_breakdown ------>', $comm_amount_breakdown);
        }


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
            'negative_net_revenue' => $negative_net_revenue, // current total negative net revenue
			'net_revenue' => $net_revenue,
            'total_net_revenue' => $total_net_revenue, // can be zero if current net revenue is negative
			'commission_rate' => 0,
			'commission_percentage_breakdown' => $commission_percentage_breakdown,
			'commission_amount' => $net_commission,
            'commission_amount_breakdown' => $comm_amount_breakdown,  // ex. 50,000 x 40% + 200,000 x 35% + 50,000 x 30%
            'commission_amount_by_tier' => $comm_tier_amount
		);

	}

	public function get_players_id_active_players_and_total_players($affiliate_id, $affiliate_settings, $start_date, $end_date) {
		
		$all_players_id 	= array(); 
		$active_players_id 	= array();

		$minimum_deposit 					= isset($affiliate_settings['minimumDeposit']) ? $affiliate_settings['minimumDeposit'] : 0;
		$minimum_total_bet 					= isset($affiliate_settings['minimumBetting']) ? $affiliate_settings['minimumBetting'] : 0;
		$game_platforms_minimum_bet 		= isset($affiliate_settings['provider_betting_amount']) ? $affiliate_settings['provider_betting_amount'] : array();
		$game_platforms_minimum_total_bet 	= isset($affiliate_settings['provider']) ? $affiliate_settings['provider'] : array();

		try {

			# GET ALL PLAYERS
			$all_players_id = $this->CI->affiliatemodel->getAllPlayersUnderAffiliateId($affiliate_id, null, $end_date);

			if ( ! empty($all_players_id)) {

				# FILTER BY MINIMUM DEPOSIT
				if ($minimum_deposit > 0) {
					$active_players_id = $this->CI->transactions->get_players_with_minimum_deposit($minimum_deposit, $start_date, $end_date, $all_players_id);
				} else {
					$active_players_id = $all_players_id;
				}

				if ( ! empty($active_players_id)) {

					$active_game_platforms_id = $this->CI->utils->getAllActiveGameApiId();

					$active_players_id = array_filter($active_players_id, function($player_id) use ($active_game_platforms_id, $game_platforms_minimum_bet, $game_platforms_minimum_total_bet, $minimum_total_bet, $start_date, $end_date) {

						$game_platforms_bet_details = $this->CI->game_model->getTotalBetsWinsLossGroupByGamePlatformByPlayers($player_id, $start_date, $end_date);

						$total_bet = 0;


						foreach ($active_game_platforms_id as $game_platform_id) {

							$minimum_platform_bet = isset($game_platforms_minimum_bet[$game_platform_id]) ? $game_platforms_minimum_bet[$game_platform_id] : 0;
							$platform_bet = isset($game_platforms_bet_details[$game_platform_id][0]) ? $game_platforms_bet_details[$game_platform_id][0] : 0;

							if (in_array($game_platform_id, $game_platforms_minimum_total_bet)) {
								$total_bet += $platform_bet;
							}

							# FILTER BY MINIMUM PLATFORM BET
							if (0 < $minimum_platform_bet && $minimum_platform_bet < $platform_bet) {
								return FALSE;
							}

						}

						if ($minimum_total_bet == 0) return TRUE;

						# FILTER BY TOTAL MINIMUM BET
						return $minimum_total_bet <= $total_bet;

					});
				}

			}

		} catch (Exception $e) {
			$this->CI->utils->error_log($e->getMessage());
		}

		return array($all_players_id, count($active_players_id), count($all_players_id));
	}

	private $earnings_details = array();

	public function get_gross_revenue_and_platform_fee($players_id, $affiliate_settings, $total_fee, $start_date, $end_date) {

		$gross_revenue = 0; $net_revenue = 0; $net_commission = 0; $total_platform_fee = 0; $total_admin_fee = 0; $commission_percentage_breakdown = array();

		try {
			if ( ! empty($players_id)) {
				switch ($affiliate_settings['baseIncomeConfig']) {
					case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN:
						$commonSettings = $this->CI->affiliatemodel->getDefaultAffSettings();

						# determine valid game platforms based on rules defined in RFE-2446
						if(isset($commonSettings['enable_commission_by_tier']) && $commonSettings['enable_commission_by_tier'] == true){
							$valid_game_platforms = $commonSettings['tier_provider'];
						} else {
							$valid_game_platforms = null;
						}

						if($this->CI->utils->isEnabledFeature('only_compute_fees_from_bet_of_valid_game_platforms')){
							list($total_bet) = $this->CI->game_model->getTotalBetsWinsLossByPlayers($players_id, $start_date, $end_date, $valid_game_platforms);
						} else {
							list($total_bet) = $this->CI->game_model->getTotalBetsWinsLossByPlayers($players_id, $start_date, $end_date);
						}

						foreach ($this->game_platforms as $game_platform) {

							$game_platform_id 		  = $game_platform['id'];

                            if(isset($commonSettings['enable_commission_by_tier']) && $commonSettings['enable_commission_by_tier'] == true){
                                if(!in_array($game_platform['id'], $commonSettings['tier_provider'])){
                                    continue;
                                }
                            }

							$commission_percentage_breakdown[$game_platform_id] = isset($affiliate_settings['platform_shares'][$game_platform_id])
								&& $affiliate_settings['platform_shares'][$game_platform_id] != 0
								?
								$affiliate_settings['platform_shares'][$game_platform_id] : $affiliate_settings['level_master'];

							$game_platform_share 	  = $commission_percentage_breakdown[$game_platform_id] / 100;

							$game_platform_rate 	  = isset($game_platform['game_platform_rate']) ? (100 - $game_platform['game_platform_rate']) / 100 : 0;
							list($bet, $win, $loss)   = $this->CI->game_model->getTotalBetsWinsLossByPlayers($players_id, $start_date, $end_date, $game_platform_id);
							$bet_result 			  = $loss - $win;
                            $total_bet_result          = $bet - $bet_result;

                            $total_rake   = $this->CI->game_logs->getTotalRakeByPlayers($players_id, $start_date, $end_date, $game_platform_id);

                            $api = $this->CI->utils->loadExternalSystemLibObject($game_platform_id);
                            if( $api->getSystemInfo('enable_rake_commission') ){
                                $game_platform_fee 		  = $total_rake * $game_platform_rate;
                                $game_platform_gross      = $total_rake - $game_platform_fee;
                            }else{
                                $game_platform_fee 		  = $bet_result * $game_platform_rate;
                                $game_platform_gross      = $bet_result - $game_platform_fee;
                            }

							$admin_fee = $game_platform_gross * $this->admin_fee_rate;
							if ($this->CI->utils->isEnabledFeature('aff_no_admin_fee_for_negative_revenue') && $game_platform_gross < 0) { # OGP-6368
								var_dump('aff_no_admin_fee_for_negative_revenue');
								$admin_fee = 0;
							}

							$bet_ratio 				  = $total_bet != 0 ? ($bet / $total_bet) : 0;
							$other_fee 				  = $total_fee * $bet_ratio;
							$game_platform_net  	  = $game_platform_gross - $admin_fee - $other_fee;
							$game_platform_commission = $game_platform_net * $game_platform_share;

							$gross_revenue 		     += $game_platform_gross;
							$net_revenue 		     += $game_platform_net;
							$net_commission 	     += $game_platform_commission;
							$total_platform_fee      += $game_platform_fee;
							$total_admin_fee         += $admin_fee;

							$this->earnings_details['game_platforms'][] = array(
								'game_platform_id' 		=> $game_platform['id'],
								'bet_percentage' 		=> $bet_ratio,
								'bet_amount' 			=> $bet,
                                'total_bet_result'      => $total_bet_result,
								'win_amount' 			=> $win,
								'loss_amount' 			=> $loss,
								'result_amount' 		=> $bet_result,
                                'total_rake'            => $total_rake,
								'platform_fee_rate' 	=> $game_platform_rate,
								'platform_fee_amount' 	=> $game_platform_fee,
								'gross_revenue' 		=> $game_platform_gross,
								'admin_fee_rate' 		=> $this->admin_fee_rate,
								'admin_fee_amount' 		=> $admin_fee,
								'other_fees_amount' 	=> $other_fee,
								'net_revenue' 			=> $game_platform_net,
								'commission_rate' 		=> $game_platform_share,
								'commission_amount' 	=> $game_platform_commission,
							);

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

    public function get_grossRevenue_bonus_transaction_netRevenue($affiliate_id, $players_id, $start_date, $end_date){
        $bonus_fee = 0; $transaction_fee = 0; $cashback_fee = 0; $gross_revenue = 0; $net_revenue = 0;
        $affiliate_settings	= $this->get_affiliate_settings($affiliate_id);
        $this->admin_fee_rate 	= isset($affiliate_settings['admin_fee']) ? $affiliate_settings['admin_fee'] / 100 : 0;

        $transaction_fee_rate 	= isset($affiliate_settings['transaction_fee']) ? $affiliate_settings['transaction_fee'] / 100 : 0;
        if(isset($affiliate_settings['split_transaction_fee']) && $affiliate_settings['split_transaction_fee'] == true){
            $trans_deposit_fee_rate = isset($affiliate_settings['transaction_deposit_fee']) ? $affiliate_settings['transaction_deposit_fee'] / 100 : 0;
            $trans_withdrawal_fee_rate = isset($affiliate_settings['transaction_withdrawal_fee']) ? $affiliate_settings['transaction_withdrawal_fee'] / 100 : 0;
            $deposit_fee = 0;
            $withdrawal_fee = 0;
            if($trans_deposit_fee_rate > 0){
                $deposit_transaction = $this->CI->transactions->sumTransactionsDepositOrWithdrawal($players_id, $start_date, $end_date, Transactions::DEPOSIT);
                $deposit_fee = $deposit_transaction * $trans_deposit_fee_rate;
            }
            if($trans_withdrawal_fee_rate > 0){
                $withdrawal_transaction = $this->CI->transactions->sumTransactionsDepositOrWithdrawal($players_id, $start_date, $end_date, Transactions::WITHDRAWAL);
                $withdrawal_fee = $withdrawal_transaction * $trans_withdrawal_fee_rate;
            }
            $transaction_fee = $deposit_fee + $withdrawal_fee;
        }else{
            if ($transaction_fee_rate > 0) {
                $total_transaction = $this->CI->transactions->sumTransactionFee($players_id, $start_date, $end_date);
                $transaction_fee = $total_transaction * $transaction_fee_rate;
            }
        }

        $bonus_fee_rate = isset($affiliate_settings['bonus_fee']) ? $affiliate_settings['bonus_fee'] / 100 : 0;
        if ($bonus_fee_rate > 0) {
            $total_bonus = $this->CI->player_model->getPlayersTotalBonus($players_id, $start_date, $end_date);
            $bonus_fee = $total_bonus * $bonus_fee_rate;
        }

        $cashback_fee_rate = isset($affiliate_settings['cashback_fee']) ? $affiliate_settings['cashback_fee'] / 100 : 0;
        if ($cashback_fee_rate > 0) {
            $total_cashback = $this->CI->player_model->getPlayersTotalCashback($players_id, $start_date, $end_date);
            $cashback_fee = $total_cashback * $cashback_fee_rate;
        }
        $total_fee = $bonus_fee + $cashback_fee + $transaction_fee;

        list($gross_revenue, $net_revenue)
            = $this->get_gross_revenue_and_platform_fee($players_id, $affiliate_settings, $total_fee, $start_date, $end_date);

        return array($gross_revenue, $bonus_fee, $transaction_fee, $net_revenue);
    }
}