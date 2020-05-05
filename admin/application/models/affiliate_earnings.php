<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

require_once dirname(__FILE__) . '/modules/aff_earning_module.php';

/**
 * Affiliate Earnings
 *
 * This model represents affiliate data. It operates the following tables:
 * - monthly_earnings
 *
 * @author	Mark Bandilla
 */

class Affiliate_earnings extends BaseModel {

	function __construct() {
		parent::__construct();
		// $this->load->model(array('affiliate', 'player_model', 'external_system'));
	}

	use aff_earning_module;

	protected $tableName = 'monthly_earnings';

	const TYPE_INIT = 1;
	const TYPE_ADJUSTMENT = 2;

	# START MONTHLY EARNINGS -------------------------------------------------

	// public function monthlyEarnings($yearmonth = null) {
	// 	# INIT DATE
	// 	if (empty($yearmonth)) {
	// 		// $start_date = date('Y-m-01');
	// 		// $end_date = date('Y-m-t');
	// 		$yearmonth = $this->utils->getLastYearMonth();
	// 		// } else {
	// 		// $start_date = date('Y-m-01', strtotime($yearmonth));
	// 		// $end_date = date('Y-m-t', strtotime($yearmonth));
	// 		// $yearmonth = $yearmonth;
	// 	}
	// 	$this->utils->debug_log('yearmonth', $yearmonth);
	// 	# CALCULATE ALL NET EARNINGS STEP 1-3
	// 	$this->monthlyEarningsMaster($yearmonth);

	// 	# CALCULATE ALL AMOUNT EARNINGS STEP 4
	// 	$this->monthlyEarningsSubLevel($yearmonth);

	// 	//transfer to wallet
	// 	$this->load->model(array('affiliatemodel'));
	// 	$defaultAllAffiliateSettings = $this->affiliatemodel->getDefaultAllAffiliateSettings();
	// 	$autoTransferToWallet=isset($defaultAllAffiliateSettings->default_affiliate_settings->autoTransferToWallet) ? $defaultAllAffiliateSettings->default_affiliate_settings->autoTransferToWallet : false ;
	// 	$this->utils->debug_log('default_affiliate_settings', $defaultAllAffiliateSettings,
	// 		'autoTransferToWallet', $autoTransferToWallet);
	// 	if ($autoTransferToWallet) {
	// 		$this->transferAllEarningsToWallet($yearmonth, $defaultAllAffiliateSettings->default_affiliate_settings->minimumPayAmount);
	// 	}
	// 	$autoTransferToLockedWallet=isset($defaultAllAffiliateSettings->default_affiliate_settings->autoTransferToLockedWallet) ? $defaultAllAffiliateSettings->default_affiliate_settings->autoTransferToLockedWallet : false ;
	// 	if ($autoTransferToLockedWallet) {
	// 		$this->transferAllEarningsToLockedWallet($yearmonth, $defaultAllAffiliateSettings->default_affiliate_settings->minimumPayAmount);
	// 	}

	// 	return true;
	// }

	# MONTHLY EARNINGS MASTER ------------------------------------------------

	public function getMonthlyEarningsNetById($ids, $yearmonth) {
		$this->db->select('SUM(net) as net');
		$this->db->where_in('affiliate_id', $ids);
		$this->db->where('year_month', $yearmonth);
		$result = $this->db->get('monthly_earnings');

		if ($result->num_rows() > 0) {
			return $result->result()[0]->net;
		} else {
			return 0;
		}
	}

	public function monthlyEarningsMaster($yearmonth) {
		$rlt = true;
		list($start_date, $end_date) = $this->utils->getMonthRange($yearmonth);
		$this->utils->debug_log('yearmonth', $yearmonth, 'start_date', $start_date, 'end_date', $end_date);

		//Admin ID
		$this->load->model(array('affiliatemodel', 'player_model', 'external_system', 'users'));
		// $this->load->model(array('users'));
		$admin_id = $this->users->getSuperAdminId();

		// $defaultAllAffiliateSettings = $this->affiliatemodel->getDefaultAllAffiliateSettings();
		//defaultAllAffiliateSettings: default_affiliate_settings, affiliate_default_terms, sub_affiliate_default_terms
		// $this->utils->debug_log((array) $defaultAllAffiliateSettings);

		// return;
		# GET OPERATOR SETTINGS
		// $operator_settings = $this->affiliatemodel->getAffiliateSettingsObject();
		// // var_dump($operator_settings); // debug: get operator settings
		// $this->utils->debug_log((array) $operator_settings);
		// $default_term = $this->affiliatemodel->getDefaultAffiliateTermsObject();
		// $this->utils->debug_log('default_term', $default_term);

		# INIT VARS
		// $baseIncomeConfig = $defaultAllAffiliateSettings->default_affiliate_settings->baseIncomeConfig;
		// $admin_fee = $defaultAllAffiliateSettings->default_affiliate_settings->admin_fee;
		// if (isset($operator_settings->admin_fee)) {
		// 	$admin_fee = $operator_settings->admin_fee;
		// }

		// $transaction_fee = $defaultAllAffiliateSettings->default_affiliate_settings->transaction_fee;
		// if (isset($operator_settings->transaction_fee)) {
		// 	$transaction_fee = $operator_settings->transaction_fee;
		// }

		// $bonus_fee = $defaultAllAffiliateSettings->default_affiliate_settings->bonus_fee;
		// if (isset($operator_settings->bonus_fee)) {
		// 	$bonus_fee = $operator_settings->bonus_fee;
		// }

		// $cashback_fee = $defaultAllAffiliateSettings->default_affiliate_settings->cashback_fee;
		// if (isset($operator_settings->cashback_fee)) {
		// 	$cashback_fee = $operator_settings->cashback_fee;
		// }

		# GET ALL AFFILIATES

		$affiliates = $this->affiliatemodel->getAllActivtedAffiliates();
		// $this->utils->debug_log('affiliates', $affiliates);
		# AFFILIATES LOOP

		// Init Data
		$data = array();

		// var_dump($affiliates); // debug: get all affiliates

		$datetime = $this->utils->getNowForMysql();
		foreach ($affiliates as $a) {
			//get settings : getAffTermsSettings
			$id = $a['affiliateId'];
			$affTermsSettings=$this->affiliatemodel->getAffTermsSettings($id);
			$baseIncomeConfig=$affTermsSettings['baseIncomeConfig'];
			$admin_fee=$affTermsSettings['admin_fee'];
			$transaction_fee=$affTermsSettings['transaction_fee'];
			$bonus_fee=$affTermsSettings['bonus_fee'];
			$cashback_fee=$affTermsSettings['cashback_fee'];
			$provider=$affTermsSettings['provider'];

			# INIT VARS
			$players_id = $this->affiliatemodel->getAllPlayersUnderAffiliateId($id);
			// $gross_net = 0;
			// $total_fee = 0;
			// $admin_id = 0; // Admin ID

			// var_dump($players_id); // debug: get players under affiliate (total_player)

			# GET AFFILIATE TERMS BY AFFILIATE / BY DEFAULT
			// $default_term_json = $this->affiliatemodel->getDefaultAffiliateTerms();
			// $default_term = json_decode($default_term_json);
			// $affiliate_terms = $this->affiliatemodel->getAffiliateTermsById($id, $defaultAllAffiliateSettings);

			// if (!empty($affiliate_term)) {
			// 	$affiliate_terms = json_decode($affiliate_term)->terms;

			// 	// GENERAL SETTINGS
			// 	$affiliate_terms->minimumBetting = $defaultAllAffiliateSettings->affiliate_default_terms->terms->minimumBetting;
			// 	$affiliate_terms->minimumDeposit = $defaultAllAffiliateSettings->affiliate_default_terms->terms->minimumDeposit;
			// } else {
			// 	$affiliate_terms = $defaultAllAffiliateSettings->affiliate_default_terms->terms;
			// }

			$this->utils->debug_log('affiliate', $id, 'affiliate_terms', $affTermsSettings, 'players_id', count($players_id));

			// var_dump($affiliate_terms); // debug: get affiliate settings by affiliate / by default

			// OVERIDE PLAYERS_ID WITH ACTIVE PLAYERS ONLY
			$players_id = $this->affiliatemodel->filterActivePlayersById($affTermsSettings, $players_id, $start_date, $end_date);

			// var_dump($players_id); //debug: filter player by minimum bet and deposit condition (active_player)

			// OVERIDE PLAYERS_ID WITH GAME PROVIDER CONDITION
			if (count($provider) > 0) {
				$count = $affTermsSettings['totalactiveplayer'];
				// echo 'count: ' . $count; //debug: filter player by game provider condition (active_player)
				$providers_id = $provider;
				// var_dump($providers_id); //debug: filter player by game provider condition (active_player)
				$players_id = $this->affiliatemodel->filterActivePlayersByIdByProvider($affTermsSettings, $players_id, $start_date, $end_date, $providers_id, $count);
			}

			$this->utils->debug_log('players_id', count($players_id));
			// var_dump($players_id); //debug: filter player by game provider condition (active_player)

			# STORE PRIMARY AFFILIATE DATA

			$affiliate = array();
			$affiliate['affiliate_id'] = $id;

			# STEP 1:

			// GET GROSS NET
			$affiliate['gross_net'] = 0;
			switch ($baseIncomeConfig) {
			// BY BET - WIN
			case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN:
				if (count($players_id) > 0) {
					// var_dump($players_id); // debug: get gross income per affiliate by Loss-Win Condition
					$affiliate['gross_net'] = $this->utils->roundCurrencyForShow($this->getLossWinIncomeByGameRate($players_id, $start_date, $end_date));
					// $gross_net = $affiliate['gross_net'];
					// var_dump($gross_net); // debug: get gross income per affiliate by Loss-Win Condition
				} else {
					$affiliate['gross_net'] = 0;
				}
				break;
			// BY DEPOSIT - WITHDRAW
			case Affiliatemodel::INCOME_CONFIG_TYPE_DEPOSIT_WITHDRAWAL:
				if (count($players_id) > 0) {
					// var_dump($players_id); // debug: get gross income per affiliate by Deposit-Withdraw Condition
					$affiliate['gross_net'] = $this->utils->roundCurrencyForShow($this->getDepositWithdrawIncome($players_id, $start_date, $end_date));
					// $gross_net = $affiliate['gross_net'];
					// var_dump($gross_net); // debug: get gross income per affiliate by Deposit-Withdraw Condition
				} else {
					$affiliate['gross_net'] = 0;
				}
				break;
			}
			$this->utils->debug_log('affiliate step 1', $affiliate);

			# STEP 2:
			$affiliate['bonus_fee'] = 0;
			$affiliate['cashback'] = 0;
			$affiliate['transaction_fee'] = 0;
			$affiliate['admin_fee'] = 0;
			$total_fee = 0;

			if (count($players_id) > 0) {

				if ($bonus_fee > 0) {
					// GET BONUS FEE PERCENT
					$bonus_percent = $bonus_fee / 100;
					// var_dump($bonus_fee); // debug: get active_player's bonus fee
					// GET BONUS FEE
					$affiliate['bonus_fee'] = $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalBonus($players_id, $start_date, $end_date) * $bonus_percent);
					// var_dump($affiliate['bonus_fee']); // debug: get active_player's bonus fee
					$total_fee = $total_fee + $affiliate['bonus_fee'];
				}

				if ($cashback_fee > 0) {
					// GET CASHBACK FEE PERCENT
					$cashback_percent = $cashback_fee / 100;
					// var_dump($cashback_percent); // debug: get active_player's cashback fee

					// GET CASHBACK FEE
					$affiliate['cashback'] = $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalCashback($players_id, $start_date, $end_date) * $cashback_percent);
					// var_dump($affiliate['cashback']); // debug: get active_player's cashback fee
					$total_fee = $total_fee + $affiliate['cashback'];
				}

				if ($transaction_fee > 0) {
					// GET TRANSACTION FEE
					$transaction_pecent = $transaction_fee / 100;
					// var_dump($transaction_pecent); // debug: get active_player's transaction fee
					// get from transactions
					$affiliate['transaction_fee'] = $this->utils->roundCurrencyForShow($this->transactions->sumDepositAndWithdraw($players_id, $start_date, $end_date) * $transaction_pecent);
					// var_dump($affiliate['transaction_fee']); // debug: get active_player's transaction fee
					$total_fee = $total_fee + $affiliate['transaction_fee'];
					// var_dump($total_fee); // debug: get active_player's admin fee
				}

				// GET ADMIN FEE PERCENT
				if ($admin_fee > 0) {
					$admin_percent = $admin_fee / 100;
					// var_dump($admin_percent); // debug: get active_player's admin fee

					// GET ADMIN FEE
					// $affiliate['admin_fee'] = $this->utils->roundCurrencyForShow(($gross_net - $total_fee) * $admin_percent);
					$affiliate['admin_fee'] = $this->utils->roundCurrencyForShow(($affiliate['gross_net']) * $admin_percent);
					// ADD ADMIN FEE TO TOTAL FEE
					$total_fee = $total_fee + $affiliate['admin_fee'];
				}
				// echo '('.$gross_net.' - '.$total_fee.') * '.$admin_fee.' = '.$total_fee; // debug: get total fee
			}

			# STEP 3:

			// GET NET INCOME
			$affiliate['net'] = $this->utils->roundCurrencyForShow($affiliate['gross_net'] - $total_fee);
			// echo $affiliate['gross_net'] .' - '. $total_fee .' = '. $affiliate['net'] .'<br>'; // debug: get net income

			# EXTRAS

			// YearMonth - 201411
			$affiliate['year_month'] = $yearmonth;

			// Created Date
			$affiliate['updated_at'] = $datetime;

			// Type 1=init, 2=adjustment
			$affiliate['type'] = self::TYPE_INIT;

			// Paid Flag 0=false, 1=true
			$affiliate['paid_flag'] = self::DB_FALSE;

			// Processed By (Admin ID)
			$affiliate['processed_by'] = $admin_id;

			// Notes
			$affiliate['note'] = "calc " . $yearmonth . " at " . $datetime;

			// Manual Flag 0=false, 1=true
			$affiliate['manual_flag'] = self::DB_FALSE;

			// GET TOTAL AMOUNT FOR RECORD FOR CONTINGENCY
			// $previous_amount = $this->getPreviousEarningsById($id, $yearmonth);
			// if ($previous_amount) {
			// var_dump($previous_amount); // debug: get previous total amount

			// if total amount not equal to zero the make a duplicate for earnings contingency
			// $affiliate['amount'] = $previous_amount->amount;
			// } else {
			$affiliate['amount'] = 0;
			// }

			// Store to Affiliate to Data
			// if ($affiliate['net'] != 0 || $affiliate['amount'] != 0) {
			$this->db->where('affiliate_id', $id);
			$this->db->where('year_month', $yearmonth);
			$result = $this->db->get('monthly_earnings');

			if ($result->num_rows() > 0) {
				$this->db->where('affiliate_id', $id);
				$this->db->where('year_month', $yearmonth);
				$this->db->update('monthly_earnings', $affiliate);
			} else {
				$this->db->insert('monthly_earnings', $affiliate);
			}

			// }

			// Debugger
			// $data[] 						= $affiliate;

		}
		return $rlt;
		// var_dump($data); // debug
	}

	public function getLossWinIncomeByGameRate($players_id, $start_date, $end_date) {
		// var_dump(func_get_args()); // debug: get gross income per affiliate by Bet-Win Condition
		$this->load->model(array('total_player_game_hour', 'game_logs', 'external_system'));
		# INIT VARS
		$total = 0;

		$use_total_hour = $this->utils->getConfig('use_total_hour');
		# GET GAME API
		$games = $this->external_system->getAllActiveSytemGameApi();

		# GET BET, WIN, RATE & SUBTOTAL BY GAME ID
		foreach ($games as $g) {
			// var_dump($g); // debug: get gross income per affiliate by Bet-Win Condition
			# GET GAME RATES
			$rate = $g['game_platform_rate'];

			# GET BET & WIN
			$bet = 0;
			$win = 0;
			if (count($players_id) > 0) {
				// if ($use_total_hour) {

				// 	list($bet, $win, $loss) = $this->total_player_game_hour->getTotalBetsWinsLossByPlayers(
				// 		$players_id, $start_date, $end_date, $g['id']);
				// } else {
				list($bet, $win, $loss) = $this->game_logs->getTotalBetsWinsLossByPlayers(
					$players_id, $start_date, $end_date, $g['id']);

				// }

				// $bet = $this->player_model->getPlayersTotalBet($players_id, $start_date, $end_date, $g['id']);
				// $win = $this->player_model->getPlayersTotalWin($players_id, $start_date, $end_date, $g['id']);
			}

			// echo $bet .' - '. $win; // debug: get gross income per affiliate by Bet-Win Condition

			# GET SUBTOTAL
			$sum = $loss - $win;
			// var_dump($sum); // debug: get gross income per affiliate by Bet-Win Condition
			if ($rate != 100) {
				$subtotal = $this->utils->roundCurrencyForShow($sum * ($rate / 100));
			} else {
				$subtotal = $this->utils->roundCurrencyForShow($sum);
			}
			// var_dump($subtotal); // debug: get gross income per affiliate by Bet-Win Condition

			# ADD SUBTOTAL INTO TOTAL
			$total += $subtotal;
		}

		return $total;
	}

	// public function getBetWinIncome($players_id, $start_date, $end_date) {
	// 	$this->load->model(array('total_player_game_hour'));

	// 	list($bet, $win, $loss) = $this->total_player_game_hour->getTotalBetsWinsLossByPlayers($players_id, $start_date, $end_date);

	// 	# GET BET & WIN
	// 	// $bet = $this->player_model->getPlayersTotalBet($players_id, $start_date, $end_date);
	// 	// $win = $this->player_model->getPlayersTotalWin($players_id, $start_date, $end_date);

	// 	# GET TOTAL
	// 	$total = $bet - $win;

	// 	return $total;
	// }

	// public function getWinLossIncome($players_id, $start_date, $end_date) {
	// 	$this->load->model(array('total_player_game_hour'));

	// 	list($bet, $win, $loss) = $this->total_player_game_hour->getTotalBetsWinsLossByPlayers($players_id, $start_date, $end_date);

	// 	# GET WIN & LOSS
	// 	// $win = $this->player_model->getPlayersTotalWin($players_id, $start_date, $end_date);
	// 	// $loss = $this->player_model->getPlayersTotalLoss($players_id, $start_date, $end_date);

	// 	# GET TOTAL
	// 	$total = $win - $loss;

	// 	return $total;
	// }

	public function getDepositWithdrawIncome($players_id, $start_date, $end_date) {
		$this->load->model(array('transactions'));

		list($deposit, $withdraw, $bonus) = $this->transactions->getPlayerTotalDepositWithdrawalBonusByPlayers(
			$players_id, $start_date, $end_date);

		# GET DEPOSIT & WITHDRAW
		// $deposit = $this->player_model->getPlayersTotalDeposit($players_id, $start_date, $end_date);
		// $withdraw = $this->player_model->getPlayersTotalWithdraw($players_id, $start_date, $end_date);
		# GET TOTAL
		$total = $deposit - $withdraw;

		return $total;
	}

	# MONTHLY EARNINGS SUB LEVEL -----------------------------------------------

	public function monthlyEarningsSubLevel($yearmonth) {
		$this->load->model(array('affiliatemodel', 'player_model', 'external_system'));
		# GET DEFAULT SUBLEVEL SETTINGS
		// $default_sublevel_settings = json_decode($this->affiliatemodel->getDefaultSubAffiliateTerms())->terms;
		// $operator_settings = json_decode($this->affiliatemodel->getAffiliateSettings());
		// var_dump($default_sublevel_settings);
		// $defaultAllAffiliateSettings = $this->affiliatemodel->getDefaultAllAffiliateSettings();

		# GET ALL AFFILIATE'S WITH NET INCOME

		$affiliates = $this->getAllMonthlyEarnings($yearmonth);
		// var_dump($affiliates); // debug: get all affiliates in monthly_earnings

		if (!empty($affiliates)) {
			foreach ($affiliates as $a) {
				$debugger = array();


				# SET VALUES
				$id = $a->affiliate_id;
				$data = array();
				$data['amount'] = 0;

				$affTermsSettings=$this->affiliatemodel->getAffTermsSettings($id);

				# GET SUBLEVEL SETTING BY AFFILIATE and Get Level Master Rate
				// $settings = $this->affiliatemodel->getSubAffiliateTermsById($id);
				// if (!empty($settings)) {
				// 	$sublevel_settings = json_decode($settings)->terms;
				// 	$sublevel_settings->level_master = $sublevel_settings->level_master;
				// } else {
				// 	$sublevel_settings = $default_sublevel_settings;
				// 	$sublevel_settings->level_master = $operator_settings->level_master;
				// }

				// OVERIDE SUBLEVEL
				// $sublevel_settings->sub_level = $default_sublevel_settings->sub_level;
				// $sublevel_settings->sub_levels = $default_sublevel_settings->sub_levels;
				// $sublevel_settings = $this->affiliatemodel->getSubAffiliateTermsById($id, $affTermsSettings);
				// var_dump($sublevel_settings); // debug: get sub level settings by affiliate / by default

				# CALCULATE MASTER
				$net = $a->net;
				$rate = $affTermsSettings['level_master'];
				$rate_percent = $rate / 100;
				$data['rate_for_affiliate'] = $rate;

				$amount = $net * $rate_percent;
				$data['amount'] += $amount;

				$debugger[] = array('level' => 'master', 'id' => $id, 'rate' => $rate_percent, 'net' => $net, 'amount' => $data['amount']);

				# CALCULATE SUBLEVEL
				$max_level = $affTermsSettings['sub_level']; // $sublevel_settings->sub_level;
				$level = 1;
				$rates = $affTermsSettings['sub_levels']; // explode(',', $sublevel_settings->sub_levels);
				$rate = $rates[$level - 1];
				$sub_ids = $this->getSubAffiliatesIds(array($id));

				while ($level < $max_level && $rate > 0 && $sub_ids != null) {
					$sub_rate = $rate / 100;
					$sub_net = $this->getMonthlyEarningsNetById($sub_ids, $yearmonth);
					$sub_amount = $sub_net * $sub_rate;

					$data['amount'] += $sub_amount;

					$debugger[] = array('level' => $level, 'id' => $sub_ids, 'rate' => $sub_rate, 'net' => $sub_net, 'amount' => $data['amount']);

					$sub_ids = $this->getSubAffiliatesIds($sub_ids);
					$level++;

					if (isSet($rates[$level - 1]) && $rates[$level - 1] > 0) {
						$rate = $rates[$level - 1];
					} else {
						$rate = 0;
					}

				}

				# GET PREVIOUS BALANCE
				$previous_balance = 0;
				$previous_amount = $this->getPreviousEarningsById($id, $yearmonth);
				if ($previous_amount) {
					$previous_balance = $previous_amount->balance;
				}

				// $data['amount'] += $previous_balance;
				$data['amount'] = $this->utils->roundCurrencyForShow($data['amount']);
				$data['balance'] = $this->utils->roundCurrencyForShow($data['amount'] + $previous_balance);

				// var_dump($debugger);

				$this->db->where('affiliate_id', $id);
				$this->db->where('year_month', $yearmonth);
				$this->db->update('monthly_earnings', $data);

			}
		}
	}

	public function getSubAffiliatesIds($ids) {
		if (!empty($ids)) {
			$sub_id = array();
			$this->db->where_in('parentId', $ids)->from('affiliates');

			$rows = $this->runMultipleRow();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					array_push($sub_id, $row->affiliateId);
				}
				return $sub_id;
			}

		}
		// $sub_id = array();
		// $this->db->where_in('parentId', $ids);
		// $result = $this->db->get('affiliates');

		// if ($result->num_rows() > 0) {
		// 	foreach ($result->result() as $r) {
		// 		array_push($sub_id, $r->affiliateId);
		// 	}
		// 	return implode(',', $sub_id);
		// } else {
		return null;
		// }
	}

	# MONTHLY EARNINGS REPORT --------------------------------------------------

	public function getMonthlyEarningsById($affiliate_id, $year_month = null) {
		if (!$year_month) {
			$year_month = date('Ym');
		}

		$this->db->where('affiliate_id', $affiliate_id);
		$this->db->where('year_month', $year_month);

		return $this->db->get('monthly_earnings')->result();
	}

	public function getAllEarningsById($affiliate_id) {
		$this->load->model(array('affiliatemodel'));
		$this->db->where('affiliate_id', $affiliate_id);

		$result = $this->db->get('monthly_earnings');

		if ($result->num_rows() > 0) {
			// $defaultAllAffiliateSettings = $this->affiliatemodel->getDefaultAllAffiliateSettings();
			$data = array();
			foreach ($result->result() as $e) {
				$players_id = array();
				$players_id = $this->affiliatemodel->getAllPlayersUnderAffiliateId($e->affiliate_id);
				$e->count_players = count($players_id);
				// $this->utils->debug_log('affiliateId', $e->affiliate_id);
				list($start_date, $end_date) = $this->utils->getMonthRange($e->year_month);
				// $affiliate_term = $this->affiliatemodel->getAffiliateTermsById($e->affiliate_id, $defaultAllAffiliateSettings);
				$affiliate_term=$this->affiliatemodel->getAffTermsSettings($e->affiliate_id);
				$e->active_players = count($this->affiliatemodel->filterActivePlayersById($affiliate_term, $players_id, $start_date, $end_date));
				// $e->active_players = 0;
				$data[] = $e;
			}

			return $data;
		} else {
			return null;
		}
	}

	public function getAllPlatformEarningsById($affiliate_id) {
		$this->db->select('*');
		$this->db->select('(game_platform_revenue - game_platform_gross_revenue) as game_platform_fee');
		$this->db->select('(game_platform_admin_fee+game_platform_bonus_fee+game_platform_cashback_fee+game_platform_transaction_fee) as game_platform_total_fee');
		$this->db->where('affiliate_id', $affiliate_id);
		$this->db->from('affiliate_game_platform_earnings');

		$query = $this->db->get();

		return $query->result_array();
	}

	public function getAllDailyEarningsById($affiliate_id) {
		$query = $this->db->where('affiliate_id', $affiliate_id)->get('aff_daily_earnings');
		return $query->result_array();
	}

	public function getAllMonthlyEarningsById_2($affiliate_id) {
		$query = $this->db->select('*')->select('year_month as date')->where('affiliate_id', $affiliate_id)->get('aff_monthly_earnings');
		return $query->result_array();
	}

	public function getAllMonthlyEarningsById($affiliate_id) {
		$this->load->model(array('affiliatemodel'));
		$this->db->select(array(
			'year_month as date',
			'gross_net as gross_revenue',
			'(bonus_fee+transaction_fee+cashback+admin_fee) as total_fee',
			'net as net_revenue',
			'rate_for_affiliate as commission_percentage',
			'amount as total_commission',
			'paid_flag',
			'note'
		));
		$this->db->where('affiliate_id', $affiliate_id);

		$result = $this->db->get('monthly_earnings');

		if ($result->num_rows() > 0) {
			// $defaultAllAffiliateSettings = $this->affiliatemodel->getDefaultAllAffiliateSettings();
			$data = array();
			foreach ($result->result_array() as $e) {
				$players_id = array();
				$players_id = $this->affiliatemodel->getAllPlayersUnderAffiliateId($e['affiliate_id']);
				$e['total_players'] = count($players_id);
				// $this->utils->debug_log('affiliateId', $e['affiliate_id']);
				list($start_date, $end_date) = $this->utils->getMonthRange($e['year_month']);
				// $affiliate_term = $this->affiliatemodel->getAffiliateTermsById($e['affiliate_id'], $defaultAllAffiliateSettings);
				$affiliate_term=$this->affiliatemodel->getAffTermsSettings($e['affiliate_id']);
				$e['active_players'] = count($this->affiliatemodel->filterActivePlayersById($affiliate_term, $players_id, $start_date, $end_date));
				// $e->active_players = 0;
				$data[] = $e;
			}

			return $data;
		} else {
			return null;
		}
	}

	public function getAllMonthlyEarnings($year_month = null, $username = null, $parentId = null, $paid_flag = null, $summary = true) {
		$this->load->model(array('affiliatemodel', 'player_model', 'external_system'));
		$this->db->select('m1.*, affiliates.username, affiliates.firstname, affiliates.lastname, affiliates.parentId, affiliates.status');
		if (!empty($year_month)) {
			$this->db->where('m1.year_month', $year_month);
		}

		if (!empty($username)) {
			$this->db->where('affiliates.username', $username);
		}

		if (!empty($parentId)) {
			$this->db->where('affiliates.parentId', $parentId);
		}

		if ($paid_flag != null) {
			$this->db->where('m1.paid_flag', $paid_flag);
		}

		// if ($summary) {
		// 	$this->db->where('m2.id IS NULL');
		// }

		$this->db->from('monthly_earnings AS m1');
		// if ($summary) {
		// 	$this->db->join('monthly_earnings AS m2', 'm1.affiliate_id = m2.affiliate_id AND m1.id < m2.id', 'left');
		// }

		$this->db->join('affiliates', 'm1.affiliate_id = affiliates.affiliateId');
		$result = $this->db->get();

		// $this->utils->printLastSQL();

		if ($result->num_rows() > 0) {
			// $defaultAllAffiliateSettings = $this->affiliatemodel->getDefaultAllAffiliateSettings();
			$data = array();
			foreach ($result->result() as $e) {
				$players_id = array();
				$players_id = $this->affiliatemodel->getAllPlayersUnderAffiliateId($e->affiliate_id);
				$e->count_players = count($players_id);
				//debug, hide
				list($start_date, $end_date) = $this->utils->getMonthRange($e->year_month);
				$affiliate_term=$this->affiliatemodel->getAffTermsSettings($e->affiliate_id);
				// $affiliate_term = $this->affiliatemodel->getAffiliateTermsById($e->affiliate_id, $defaultAllAffiliateSettings);
				$e->active_players = $this->affiliatemodel->filterActivePlayersById($affiliate_term, $players_id, $start_date, $end_date);
				// $e->active_players = array();
				$e->sub_affiliates = count($this->affiliatemodel->getAllAffiliatesUnderAffiliate($e->affiliate_id));
				$e->balance = $this->utils->roundCurrencyForShow($e->balance);
				$e->amount = $this->utils->roundCurrencyForShow($e->amount);
				$data[] = $e;
			}

			return $data;
			// } else {
			// 	$this->db->select('m1.*, affiliates.username, affiliates.firstname, affiliates.lastname, affiliates.parentId, affiliates.status');
			// 	if (!empty($year_month)) {
			// 		$this->db->where('m1.year_month', $year_month);
			// 	}

			// 	if (!empty($username)) {
			// 		$this->db->where('affiliates.username', $username);
			// 	}

			// 	if (!empty($parentId)) {
			// 		$this->db->where('affiliates.parentId', $parentId);
			// 	}

			// 	if ($paid_flag != null) {
			// 		$this->db->where('m1.paid_flag', $paid_flag);
			// 	}

			// 	$this->db->from('monthly_earnings AS m1');
			// 	$this->db->join('affiliates', 'm1.affiliate_id = affiliates.affiliateId');
			// 	$result = $this->db->get();

			// 	// $this->utils->printLastSQL();

			// 	if ($result->num_rows() > 0) {
			// 		$defaultAllAffiliateSettings = $this->affiliatemodel->getDefaultAllAffiliateSettings();
			// 		$data = array();
			// 		foreach ($result->result() as $e) {
			// 			$players_id = array();
			// 			$players_id = $this->affiliatemodel->getAllPlayersUnderAffiliateId($e->affiliate_id);
			// 			$e->count_players = count($players_id);
			// 			list($start_date, $end_date) = $this->utils->getMonthRange($e->year_month);
			// 			$affiliate_term = $this->affiliatemodel->getAffiliateTermsById($e->affiliate_id, $defaultAllAffiliateSettings);
			// 			$e->active_players = $this->affiliatemodel->filterActivePlayersById($affiliate_term, $players_id, $start_date, $end_date);
			// 			$e->sub_affiliates = count($this->affiliatemodel->getAllAffiliatesUnderAffiliate($e->affiliate_id));
			// 			$data[] = $e;
			// 		}

			// 		return $data;
			// 	} else {
			// 		return null;
			// 	}
		}
		return null;
	}

	/**
	 * spencer.kuo
	 */
	public function getMonthlyEarnings($earningid) {
		$this->db->where('id', $earningid);
		$this->db->where('paid_flag', self::DB_FALSE);
		$this->db->order_by('updated_at', 'DESC');
		$result = $this->db->get('aff_monthly_earnings');
		if ($result->num_rows() > 0) {
			return $result->result()[0];
		} else {
			return null;
		}
	}

	/**
	 * spencer.kuo
	 */
	public function getPreviousEarning($affiliate_id, $year_month) {
		$d = new \DateTime();
		$year = substr($year_month, 0, 4);
		$month = substr($year_month, 4, 2);
		$d->setDate($year, $month, 1);
		$d->setTime(0, 0, 0);
		$d->modify('-1 month');
		$prev_year_month = $d->format('Ym');
		$this->db->where('affiliate_id', $affiliate_id);
		$this->db->where('year_month', $prev_year_month);
		$this->db->where('paid_flag', self::DB_FALSE); // this will reset to zero if paid
		$this->db->order_by('updated_at', 'DESC'); // this will get the adjustment
		$result = $this->db->get('aff_monthly_earnings');
		if ($result->num_rows() > 0) {
			return $result->result()[0]; // return first record
		} else {
			return null;
		}
	}


	public function getPreviousEarningsById($affiliate_id, $year_month) {
		$d = new \DateTime();
		$year = substr($year_month, 0, 4);
		$month = substr($year_month, 4, 2);
		$d->setDate($year, $month, 1);
		$d->setTime(0, 0, 0);
		$d->modify('-1 month');
		$prev_year_month = $d->format('Ym');
		// var_dump(func_get_args()); // debug: get previous total amount
		// $prev_year_month = date('Ym', strtotime($year_month . ' -1 months'));
		// var_dump($prev_year_month); // debug: get previous total amount

		$this->db->where('affiliate_id', $affiliate_id);
		$this->db->where('year_month', $prev_year_month);
		$this->db->where('paid_flag', self::DB_FALSE); // this will reset to zero if paid
		$this->db->order_by('updated_at', 'DESC'); // this will get the adjustment

		// $this->utils->debug_log('prev_year_month', $prev_year_month, 'affiliate_id', $affiliate_id);

		$result = $this->db->get('monthly_earnings');
		if ($result->num_rows() > 0) {
			// var_dump($result->result()[0]);
			return $result->result()[0]; // return first record
		} else {
			return null;
		}
	}

	public function getMinimumMonthlyPayAmountSetting() {
		$minimumMonthlyPayAmount = 0;
		try {
			$query = $this->db->get_where('operator_settings', array('name' => 'affiliate_common_settings'));
			$setting = json_decode($query->row()->template);
			$minimumMonthlyPayAmount = $setting->minimumPayAmount;
		} catch (Exception $e) {

		}
		return $minimumMonthlyPayAmount;
	}

	public function getMinimumPayAmountSetting() {
		$minimumPayAmount = 0;
		try {
			$query = $this->db->get_where('operator_settings', array('name' => 'affiliate_common_settings'));
			$setting = json_decode($query->row()->template);
			$minimumPayAmount = $setting->minimumPayAmount;
		} catch (Exception $e) {

		}
		return $minimumPayAmount;
	}

	/**
	 * spencer.kuo
	 */
	public function getTotalActivePlayerSetting() {
		$totalactiveplayer = 0;
		try {
			$query = $this->db->get_where('operator_settings', array('name' => 'affiliate_common_settings'));
			$setting = json_decode($query->row()->template);
			$totalactiveplayer = $setting->totalactiveplayer;
		} catch (Exception $e) {

		}
		return $totalactiveplayer;
	}

	public function getCurrentUnpaidAmount($year_month, $min_amount) {
		$this->db->select('Sum(amount) as amount');
		$this->db->where('year_month', $year_month);
		$this->db->where('paid_flag', 0);
		$this->db->where('amount >=', $min_amount);
		$result = $this->db->get('monthly_earnings');

		if ($result->num_rows() > 0) {
			return $result->result()[0]->amount;
		} else {
			return 0;
		}

	}

	public function getCurrentUnpaidBalance($year_month, $min_amount) {
		$this->db->select('Sum(balance) as balance');
		$this->db->where('year_month', $year_month);
		$this->db->where('paid_flag', 0);
		$this->db->where('balance >=', $min_amount);
		$result = $this->db->get('monthly_earnings');

		if ($result->num_rows() > 0) {
			return $result->result()[0]->balance;
		} else {
			return 0;
		}

	}

	public function getYearMonthEarnings($all = false) {
		if ($all) {
			$this->db->select('year_month');
			$this->db->group_by('year_month');
			$this->db->order_by('year_month', 'desc');
			$result = $this->db->get('monthly_earnings');

			if ($result->num_rows() > 0) {
				$year_months = array();

				foreach ($result->result() as $ym) {
					$year_months[] = $ym->year_month;
				}
				return $year_months;
			}
		} else {
			$this->db->select('year_month');
			$this->db->group_by('year_month');
			$this->db->order_by('year_month', 'desc');
			$result = $this->db->get('monthly_earnings');

			if ($result->num_rows() > 0) {
				return $result->result()[0]->year_month;
			}
		}
	}
	# CRON JOB -----------------------------------------------------------------

	//default day is 1
	const DEFAULT_PAY_DAY = 1;

	public function getPayDay() {
		$this->db->where('name', 'affiliate_common_settings');
		$query = $this->db->get('operator_settings');

		if ($query->num_rows() > 0) {

			$row = $query->row();
			$payday = json_decode($row->template)->paymentDay;

			return intval($payday);
		} else {
			return self::DEFAULT_PAY_DAY;
		}

	}

	public function getPaymentSchedule() {
		$this->load->model('operatorglobalsettings');
		$affiliate_common_settings = $this->operatorglobalsettings->getSettingJson('affiliate_common_settings','template');
		return $affiliate_common_settings['paymentSchedule'] == 'monthly' ? min($affiliate_common_settings['paymentDay'], date('t')) : $affiliate_common_settings['paymentSchedule'];
	}

	public function isPayday() {
		$this->load->model('operatorglobalsettings');
		$affiliate_common_settings = $this->operatorglobalsettings->getSettingJson('affiliate_common_settings','template');
		if ($affiliate_common_settings['paymentSchedule'] == 'monthly') {
			$paymentDay = min($affiliate_common_settings['paymentDay'], date('t')); # Get lower value (ex. payment day is 31 and month has only 28 days)
			return $paymentDay == date('j');
		} else {
			# If paymentSchedule is not monthly, it will have values like monday / tuesday which denotes weekly payment
			return strcasecmp($affiliate_common_settings['paymentSchedule'], date('l')) === 0;
		}
	}

	public function todayIsPayday() {
		$payDay = $this->getPayDay();
		$today = intval(date('d'));
		//last day
		if ($payDay > date('t')) {
			$payDay = date('t');
		}
		$this->utils->debug_log('today', $today, 'payday', $payDay);
		return $payDay == $today;
	}

	public function transferToWalletById($earningid, $min_amount = 0) {
		$this->db->from('monthly_earnings')->where('id', $earningid);
		$success = true;
		$me = $this->runOneRow();
		if ($me) {
			$success = $this->transferOneEarningToWallet($me, $min_amount);
		}
		return $success;
	}

	public function transferToWalletById_2($earningid, $min_amount = 0) {
		$this->db->from('aff_monthly_earnings')->where('id', $earningid);
		$success = true;
		$me = $this->runOneRow();
		if ($me) {
			$success = $this->transferOneEarningToWallet_2($me, $min_amount);
		}
		return $success;
	}

	public function transferToWalletById_3($earningid, $min_amount = 0) {
		$this->db->from('aff_daily_earnings')->where('id', $earningid);
		$success = true;
		$me = $this->runOneRow();
		if ($me) {
			$success = $this->transferOneEarningToWallet_3($me, $min_amount);
		}
		return $success;
	}

	public function transferToWalletById_4($earningid, $min_amount = 0) {
		$this->db->from('affiliate_game_platform_earnings')->where('id', $earningid);
		$success = true;
		$me = $this->runOneRow();
		if ($me) {
			$success = $this->transferOneEarningToWallet_4($me, $min_amount);
		}
		return $success;
	}

	public function transferOneEarningToWallet($me, $min_amount = 0) {
		$success = true;
		$affId = $me->affiliate_id;
		// $balance = $me->balance;
		$amount = $me->amount;
		$yearmonth = $me->year_month;
		$extraNotes = " for " . $yearmonth . ' earning id:' . $me->id;
		//lock affiliate balance
		$lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
		$lock_it = $this->utils->lockResourceBy($affId, $lock_type, $locked_key);
		$this->utils->debug_log('lock aff', $affId, 'earning id', $me->id, 'amount', $amount);
		// $created_at = date('Y-m-d H-m-s');
		try {
			if ($lock_it) {
				//check paid flag
				$this->db->from('monthly_earnings')->where('id', $me->id)->where('paid_flag', self::DB_FALSE);
				if ($this->runExistsResult() && $amount != 0) {
					$this->startTrans();
					if ($amount > 0) {
						$tranId = $this->transactions->depositToAff($affId, abs($amount), $extraNotes);
					} else {
						$tranId = $this->transactions->withdrawFromAff($affId, abs($amount), $extraNotes);
					}
					if ($tranId) {
						//update paid
						$this->db->set('paid_flag', self::DB_TRUE)->where('id', $me->id);
						$this->runAnyUpdate('monthly_earnings');
					} else {
						$this->utils->error_log('deposit/withdraw aff failed', $affId, $amount, $extraNotes);
						$success = false;
					}
					$success = $this->endTransWithSucc() && $success;
				}
			} else {
				$this->utils->error_log('lock aff failed', $affId, $amount, $extraNotes);
				$success = false;
			}
		} finally {
			$rlt = $this->utils->releaseResourceBy($affId, $lock_type, $locked_key);
		}
		return $success;
	}

	//add by spencer.kuo
	public function transferOneEarningToWallet_2($me, $min_amount = 0, $check = true) {
		$this->load->model('affiliatemodel');
	//marked by spencer.kuo
	//public function transferOneEarningToWallet_2($me, $min_amount = 0, $check = true) {
		//added by spencer.kuo
		$transferOldEarnings = Array();
		if ($this->utils->isEnabledFeature('switch_to_ibetg_commission') && $check) {
			//check total active players
			$totalActivePlayer = $this->getTotalActivePlayerSetting();
			$yearmonth = $me->year_month;
			$activeplayers = $me->active_players;
			for ($i = 1; $i <= 3; $i++) {
				//$this->utils->debug_log('me year_month : ' . $me->year_month . ' year_month : ' . $yearmonth);
				$previousReport = $this->getPreviousEarning($me->affiliate_id, $yearmonth);
				$this->utils->debug_log('me year_month : ' . $me->year_month . 'year_month : ' . $yearmonth . ', previousReport : ', $previousReport);
				if (!empty($previousReport)) {
					$activeplayers += $previousReport->active_players;
					$yearmonth = $previousReport->year_month;
					$transferOldEarnings[] = $previousReport;
				}
			}
			if ($activeplayers <= $totalActivePlayer)
				return false;
			$transferOldEarnings = array_reverse($transferOldEarnings);
			foreach($transferOldEarnings as $oldEarnings) {
				$this->transferOneEarningToWallet_2($oldEarnings, $min_amount, false);
			}
		}
		$this->utils->debug_log('transferOneEarningToWallet_2 : ', $me);
		$success = true;
		$affId = $me->affiliate_id;
		// $balance = $me->balance;
		$amount = $me->total_commission;
		$yearmonth = $me->year_month;
		$extraNotes = " for " . $yearmonth . ' earning id:' . $me->id;
		//lock affiliate balance
		if($amount == 0){
			$success = $this->updatePaidFlag($me->id, Affiliatemodel::DB_TRUE);
			return $success;
		}
		$lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
		$lock_it = $this->utils->lockResourceBy($affId, $lock_type, $locked_key);
		$this->utils->debug_log('lock aff', $affId, 'earning id', $me->id, 'amount', $amount);
		// $created_at = date('Y-m-d H-m-s');
		try {
			if ($lock_it) {
				//check paid flag
				$this->db->from('aff_monthly_earnings')->where('id', $me->id)->where('paid_flag', self::DB_FALSE);
				if ($this->runExistsResult() && $amount != 0) {
					$this->startTrans();
					if ($amount > 0) {
						$tranId = $this->transactions->depositToAff($affId, abs($amount), $extraNotes);
					} else {
						$tranId = $this->transactions->withdrawFromAff($affId, abs($amount), $extraNotes);
					}
					if ($tranId) {
						//update paid
						$this->updatePaidFlag($me->id, Affiliatemodel::DB_TRUE);
					} else {
						$this->utils->error_log('deposit/withdraw aff failed', $affId, $amount, $extraNotes);
						$success = false;
					}
					$success = $this->endTransWithSucc() && $success;
				}
			} else {
				$this->utils->error_log('lock aff failed', $affId, $amount, $extraNotes);
				$success = false;
			}
		} finally {
			$rlt = $this->utils->releaseResourceBy($affId, $lock_type, $locked_key);
		}
		return $success;
	}

	//add by spencer.kuo
	public function transferOneEarningToWallet_3($me, $min_amount = 0, $check = true) {
		//added by spencer.kuo
		$transferOldEarnings = Array();
		if ($this->utils->isEnabledFeature('switch_to_ibetg_commission') && $check) {
			//check total active players
			$totalActivePlayer = $this->getTotalActivePlayerSetting();
			$date = $me->date;
			$activeplayers = $me->active_players;
			for ($i = 1; $i <= 3; $i++) {
				$previousReport = $this->getPreviousEarning($me->affiliate_id, $date);
				$this->utils->debug_log('me date : ' . $me->date . 'date : ' . $date . ', previousReport : ', $previousReport);
				if (!empty($previousReport)) {
					$activeplayers += $previousReport->active_players;
					$date = $previousReport->date;
					$transferOldEarnings[] = $previousReport;
				}
			}
			if ($activeplayers <= $totalActivePlayer)
				return false;
			$transferOldEarnings = array_reverse($transferOldEarnings);
			foreach($transferOldEarnings as $oldEarnings) {
				$this->transferOneEarningToWallet_3($oldEarnings, $min_amount, false);
			}
		}
		$this->utils->debug_log('transferOneEarningToWallet_3 : ', $me);
		$success = true;
		$affId = $me->affiliate_id;
		$amount = $me->total_commission;
		$date = $me->date;
		$extraNotes = " for " . $date . ' earning id:' . $me->id;
		//lock affiliate balance
		if($amount == 0){
			$this->utils->debug_log('cannot transfer 0');
			return $success;
		}
		$lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
		$lock_it = $this->utils->lockResourceBy($affId, $lock_type, $locked_key);
		$this->utils->debug_log('lock aff', $affId, 'earning id', $me->id, 'amount', $amount);
		try {
			if ($lock_it) {
				//check paid flag
				$this->db->from('aff_daily_earnings')->where('id', $me->id)->where('paid_flag', self::DB_FALSE);
				if ($this->runExistsResult() && $amount != 0) {
					$this->startTrans();
					if ($amount > 0) {
						$tranId = $this->transactions->depositToAff($affId, abs($amount), $extraNotes);
					} else {
						$tranId = $this->transactions->withdrawFromAff($affId, abs($amount), $extraNotes);
					}
					if ($tranId) {
						//update paid
						$this->db->set('paid_flag', self::DB_TRUE)->where('id', $me->id);
						$this->runAnyUpdate('aff_daily_earnings');
					} else {
						$this->utils->error_log('deposit/withdraw aff failed', $affId, $amount, $extraNotes);
						$success = false;
					}
					$success = $this->endTransWithSucc() && $success;
				}
			} else {
				$this->utils->error_log('lock aff failed', $affId, $amount, $extraNotes);
				$success = false;
			}
		} finally {
			$rlt = $this->utils->releaseResourceBy($affId, $lock_type, $locked_key);
		}
		return $success;
	}

	//add by spencer.kuo
	public function transferOneEarningToWallet_4($me, $min_amount = 0, $check = true) {
		//added by spencer.kuo
		$this->utils->debug_log('transferOneEarningToWallet_4 : ', $me);
		$success 	= true;
		$affId 		= $me->affiliate_id;
		$date 		= date('Y-m-d', strtotime($me->start_date));
		$amount 	= $me->game_platform_commission_amount;
		$extraNotes = " for " . $date . ' earning id:' . $me->id;

		//lock affiliate balance
		if($amount == 0){
			$success = false;
			$this->utils->debug_log('cannot transfer 0');
			return $success;
		}

        $adminUserId = null;
        if (method_exists($this->authentication, 'getUserId')) {
            $adminUserId = $this->authentication->getUserId();
        }
        if (empty($adminUserId)) {
            //get super admin
            $adminUserId = $this->users->getSuperAdminId();
        }

		$lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
		$lock_it = $this->utils->lockResourceBy($affId, $lock_type, $locked_key);
		$this->utils->debug_log('lock aff', $affId, 'earning id', $me->id, 'amount', $amount);
		try {
			if ($lock_it) {
				//check paid flag
				$this->db->from('affiliate_game_platform_earnings')->where('id', $me->id)->where('paid_flag', self::DB_FALSE);
				if ($this->runExistsResult() && $amount != 0) {
					$this->startTrans();
					if ($amount > 0) {
						$tranId = $this->transactions->depositToAff($affId, abs($amount), $extraNotes);
					} else {
						$tranId = $this->transactions->withdrawFromAff($affId, abs($amount), $extraNotes);
					}
					if ($tranId) {
						//update paid
						$this->db->set('paid_flag', self::DB_TRUE)->set('updated_by', $adminUserId)->where('id', $me->id);
						$this->runAnyUpdate('affiliate_game_platform_earnings');
					} else {
						$this->utils->error_log('deposit/withdraw aff failed', $affId, $amount, $extraNotes);
						$success = false;
					}
					$success = $this->endTransWithSucc() && $success;
				}
			} else {
				$this->utils->error_log('lock aff failed', $affId, $amount, $extraNotes);
				$success = false;
			}
		} finally {
			$rlt = $this->utils->releaseResourceBy($affId, $lock_type, $locked_key);
		}
		return $success;
	}

	public function transferAllEarningsToWallet($year_month = null, $min_amount = 0) {
		$this->utils->debug_log('year_month', $year_month, 'min_amount', $min_amount);
		$this->load->model('transactions');

		$this->db->from('monthly_earnings')
			->where('paid_flag', self::DB_FALSE)
			->where('amount !=', 0);

		if ($year_month) {
			$this->db->where('year_month', $year_month);
		}

		$rows = $this->runMultipleRow();
		$success = true;
		$cnt = 0;
		if ( ! empty($rows)) {
			foreach ($rows as $me) {
				if ($me->amount < 0 || $me->amount >= $min_amount) {
					$success = $this->transferOneEarningToWallet($me);
				}
				if ( ! $success) {
					break;
				}
				$cnt++;
			}
		}
		$this->utils->debug_log('cnt', $cnt);
		return $success;
	}

	public function transferAllEarningsToWallet_2($year_month = null, $min_amount = 0) {
		$this->load->model('transactions');
		$this->utils->debug_log('year_month', $year_month, 'min_amount', $min_amount);

		$this->db->from('aff_monthly_earnings')
			->where('paid_flag', self::DB_FALSE);
			// ->where('total_commission !=', 0);

		if ($year_month) {
			$this->db->where('year_month', $year_month);
		}

		$rows = $this->runMultipleRow();
		$success = true;
		$cnt = 0;
		if ( ! empty($rows)) {
			foreach ($rows as $me) {
				if ($me->total_commission < 0 || $me->total_commission >= $min_amount) {
					$success = $this->transferOneEarningToWallet_2($me);
				}
				if ( ! $success) {
					break;
				}
				$cnt++;
			}
		}
		$this->utils->debug_log('cnt', $cnt);
		return $success;
	}

	public function transferAllEarningsToWallet_3($date = null, $min_amount = 0) {
		$this->load->model('transactions');
		$this->utils->debug_log('date', $date, 'min_amount', $min_amount);

		$this->db->from('aff_daily_earnings')
			->where('paid_flag', self::DB_FALSE)
			->where('total_commission !=', 0);

		if ($date) {
			$this->db->where('date', $date);
		}

		$rows = $this->runMultipleRow();
		$success = true;
		$cnt = 0;
		if ( ! empty($rows)) {
			foreach ($rows as $me) {
				if ($me->total_commission < 0 || $me->total_commission >= $min_amount) {
					$success = $this->transferOneEarningToWallet_3($me);
				}
				if ( ! $success) {
					break;
				}
				$cnt++;
			}
		}
		$this->utils->debug_log('cnt', $cnt);
		return $success;
	}

	public function transferAllEarningsToWallet_4($date = null, $min_amount = 0) {
		$this->load->model('transactions');
		$this->utils->debug_log('date', $date, 'min_amount', $min_amount);

		$this->db->from('affiliate_game_platform_earnings')
			->where('paid_flag', self::DB_FALSE)
			->where('game_platform_commission_amount !=', 0);

		if ($date) {
			$this->db->where('end_date <=', $date);
		}

		$rows = $this->runMultipleRow();
		$success = true;
		$cnt = 0;
		if ( ! empty($rows)) {
			foreach ($rows as $me) {
				if ($me->game_platform_commission_amount < 0 || $me->game_platform_commission_amount >= $min_amount) {
					$success = $this->transferOneEarningToWallet_4($me);
				}
				if ( ! $success) {
					break;
				}
				$cnt++;
			}
		}
		$this->utils->debug_log('cnt', $cnt);
		return $success;
	}

	public function transferOneEarningToLockedWallet($me, $min_amount = 0) {
		$success = true;
		$affId = $me->affiliate_id;
		// $balance = $me->balance;
		$amount = $me->amount;
		$yearmonth = $me->year_month;
		$extraNotes = " for " . $yearmonth . ' earning id:' . $me->id;
		//lock affiliate balance

		if($amount==0){
			$this->utils->debug_log('cannot transfer 0');
			return $success;
		}

		$lock_type = Utils::LOCK_ACTION_AFF_BALANCE;
		$lock_it = $this->utils->lockResourceBy($affId, $lock_type, $locked_key);

		$this->utils->debug_log('lock aff', $affId, 'earning id', $me->id, 'amount', $amount);

		// $created_at = date('Y-m-d H-m-s');
		try {
			if ($lock_it) {
				//check paid flag
				$this->db->from('monthly_earnings')->where('id', $me->id)->where('paid_flag', self::DB_FALSE);
				if ($this->runExistsResult() && $amount != 0) {

					$this->startTrans();

					if ($amount > 0) {
						$tranId = $this->transactions->depositToAffLockedWallet($affId, abs($amount), $extraNotes);
					// } else {
					// 	$tranId = $this->transactions->withdrawFromAffLockedWallet($affId, abs($amount), $extraNotes);
					}
					if ($tranId) {
						//update paid
						$this->db->set('paid_flag', self::DB_TRUE)->where('id', $me->id);
						$this->runAnyUpdate('monthly_earnings');
					} else {
						$this->utils->error_log('deposit/withdraw aff failed', $affId, $amount, $extraNotes);
						$success = false;
					}
					$success = $this->endTransWithSucc() && $success;
				}
			} else {
				$this->utils->error_log('lock aff failed', $affId, $amount, $extraNotes);
				$success = false;
			}
		} finally {
			$rlt = $this->utils->releaseResourceBy($affId, $lock_type, $locked_key);
		}

		return $success;
	}

	public function transferAllEarningsToLockedWallet($year_month = null, $min_amount = 0){
		$this->utils->debug_log('year_month', $year_month, 'min_amount', $min_amount);
		$this->load->model('transactions');
		// $this->db->where('year_month', $year_month); # IF YOU UNCOMMENT THIS, PLEASE CONSIDER YEAR_WEEK
		$this->db->from('monthly_earnings')
			->where('paid_flag', self::DB_FALSE)
		// ->where('amount >=', $min_amount)
			->where('amount !=', 0);
		// $result = $this->db->get('monthly_earnings');
		$rows = $this->runMultipleRow();

		// if (!empty($rows)) {
		$success = true;
		$cnt = 0;
		if (!empty($rows)) {
			foreach ($rows as $me) {
				if ($me->amount < 0 || $me->amount >= $min_amount) {
					$success=$this->transferOneEarningToLockedWallet($me);
				}
				if (!$success) {
					break;
				}
				$cnt++;
			}
		}
		$this->utils->debug_log('transferAllEarningsToLockedWallet cnt', $cnt);

		// $this->db->from('monthly_earnings')
		// 	->where('paid_flag', self::DB_FALSE)
		// 	->where('amount <', 0);
		// // $result = $this->db->get('monthly_earnings');
		// $rows = $this->runMultipleRow();

		return $success;

	}

	public function getYearMonthEarningsListInfo() {

		$first_year_month = $this->getFirstYearMonthEarnings();
		if (empty($first_year_month)) {
			$first_year_month = $this->utils->getLastYearMonth();
		}
		$year_month_list = $this->getYearMonthListToNow($first_year_month);
		$last_year_month = $this->getLastYearMonthFromList($year_month_list);

		return array($year_month_list, $first_year_month, $last_year_month);
	}

	public function getLastYearMonthFromList($list) {
		$last = null;
		if (!empty($list)) {
			foreach ($list as $key => $value) {
				$last = $key;
			}
		}
		return $last;
	}

	public function getFirstYearMonthEarnings() {
		$this->db->select('min(`year_month`) first_year_month', null, false)->from('monthly_earnings');
		return $this->runOneRowOneField('first_year_month');
	}

	public function getYearMonthListToNow($first_year_month, $last_year_month = null) {
		if (empty($last_year_month)) {
			$last_year_month = $this->utils->getLastYearMonth();
		}
		//from first to last
		$year = intval(substr($first_year_month, 0, 4));
		$month = intval(substr($first_year_month, 4, 2));

		$last_year = intval(substr($last_year_month, 0, 4));
		$last_month = intval(substr($last_year_month, 4, 2));

		$list = array();

		for ($i = $year; $i <= $last_year; $i++) {
			$stop_month = 12;
			if ($i == $last_year) {
				$stop_month = $last_month;
			}
			$start_month = 1;
			if ($i == $year) {
				$start_month = $month;
			}
			for ($j = $start_month; $j <= $stop_month; $j++) {
				//add to list
				$list[$i . str_pad($j, 2, '0', STR_PAD_LEFT)] = $i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT);
			}

		}

		return $list;
	}

	public function getYearMonthListToNow_2() {
		$this->db->distinct();
		$this->db->select('year_month');
		$this->db->order_by('year_month', 'desc');
		$query = $this->db->get('aff_monthly_earnings');
		$rows = $query->result_array();
		$year_months = array_column($rows, 'year_month');
		return array_combine($year_months, $year_months);
	}

	public function getAffiliatePlatformCommission($affiliateCommissionId) {
		$this->db->where('id', $affiliateCommissionId);
		$query = $this->db->get('affiliate_game_platform_earnings', 1);
		return $query->row_array();
	}

	public function getAffiliateDailyCommission($affiliateCommissionId) {
		$this->db->where('id', $affiliateCommissionId);
		$query = $this->db->get('aff_daily_earnings', 1);
		return $query->row_array();
	}

	public function getAffiliateMonthlyCommission($affiliateCommissionId) {
		$this->db->where('id', $affiliateCommissionId);
		$query = $this->db->get('aff_monthly_earnings', 1);
		return $query->row_array();
	}

	public function updateAffiliateTotalPlatformCommission($affiliateCommissionId, $newCommission, $adjustment_notes = '') {
		$amount = array('game_platform_commission_amount' => $newCommission, 'adjustment_notes' => $adjustment_notes);
		$this->db->where('id', $affiliateCommissionId);
		$this->db->update('affiliate_game_platform_earnings', $amount);
	}

	public function updateAffiliateTotalDailyCommission($affiliateCommissionId, $newCommission, $adjustment_notes = '') {
		$amount = array('total_commission' => $newCommission, 'adjustment_notes' => $adjustment_notes);
		$this->db->where('id', $affiliateCommissionId);
		$this->db->update('aff_daily_earnings', $amount);
	}

	public function updateAffiliateTotalMonthlyCommission($affiliateCommissionId, $newCommission, $adjustment_notes = '') {
		$amount = array('total_commission' => $newCommission, 'adjustment_notes' => $adjustment_notes);
		$this->db->where('id', $affiliateCommissionId);
		$this->db->update('aff_monthly_earnings', $amount);
	}

	public function updatePaidFlag($earningsId, $newFlag) {

        $adminUserId = null;
        if (method_exists($this->authentication, 'getUserId')) {
            $adminUserId = $this->authentication->getUserId();
        }
        if (empty($adminUserId)) {
            //get super admin
            $adminUserId = $this->users->getSuperAdminId();
        }

		$this->db->set('paid_flag', $newFlag)->set('updated_by', $adminUserId)->where('id', $earningsId);
		$this->runAnyUpdate('aff_monthly_earnings');
		return TRUE;
	}
	# END MONTHLY EARNINGS ----------------------------------------------------
}

////END OF FILE/////////////////