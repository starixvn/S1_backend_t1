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

class Affiliate_commission_3 {

	const CUSTOM  = 0;
	const DAILY   = 1;
	const WEEKLY  = 2;
	const MONTHLY = 3;
	
	private $affiliate_players = array();

	public function __construct() {

		$this->CI = &get_instance();

		$this->CI->load->model(array('affiliatemodel','affiliate_earnings','external_system','player_model'));
		
		if ($this->CI->utils->getConfig('use_total_day')) {
			$this->CI->load->model('total_player_game_day','game_model');
		} else if ($this->CI->utils->getConfig('use_total_hour')) {
			$this->CI->load->model('total_player_game_hour','game_model');
		} else if ($this->CI->utils->getConfig('use_total_minute')) {
			$this->CI->load->model('total_player_game_minute','game_model');
		} else {
			$this->CI->load->model('game_logs','game_model');
		}

		$this->user_id = $this->CI->users->getSuperAdminId();
		$this->game_platforms = $this->CI->external_system->getAllSytemGameApi();

	}

	public function generate_earnings_for_all($start_date, $end_date) {

		$affiliates = $this->get_all_affiliates();

		foreach ($affiliates as $affiliate) {
			$this->generate_earnings_for_one($affiliate, $start_date, $end_date);
		}

		// if (isset($this->default_settings['autoTransferToWallet']) && $this->default_settings['autoTransferToWallet']) {
			// TODO: $this->CI->affiliate_earnings->transferAllEarningsToWallet($this->yearmonth, $this->default_settings['minimumPayAmount']);
		// }

		return TRUE;

	}

	public function generate_earnings_by_username($username, $start_date, $end_date) {

		$query = $this->CI->db->select('affiliateId')->get_where('affiliates', ['username' => $username], 1);

		$affiliate = $query->row_array();

		if ( ! empty($affiliate)) {

			$affiliates = $this->get_all_affiliates($affiliate['affiliateId']);

			foreach ($affiliates as $affiliate) {
				$this->generate_earnings_for_one($affiliate, $start_date, $end_date);
			}

		}

		return TRUE;

	}

	public function get_affiliate_players_id($affiliate_id) {
		
		if ( ! isset($this->affiliate_players[$affiliate_id])) {
			$this->affiliate_players[$affiliate_id] = $this->CI->affiliatemodel->getAllPlayersUnderAffiliateId($affiliate_id);
		}

		return $this->affiliate_players[$affiliate_id];

	}

	public function generate_earnings_for_one($affiliate = NULL, $start_date = NULL, $end_date = NULL) {

		$affiliate['id'] 		 = $affiliate['affiliateId'];
		$affiliate['settings'] 	 = $this->get_affiliate_settings($affiliate['id']);
		$affiliate['players_id'] = $this->get_affiliate_players_id($affiliate['id']);

		if ( ! empty($affiliate['players_id'])) {
			$this->generate_affiliate_commission_records($affiliate, $start_date, $end_date);
		}

	}

	public function get_affiliate_settings($affiliate_id) {

		if (empty($this->affiliates_settings)) {
			$this->affiliates_settings 	= $this->CI->affiliatemodel->getAllAffiliateTerm();
			$this->default_settings 	= $this->affiliates_settings[-1];
		}

		$affiliate_settings = isset($this->affiliates_settings[$affiliate_id]) ? $this->affiliates_settings[$affiliate_id] : $this->default_settings;

		array_unshift($affiliate_settings['sub_levels'], $affiliate_settings['level_master']);

		$affiliate_settings['bonus_fee_rate'] 		= isset($affiliate_settings['bonus_fee']) ? $affiliate_settings['bonus_fee'] / 100 : 0;
		$affiliate_settings['cashback_fee_rate'] 	= isset($affiliate_settings['cashback_fee']) ? $affiliate_settings['cashback_fee'] / 100 : 0;
		$affiliate_settings['transaction_fee_rate'] = isset($affiliate_settings['transaction_fee']) ? $affiliate_settings['transaction_fee'] / 100 : 0;
		$affiliate_settings['admin_fee_rate'] 		= isset($affiliate_settings['admin_fee']) ? $affiliate_settings['admin_fee'] / 100 : 0;

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

	public function generate_affiliate_commission_records($affiliate, $start_date, $end_date) {

		# BONUS
		$bonus_fee = 0;
		if ($affiliate['settings']['bonus_fee_rate'] > 0) {
			$total_bonus = $this->CI->player_model->getPlayersTotalBonus($affiliate['players_id'], $start_date, $end_date);
			$bonus_fee = $total_bonus * $affiliate['settings']['bonus_fee_rate'];
		}

		# CASHBACK
		$cashback_fee = 0;
		if ($affiliate['settings']['cashback_fee_rate'] > 0) {
			$total_cashback = $this->CI->player_model->getPlayersTotalCashback($affiliate['players_id'], $start_date, $end_date);
			$cashback_fee = $total_cashback * $affiliate['settings']['cashback_fee_rate'];
		}

		# TRANSACTION FEE
		$transaction_fee = 0;
		if ($affiliate['settings']['transaction_fee_rate'] > 0) {
			$total_transaction = $this->CI->transactions->sumTransactionFee($affiliate['players_id'], $start_date, $end_date);
			$transaction_fee = $total_transaction * $affiliate['settings']['transaction_fee_rate'];
		}

		list($total_bet, $total_win, $total_loss) = $this->CI->game_model->getTotalBetsWinsLossByPlayers($affiliate['players_id'], $start_date, $end_date);

		$affiliate_earnings = array_map( function ($game_platform) use ($affiliate, $start_date, $end_date, $bonus_fee, $cashback_fee, $transaction_fee, $total_bet, $total_win, $total_loss) {

			$affiliate_id = $affiliate['id'];
			$game_platform_id = $game_platform['id'];

			$is_paid = $this->CI->db
					 ->where('affiliate_id', $affiliate_id)
					 ->where('period','daily')
					 ->where('start_date',$start_date)
					 ->where('end_date',$end_date)
					 ->where('game_platform_id',$game_platform_id)
					 ->where('paid_flag', Affiliate_earnings::DB_TRUE)
					 ->count_all_results('affiliate_game_platform_earnings');
					 
			if ($is_paid) return NULL;

			list($bet, $win, $loss) = $this->CI->game_model->getTotalBetsWinsLossByPlayers($affiliate['players_id'], $start_date, $end_date, $game_platform_id);

			$game_platform_revenue = $loss - $win;
			$game_platform_rate = (isset($game_platform['game_platform_rate']) && $game_platform['game_platform_rate'] != 0) ? ($game_platform['game_platform_rate'] / 100) : 0;
			$game_platform_gross_revenue = $game_platform_revenue * $game_platform_rate;

			$game_platform_admin_fee = $game_platform_gross_revenue * $affiliate['settings']['admin_fee_rate'];
			if ($this->CI->utils->isEnabledFeature('aff_no_admin_fee_for_negative_revenue') && $game_platform_gross_revenue < 0) { # OGP-6368
				var_dump('aff_no_admin_fee_for_negative_revenue');
				$game_platform_admin_fee = 0;
			}

			$game_platform_shares = ($total_bet != 0) ? ($bet / $total_bet) : 0;
			$game_platform_bonus_fee = $bonus_fee * $game_platform_shares;
			$game_platform_cashback_fee = $cashback_fee * $game_platform_shares;
			$game_platform_transaction_fee = $transaction_fee * $game_platform_shares;
			$game_platform_net_revenue = $game_platform_gross_revenue - $game_platform_admin_fee - $game_platform_bonus_fee - $game_platform_cashback_fee - $game_platform_transaction_fee;
			$game_platform_commission_rate = (isset($affiliate['settings']['platform_shares'][$game_platform_id]) ? $affiliate['settings']['platform_shares'][$game_platform_id] : $affiliate['settings']['level_master']);
			$game_platform_commission_amount = $game_platform_net_revenue * ($game_platform_commission_rate / 100);

			return array(
				'affiliate_id' 						=> $affiliate_id,
				'game_platform_id' 					=> $game_platform_id,
				'start_date' 						=> $start_date,
				'end_date' 							=> $end_date,
				'game_platform_shares' 				=> $game_platform_shares,
				'game_platform_revenue' 			=> $game_platform_revenue,
				'game_platform_rate' 				=> $game_platform_rate,
				'game_platform_gross_revenue' 		=> $game_platform_gross_revenue,
				'game_platform_admin_fee' 			=> $game_platform_admin_fee,
				'game_platform_bonus_fee' 			=> $game_platform_bonus_fee,
				'game_platform_cashback_fee' 		=> $game_platform_cashback_fee,
				'game_platform_transaction_fee' 	=> $game_platform_transaction_fee,
				'game_platform_net_revenue' 		=> $game_platform_net_revenue,
				'game_platform_commission_rate' 	=> $game_platform_commission_rate,
				'game_platform_commission_amount' 	=> $game_platform_commission_amount,

				'period' => 'daily',
				'updated_by' => $this->user_id,
				'updated_at' => date('Y-m-d H:i:s'),
			);

		}, $this->game_platforms);

		$affiliate_earnings = array_filter($affiliate_earnings);

		if ( ! empty($affiliate_earnings)) {

			$game_platform_ids = array_column($affiliate_earnings, 'game_platform_id');

			$this->CI->db
				->where('affiliate_id',$affiliate['id'])
				->where('period','daily')
				->where('start_date',$start_date)
				->where('end_date',$end_date)
				->where_in('game_platform_id',$game_platform_ids)
				->delete('affiliate_game_platform_earnings');

			$this->CI->db->insert_batch('affiliate_game_platform_earnings', $affiliate_earnings);

		}

	}

}