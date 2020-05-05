<?php
trait aff_earning_module {

	public function generate_earnings() {

		$this->load->model(array('affiliate_earnings','affiliatemodel'));

		$paymentSchedule = $this->affiliate_earnings->getPaymentSchedule();

		if (is_integer($paymentSchedule)) {
			$rlt = $this->generate_monthly_earnings();
		} else {
			$rlt = $this->generate_weekly_earnings();
		}

		return $rlt;
	}

	# START WEEKLY EARNINGS -------------------------------------------------

	public function generate_weekly_earnings() {

		$startOfWeek 	= 'monday';
		$endOfWeek 		= 'sunday';

		$startDate 		= date('Y-m-d 00:00:00', strtotime("{$startOfWeek} last week"));
		$endDate 		= date('Y-m-d 23:59:59', strtotime("{$endOfWeek} last week"));

		# should load all affiliates with terms
		$terms = $this->affiliatemodel->getAllAffiliateTerm();

		# CALCULATE ALL NET EARNINGS STEP 1-3
		$this->generate_weekly_earnings_master($startDate, $endDate);


		# CALCULATE ALL AMOUNT EARNINGS STEP 4
		$this->generate_weekly_earnings_sublevel($startDate, $endDate);

		// //transfer to wallet
		$defaultAllAffiliateSettings = $terms[-1];// $this->affiliatemodel->getDefaultAllAffiliateSettings();
		$autoTransferToWallet = isset($defaultAllAffiliateSettings['autoTransferToWallet']) ? $defaultAllAffiliateSettings['autoTransferToWallet'] : false;
		$autoTransferToLockedWallet = isset($defaultAllAffiliateSettings['autoTransferToLockedWallet']) ? $defaultAllAffiliateSettings['autoTransferToLockedWallet'] : false;
		$this->utils->debug_log('default_affiliate_settings', $defaultAllAffiliateSettings, 'autoTransferToWallet', $autoTransferToWallet, 'autoTransferToLockedWallet', $autoTransferToLockedWallet);

		if ($autoTransferToWallet) {
			$this->utils->debug_log('transfer to main wallet', $autoTransferToWallet);
			$this->transferAllEarningsToWallet(NULL, $defaultAllAffiliateSettings['minimumPayAmount']);
		}

		if ($autoTransferToLockedWallet) {
			$this->utils->debug_log('transfer to locked wallet', $autoTransferToLockedWallet);
			$this->transferAllEarningsToLockedWallet(NULL, $defaultAllAffiliateSettings['minimumPayAmount']);
		}

		return true;
	}

	# WEEKLY EARNINGS MASTER ------------------------------------------------

	public function generate_weekly_earnings_master($start_date, $end_date) {

		$yearweek = date('YW', strtotime($start_date));

		$rlt = true;

		// list($start_date, $end_date) = $this->utils->getMonthRange($yearmonth);
		// $this->utils->debug_log('create month yearmonth', $yearmonth, 'start_date', $start_date, 'end_date', $end_date);
		$this->utils->debug_log('create weekly yearweek', $yearweek, 'start_date', $start_date, 'end_date', $end_date);

		//Admin ID
		$this->load->model(array('affiliatemodel', 'player_model', 'external_system', 'users'));
		// $this->load->model(array('users'));
		$admin_id = $this->users->getSuperAdminId();

		# GET ALL AFFILIATES

		$affiliates = $this->affiliatemodel->getAllActivtedAffiliates();
		// $this->utils->debug_log('affiliates', $affiliates);
		# AFFILIATES LOOP

		// Init Data
		$data = array();

		// var_dump($affiliates); // debug: get all affiliates
		$terms=$this->affiliatemodel->getAllAffiliateTerm();

		$datetime = $this->utils->getNowForMysql();
		foreach ($affiliates as $a) {
			//get settings : getAffTermsSettings
			$id = $a['affiliateId'];
			$affTermsSettings=$terms[$id];
			$baseIncomeConfig=$affTermsSettings['baseIncomeConfig'];
			$admin_fee=$affTermsSettings['admin_fee'];
			$transaction_fee=$affTermsSettings['transaction_fee'];
			$bonus_fee=$affTermsSettings['bonus_fee'];
			$cashback_fee=$affTermsSettings['cashback_fee'];
			$gameProviderArr=$affTermsSettings['provider'];
			//remove useless
			$provider=$this->utils->filterActiveGameApi($gameProviderArr);

			//load term info
			// $this->utils->debug_log('load term for '.$id, $affTermsSettings);

			# INIT VARS
			$players_id = $this->affiliatemodel->getAllPlayersUnderAffiliateId($id);

			$this->utils->debug_log('affiliate', $id, 'affiliate_terms', $affTermsSettings, 'players_id', count($players_id));

			// OVERIDE PLAYERS_ID WITH ACTIVE PLAYERS ONLY
			$players_id = $this->affiliatemodel->filterActivePlayersById($affTermsSettings, $players_id, $start_date, $end_date, 'day');
			$this->utils->debug_log('get available players', count($players_id));

			// OVERIDE PLAYERS_ID WITH GAME PROVIDER CONDITION
			if (!empty($provider)) {
				$this->utils->debug_log('filter game provider available players', $provider);
				$count = $affTermsSettings['totalactiveplayer'];
				// echo 'count: ' . $count; //debug: filter player by game provider condition (active_player)
				$providers_id = $provider;
				// var_dump($providers_id); //debug: filter player by game provider condition (active_player)
				$players_id = $this->affiliatemodel->filterActivePlayersByIdByProvider($affTermsSettings, $players_id, $start_date, $end_date, $providers_id, $count);
			}

			$this->utils->debug_log('after filter game provider players_id', count($players_id));
			// var_dump($players_id); //debug: filter player by game provider condition (active_player)

			# STORE PRIMARY AFFILIATE DATA

			$affiliate = array();
			$affiliate['affiliate_id'] = $id;
			$affiliate['count_active_player']=count($players_id);

			# STEP 1:

			// GET GROSS NET
			$affiliate['gross_net'] = 0;
			switch ($baseIncomeConfig) {
			// BY BET - WIN
			case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN:
				if (count($players_id) > 0) {
					// var_dump($players_id); // debug: get gross income per affiliate by Loss-Win Condition
					$affiliate['gross_net'] = $this->utils->roundCurrencyForShow($this->get_loss_win_income_by_game_rate($players_id, $start_date, $end_date));
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
					$affiliate['gross_net'] = $this->utils->roundCurrencyForShow($this->get_deposit_withdraw_income($players_id, $start_date, $end_date));
					// $gross_net = $affiliate['gross_net'];
					// var_dump($gross_net); // debug: get gross income per affiliate by Deposit-Withdraw Condition
				} else {
					$affiliate['gross_net'] = 0;
				}
				break;
			}
			$this->utils->debug_log('use base income '.$baseIncomeConfig.' affiliate ', $affiliate);

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

					$this->utils->debug_log('generate bonus fee = bonus * percent');
				}

				if ($cashback_fee > 0) {
					// GET CASHBACK FEE PERCENT
					$cashback_percent = $cashback_fee / 100;
					// var_dump($cashback_percent); // debug: get active_player's cashback fee

					// GET CASHBACK FEE
					$affiliate['cashback'] = $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalCashback($players_id, $start_date, $end_date) * $cashback_percent);
					// var_dump($affiliate['cashback']); // debug: get active_player's cashback fee
					$total_fee = $total_fee + $affiliate['cashback'];

					$this->utils->debug_log('generate cashback fee = cashback * percent');
				}

				if ($transaction_fee > 0) {
					//should be (deposit+withdraw) * percent
					// GET TRANSACTION FEE
					$transaction_percent = $transaction_fee / 100;
					// get from transactions
					// $affiliate['transaction_fee'] = $this->utils->roundCurrencyForShow($this->transactions->sumDepositAndWithdraw($players_id, $start_date, $end_date) * $transaction_percent);

					$affiliate['transaction_fee'] = $this->utils->roundCurrencyForShow($this->transactions->sumTransactionFee($players_id, $start_date, $end_date) * $transaction_percent);
					// var_dump($affiliate['transaction_fee']); // debug: get active_player's transaction fee
					$total_fee = $total_fee + $affiliate['transaction_fee'];

					$this->utils->debug_log('generate transaction fee = (transaction fee) * percent');

					//or sum transaction fee
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

					$this->utils->debug_log('generate admin fee = gross_net * percent');
				}
				// echo '('.$gross_net.' - '.$total_fee.') * '.$admin_fee.' = '.$total_fee; // debug: get total fee
			}

			# STEP 3:

			// GET NET INCOME
			$affiliate['net'] = $this->utils->roundCurrencyForShow($affiliate['gross_net'] - $total_fee);
			// echo $affiliate['gross_net'] .' - '. $total_fee .' = '. $affiliate['net'] .'<br>'; // debug: get net income

			# EXTRAS

			// YearWeek - 201411
			$affiliate['year_week'] = $yearweek;

			// Created Date
			$affiliate['updated_at'] = $datetime;

			// Type 1=init, 2=adjustment
			$affiliate['type'] = self::TYPE_INIT;

			// Paid Flag 0=false, 1=true
			$affiliate['paid_flag'] = self::DB_FALSE;

			// Processed By (Admin ID)
			$affiliate['processed_by'] = $admin_id;

			// Notes
			$affiliate['note'] = "calc year week " . $yearweek . " at " . $datetime;

			// Manual Flag 0=false, 1=true
			$affiliate['manual_flag'] = self::DB_FALSE;

			// GET TOTAL AMOUNT FOR RECORD FOR CONTINGENCY
			$affiliate['amount'] = 0;

			// Store to Affiliate to Data
			// if ($affiliate['net'] != 0 || $affiliate['amount'] != 0) {
			$this->db->from('monthly_earnings')->where('affiliate_id', $id)->where('year_week', $yearweek);
			// $result = $this->db->get('monthly_earnings');
			$row=$this->runOneRowArray();

			if (!empty($row)) {
				$earningId=$row['id'];
				$this->db->where('id', $earningId)->set($affiliate);
				$this->runAnyUpdate('monthly_earnings');

				$this->utils->debug_log('update weekly earning', $earningId , 'affiliate', $affiliate['affiliate_id']);
				// $this->db->where('affiliate_id', $id);
				// $this->db->where('year_week', $yearmonth);
				// $this->db->update('monthly_earnings', $affiliate);
			} else {
				$earningId=$this->insertData('monthly_earnings', $affiliate);
				$this->utils->debug_log('insert weekly earning', $earningId , 'affiliate', $affiliate['affiliate_id']);
			}

		}
		return $rlt;
	}

	# WEEKLY EARNINGS SUB LEVEL -----------------------------------------------

	public function generate_weekly_earnings_sublevel($start_date, $end_date) {

		$yearweek = date('YW', strtotime($start_date));

		$this->load->model(array('affiliatemodel', 'player_model', 'external_system'));

		# GET ALL AFFILIATE'S WITH NET INCOME

		$affiliates = $this->get_all_weekly_earnings($yearweek);

		//should load all terms
		$this->utils->debug_log('get_all_weekly_earnings yearweek', $yearweek, 'affiliates', count($affiliates));

		if (!empty($affiliates)) {

			$terms=$this->affiliatemodel->getAllAffiliateTerm();

			foreach ($affiliates as $aff) {
				$debugger = array();


				# SET VALUES
				$id = $aff['affiliate_id'];
				$data = array();
				$data['amount'] = 0;
				//get current affiliate
				$affTermsSettings=$terms[$id];

				# CALCULATE MASTER
				$net = $aff['net'];
				$rate = $affTermsSettings['level_master'];
				$rate_percent = $rate / 100;
				$data['rate_for_affiliate'] = $rate;
				//FIXME rate by game

				$amount = $net * $rate_percent;
				$data['amount'] += $amount;
				$income_json[0]=['amount'=>$amount, 'net'=>$net, 'percent'=>$rate_percent];

				// $debugger[] = array('level' => 'master', 'id' => $id, 'rate' => $rate_percent, 'net' => $net, 'amount' => $data['amount']);
				$this->utils->debug_log('level master: '.$id, 'rate', $rate_percent, 'net', $net, 'amount of income', $data['amount']);

				# CALCULATE SUBLEVEL
				$max_level = $affTermsSettings['sub_level']; // $sublevel_settings->sub_level;
				$level = 1;
				$rates = $affTermsSettings['sub_levels']; // explode(',', $sublevel_settings->sub_levels);
				$rate = $rates[$level - 1];
				$sub_ids = $this->getSubAffiliatesIds(array($id));

				while ($level < $max_level && !empty($sub_ids)) {

					$this->utils->debug_log('sub level', count($sub_ids), 'aff id', $id );

					if($rate > 0){

						$sub_rate = $rate / 100;
						//sum sub net
						$sub_net = $this->get_weekly_earnings_net_by_id($sub_ids, $yearweek);
						$sub_amount = $sub_net * $sub_rate;
						//bonus from sub
						$data['amount'] += $sub_amount;

						$income_json[$level]= ['amount'=>$sub_amount, 'net'=>$sub_net, 'percent'=>$sub_rate];

						$this->utils->debug_log('level '.$level.': '.$id, 'sub_rate', $rate_percent, 'sub_net', $sub_net,'amount of bonus', $sub_amount, 'amount of income', $data['amount']);

						// $debugger[] = array('level' => $level, 'id' => $sub_ids, 'rate' => $sub_rate, 'net' => $sub_net, 'amount' => $data['amount']);
					}else{

						$income_json[$level]= ['amount'=>0, 'net'=>0, 'percent'=>0];

						$this->utils->debug_log('ignore level '.$level.': '.$id, 'rate', $rate_percent, 'net', $net);
					}

					$sub_ids = $this->getSubAffiliatesIds($sub_ids);
					$level++;

					if (isset($rates[$level - 1]) && $rates[$level - 1] > 0) {
						$rate = $rates[$level - 1];
					} else {
						$rate = 0;
					}


				}

				# GET PREVIOUS BALANCE
				// $previous_balance = 0;
				// $previous_amount = $this->getPreviousEarningsById($id, $yearweek);
				// if ($previous_amount) {
				// 	$previous_balance = $previous_amount->balance;
				// }

				$previous_balance=$this->sum_unpaid_amount_before_week($id, $yearweek);

				// $data['amount'] += $previous_balance;
				$data['amount'] = $this->utils->roundCurrencyForShow($data['amount']);
				//sum unpaid amount
				$data['balance'] = $this->utils->roundCurrencyForShow($data['amount'] + $previous_balance);
				$data['income_json']=$this->utils->encodeJson($income_json);

				// var_dump($debugger);

				$this->db->where('affiliate_id', $id);
				$this->db->where('year_week', $yearweek);
				$this->db->set($data);
				$success=$this->runAnyUpdate('monthly_earnings');
				// $this->db->update('monthly_earnings', $data);
				$this->utils->debug_log('update aff:'.$id, $yearweek, 'amount', $data['amount'], 'balance', $data['balance'] , $success);
			}
		}
	}



	# START MONTHLY EARNINGS -------------------------------------------------

	public function generate_monthly_earnings($yearmonth = null) {
		# INIT DATE
		if (empty($yearmonth)) {
			// $start_date = date('Y-m-01');
			// $end_date = date('Y-m-t');
			$yearmonth = $this->utils->getLastYearMonth();
			// } else {
			// $start_date = date('Y-m-01', strtotime($yearmonth));
			// $end_date = date('Y-m-t', strtotime($yearmonth));
			// $yearmonth = $yearmonth;
		}
		$this->utils->debug_log('yearmonth', $yearmonth);

		//should load all affiliates with terms
		$this->load->model(array('affiliatemodel'));
		$terms=$this->affiliatemodel->getAllAffiliateTerm();

		# CALCULATE ALL NET EARNINGS STEP 1-3
		$this->generate_monthly_earnings_master($yearmonth);

		# CALCULATE ALL AMOUNT EARNINGS STEP 4
		$this->generate_monthly_earnings_sublevel($yearmonth);

		//transfer to wallet
		$defaultAllAffiliateSettings =$terms[-1];// $this->affiliatemodel->getDefaultAllAffiliateSettings();
		$autoTransferToWallet=isset($defaultAllAffiliateSettings['autoTransferToWallet']) ? $defaultAllAffiliateSettings['autoTransferToWallet'] : false ;
		$this->utils->debug_log('default_affiliate_settings', $defaultAllAffiliateSettings, 'autoTransferToWallet', $autoTransferToWallet);

		if ($autoTransferToWallet) {
			$this->utils->debug_log('transfer to main wallet', $autoTransferToWallet);
			$this->transferAllEarningsToWallet($yearmonth, $defaultAllAffiliateSettings['minimumPayAmount']);
		}
		$autoTransferToLockedWallet=isset($defaultAllAffiliateSettings['autoTransferToLockedWallet']) ? $defaultAllAffiliateSettings['autoTransferToLockedWallet'] : false ;
		if ($autoTransferToLockedWallet) {
			$this->utils->debug_log('transfer to locked wallet', $autoTransferToLockedWallet);
			$this->transferAllEarningsToLockedWallet($yearmonth, $defaultAllAffiliateSettings['minimumPayAmount']);
		}

		return true;
	}

	# MONTHLY EARNINGS MASTER ------------------------------------------------

	public function generate_monthly_earnings_master($yearmonth) {
		$rlt = true;
		list($start_date, $end_date) = $this->utils->getMonthRange($yearmonth);
		$this->utils->debug_log('create month yearmonth', $yearmonth, 'start_date', $start_date, 'end_date', $end_date);

		//Admin ID
		$this->load->model(array('affiliatemodel', 'player_model', 'external_system', 'users'));
		// $this->load->model(array('users'));
		$admin_id = $this->users->getSuperAdminId();

		# GET ALL AFFILIATES

		$affiliates = $this->affiliatemodel->getAllActivtedAffiliates();
		// $this->utils->debug_log('affiliates', $affiliates);
		# AFFILIATES LOOP

		// Init Data
		$data = array();

		// var_dump($affiliates); // debug: get all affiliates
		$terms=$this->affiliatemodel->getAllAffiliateTerm();

		$datetime = $this->utils->getNowForMysql();
		foreach ($affiliates as $a) {
			//get settings : getAffTermsSettings
			$id = $a['affiliateId'];
			$affTermsSettings=$terms[$id];
			$baseIncomeConfig=$affTermsSettings['baseIncomeConfig'];
			$admin_fee=$affTermsSettings['admin_fee'];
			$transaction_fee=$affTermsSettings['transaction_fee'];
			$bonus_fee=$affTermsSettings['bonus_fee'];
			$cashback_fee=$affTermsSettings['cashback_fee'];
			$gameProviderArr=$affTermsSettings['provider'];
			//remove useless
			$provider=$this->utils->filterActiveGameApi($gameProviderArr);

			//load term info
			// $this->utils->debug_log('load term for '.$id, $affTermsSettings);

			# INIT VARS
			$players_id = $this->affiliatemodel->getAllPlayersUnderAffiliateId($id);

			$this->utils->debug_log('affiliate', $id, 'affiliate_terms', $affTermsSettings, 'players_id', count($players_id));

			// OVERIDE PLAYERS_ID WITH ACTIVE PLAYERS ONLY
			$players_id = $this->affiliatemodel->filterActivePlayersById($affTermsSettings, $players_id, $start_date, $end_date, 'day');
			$this->utils->debug_log('get available players', count($players_id));

			// OVERIDE PLAYERS_ID WITH GAME PROVIDER CONDITION
			if (!empty($provider)) {
				$this->utils->debug_log('filter game provider available players', $provider);
				$count = $affTermsSettings['totalactiveplayer'];
				// echo 'count: ' . $count; //debug: filter player by game provider condition (active_player)
				$providers_id = $provider;
				// var_dump($providers_id); //debug: filter player by game provider condition (active_player)
				$players_id = $this->affiliatemodel->filterActivePlayersByIdByProvider($affTermsSettings, $players_id, $start_date, $end_date, $providers_id, $count);
			}

			$this->utils->debug_log('after filter game provider players_id', count($players_id));
			// var_dump($players_id); //debug: filter player by game provider condition (active_player)

			# STORE PRIMARY AFFILIATE DATA

			$affiliate = array();
			$affiliate['affiliate_id'] = $id;
			$affiliate['count_active_player']=count($players_id);

			# STEP 1:

			// GET GROSS NET
			$affiliate['gross_net'] = 0;
			switch ($baseIncomeConfig) {
			// BY BET - WIN
			case Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN:
				if (count($players_id) > 0) {
					// var_dump($players_id); // debug: get gross income per affiliate by Loss-Win Condition
					$affiliate['gross_net'] = $this->utils->roundCurrencyForShow($this->get_loss_win_income_by_game_rate($players_id, $start_date, $end_date));
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
					$affiliate['gross_net'] = $this->utils->roundCurrencyForShow($this->get_deposit_withdraw_income($players_id, $start_date, $end_date));
					// $gross_net = $affiliate['gross_net'];
					// var_dump($gross_net); // debug: get gross income per affiliate by Deposit-Withdraw Condition
				} else {
					$affiliate['gross_net'] = 0;
				}
				break;
			}
			$this->utils->debug_log('use base income '.$baseIncomeConfig.' affiliate ', $affiliate);

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

					$this->utils->debug_log('generate bonus fee = bonus * percent');
				}

				if ($cashback_fee > 0) {
					// GET CASHBACK FEE PERCENT
					$cashback_percent = $cashback_fee / 100;
					// var_dump($cashback_percent); // debug: get active_player's cashback fee

					// GET CASHBACK FEE
					$affiliate['cashback'] = $this->utils->roundCurrencyForShow($this->player_model->getPlayersTotalCashback($players_id, $start_date, $end_date) * $cashback_percent);
					// var_dump($affiliate['cashback']); // debug: get active_player's cashback fee
					$total_fee = $total_fee + $affiliate['cashback'];

					$this->utils->debug_log('generate cashback fee = cashback * percent');
				}

				if ($transaction_fee > 0) {
					//should be (deposit+withdraw) * percent
					// GET TRANSACTION FEE
					$transaction_percent = $transaction_fee / 100;
					// get from transactions
					// $affiliate['transaction_fee'] = $this->utils->roundCurrencyForShow($this->transactions->sumDepositAndWithdraw($players_id, $start_date, $end_date) * $transaction_percent);

					$affiliate['transaction_fee'] = $this->utils->roundCurrencyForShow($this->transactions->sumTransactionFee($players_id, $start_date, $end_date) * $transaction_percent);
					// var_dump($affiliate['transaction_fee']); // debug: get active_player's transaction fee
					$total_fee = $total_fee + $affiliate['transaction_fee'];

					$this->utils->debug_log('generate transaction fee = (transaction fee) * percent');

					//or sum transaction fee
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

					$this->utils->debug_log('generate admin fee = gross_net * percent');
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
			$affiliate['note'] = "calc year month " . $yearmonth . " at " . $datetime;

			// Manual Flag 0=false, 1=true
			$affiliate['manual_flag'] = self::DB_FALSE;

			// GET TOTAL AMOUNT FOR RECORD FOR CONTINGENCY
			$affiliate['amount'] = 0;

			// Store to Affiliate to Data
			// if ($affiliate['net'] != 0 || $affiliate['amount'] != 0) {
			$this->db->from('monthly_earnings')->where('affiliate_id', $id)->where('year_month', $yearmonth);
			// $result = $this->db->get('monthly_earnings');
			$row=$this->runOneRowArray();

			if (!empty($row)) {
				$earningId=$row['id'];
				$this->db->where('id', $earningId)->set($affiliate);
				$this->runAnyUpdate('monthly_earnings');

				$this->utils->debug_log('update monthly earning', $earningId , 'affiliate', $affiliate['affiliate_id']);
				// $this->db->where('affiliate_id', $id);
				// $this->db->where('year_month', $yearmonth);
				// $this->db->update('monthly_earnings', $affiliate);
			} else {
				$earningId=$this->insertData('monthly_earnings', $affiliate);
				$this->utils->debug_log('insert monthly earning', $earningId , 'affiliate', $affiliate['affiliate_id']);
			}

		}
		return $rlt;
	}

	# MONTHLY EARNINGS SUB LEVEL -----------------------------------------------

	public function generate_monthly_earnings_sublevel($yearmonth) {
		$this->load->model(array('affiliatemodel', 'player_model', 'external_system'));

		# GET ALL AFFILIATE'S WITH NET INCOME

		$affiliates = $this->get_all_monthly_earnings($yearmonth);

		//should load all terms
		$this->utils->debug_log('get_all_monthly_earnings yearmonth', $yearmonth, 'affiliates', count($affiliates));

		if (!empty($affiliates)) {

			$terms=$this->affiliatemodel->getAllAffiliateTerm();

			foreach ($affiliates as $aff) {
				$debugger = array();


				# SET VALUES
				$id = $aff['affiliate_id'];
				$data = array();
				$data['amount'] = 0;
				//get current affiliate
				$affTermsSettings=$terms[$id];

				# CALCULATE MASTER
				$net = $aff['net'];
				$rate = $affTermsSettings['level_master'];
				$rate_percent = $rate / 100;
				$data['rate_for_affiliate'] = $rate;
				//FIXME rate by game

				$amount = $net * $rate_percent;
				$data['amount'] += $amount;
				$income_json[0]=['amount'=>$amount, 'net'=>$net, 'percent'=>$rate_percent];

				// $debugger[] = array('level' => 'master', 'id' => $id, 'rate' => $rate_percent, 'net' => $net, 'amount' => $data['amount']);
				$this->utils->debug_log('level master: '.$id, 'rate', $rate_percent, 'net', $net, 'amount of income', $data['amount']);

				# CALCULATE SUBLEVEL
				$max_level = $affTermsSettings['sub_level']; // $sublevel_settings->sub_level;
				$level = 1;
				$rates = $affTermsSettings['sub_levels']; // explode(',', $sublevel_settings->sub_levels);
				$rate = $rates[$level - 1];
				$sub_ids = $this->getSubAffiliatesIds(array($id));

				while ($level < $max_level && !empty($sub_ids)) {

					$this->utils->debug_log('sub level', count($sub_ids), 'aff id', $id );

					if($rate > 0){

						$sub_rate = $rate / 100;
						//sum sub net
						$sub_net = $this->get_monthly_earnings_net_by_id($sub_ids, $yearmonth);
						$sub_amount = $sub_net * $sub_rate;
						//bonus from sub
						$data['amount'] += $sub_amount;

						$income_json[$level]= ['amount'=>$sub_amount, 'net'=>$sub_net, 'percent'=>$sub_rate];

						$this->utils->debug_log('level '.$level.': '.$id, 'sub_rate', $rate_percent, 'sub_net', $sub_net,'amount of bonus', $sub_amount, 'amount of income', $data['amount']);

						// $debugger[] = array('level' => $level, 'id' => $sub_ids, 'rate' => $sub_rate, 'net' => $sub_net, 'amount' => $data['amount']);
					}else{

						$income_json[$level]= ['amount'=>0, 'net'=>0, 'percent'=>0];

						$this->utils->debug_log('ignore level '.$level.': '.$id, 'rate', $rate_percent, 'net', $net);
					}

					$sub_ids = $this->getSubAffiliatesIds($sub_ids);
					$level++;

					if (isset($rates[$level - 1]) && $rates[$level - 1] > 0) {
						$rate = $rates[$level - 1];
					} else {
						$rate = 0;
					}


				}

				# GET PREVIOUS BALANCE
				// $previous_balance = 0;
				// $previous_amount = $this->getPreviousEarningsById($id, $yearmonth);
				// if ($previous_amount) {
				// 	$previous_balance = $previous_amount->balance;
				// }

				$previous_balance=$this->sum_unpaid_amount_before_month($id, $yearmonth);

				// $data['amount'] += $previous_balance;
				$data['amount'] = $this->utils->roundCurrencyForShow($data['amount']);
				//sum unpaid amount
				$data['balance'] = $this->utils->roundCurrencyForShow($data['amount'] + $previous_balance);
				$data['income_json']=$this->utils->encodeJson($income_json);

				// var_dump($debugger);

				$this->db->where('affiliate_id', $id);
				$this->db->where('year_month', $yearmonth);
				$this->db->set($data);
				$success=$this->runAnyUpdate('monthly_earnings');
				// $this->db->update('monthly_earnings', $data);
				$this->utils->debug_log('update aff:'.$id, $yearmonth, 'amount', $data['amount'], 'balance', $data['balance'] , $success);
			}
		}
	}

	# OTHERS

	public function sum_unpaid_amount_before_month($affId, $yearmonth){
		$this->db->select_sum('amount')->from('monthly_earnings')->where('affiliate_id', $affId)->where('year_month <', $yearmonth);
		$this->db->where('paid_flag', self::DB_FALSE); // this will reset to zero if paid

		return $this->runOneRowOneField('amount');
	}

	public function sum_unpaid_amount_before_week($affId, $yearweek){
		$this->db->select_sum('amount')->from('monthly_earnings')->where('affiliate_id', $affId)->where('year_week <', $yearweek);
		$this->db->where('paid_flag', self::DB_FALSE); // this will reset to zero if paid
		return $this->runOneRowOneField('amount');
	}

	public function get_all_monthly_earnings($year_month) {

		$this->db->from('monthly_earnings')->where('year_month', $year_month);

		return $this->runMultipleRowArray();

		// $this->load->model(array('affiliatemodel', 'player_model', 'external_system'));
		// $this->db->select('m1.*, affiliates.username, affiliates.firstname, affiliates.lastname, affiliates.parentId, affiliates.status');
		// if (!empty($year_month)) {
		// 	$this->db->where('m1.year_month', $year_month);
		// }

		// if (!empty($username)) {
		// 	$this->db->where('affiliates.username', $username);
		// }

		// if (!empty($parentId)) {
		// 	$this->db->where('affiliates.parentId', $parentId);
		// }

		// if ($paid_flag != null) {
		// 	$this->db->where('m1.paid_flag', $paid_flag);
		// }

		// if ($summary) {
		// 	$this->db->where('m2.id IS NULL');
		// }

		// $this->db->from('monthly_earnings AS m1');
		// if ($summary) {
		// 	$this->db->join('monthly_earnings AS m2', 'm1.affiliate_id = m2.affiliate_id AND m1.id < m2.id', 'left');
		// }

		// $this->db->join('affiliates', 'm1.affiliate_id = affiliates.affiliateId');

		// return $this->runMultipleRowArray();

		// $this->utils->printLastSQL();

		// if (!empty($rows)) {
			// $defaultAllAffiliateSettings = $this->affiliatemodel->getDefaultAllAffiliateSettings();

			// $terms=$this->affiliatemodel->getAllAffiliateTerm();

			// $data = array();
			// foreach ($rows as $e) {
				// $affiliate_term = $terms[$e->affiliate_id];

				// $players_id = array();
				// $players_id = $this->affiliatemodel->getAllPlayersUnderAffiliateId($e->affiliate_id);
				// $e->count_players = count($players_id);
				//get month from/to
				// list($start_date, $end_date) = $this->utils->getMonthRange($e->year_month);
				//get active player
				// $e->active_players = $this->affiliatemodel->filterActivePlayersById($affiliate_term, $players_id, $start_date, $end_date);
				// $e->active_players = array();
				// $e->sub_affiliates = count($this->affiliatemodel->getAllAffiliatesUnderAffiliate($e->affiliate_id));
				// $e->balance = $this->utils->roundCurrencyForShow($e->balance);
				// $e->amount = $this->utils->roundCurrencyForShow($e->amount);
				// $data[] = $e;
			// }

			// return $data;
		// }
		// return null;
	}

	public function get_all_weekly_earnings($year_week) {
		$this->db->from('monthly_earnings')->where('year_week', $year_week);
		return $this->runMultipleRowArray();
	}

	public function get_monthly_earnings_net_by_id($ids, $yearmonth) {
		$this->db->select('SUM(net) as net')->from('monthly_earnings');
		$this->db->where_in('affiliate_id', $ids);
		$this->db->where('year_month', $yearmonth)->limit(1);

		return $this->runOneRowOneField('net');
		// $result = $this->db->get('monthly_earnings');

		// if ($result->num_rows() > 0) {
			// return $result->result()[0]->net;
		// } else {
			// return 0;
		// }
	}

	public function get_weekly_earnings_net_by_id($ids, $yearweek) {
		$this->db->select('SUM(net) as net')->from('monthly_earnings');
		$this->db->where_in('affiliate_id', $ids);
		$this->db->where('year_week', $yearweek)->limit(1);
		return $this->runOneRowOneField('net');
	}

	public function get_loss_win_income_by_game_rate($players_id, $start_date, $end_date) {

		$info = array();

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
			if (!empty($players_id)) {
				if ($use_total_hour) {
					list($bet, $win, $loss) = $this->total_player_game_hour->getTotalBetsWinsLossByPlayers(
						$players_id, $start_date, $end_date, $g['id']);
				} else {
					list($bet, $win, $loss) = $this->game_logs->getTotalBetsWinsLossByPlayers(
						$players_id, $start_date, $end_date, $g['id']);
				}

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

			$info[] = array(
				'id' 	 	 => $g['id'],
				'players_id' => $players_id,
				'rate' 	 	 => $g['game_platform_rate'],
				'bet' 	 	 => $bet,
				'win' 	 	 => $win,
				'loss' 	 	 => $loss,
				'result' 	 => $sum,
				'amount' 	 => $subtotal,
			);

			# ADD SUBTOTAL INTO TOTAL
			$total += $subtotal;
		}

		$this->utils->debug_log('get_loss_win_income_by_game_rate', $info);

		return $total;
	}

	public function get_deposit_withdraw_income($players_id, $start_date, $end_date) {
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

}