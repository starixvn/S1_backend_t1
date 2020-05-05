<?php
/**
 * General behaviors include
 * * get super player reports
 *
 * @category super_player_report
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait super_report_module {
	
	/**
	 * detail: get super player reports
	 *
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function super_player_report($request, $is_export = false) {


		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->helper(['player_helper']);

		$currency_list_super_report = $this->utils->getConfig('currency_list_super_report');

		# START DEFINE COLUMNS #################################################################################################################################################
		$input = $this->data_tables->extra_search($request);

		$i = 0;
		$columns = array();
			$columns[] =  array(
				'select' => 'id',
				'alias' => 'data_id',
				'name' => lang('lang.action'),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'currency',
				'select' => 'currency',
				'name' => lang('Currency'),
				'formatter' => function ($d) use ($is_export,$currency_list_super_report) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						$labelColor =isset($currency_list_super_report[$d]['label-color'])? $currency_list_super_report[$d]['label-color'] : 'label-success';
						return !empty($d) ? '<i class="label '.$labelColor.'">'.$d.'</i>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'player_username',
				'select' => 'player_username',
				'name' => lang("report.pr01"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'player_level',
				'select' => 'player_level',
				'name' => lang("report.pr03"),
				'formatter' => function ($d) use ($is_export) {
					$d = (explode("|",$d));
					if ($is_export) {
						return !empty($d) ? lang($d[0]).' - '.lang($d[1]) : lang('lang.norecyet');
					} else {
						return !empty($d) ? lang($d[0]).' - '.lang($d[1]) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'registered_by',
				'select' => 'registered_by',
				'name' => lang("report.pr05"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_deposit_bonus',
				'select' => 'total_deposit_bonus',
				'name' => lang("report.pr15"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},

			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_cashback_bonus',
				'select' => 'total_cashback_bonus',
				'name' => lang("report.pr16"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_referral_bonus',
				'select' => 'total_referral_bonus',
				'name' => lang("report.pr17"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_manual_bonus',
				'select' => 'total_manual_bonus',
				'name' => lang("report.pr17"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},

			);
			$columns[] = array(
                'dt' => $i++,
                'alias' => 'total_first_deposit',
                'select' => 'total_first_deposit' ,
                'name' => lang("report.pr19"),
                'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
            );
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_second_deposit',
				'select' => 'total_second_deposit',
				'name' => lang("report.pr20"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_deposit',
				'select' => 'total_deposit',
				'name' => lang("report.pr21"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_withdraw',
				'select' => 'total_withdraw',
				'name' => lang('report.pr22'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			
			# END DEFINE COLUMNS #################################################################################################################################################

			$this->benchmark->mark('data_gathering_start');
			$table = 'super_player_report';
			$joins = array();
			# START PROCESS SEARCH FORM #################################################################################################################################################
			$where = array();
			$values = array();
			if (isset($input['username'])) {
				$username = $input['username'];
				if(!empty($username)){
					$where[] = "player_username = ?";
					$values[] = $username;
				}			
			}

			$date_from = null;
			$date_to = null;
			if (isset($input['date_from'], $input['date_to'])) {
				$date_from = $input['date_from'];
				$date_to = $input['date_to'];
			}

			if (!empty($date_from) && !empty($date_to)) {
				$where[] = "report_date BETWEEN ? AND ?";
				$values[] = $date_from;
				$values[] = $date_to;
			}

			if (isset($input['currency'])) {
				$currency = $input['currency'];
				if(!empty($currency)){
					$where[] = "currency = ?";
					$values[] = $currency;
				}
			}
			# END PROCESS SEARCH FORM #################################################################################################################################################
			if($is_export){
            	$this->data_tables->options['is_export']=true;
	            if(empty($csv_filename)){
	                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
	            }
	            $this->data_tables->options['csv_filename']=$csv_filename;
			}

			$group_by=[];
			$having=[];
			$distinct=true;
			$external_order=[];
			$not_datatable='';
			$countOnlyField='super_player_report.id';
			
			$this->benchmark->mark('data_gathering_end');

			$this->benchmark->mark('player_super_reports_result_start');
			
			$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins,
						$group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);

			$this->benchmark->mark('player_super_reports_result_end');

			$this->utils->debug_log(array(
			'player_super_reports data gathering================================' => $this->benchmark->elapsed_time('data_gathering_start', 'data_gathering_end'),
		 	'player_super_reports result=============================' => $this->benchmark->elapsed_time('player_super_reports_result_start', 'player_super_reports_result_end'),
		));
		return $result;
	}

	/**
	 * detail: get super player reports
	 *
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function super_summary_report($request, $is_export = false) {


		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->helper(['player_helper']);

		$currency_list_super_report = $this->utils->getConfig('currency_list_super_report');

		# START DEFINE COLUMNS #################################################################################################################################################
		$input = $this->data_tables->extra_search($request);

		$i = 0;
		$columns = array();
			$columns[] =  array(
				'select' => 'id',
				'alias' => 'data_id',
				'name' => lang('lang.action'),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'currency',
				'select' => 'currency',
				'name' => lang('Currency'),
				'formatter' => function ($d) use ($is_export,$currency_list_super_report) {
					
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						$labelColor =isset($currency_list_super_report[$d]['label-color'])? $currency_list_super_report[$d]['label-color'] : 'label-success';
						return !empty($d) ? '<i class="label '.$labelColor.'">'.$d.'</i>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'report_date',
				'select' => 'report_date',
				'name' => lang("report.sum02"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'new_players',
				'select' => 'new_players',
				'name' => lang("report.sum05"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_players',
				'select' => 'total_players',
				'name' => lang("report.sum06"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'first_deposit_count',
				'select' => 'first_deposit_count',
				'name' => lang("report.sum07"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},

			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'second_deposit_count',
				'select' => 'second_deposit_count',
				'name' => lang("report.sum08"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_deposit',
				'select' => 'total_deposit',
				'name' => lang("report.sum09"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_withdraw',
				'select' => 'total_withdraw',
				'name' => lang("report.sum10"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},

			);
			$columns[] = array(
                'dt' => $i++,
                'alias' => 'total_bonus',
                'select' => 'total_bonus' ,
                'name' => lang("report.sum14"),
                'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
            );
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_cashback',
				'select' => 'total_cashback',
				'name' => lang("report.sum15"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_fee',
				'select' => 'total_fee',
				'name' => lang("report.sum16"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_bank_cash_amount',
				'select' => 'total_bank_cash_amount',
				'name' => lang('report.sum18'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_bet',
				'select' => 'total_bet',
				'name' => lang('Total Bet'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_win',
				'select' => 'total_win',
				'name' => lang('Total Win'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_loss',
				'select' => 'total_loss',
				'name' => lang('Total Loss'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'gross_payout',
				'select' => 'gross_payout',
				'name' => lang('Gross Payout'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			# END DEFINE COLUMNS #################################################################################################################################################

			$this->benchmark->mark('data_gathering_start');
			$table = 'super_summary_report';
			$joins = array();
			# START PROCESS SEARCH FORM #################################################################################################################################################
			$where = array();
			$values = array();

			$date_from = null;
			$date_to = null;
			if (isset($input['date_from'], $input['date_to'])) {
				$date_from = $input['date_from'];
				$date_to = $input['date_to'];
			}

			if (!empty($date_from) && !empty($date_to)) {
				$where[] = "report_date BETWEEN ? AND ?";
				$values[] = $date_from;
				$values[] = $date_to;
			}

			if (isset($input['currency'])) {
				$currency = $input['currency'];
				if(!empty($currency)){
					$where[] = "currency = ?";
					$values[] = $currency;
				}
			}
			# END PROCESS SEARCH FORM #################################################################################################################################################
			if($is_export){
            	$this->data_tables->options['is_export']=true;
	            if(empty($csv_filename)){
	                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
	            }
	            $this->data_tables->options['csv_filename']=$csv_filename;
			}

			$group_by=[];
			$having=[];
			$distinct=true;
			$external_order=[];
			$not_datatable='';
			$countOnlyField='super_summary_report.id';
			
			$this->benchmark->mark('data_gathering_end');

			$this->benchmark->mark('super_summary_report_result_start');
			
			$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins,
						$group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);

			$this->benchmark->mark('super_summary_report_result_end');

			$this->utils->debug_log(array(
			'super_summary_report data gathering================================' => $this->benchmark->elapsed_time('data_gathering_start', 'data_gathering_end'),
		 	'super_summary_report result=============================' => $this->benchmark->elapsed_time('super_summary_report_result_start', 'super_summary_report_result_end'),
		));
		return $result;
	}

	/**
	 * detail: get super game reports
	 *
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function super_game_report($request, $is_export = false) {


		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->helper(['player_helper']);

		$currency_list_super_report = $this->utils->getConfig('currency_list_super_report');

		# START DEFINE COLUMNS #################################################################################################################################################
		$input = $this->data_tables->extra_search($request);

		$i = 0;
		$columns = array();
			$columns[] =  array(
				'select' => 'id',
				'alias' => 'data_id',
				'name' => lang('lang.action'),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'currency',
				'select' => 'currency',
				'name' => lang('Currency'),
				'formatter' => function ($d) use ($is_export,$currency_list_super_report) {
					
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						$labelColor =isset($currency_list_super_report[$d]['label-color'])? $currency_list_super_report[$d]['label-color'] : 'label-success';
						return !empty($d) ? '<i class="label '.$labelColor.'">'.$d.'</i>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'report_date',
				'select' => 'report_date',
				'name' => lang("report.sum02"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'game_platform_id',
				'select' => 'game_platform_id',
				'name' => lang("Game Platform"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'game_type_id',
				'select' => 'game_type_id',
				'name' => lang("Game Type"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'game_description_name',
				'select' => 'game_description_name',
				'name' => lang("Game"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? lang($d): lang('lang.norecyet');
					} else {
						return !empty($d) ? lang($d) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},

			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'player_username',
				'select' => 'player_username',
				'name' => lang("Player Username"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'affiliate_username',
				'select' => 'affiliate_username',
				'name' => lang("Affiliate Username"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_bet_amount',
				'select' => 'total_bet_amount',
				'name' => lang('Total Bet'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_win_amount',
				'select' => 'total_win_amount',
				'name' => lang('Total Win'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_loss_amount',
				'select' => 'total_loss_amount',
				'name' => lang('Total Loss'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_result_amount',
				'select' => 'total_result_amount',
				'name' => lang('cms.totalResultAmount'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			# END DEFINE COLUMNS #################################################################################################################################################

			$this->benchmark->mark('data_gathering_start');
			$table = 'super_game_report';
			$joins = array();
			# START PROCESS SEARCH FORM #################################################################################################################################################
			$where = array();
			$values = array();

			$date_from = null;
			$date_to = null;
			if (isset($input['date_from'], $input['date_to'])) {
				$date_from = $input['date_from'];
				$date_to = $input['date_to'];
			}

			if (!empty($date_from) && !empty($date_to)) {
				$where[] = "report_date BETWEEN ? AND ?";
				$values[] = $date_from;
				$values[] = $date_to;
			}

			if (isset($input['currency'])) {
				$currency = $input['currency'];
				if(!empty($currency)){
					$where[] = "currency = ?";
					$values[] = $currency;
				}
			}
			# END PROCESS SEARCH FORM #################################################################################################################################################
			if($is_export){
            	$this->data_tables->options['is_export']=true;
	            if(empty($csv_filename)){
	                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
	            }
	            $this->data_tables->options['csv_filename']=$csv_filename;
			}

			$group_by=[];
			$having=[];
			$distinct=true;
			$external_order=[];
			$not_datatable='';
			$countOnlyField='super_game_report.id';
			
			$this->benchmark->mark('data_gathering_end');

			$this->benchmark->mark('super_game_report_result_start');
			
			$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins,
						$group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);

			$this->benchmark->mark('super_game_report_result_end');

			$this->utils->debug_log(array(
			'super_game_report data gathering================================' => $this->benchmark->elapsed_time('data_gathering_start', 'data_gathering_end'),
		 	'super_game_report result=============================' => $this->benchmark->elapsed_time('super_game_report_result_start', 'super_game_report_result_end'),
		));
		return $result;
	}

	/**
	 * detail: get super payment reports
	 *
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function super_payment_report($request, $is_export = false) {


		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->helper(['player_helper']);

		$currency_list_super_report = $this->utils->getConfig('currency_list_super_report');

		# START DEFINE COLUMNS #################################################################################################################################################
		$input = $this->data_tables->extra_search($request);

		$i = 0;
		$columns = array();
			$columns[] =  array(
				'select' => 'id',
				'alias' => 'data_id',
				'name' => lang('lang.action'),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'currency',
				'select' => 'currency',
				'name' => lang('Currency'),
				'formatter' => function ($d) use ($is_export,$currency_list_super_report) {
					
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						$labelColor =isset($currency_list_super_report[$d]['label-color'])? $currency_list_super_report[$d]['label-color'] : 'label-success';
						return !empty($d) ? '<i class="label '.$labelColor.'">'.$d.'</i>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'report_date',
				'select' => 'report_date',
				'name' => lang("Date"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'player_username',
				'select' => 'player_username',
				'name' => lang("Player Username"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'player_level',
				'select' => 'player_level',
				'name' => lang("Group Level"),
				'formatter' => function ($d) use ($is_export) {
					$d = (explode("|",$d));
					if ($is_export) {
						return !empty($d) ? lang($d[0]).' - '.lang($d[1]) : lang('lang.norecyet');
					} else {
						return !empty($d) ? lang($d[0]).' - '.lang($d[1]) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'transaction_type',
				'select' => 'transaction_type',
				'name' => lang("Transaction Type"),
				'formatter' => function ($d) use ($is_export) {
					switch ($d) {
						case 1:
						case 2:
						case 3:
						case 4:
						case 5:
						case 6:
						case 7:
						case 8:
						case 9:
						case 10:
						case 11:
						case 12:
						case 13:
						case 14:
							$d = lang('transaction.transaction.type.'.$d);
							break;
						default:
							break;
					}
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'payment_account_type',
				'select' => 'payment_account_type',
				'name' => lang("Payment Type"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},

			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'amount',
				'name' => lang("Amount"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			# END DEFINE COLUMNS #################################################################################################################################################

			$this->benchmark->mark('data_gathering_start');
			$table = 'super_payment_report';
			$joins = array();
			# START PROCESS SEARCH FORM #################################################################################################################################################
			$where = array();
			$values = array();

			if (isset($input['username'])) {
				$username = $input['username'];
				if(!empty($username)){
					$where[] = "player_username = ?";
					$values[] = $username;
				}			
			}
			
			$date_from = null;
			$date_to = null;
			if (isset($input['date_from'], $input['date_to'])) {
				$date_from = $input['date_from'];
				$date_to = $input['date_to'];
			}

			if (!empty($date_from) && !empty($date_to)) {
				$where[] = "report_date BETWEEN ? AND ?";
				$values[] = $date_from;
				$values[] = $date_to;
			}

			if (isset($input['currency'])) {
				$currency = $input['currency'];
				if(!empty($currency)){
					$where[] = "currency = ?";
					$values[] = $currency;
				}
			}
			# END PROCESS SEARCH FORM #################################################################################################################################################
			if($is_export){
            	$this->data_tables->options['is_export']=true;
	            if(empty($csv_filename)){
	                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
	            }
	            $this->data_tables->options['csv_filename']=$csv_filename;
			}

			$group_by=[];
			$having=[];
			$distinct=true;
			$external_order=[];
			$not_datatable='';
			$countOnlyField='super_payment_report.id';
			
			$this->benchmark->mark('data_gathering_end');

			$this->benchmark->mark('super_payment_report_result_start');
			
			$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins,
						$group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);

			$this->benchmark->mark('super_payment_report_result_end');

			$this->utils->debug_log(array(
			'super_payment_report data gathering================================' => $this->benchmark->elapsed_time('data_gathering_start', 'data_gathering_end'),
		 	'super_payment_report result=============================' => $this->benchmark->elapsed_time('super_payment_report_result_start', 'super_payment_report_result_end'),
		));
		return $result;
	}

	/**
	 * detail: get super payment reports
	 *
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function super_promotion_report($request, $is_export = false) {


		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->helper(['player_helper']);

		$currency_list_super_report = $this->utils->getConfig('currency_list_super_report');

		# START DEFINE COLUMNS #################################################################################################################################################
		$input = $this->data_tables->extra_search($request);

		$i = 0;
		$columns = array();
			$columns[] =  array(
				'select' => 'id',
				'alias' => 'data_id',
				'name' => lang('lang.action'),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'currency',
				'select' => 'currency',
				'name' => lang('Currency'),
				'formatter' => function ($d) use ($is_export,$currency_list_super_report) {
					
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						$labelColor =isset($currency_list_super_report[$d]['label-color'])? $currency_list_super_report[$d]['label-color'] : 'label-success';
						return !empty($d) ? '<i class="label '.$labelColor.'">'.$d.'</i>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'report_date',
				'select' => 'report_date',
				'name' => lang("aff.ap07"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'player_username',
				'select' => 'player_username',
				'name' => lang("Player Username"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'player_level',
				'select' => 'player_level',
				'name' => lang("Group Level"),
				'formatter' => function ($d) use ($is_export) {
					$d = (explode("|",$d));
					if ($is_export) {
						return !empty($d) ? lang($d[0]).' - '.lang($d[1]) : lang('lang.norecyet');
					} else {
						return !empty($d) ? lang($d[0]).' - '.lang($d[1]) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'promorule_name',
				'select' => 'promorule_name',
				'name' => lang("Promo Rule"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'promorule_status',
				'select' => 'promorule_status',
				'name' => lang("Promotion Status"),
				'formatter' => function ($d) use ($is_export) {
					switch ($d) {
						case 0:
							$d = lang('Pending');
						case 1:
							$d = lang('Approved');
						case 3:
							$d = lang('Declined');
						case 9:
							$d = lang('Finished');
							break;
						default:
							break;
					}
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},

			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'amount',
				'name' => lang("Amount"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			# END DEFINE COLUMNS #################################################################################################################################################

			$this->benchmark->mark('data_gathering_start');
			$table = 'super_promotion_report';
			$joins = array();
			# START PROCESS SEARCH FORM #################################################################################################################################################
			$where = array();
			$values = array();

			if (isset($input['username'])) {
				$username = $input['username'];
				if(!empty($username)){
					$where[] = "player_username = ?";
					$values[] = $username;
				}			
			}
			
			$date_from = null;
			$date_to = null;
			if (isset($input['date_from'], $input['date_to'])) {
				$date_from = $input['date_from'];
				$date_to = $input['date_to'];
			}

			if (!empty($date_from) && !empty($date_to)) {
				$where[] = "report_date BETWEEN ? AND ?";
				$values[] = $date_from;
				$values[] = $date_to;
			}

			if (isset($input['currency'])) {
				$currency = $input['currency'];
				if(!empty($currency)){
					$where[] = "currency = ?";
					$values[] = $currency;
				}
			}
			# END PROCESS SEARCH FORM #################################################################################################################################################
			if($is_export){
            	$this->data_tables->options['is_export']=true;
	            if(empty($csv_filename)){
	                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
	            }
	            $this->data_tables->options['csv_filename']=$csv_filename;
			}

			$group_by=[];
			$having=[];
			$distinct=true;
			$external_order=[];
			$not_datatable='';
			$countOnlyField='super_promotion_report.id';
			
			$this->benchmark->mark('data_gathering_end');

			$this->benchmark->mark('super_promotion_report_result_start');
			
			$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins,
						$group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);

			$this->benchmark->mark('super_promotion_report_result_end');

			$this->utils->debug_log(array(
			'super_promotion_report data gathering================================' => $this->benchmark->elapsed_time('data_gathering_start', 'data_gathering_end'),
		 	'super_promotion_report result=============================' => $this->benchmark->elapsed_time('super_promotion_report_result_start', 'super_promotion_report_result_end'),
		));
		return $result;
	}

	/**
	 * detail: get super payment reports
	 *
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function super_cashback_report($request, $is_export = false) {


		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->helper(['player_helper']);

		$currency_list_super_report = $this->utils->getConfig('currency_list_super_report');

		# START DEFINE COLUMNS #################################################################################################################################################
		$input = $this->data_tables->extra_search($request);

		$i = 0;
		$columns = array();
			$columns[] =  array(
				'select' => 'id',
				'alias' => 'data_id',
				'name' => lang('lang.action'),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'currency',
				'select' => 'currency',
				'name' => lang('Currency'),
				'formatter' => function ($d) use ($is_export,$currency_list_super_report) {
					
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						$labelColor =isset($currency_list_super_report[$d]['label-color'])? $currency_list_super_report[$d]['label-color'] : 'label-success';
						return !empty($d) ? '<i class="label '.$labelColor.'">'.$d.'</i>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'report_date',
				'select' => 'report_date',
				'name' => lang("aff.ap07"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'player_username',
				'select' => 'player_username',
				'name' => lang("Player Username"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'player_level',
				'select' => 'player_level',
				'name' => lang("Group Level"),
				'formatter' => function ($d) use ($is_export) {
					$d = (explode("|",$d));
					if ($is_export) {
						return !empty($d) ? lang($d[0]).' - '.lang($d[1]) : lang('lang.norecyet');
					} else {
						return !empty($d) ? lang($d[0]).' - '.lang($d[1]) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'amount',
				'name' => lang("Amount"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'bet_amount',
				'select' => 'bet_amount',
				'name' => lang("Bet Amount"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'paid_flag',
				'select' => 'paid_flag',
				'name' => lang("Paid"),
				'formatter' => function ($d) use ($is_export) {
					if($d == 0) {
			            $d = lang('Not pay');
			        } else if($data == 1) {
			            $d = lang('Paid');
			        }
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'game_platform_id',
				'select' => 'game_platform_id',
				'name' => lang("Game Platform"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'game_type_name',
				'select' => 'game_type_name',
				'name' => lang("Game Type"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? lang($d) : lang('lang.norecyet');
					} else {
						return !empty($d) ? lang($d) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'game_description_name',
				'select' => 'game_description_name',
				'name' => lang("Game"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? lang($d): lang('lang.norecyet');
					} else {
						return !empty($d) ? lang($d) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'updated_at',
				'select' => 'updated_at',
				'name' => lang("Updated at"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'paid_date',
				'select' => 'paid_date',
				'name' => lang("Paid date"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'paid_amount',
				'select' => 'paid_amount',
				'name' => lang("Paid amount"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'withdraw_condition_amount',
				'select' => 'withdraw_condition_amount',
				'name' => lang("Withdraw Condition amount"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return !empty($d) ? $d: lang('lang.norecyet');
					} else {
						return !empty($d) ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			# END DEFINE COLUMNS #################################################################################################################################################

			$this->benchmark->mark('data_gathering_start');
			$table = 'super_cashback_report';
			$joins = array();
			# START PROCESS SEARCH FORM #################################################################################################################################################
			$where = array();
			$values = array();

			if (isset($input['username'])) {
				$username = $input['username'];
				if(!empty($username)){
					$where[] = "player_username = ?";
					$values[] = $username;
				}			
			}
			
			$date_from = null;
			$date_to = null;
			if (isset($input['date_from'], $input['date_to'])) {
				$date_from = $input['date_from'];
				$date_to = $input['date_to'];
			}

			if (!empty($date_from) && !empty($date_to)) {
				$where[] = "report_date BETWEEN ? AND ?";
				$values[] = $date_from;
				$values[] = $date_to;
			}

			if (isset($input['currency'])) {
				$currency = $input['currency'];
				if(!empty($currency)){
					$where[] = "currency = ?";
					$values[] = $currency;
				}
			}
			# END PROCESS SEARCH FORM #################################################################################################################################################
			if($is_export){
            	$this->data_tables->options['is_export']=true;
	            if(empty($csv_filename)){
	                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
	            }
	            $this->data_tables->options['csv_filename']=$csv_filename;
			}

			$group_by=[];
			$having=[];
			$distinct=true;
			$external_order=[];
			$not_datatable='';
			$countOnlyField='super_cashback_report.id';
			
			$this->benchmark->mark('data_gathering_end');

			$this->benchmark->mark('super_cashback_report_result_start');
			
			$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins,
						$group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);

			$this->benchmark->mark('super_cashback_report_result_end');

			$this->utils->debug_log(array(
			'super_cashback_report data gathering================================' => $this->benchmark->elapsed_time('data_gathering_start', 'data_gathering_end'),
		 	'super_cashback_report result=============================' => $this->benchmark->elapsed_time('super_cashback_report_result_start', 'super_cashback_report_result_end'),
		));
		return $result;
	}
}