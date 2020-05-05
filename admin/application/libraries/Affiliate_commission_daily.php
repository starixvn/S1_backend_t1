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
class Affiliate_commission_daily {

	private $affiliates_settings = array();

	public function __construct() {
		$this->CI = &get_instance();
		$use_total_hour = $this->CI->utils->getConfig('use_total_hour');
		$this->CI->load->model(array('affiliatemodel','affiliate_earnings','external_system','player_model'));
		$this->CI->load->model(($use_total_hour ? 'total_player_game_hour' : 'game_logs'), 'game_model');
		$this->game_platforms = $this->CI->external_system->getAllActiveSytemGameApi();
	}

	# INTERFACE
	# generate_affiliate_commission_earnings

	# date - date to generate
	# affiliates - affiliate username or list of affiliate usernames
	public function generate_affiliate_commission_earnings($date = NULL, $affiliate_ids = NULL) {

		$date 	    = empty($date) ? date('Y-m-d') : $date;
		$start_date = $date . '00:00:00';
		$end_date   = $date . '23:59:59';

		# GET AFFILIATE
		$affiliates = $this->_get_affiliates($affiliate_ids);

		# GENERATE AFFILIATE COMMISSION EARNINGS FOR EACH AFFILIATE
		foreach ($affiliates as $affiliate) {
			$this->_generate_affiliate_commission_earnings($start_date, $end_date, $affiliate);
		}

		# CHECK IF TRANSFER TO WALLET IS ENABLED
		// if (isset($this->default_settings['autoTransferToWallet']) && $this->default_settings['autoTransferToWallet']) {
		// 	$this->CI->affiliate_earnings->transferAllEarningsToWallet($this->yearmonth, $this->default_settings['minimumPayAmount']);
		// }
	}


	# TODO: move to model
	private function _get_affiliates($affiliate_ids = NULL) {

		$query = $this->CI->db->from('affiliates')
			->select('affiliateId')
			->select('parentId')
			->select('username')
			->get();

		$rows = $query->result_array();

		$affiliates = array_column($rows, NULL, 'affiliateId');

		foreach ($affiliates as &$affiliate) {
			if ( ! empty($affiliate['parentId'])) {
				$affiliates[$affiliate['parentId']]['sub_affiliates'][] = &$affiliate;
			}
		}

		if (empty($affiliate_ids)) {
			return $affiliates;
		}

		if ( ! is_array($affiliate_ids)) {
			$affiliate_ids = array($affiliate_ids);
		}

		return array_filter($affiliates, function($affiliate, $affiliate_id) use ($affiliate_ids) {
			return in_array($affiliate_id, $affiliate_ids);
		}, ARRAY_FILTER_USE_BOTH);
	}

	private function _generate_affiliate_commission_earnings($start_date, $end_date, $affiliate) {
		$affiliate_settings	= $this->get_affiliate_settings($affiliate['affiliateId']);

		$bonus_fee_rate 		= isset($affiliate_settings['bonus_fee']) ? $affiliate_settings['bonus_fee'] / 100 : 0;
		$cashback_fee_rate 		= isset($affiliate_settings['cashback_fee']) ? $affiliate_settings['cashback_fee'] / 100 : 0;
		$transaction_fee_rate 	= isset($affiliate_settings['transaction_fee']) ? $affiliate_settings['transaction_fee'] / 100 : 0;
		$this->admin_fee_rate 	= isset($affiliate_settings['admin_fee']) ? $affiliate_settings['admin_fee'] / 100 : 0;

	}

	public function generate_monthly_earnings_for_one($start_date, $end_date, $affiliate = NULL) {

		

		$affiliate_earnings = $this->generate_affiliate_commission_record($affiliate);

		$commission_from_sub_affiliates = 0;
		$total_commission = $affiliate_earnings['commission_amount'] + $commission_from_sub_affiliates;

		// $current_timestamp = date('Y-m-d H:i:s');

		# INSERT TO DATABASE
		// $data = array(
		// 	'year_month'						=> $this->yearmonth,
		// 	'affiliate_id'						=> $affiliate_id,
		// 	'active_players'					=> $affiliate_earnings['active_players'],
		// 	'total_players'						=> $affiliate_earnings['total_players'],
		// 	'gross_revenue'						=> $affiliate_earnings['gross_revenue'],
		// 	'platform_fee'						=> $affiliate_earnings['platform_fee'],
		// 	'bonus_fee'							=> $affiliate_earnings['bonus_fee'],
		// 	'cashback_fee'						=> $affiliate_earnings['cashback_fee'],
		// 	'transaction_fee'					=> $affiliate_earnings['transaction_fee'],
		// 	'admin_fee'							=> $affiliate_earnings['admin_fee'],
		// 	'total_fee'							=> $affiliate_earnings['total_fee'],
		// 	'net_revenue'						=> $affiliate_earnings['net_revenue'],
		// 	'commission_percentage'				=> $affiliate_earnings['commission_rate'],
		// 	'commission_amount'					=> $affiliate_earnings['commission_amount'],
		// 	'commission_from_sub_affiliates'	=> $commission_from_sub_affiliates,
		// 	'total_commission'					=> $total_commission,
		// 	'type'								=> Affiliate_earnings::TYPE_INIT,
		// 	'paid_flag'							=> Affiliate_earnings::DB_FALSE,
		// 	'manual_flag'						=> Affiliate_earnings::DB_FALSE,
		// 	'note'								=> 'calc year month ' . $this->yearmonth . ' at ' . $current_timestamp,
		// 	'updated_by'						=> $this->user_id,
		// 	'updated_at'						=> $current_timestamp,
		// );

		// $this->CI->db->replace('aff_monthly_earnings', $data);

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

		list($gross_revenue, $net_revenue, $net_commission, $platform_fee, $admin_fee) = $this->get_gross_revenue_and_platform_fee($players_id, $affiliate_settings, $total_fee, $this->start_date, $this->end_date);

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

		$gross_revenue = 0; $net_revenue = 0; $net_commission = 0; $total_platform_fee = 0; $total_admin_fee = 0;
		
		try {
			if ( ! empty($players_id)) {
				switch ($affiliate_settings['baseIncomeConfig']) {
					case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN:
						list($total_bet) = $this->CI->game_model->getTotalBetsWinsLossByPlayers($players_id, $start_date, $end_date);
						foreach ($this->game_platforms as $game_platform) {
							$game_platform_id 		  = $game_platform['id'];
							$game_platform_share 	  = (isset($affiliate_settings['platform_shares'][$game_platform_id]) ? $affiliate_settings['platform_shares'][$game_platform_id] : $affiliate_settings['level_master']) / 100;
							$game_platform_rate 	  = isset($game_platform['game_platform_rate']) ? (100 - $game_platform['game_platform_rate']) / 100 : 0;
							list($bet, $win, $loss)   = $this->CI->game_model->getTotalBetsWinsLossByPlayers($players_id, $start_date, $end_date, $game_platform_id);
							$bet_result 			  = $loss - $win;
							$game_platform_fee 		  = $bet_result * $game_platform_rate;
							$game_platform_gross      = $bet_result - $game_platform_fee;
							$admin_fee  			  = $game_platform_gross * $this->admin_fee_rate;
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

		return array($gross_revenue, $net_revenue, $net_commission, $total_platform_fee, $total_admin_fee);
	}

}