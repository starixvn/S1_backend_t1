<?php
/**
 * General behaviors include
 * * get transaction details of a certain player
 * * get summary reports
 * * the sum/total of the win and loss payout
 * * get new added and total number of players
 * * get all new players
 * * get total number of players
 * * get the first and second deposit record
 * * get first/second deposit record
 * * get certain payments report
 * * get balance history of a certain player
 *
 * @category report_module_transaction
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

const APPROVED_MODAL = 1;  // 'approvedDetailsModal';
const DECLINED_MODAL = 2;  // 'declinedDetailsModal';
const REQUEST_MODAL = 3;   //'requestDetailsModal';

trait report_module_transaction {

	/**
	 * detail: get transaction details of a certain player
	 *
	 * @param int $player_id
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function transaction_details($player_id, $request, $is_export = false, $not_datatable = '', $where_status = '',$mobile = '',$csv_filename=null) {

		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('transactions', 'player_model', 'affiliatemodel', 'transaction_notes','common_category','walletaccount_notes'));
		$this->load->helper(['player_helper']);

		$this->data_tables->is_export = $is_export;

		$this->benchmark->mark('pre_processing_start');

		$common_category = $this->common_category;
		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'alias' => 'id',
				'select' => 'transactions.id',
			),
			array(
				'alias' => 'from_type',
				'select' => 'transactions.from_type',
			),
			array(
				'alias' => 'from_id',
				'select' => 'transactions.from_id',
			),
			array(
				'alias' => 'to_type',
				'select' => 'transactions.to_type',
			),
			array(
				'alias' => 'to_id',
				'select' => 'transactions.to_id',
			),
			array(
				'alias' => 'order_id',
				'select' => 'sale_orders.secure_id',
			),
			array(
				'alias' => 'walletAccountId',
				'select' => 'walletaccount.walletAccountId',
			),
			array(
				'alias' => 'status',
				'select' => 'transactions.status',
			),
			array(
				'dt' => $i++,
				'alias' => 'created_at',
				'select' => 'transactions.created_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('player.ut01'),
			),
			array(
				'dt' => $i++,
				'alias' => 'transaction_type',
				'select' => 'transactions.transaction_type',
				'formatter' => function ($d, $row) use ($is_export) {
					$val = '';
					if ($is_export) {
						$val = lang('transaction.transaction.type.' . $d) ?: lang('lang.norecyet');
					} else {
						$val = lang('transaction.transaction.type.' . $d) ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						//sub query
						// "GROUP_CONCAT(manual_subtract_balance_tag.adjust_tag_name ORDER BY transactions.id, manual_subtract_balance_tag.id SEPARATOR ';')"
						$tags=$this->transactions->getManualSubtractBalanceTagsBy($row['id']);
						if(!empty($tags)){
							foreach ($tags as $tag) {
								$val.=' <span class="label label-info">'. lang($tag['adjust_tag_name']) . '</span>';
							}
						}
					}
					return $val;
				},
				'name' => lang('player.ut02'),
			),
			array(
				'dt' => $i++,
				'alias' => 'from_username',
				'select' => '(CASE transactions.from_type WHEN 1 THEN fromUser.username WHEN 2 THEN fromPlayer.username WHEN 3 THEN fromAffiliate.username ELSE NULL END)',
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return ($d ? $d : lang('lang.norecyet'));
					} else {
						switch ($row['from_type']) {
						case Transactions::ADMIN:
							return '<i class="fa fa-user-secret" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>');
						case Transactions::PLAYER:
							return '<i class="fa fa-user" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ? '<a href="/player_management/userInformation/' . $row['from_id'] . '" target="_blank">' . $d . '</a>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>');
						case Transactions::AFFILIATE:
							return '<i class="fa fa-users" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>');
						default:
							return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}
					}
				},
				'name' => lang('player.ut03'),
			),
			array(
				'dt' => $i++,
				'alias' => 'to_username',
				'select' => '(CASE transactions.to_type WHEN 1 THEN toUser.username WHEN 2 THEN toPlayer.username WHEN 3 THEN toAffiliate.username WHEN 5 THEN to_username ELSE NULL END)',
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return ($d ? $d : lang('lang.norecyet'));
					} else {
						switch ($row['to_type']) {
						case Transactions::ADMIN:
							return '<i class="fa fa-user-secret" title="' . lang('transaction.from.to.type.' . $row['to_type']) . '"></i> ' . ($d ?: '<i>' . lang('lang.norecyet') . '</i>');
						case Transactions::PLAYER:
							return '<i class="fa fa-user" title="' . lang('transaction.from.to.type.' . $row['to_type']) . '"></i> ' . ($d ? '<a href="/player_management/userInformation/' . $row['to_id'] . '" target="_blank">' . $d . '</a>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>');
						case Transactions::AFFILIATE:
							return '<i class="fa fa-users" title="' . lang('transaction.from.to.type.' . $row['to_type']) . '"></i> ' . ($d ?: '<i>' . lang('lang.norecyet') . '</i>');
						case Transactions::LIVECHAT_ADMIN:
							return '<i class="fa fa-user" title="' . lang('transaction.from.to.type.' . $row['to_type']) . '"></i> ' . ($d ?  $d  : '<i class="text-muted">' . lang('lang.norecyet') . '</i>');
						default:
							return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}
					}
				},
				'name' => lang('player.ut04'),
			),
			array(
				'dt' => $i++,
				'alias' => 'tagName',
				'select' => $is_export ? 'toPlayerTag.tagid' : 'transactions.to_id',
				'name' => lang("player.41"),
				'formatter' => function ($d, $row) use ($is_export, $request) {
					//show tag name with duplicate rows only for export and enable_tag_column_on_transaction
					if (!$is_export || !$this->utils->isEnabledFeature('enable_tag_column_on_transaction')) {
						$input = $this->data_tables->extra_search($request);
						if (isset($input['tag_list']) ){
							if ($input['tag_list']!= ''){
								return tag_formatted($input['tag_list']);
							}
						} else {
							return player_tagged_list($row['to_id']);
						}
					} else {
						return strip_tags(tag_formatted($d));
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'round(transactions.amount,2)',//round
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return $this->utils->formatCurrencyNoSym($d);
					} else {
						return $d == 0 ? '<span class="text-muted">' . $this->utils->formatCurrencyNoSym($d) . '</span>' : '<strong>' . $this->utils->formatCurrencyNoSym($d) . '</strong>';
					}
				},
				'name' => lang('player.ut05'),
			),
			array(
				'dt' => $i++,
				'alias' => 'before_balance',
				'select' => 'transactions.before_balance',
				'formatter' => 'currencyFormatter',
				'name' => lang('player.ut06'),
			),
			array(
				'dt' => $i++,
				'alias' => 'after_balance',
				'select' => 'transactions.after_balance',
				'formatter' => 'currencyFormatter',
				'name' => lang('player.ut07'),
			),
			array(
				'dt' => $i++,
				'alias' => 'subwallet',
				'select' => 'external_system.system_code',
				'formatter' => 'defaultFormatter',
				'name' => lang('player.ut08'),
			),
			array(
				'dt' => $i++,
				'alias' => 'promoTypeName',
				'select' => 'promotype.promoTypeName',
				'formatter' => 'languageFormatter',
				'name' => lang('cms.promoCat'),
			),
			array(
				'dt' => $i++,
				'alias' => 'adjustment_category_id',
				'select' => 'transactions.adjustment_category_id',
				'formatter' => function ($d, $row) use ($is_export,$common_category) {
					$category_info = $common_category->getCategoryInfoById($d);
					$category_name = null;

					if(!empty($category_info)){
						if(isset($category_info['category_name'])){
							$category_name = lang($category_info['category_name']);
						}
					}

					if ($is_export) {
						return $category_name ? : lang('lang.norecyet');
					} else {
						return $category_name ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang('Adjustment Category'),
			),
			array(
				'dt' => $i++,
				'alias' => 'promo_name',
				'select' => 'promorules.promoName',
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return $d ?: lang('lang.norecyet');
					} else {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang('Promo rule name'),
			),
			array(
				'dt' => $i++,
				'alias' => 'total_before_balance',
				'select' => 'transactions.changed_balance',
				// 'formatter' => 'currencyFormatter',
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return '';
					} else {
						return '<a href="javascript:void(0)" onclick="_pubutils.showDiffBigWallet(this)" data-diffbigwallet="#bal_info_' . $row['id'] . '" class="btn btn-primary btn-xs diff_bigwallet">' . lang('Show') . '</a><div id="bal_info_' . $row['id'] . '" style="display:none">' . $d . '</div>';
					}
				},
				'name' => lang('pay.totalbal'),
			),
			array(
				'dt' => $i++,
				'alias' => 'flag',
				'select' => 'transactions.flag',
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return lang('transaction.flag.' . $d) ?: lang('lang.norecyet');
					} else {
						return lang('transaction.flag.' . $d) ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang('player.ut10'),
			),
			array(
				'dt' => $i++,
				'alias' => 'secure_id',
				'select' => 'transactions.request_secure_id',
				'name' => lang('Request ID'),
				'formatter' => function ($d, $row) use ($is_export) {
					return $d;
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'transaction_id',
				'select' => 'transactions.id',
				'formatter' => 'defaultFormatter',
				'name' => lang('player.ut11'),
			),
			array(
				'dt' => $i++,
				'alias' => 'external_transaction_id',
				'select' => 'transactions.external_transaction_id',
				'formatter' => 'defaultFormatter',
				'name' => lang('player.ut11'),
			),
			array(
				'dt' => $i++,
				'alias' => 'note',
				'select' => 'transactions.note',
				'formatter' => 'defaultFormatter',
				'name' => lang('player.ut12'),
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return strip_tags($d);
					} else {
						return $d;
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'Remarks',
				'select' => 'sale_orders.reason',
				'name' => lang('Remarks'),
				'formatter' => function ($d, $row) use ($is_export) {

						if ($row['transaction_type'] == Transactions::DEPOSIT) {
							return $d ?: lang('lang.norecyet');
						}

						$allNotes = '';
						if ($row['transaction_type'] == Transactions::WITHDRAWAL) {
							$allNotes = $this->walletaccount_notes->getNotesByNoteType(Walletaccount_notes::ACTION_LOG, $row['walletAccountId']);
						}

						if ($row['transaction_type'] == Transactions::AUTO_ADD_CASHBACK_TO_BALANCE) {
							$allNotes = $this->transaction_notes->getNotesByTransaction(Transactions::AUTO_ADD_CASHBACK_TO_BALANCE, $row['id']);
						}

						if(!empty($allNotes)){
							if($is_export){
								return str_replace(array('</br>','\n'), "\n", $allNotes);
							}
							return  nl2br($allNotes);
						}

						return lang('lang.norecyet');
				},
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'transactions';
		$joins = array(
			'adminusers fromUser' => "transactions.from_type = 1 AND fromUser.userId = transactions.from_id",
			'player fromPlayer' => "transactions.from_type = 2 AND fromPlayer.playerId = transactions.from_id",
			'affiliates fromAffiliate' => "transactions.from_type = 3 AND fromAffiliate.affiliateId = transactions.from_id",
			'adminusers toUser' => "transactions.to_type = 1 AND toUser.userId = transactions.to_id",
			'player toPlayer' => "transactions.to_type = 2 AND toPlayer.playerId = transactions.to_id",
			'affiliates toAffiliate' => "transactions.to_type = 3 AND toAffiliate.affiliateId = transactions.to_id",
			'external_system' => "external_system.id = transactions.sub_wallet_id",
			'promotype' => "promotype.promotypeId = transactions.promo_category",
			'playerpromo' => "playerpromo.playerpromoId = transactions.player_promo_id",
			'promorules' => "promorules.promorulesId = playerpromo.promorulesId",
  			'sale_orders' => 'sale_orders.transaction_id = transactions.id',
			'walletaccount' => 'walletaccount.transaction_id = transactions.id'
		);

		if ($this->utils->isEnabledFeature('enable_tag_column_on_transaction') && $is_export){
			$joins['playertag toPlayerTag'] = 'toPlayerTag.playerId = transactions.to_id';
			$joins['tag'] = 'tag.tagId = toPlayerTag.tagId';
		}


		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$group_by=[];
		$having=[];
		$distinct = false;
		$external_order=[];

		$input = $this->data_tables->extra_search($request);


		// -- Validate if date exists and user permission to load transaction data , OGP-9225
		// -- Check if CLI Exporting, if not then proceed with date validation
		if(!isset($input['CdateRangeValueStart']) && !isset($input['CdateRangeValueEnd'])){

			// -- Check if from and to exists, and Validate permission
			$this->load->library('permissions');

			// Skip permission check when Cli export
			$has_permission = $is_export || (isset($input['is_player_transaction_history']) ? $this->permissions->checkPermissions('transaction_report') : $this->permissions->checkPermissions('report_transactions')) || isset($input['is_aff']);

			if(!isset($input['dateRangeValueStart']) || !isset($input['dateRangeValueEnd']) ||  trim($input['dateRangeValueStart']) == '' || trim($input['dateRangeValueEnd']) == '' || !$has_permission) {
				$result = $this->data_tables->empty_data($request);
				$result['header_data'] = $this->data_tables->get_columns($columns);
				$result['summary'] = array();
				return $result;
			}
		}


		if( ! empty( $not_datatable ) ) $input = $request;

		if( ! empty( $where_status ) ){

			$where[] = $where_status;

		}else{

			$where[]="transactions.status = ?";
			$values[]=Transactions::APPROVED;

		}

		if (isset($input['transaction_id'])) {
			$where[] = "transactions.id = ?";
			$values[] = $input['transaction_id'];
		}

		if (isset($input['from_type'])) {
			$where[] = "transactions.from_type = ?";
			$values[] = $input['from_type'];
		}
		//tag id
		if (isset($input['tag_list'])) {
			$joins['playertag toPlayerTag'] = 'toPlayerTag.playerId = transactions.to_id';
			$joins['tag'] = 'tag.tagId = toPlayerTag.tagId';
			$where[] = "toPlayerTag.tagId = ?";
			$values[] = $input['tag_list'];
		}

		if (isset($input['fromUsernameId'])) {
			$where[] = "(CASE transactions.from_type WHEN ? THEN fromUser.userId WHEN ? THEN fromPlayer.playerId WHEN ? THEN fromAffiliate.affiliateId ELSE NULL END) = ?";
			$values[] = Transactions::ADMIN;
			$values[] = Transactions::PLAYER;
			$values[] = Transactions::AFFILIATE;
			$values[] = $input['fromUsernameId'];
		}

		if (isset($input['no_affiliate']) && $input['no_affiliate'] == true) {
			$where[] = "(fromPlayer.affiliateId IS NULL AND toPlayer.affiliateId IS NULL)";
		} else if (isset($input['belongAff']) && !empty($input['belongAff'])) {

			$this->load->model('affiliatemodel');
			$affiliateId = $this->affiliatemodel->getAffiliateIdByUsername($input['belongAff']);
			$affiliateIds=null;

			if (isset($input['aff_include_all_downlines']) && $input['aff_include_all_downlines']
					&& !empty($affiliateId)) {
				$affiliateIds = $this->affiliatemodel->includeAllDownlineAffiliateIds($affiliateId);
			}

			if(!empty($affiliateIds)){
				$where[] = '(fromPlayer.affiliateId IN('.implode(',', $affiliateIds).') OR toPlayer.affiliateId IN('.implode(',', $affiliateIds).'))';
			}else{
				$where[] = "(fromPlayer.affiliateId = ? OR toPlayer.affiliateId = ?)";
				$values[] = $affiliateId;
				$values[] = $affiliateId;
			}

		}
        if (isset($input['no_agent']) && $input['no_agent'] == true) {
            $where[] = "(fromPlayer.agent_id IS NULL AND toPlayer.agent_id IS NULL OR fromPlayer.agent_id = 0 AND toPlayer.agent_id = 0)";
        }

		if (isset($input['to_type'])) {
			$where[] = "transactions.to_type = ?";
			$values[] = $input['to_type'];
		}

		if (isset($input['toUsernameId'])) {
			$where[] = "(CASE transactions.to_type WHEN ? THEN toUser.userId WHEN ? THEN toPlayer.playerId WHEN ? THEN toAffiliate.affiliateId ELSE NULL END) = ? ";
			$values[] = Transactions::ADMIN;
			$values[] = Transactions::PLAYER;
			$values[] = Transactions::AFFILIATE;
			$values[] = $input['toUsernameId'];
		}

		if (isset($input['amountStart'])) {
			$where[] = "transactions.amount >= ?";
			$values[] = $input['amountStart'];
		}

		if (isset($input['amountEnd'])) {
			$where[] = "transactions.amount <= ?";
			$values[] = $input['amountEnd'];
		}

		if (isset($input['transactionTypeFilter'])) {
			$where[] = "transactions.transaction_type in (".$input['transactionTypeFilter'].")";
		}

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd']) && ( ! empty( $input['dateRangeValueStart'] ) && ! empty( $input['dateRangeValueEnd'] ) )) {
			$where[] = "transactions.created_at BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		 //NOTE: For command line export ONLY !/ ask me: aris
		if (isset($request['CdateRangeValueStart'], $request['CdateRangeValueEnd'])) {
			$where[] = "transactions.created_at >= ? AND transactions.created_at <= ?";
			$values[] = $request['CdateRangeValueStart'];
			$values[] = $request['CdateRangeValueEnd'];
		}

		if (isset($input['flag'])) {
			$where[] = "transactions.flag = ?";
			$values[] = $input['flag'];
		}

		if (isset($input['promo_category'])) {
			$where[] = "transactions.promo_category = ?";
			$values[] = $input['promo_category'];
		}

		//convert memberUsername to player id
		if (isset($input['memberUsername']) && !empty($input['memberUsername'])) {
			if (isset($input['search_by']) && $input['search_by'] == 2) {
				//search player id first
				$memberUsername=$input['memberUsername'];
				$player_id = $this->player_model->getPlayerIdByUsername($memberUsername);
			} else {
				$where[] = "(fromPlayer.username LIKE ? OR toPlayer.username LIKE ?)";
				$values[] = '%' . $input['memberUsername'] . '%';
				$values[] = '%' . $input['memberUsername'] . '%';
			}
		}

		$player_id=intval($player_id);
		if ($player_id>0) {
			$where[] = "((transactions.to_type=2 and transactions.to_id = ?) OR (transactions.from_type=2 and transactions.from_id = ?) )";
			$values[] = $player_id;
			$values[] = $player_id;
		}

		//check not all type
		if (isset($input['transaction_type_all']) && $input['transaction_type_all'] == 'checkall') {
			//ignore transaction type
		} else if (isset($input['transaction_type'])) {
			if (is_array($input['transaction_type'])) {
				$where[] = "transactions.transaction_type IN (" . implode(',', array_fill(0, count($input['transaction_type']), '?')) . ")";
				$values = array_merge($values, $input['transaction_type']);
			} else {
				$where[] = "transactions.transaction_type = ?";
				$values[] = $input['transaction_type'];
			}
		}

		if (isset($input['disabled_transaction_types'])) {
			$where[] = "transactions.transaction_type != ?";
			$values[] = $input['disabled_transaction_types'];
		}

		//this is for filtering the record one by one (withdrawal, deposit, transfer)
		if( isset($input['trans_type']) ){

			switch ( $input['trans_type'] ) {
				case 'transfer':
					$where[] = "transactions.transaction_type IN (?,?)";
					$values[] = Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET;
					$values[] = Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET;
					break;
				default:
					$where[] = "transactions.transaction_type = ?";
					$values[] = $input['trans_type_value'] ;
					break;
			}

		}

		// to see test player transaction  on their user information page
		$where[] = "toPlayer.deleted_at IS NULL";


		$this->utils->debug_log('where', $where, 'values', $values);

		$payment_account_all=isset($input['payment_account_all']) ? $input['payment_account_all']=='true' : false ;

		if(!$payment_account_all){
			if (isset($input['payment_account_id'])) {

				if (is_array($input['payment_account_id'])) {
					if (isset($input['payment_account_id_null'])) {
						$where[] = "(transactions.payment_account_id IN (" . implode(',', array_fill(0, count($input['payment_account_id']), '?')) . ") OR transactions.payment_account_id IS NULL)";
					} else {
						$where[] = "transactions.payment_account_id IN (" . implode(',', array_fill(0, count($input['payment_account_id']), '?')) . ")";
					}
					$values = array_merge($values, $input['payment_account_id']);

				} else {
					if (isset($input['payment_account_id_null'])) {
						$where[] = "(transactions.payment_account_id = ? OR transactions.payment_account_id IS NULL)";
					} else {
						$where[] = "transactions.payment_account_id = ?";
					}
					$values[] = $input['payment_account_id'];
				}
			} else if (isset($input['payment_account_id_null'])) {
				$where[] = "transactions.payment_account_id IS NULL";
			}
		}

		if($this->utils->isEnabledFeature('enable_adjustment_category')){
			if(isset($input['promo_category'])){
				if(!empty($input['promo_category'])){
					$where[] = "transactions.adjustment_category_id = ?";
					$values[] = $input['promo_category'] ;
				}
			}
		}

		$this->benchmark->mark('pre_processing_end');

		# END PROCESS SEARCH FORM #################################################################################################################################################
		$this->benchmark->mark('data_sql_start');

		$columns = $this->checkIfEnabled($this->utils->isEnabledFeature('enable_adjustment_category'), array('adjustment_category_id'), $columns);
		$columns = $this->checkIfEnabled($this->utils->isEnabledFeature('enable_tag_column_on_transaction'), array('tagName'), $columns);

		$this->utils->debug_log('Columns', $columns);

		$countOnlyField='transactions.id';
		if( ! empty( $not_datatable ) ){
			if($mobile){
				$result = $this->data_tables->get_data(null, $columns, $table, $where, $values, $joins, $group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);
			}else{
				$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);
			}
			return $result;
		}

		if($is_export){
            $this->data_tables->options['is_export']=true;
			// $this->data_tables->options['only_sql']=true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename']=$csv_filename;
		}

		$countOnlyField='transactions.id';
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins,
			$group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);

		if($is_export){
		    //drop result if export
			return $csv_filename;
		}

		$this->benchmark->mark('data_sql_end');

		$this->benchmark->mark('summary_sql_start');
		unset($joins['transactions_tag']);
		unset($joins['manual_subtract_balance_tag']);
		$summary = $this->data_tables->summary($request, $table, $joins, 'transactions.transaction_type, external_system.system_code, SUM(round(transactions.amount,2)) total_amount', 'transactions.transaction_type, external_system.id', $columns, $where, $values);
		$this->benchmark->mark('summary_sql_end');


		if (isset($input['transaction_type'])) {

			$this->benchmark->mark('deposit_sql_start');
			$deposit_summary = $this->data_tables->summary($request, $table, $joins,
				'sale_orders.payment_type_name, sale_orders.payment_account_name, SUM(round(transactions.amount,2)) total_amount, SUBSTR(sale_orders.payment_account_number,-4) account_num', 'sale_orders.payment_account_id, `sale_orders`.`payment_account_number`', $columns, $where, $values);
			$this->benchmark->mark('deposit_sql_end');

			$this->benchmark->mark('summary_processing_start');
			$result['summary'] = array();
			foreach ($summary as $row) {
				$arr = array(
					'transaction_name' => lang('transaction.transaction.type.' . $row['transaction_type']) . ($row['system_code'] ? (' - ' . $row['system_code']) : ''),
					'amount' => $this->utils->formatCurrencyNoSym($row['total_amount']),
					'transaction_type' => $row['transaction_type'],
				);

				array_push($result['summary'], $arr);
			}

			foreach ($deposit_summary as $row) {
				$result['bank_summary'][lang($row['payment_type_name']) . ' - ' . $row['payment_account_name'] . ($row['account_num'] ? (' - ' . $row['account_num']) : '')] = $this->utils->formatCurrencyNoSym($row['total_amount']);
			}

			$this->benchmark->mark('summary_processing_end');
		} else {
			$this->benchmark->mark('deposit_sql_start');
			$deposit_summary = $this->data_tables->summary($request, $table, $joins, 'SUM(round(transactions.amount,2)) AS total_amount', 'transactions.transaction_type', $columns, $where, $values);
			$this->benchmark->mark('deposit_sql_end');

			$this->benchmark->mark('summary_processing_start');
			$result['summary'] = array();
			foreach ($summary as $row) {
				$arr = array(
					'transaction_name' => lang('transaction.transaction.type.' . $row['transaction_type']) . ($row['system_code'] ? (' - ' . $row['system_code']) : ''),
					'amount' => $this->utils->formatCurrencyNoSym($row['total_amount']),
					'transaction_type' => $row['transaction_type'],
				);
			}
			$this->benchmark->mark('summary_processing_end');
		}

		return $result;
	}


	/*not real transcation for fast finished only end*/

	public function transactionsByWalletDeposit($player_id, $request, $not_datatable = '') {

		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('sale_order', 'wallet_model', 'player_attached_proof_file_model', 'sale_orders_notes'));

		$this->benchmark->mark('pre_processing_start');

		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			// array(
			// 	'alias' => 'secure_id',
			// 	'select' => 'sale_orders.secure_id',
			// 	'name' => 'secure_id',
			// ),
			// array(
			// 	'alias' => 'player_deposit_transaction_code',
			// 	'select' => 'sale_orders.player_deposit_transaction_code',
			// 	'name' => 'player_deposit_transaction_code',
			// ),
			// array(
			// 	'alias' => 'reason',
			// 	'select' => 'sale_orders.reason',
			// 	'name' => 'reason',
			// ),
			// array(
			// 	'alias' => 'show_reason_to_player',
			// 	'select' => 'sale_orders.payment_account_name',
			// 	'name' => 'show_reason_to_player',
			// ),
			// array(
			// 	'select' => 'payment_account.show_reason_to_player',
			// 	'name' => 'show_reason_to_player',
			// ),
			// array(
			// 	'select' => 'banktype.bankName',
			// 	'name' => 'bankName',
			// ),
			array(
				'dt' => $i++,
				'alias' => 'orderId',
				'select' => 'sale_orders.secure_id',
				'name' => lang('pay.sale_order_id'),
			),
			array(
				'dt' => $i++,
				'alias' => 'created_at',
				'select' => 'sale_orders.created_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('pay.reqtime'),
			),
			array(
				'dt' => $i++,
				'alias' => 'second_category_flag',
				'select' => 'payment_account.second_category_flag',
				'formatter' => function ($d) {
					$secondCats = $this->utils->getPaymentAccountSecondCategoryAllFlagsKV();
					$secondCat = isset($secondCats[$d]) ? $secondCats[$d] : lang('N/A');
                    return $secondCat;
				},
				'name' => lang('Type'),
			),
			// array(
			// 	'dt' => $i++,
			// 	'alias' => 'payment_flag',
			// 	'select' => 'sale_orders.payment_flag',
			// 	'formatter' => function ($d) {
   //                  $payment_flag = lang('pay.auto_online_payment');
   //                  switch ($d) {
   //                      case LOCAL_BANK_OFFLINE:
   //                          $payment_flag = lang('pay.local_bank_offline');
   //                          break;
   //                      case MANUAL_ONLINE_PAYMENT:
   //                          $payment_flag = lang('pay.manual_online_payment');
   //                          break;
   //                      case AUTO_ONLINE_PAYMENT:
   //                          $payment_flag = lang('pay.auto_online_payment');
   //                          break;
   //                  }
			// 		return $payment_flag;
			// 	},
			// 	'name' => lang('Type'),
			// ),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'sale_orders.status',
				'formatter' => function ($d) {
				    $status = array(
				        Sale_order::STATUS_PROCESSING => lang('Pending'),
				        Sale_order::STATUS_SETTLED => lang('Approved'),
				        Sale_order::STATUS_BROWSER_CALLBACK => lang('Approved'),
				        Sale_order::STATUS_DECLINED => lang('Declined')
				    );
					return $status[$d];
				},
				'name' => lang('Status'),
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'sale_orders.amount',
				'formatter' => function ($d) {
					return $this->utils->toCurrencyNumber($d);
				},
				'name' => lang('Amount'),
			),
            array(
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'sale_orders.id',
				'name' => lang('Player Deposit Note'),
				'formatter' => function ($sale_order_id){
					$display_last_notes = false;
					$donotShowUsername = true;
					$allNotes = $this->sale_orders_notes->getNotesByNoteType(Sale_orders_notes::PLAYER_NOTES, $sale_order_id, $display_last_notes, $donotShowUsername);
					return $allNotes ?: lang('lang.norecyet');
				},
			),

            array(
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'sale_orders.id',
				'name' => lang('Notes'),
				'formatter' => function ($sale_order_id){
					$display_last_notes = false;
					$donotShowUsername = true;
					$allNotes = $this->sale_orders_notes->getNotesByNoteType(Sale_orders_notes::EXTERNAL_NOTE, $sale_order_id, $display_last_notes, $donotShowUsername);
					return $allNotes ?: lang('lang.norecyet');
				},
			),
            array(
                'dt' => $i++,
                'alias' => 'receipt',
                'name' => lang('cms.uploadBanner'),
                'formatter' => function ($d, $row) use ($player_id){
                    $receipt_info = $this->player_attached_proof_file_model->getDepositReceiptFileList($player_id, $row['sale_orders_id']);
                    if(!empty($receipt_info)){
                        return $receipt_info;
                    }
                    return null;
                },
            ),
            array(
                'select' => 'sale_orders.show_reason_to_player',
                'alias' => 'show_reason_to_player',
                'name' => lang('sys.gd11'),
            ),
            array(
                'dt' => $i++,
                'select' => 'sale_orders.id',
                'alias' => 'sale_orders_id',
            ),

		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'sale_orders';
		$joins = array(
			'payment_account' => 'sale_orders.payment_account_id = payment_account.id',
			'banktype' => 'payment_account.payment_type_id = banktype.bankTypeId',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$group_by=[];
		$having=[];
		$distinct = true;
		$external_order=[]; //[['column'=>'id', 'dir'=> 'desc']];
		$input = $this->data_tables->extra_search($request);

		$where[] = "sale_orders.payment_kind = ? ";
		$values[] = Sale_order::PAYMENT_KIND_DEPOSIT;

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd']) && ( ! empty( $input['dateRangeValueStart'] ) && ! empty( $input['dateRangeValueEnd'] ) ))
		{
			$where[] = "sale_orders.created_at BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		if ($player_id) {
			$where[] = "sale_orders.player_id = ? ";
			$values[] = $player_id;
		}

		$this->utils->debug_log('where', $where, 'values', $values);

		$this->benchmark->mark('pre_processing_end');

		# END PROCESS SEARCH FORM #################################################################################################################################################
		$this->benchmark->mark('data_sql_start');

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, $distinct, $external_order, $not_datatable);

		$this->utils->printLastSQL();

		$this->benchmark->mark('data_sql_end');

		$this->benchmark->mark('summary_sql_start');

		//only count successed
		$where[] = "sale_orders.status = ?";
		$values[] = Sale_order::STATUS_SETTLED;
		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(sale_orders.amount) total_amount', null, $columns, $where, $values);
		$this->benchmark->mark('summary_sql_end');

		$this->benchmark->mark('summary_processing_start');
		$result['summary'] = array();
		foreach ($summary as $row) {
            // $payment_flag = lang('pay.auto_online_payment');
            // switch ($row['payment_flag']) {
            //     case LOCAL_BANK_OFFLINE:
            //         $payment_flag = lang('pay.local_bank_offline');
            //         break;
            //     case MANUAL_ONLINE_PAYMENT:
            //         $payment_flag = lang('pay.manual_online_payment');
            //         break;
            //     case AUTO_ONLINE_PAYMENT:
            //         $payment_flag = lang('pay.auto_online_payment');
            //         break;
            // }

			$arr = array(
				// 'payment_flag_name' => $payment_flag,
				'amount' => $this->utils->formatCurrencyNoSym($row['total_amount']),
			);

			array_push($result['summary'], $arr);
		}

		$this->benchmark->mark('summary_processing_end');

		return $result;
	}
	public function transactionsByWalletWithdraw($player_id, $request, $not_datatable = '') {


		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('player_model', 'transaction_notes', 'wallet_model','walletaccount_notes'));

		$this->benchmark->mark('pre_processing_start');

		$withdrawalStages=$this->operatorglobalsettings->getCustomWithdrawalProcessingStage();
		$this->utils->debug_log('withdrawalStages', $withdrawalStages);

		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			// array(
			// 	'alias' => 'dwLocation',
			// 	'select' => 'walletaccount.dwLocation',
			// 	'formatter' => 'defaultFormatter',
			// 	'name' => 'dwLocation',
			// ),
			array(
				'alias' => 'showNotesFlag',
				'select' => 'walletaccount.showNotesFlag',
			),
			array(
				'dt' => $i++,
				'alias' => 'dwDateTime',
				'select' => 'walletaccount.dwDateTime',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('pay.reqtime'),
			),
			array(
				'dt' => $i++,
				'alias' => 'transactionCode',
				'select' => 'walletaccount.transactionCode',
				'formatter' => 'defaultFormatter',
				'name' => lang('Withdrawal Code'),
			),
			array(
				'dt' => $i++,
				'alias' => 'dwStatus',
				'select' => 'walletaccount.dwStatus',
				'formatter' => function ($d) use($withdrawalStages){

					switch ($d) {
		                case Wallet_model::REQUEST_STATUS: return lang('sale_orders.status.3');
		                case Wallet_model::PENDING_REVIEW_STATUS: return lang('sale_orders.status.3');
		                case Wallet_model::LOCK_API_UNKNOWN_STATUS: return lang('sys.vu51');
						case 'approved': return lang('transaction.status.1');
		                case Wallet_model::DECLINED_STATUS: return lang('transaction.status.2');
		                case Wallet_model::PAID_STATUS: return lang('Paid');
		                case Wallet_model::PAY_PROC_STATUS:
							return lang('Payment Processing');
							// return lang(@$withdrawalStages['payProc']['name']);
		                default:
		                	//others
							return lang('sale_orders.status.3');
							// return lang(@$withdrawalStages[str_replace('CS', '',$d)]['name']);
					}
				},
				'name' => lang('Status'),
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'walletaccount.amount',
				'formatter' => function ($d) {
					return $this->utils->toCurrencyNumber($d);
				},
				'name' => lang('Amount'),
			),
			array(
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'walletaccount.walletAccountId', #'transaction_notes.note',
				'name' => lang('Notes'),
				'formatter' => function ($walletAccountId)  {
					$display_last_notes = false;
					$donotShowUsername = true;
					$allNotes = $this->walletaccount_notes->getNotesByNoteType(Walletaccount_notes::EXTERNAL_NOTE, $walletAccountId, $display_last_notes, $donotShowUsername);
					return $allNotes ?: '<i class="text-muted">' .lang('lang.norecyet') . '</i>';
				},
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'walletaccount';
		$joins = array(
			'playeraccount' => 'walletaccount.playerAccountId = playeraccount.playerAccountId',
			'transaction_notes' => 'walletaccount.walletAccountId = transaction_notes.transaction_id'
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$group_by=[];
		//$group_by = array('transaction_notes.transaction_id');
		$having=[];
		$distinct = true;
		$external_order=[]; //[['column'=>'id', 'dir'=> 'desc']];

		$input = $this->data_tables->extra_search($request);

		$where[] = "walletaccount.transactionType = ? ";
		$values[] = "withdrawal";

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd']) && ( ! empty( $input['dateRangeValueStart'] ) && ! empty( $input['dateRangeValueEnd'] ) )) {
			$where[] = "walletaccount.dwDateTime BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		if ($player_id) {
			$where[] = "playeraccount.playerId = ? ";
			$values[] = $player_id;
		}

		$this->utils->debug_log('where', $where, 'values', $values);

		$this->benchmark->mark('pre_processing_end');

		# END PROCESS SEARCH FORM #################################################################################################################################################
		$this->benchmark->mark('data_sql_start');

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, $distinct, $external_order, $not_datatable);
		$this->utils->printLastSQL();

		$this->benchmark->mark('data_sql_end');

		$this->utils->debug_log($result);

		$this->benchmark->mark('summary_sql_start');

		$joins_sum = array(
			'playeraccount' => 'walletaccount.playerAccountId = playeraccount.playerAccountId'
		);

		//only count successed
		$where[] = "walletaccount.dwStatus = ?";
		$values[] = "paid";
		$summary = $this->data_tables->summary($request, $table, $joins_sum, 'SUM(walletaccount.amount) total_amount', null, $columns, $where, $values);
		$this->benchmark->mark('summary_sql_end');

		$this->benchmark->mark('summary_processing_start');
		$result['summary'] = array();
		foreach ($summary as $row) {
            // $payment_flag = lang('pay.auto_online_payment');
            // switch ($row['payment_flag']) {
            //     case LOCAL_BANK_OFFLINE:
            //         $payment_flag = lang('pay.local_bank_offline');
            //         break;
            //     case MANUAL_ONLINE_PAYMENT:
            //         $payment_flag = lang('pay.manual_online_payment');
            //         break;
            //     case AUTO_ONLINE_PAYMENT:
            //         $payment_flag = lang('pay.auto_online_payment');
            //         break;
            // }

			$arr = array(
				// 'payment_flag_name' => $payment_flag,
				'amount' => $this->utils->formatCurrencyNoSym($row['total_amount']),
			);

			array_push($result['summary'], $arr);
		}

		$this->benchmark->mark('summary_processing_end');


		return $result;
	}

	public function transactionsByWalletTransfer($player_id, $request, $not_datatable = '') {
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('player_model'));

		$this->benchmark->mark('pre_processing_start');

		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'created_at',
				'select' => 'transactions.created_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('Transfer time'),
			),
			array(
				'dt' => $i++,
				'alias' => 'transferFrom',
				'select' => "if(transactions.transaction_type=5, 'Main', external_system.system_code )",
				'formatter' => function( $d ) {
					if ($d == "Main")
						return lang('Main Wallet');
					else
						return $d;
				},
				'name' => lang('From'),
			),
			array(
				'dt' => $i++,
				'alias' => 'transferTo',
				'select' => "if(transactions.transaction_type=5, external_system.system_code, 'Main' )",
				'formatter' => function ($d) {
					if ($d == "Main")
						return lang('Main Wallet');
					else
						return $d;
				},
				'name' => lang('player.12'),
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'transactions.amount',
				'formatter' => function ($d) {
					return $this->utils->toCurrencyNumber($d);
				},
				'name' => lang('Amount'),
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'transactions';
		$joins = array(
			'external_system' => 'external_system.id=transactions.sub_wallet_id',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$group_by=[];
		$having=[];
		$distinct = true;
		$external_order=[]; //[['column'=>'id', 'dir'=> 'desc']];

		$input = $this->data_tables->extra_search($request);

		$where[] = "transactions.from_type = ? ";
		$values[] = Transactions::PLAYER;
		$where[] = "transactions.transaction_type in (?,?) ";
		$values[] = Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET;
		$values[] = Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET;

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd']) && ( ! empty( $input['dateRangeValueStart'] ) && ! empty( $input['dateRangeValueEnd'] ) )) {
			$where[] = "transactions.created_at BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		if ($player_id) {
			$where[] = "transactions.from_id = ? ";
			$values[] = $player_id;
		}

		$this->utils->debug_log('where', $where, 'values', $values);

		$this->benchmark->mark('pre_processing_end');

		# END PROCESS SEARCH FORM #################################################################################################################################################
		$this->benchmark->mark('data_sql_start');

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, $distinct, $external_order, $not_datatable);
		$this->utils->printLastSQL();

		$this->benchmark->mark('data_sql_end');

		$this->utils->debug_log($result);

		return $result;
	}

	/**
	 * detail: get summary reports
	 *
	 * @param string $type
	 * @param string $date
	 *
	 * @return array
	 */
	public function report_summary($type = 'YEAR', $date = null, $selected_tag = null) {

		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->model('transactions');

		if ($type == 'DATE') {
			$readOnlyDB->select("DATE(created_at) AS common_date");
		} else {
			$readOnlyDB->select("EXTRACT({$type} FROM created_at) AS common_date");
		}
		$readOnlyDB->select_sum(sprintf('IF(transaction_type = %s, amount, 0)', Transactions::DEPOSIT), 'total_deposit');
		$readOnlyDB->select_sum(sprintf('IF(transaction_type = %s, amount, 0)', Transactions::WITHDRAWAL), 'total_withdraw');
		$readOnlyDB->select_sum(sprintf('(CASE WHEN transaction_type IN (%s) THEN amount WHEN transaction_type = %s THEN -amount ELSE 0 END)', implode(',', array(Transactions::ADD_BONUS, Transactions::MEMBER_GROUP_DEPOSIT_BONUS, Transactions::PLAYER_REFER_BONUS, Transactions::RANDOM_BONUS)), Transactions::SUBTRACT_BONUS), 'total_bonus');
		$readOnlyDB->select_sum(sprintf('IF(transaction_type = %s, amount, 0)', Transactions::AUTO_ADD_CASHBACK_TO_BALANCE), 'total_cashback');
		$readOnlyDB->select_sum(sprintf('IF(transaction_type IN (%s), amount, 0)', implode(',', array(Transactions::FEE_FOR_PLAYER, Transactions::FEE_FOR_OPERATOR))), 'total_transaction_fee');
		$readOnlyDB->select_sum(sprintf('(CASE WHEN transaction_type = %s THEN amount WHEN transaction_type = %s THEN -amount ELSE 0 END)', Transactions::DEPOSIT, Transactions::WITHDRAWAL), 'bank_cash_amount');
		$readOnlyDB->from('transactions');
		$readOnlyDB->join('player fromPlayer', 'transactions.from_type = 2 AND fromPlayer.playerId = transactions.from_id', 'left');
		$readOnlyDB->join('player toPlayer', 'transactions.to_type = 2 AND toPlayer.playerId = transactions.to_id', 'left');
        if(!empty($selected_tag)){
            $readOnlyDB->join('playertag', 'playertag.playerId = toPlayer.playerId', 'left');
            $readOnlyDB->where('(playertag.tagId NOT IN ('.implode(',', $selected_tag).") OR playertag.tagId is NULL)");
        }
		$readOnlyDB->where("(fromPlayer.deleted_at IS NULL AND toPlayer.deleted_at IS NULL)");
		$readOnlyDB->where('transactions.status', transactions::APPROVED);

		if ($type == 'DATE') {
			$readOnlyDB->group_by("DATE(created_at)");
		} else {
			$readOnlyDB->group_by("EXTRACT({$type} FROM created_at)");
		}

		if ($date) {
			switch ($type) {
			case 'YEAR_MONTH':
				$readOnlyDB->where(sprintf("EXTRACT(YEAR FROM created_at) = '%s'", $date));
				break;

			case 'DATE':
				$readOnlyDB->where(sprintf("EXTRACT(YEAR_MONTH FROM created_at) = '%s'", $date));
				break;

			default:
				break;
			}
		}


        $this->utils->debug_log('the summary report ------>', $readOnlyDB->_compile_select());

		$query = $readOnlyDB->get();
		return $query->result_array();
	}


	/**
	 * detail: get summary reports 2
	 *
	 * @param string $dateFrom
	 * @param string $dateTo
	 *
	 * @return array
	 */
	public function report_summary_2($dateFrom = null, $dateTo = null) {
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->model('transactions');

		$readOnlyDB->select("DATE(created_at) AS common_date");
		$readOnlyDB->select_sum(sprintf('IF(transaction_type = %s, amount, 0)', Transactions::DEPOSIT), 'total_deposit');
		$readOnlyDB->select_sum(sprintf('IF(transaction_type = %s, amount, 0)', Transactions::WITHDRAWAL), 'total_withdraw');
		$readOnlyDB->select_sum(sprintf('(CASE WHEN transaction_type IN (%s) THEN amount WHEN transaction_type = %s THEN -amount ELSE 0 END)', implode(',', array(Transactions::ADD_BONUS, Transactions::MEMBER_GROUP_DEPOSIT_BONUS, Transactions::PLAYER_REFER_BONUS, Transactions::RANDOM_BONUS)), Transactions::SUBTRACT_BONUS), 'total_bonus');
		$readOnlyDB->select_sum(sprintf('IF(transaction_type = %s, amount, 0)', Transactions::AUTO_ADD_CASHBACK_TO_BALANCE), 'total_cashback');
		$readOnlyDB->select_sum(sprintf('IF(transaction_type IN (%s), amount, 0)', implode(',', array(Transactions::FEE_FOR_PLAYER, Transactions::FEE_FOR_OPERATOR))), 'total_transaction_fee');
		$readOnlyDB->select_sum(sprintf('(CASE WHEN transaction_type = %s THEN amount WHEN transaction_type = %s THEN -amount ELSE 0 END)', Transactions::DEPOSIT, Transactions::WITHDRAWAL), 'bank_cash_amount');

		$readOnlyDB->from('transactions');
		$readOnlyDB->join('player' , "transactions.to_type = 2 AND player.playerId = transactions.to_id");

		$readOnlyDB->where("DATE(created_at) >=", $dateFrom);
		$readOnlyDB->where("DATE(created_at) <=", $dateTo);
		$readOnlyDB->where('transactions.status', transactions::APPROVED);
		$readOnlyDB->where('player.deleted_at IS NULL');

		$readOnlyDB->group_by("DATE(created_at)");
        $this->utils->debug_log('the summary report 2 ------>', $readOnlyDB->_compile_select());
		$query = $readOnlyDB->get();

		return $query->result_array();
	}

	/**
	 * new summary2 report
	 * @param  string $dateFrom
	 * @param  string $dateTo
	 * @param  bool $month_only
	 * @return array
	 */
	public function report_summary2($dateFrom, $dateTo, $month_only) {
		$readOnlyDB = $this->getReadOnlyDB();

		if($month_only){
			$sql=<<<EOD
select DATE_FORMAT(summary_date, "%Y%m") as summary_date,
sum(total_bet) as total_bet,sum(total_win) as total_win,sum(total_loss) as total_loss,sum(total_payout) as total_payout,
sum(total_deposit) as total_deposit,sum(total_withdrawal) as total_withdrawal,sum(total_bonus) as total_bonus,
sum(total_cashback) as total_cashback,sum(total_fee) as total_fee,sum(total_bank_cash_amount) as total_bank_cash_amount,
max(count_all_players) as count_all_players,sum(count_new_player) as count_new_player,sum(count_first_deposit) as count_first_deposit,sum(count_second_deposit) as count_second_deposit
from summary2_report_daily
where
summary_date >= ?
and summary_date<= ?
group by DATE_FORMAT(summary_date, "%Y%m")
order by DATE_FORMAT(summary_date, "%Y%m") desc

EOD;
			//group by month
			// $readOnlyDB->select(
			// 	'DATE_FORMAT(summary_date, "%Y%m") as summary_date,'.
			// 	'sum(total_bet) as total_bet,sum(total_win) as total_win,sum(total_loss) as total_loss,sum(total_payout) as total_payout,'.
			// 	'sum(total_deposit) as total_deposit,sum(total_withdrawal) as total_withdrawal,sum(total_bonus) as total_bonus,'.
			// 	'sum(total_cashback) as total_cashback,sum(total_fee) as total_fee,sum(total_bank_cash_amount) as total_bank_cash_amount,'.
			// 	'max(count_all_players) as count_all_players,sum(count_new_player) as count_new_player,sum(count_first_deposit) as count_first_deposit,sum(count_second_deposit) as count_second_deposit',
			// null, false)->from('summary2_report_daily')
			// 	->where('summary_date >=', $dateFrom)
			// 	->where('summary_date <=', $dateTo)
			// 	->group_by('DATE_FORMAT(summary_date, "%Y%m")')
			// 	->order_by('summary_date', 'desc');
			// return $this->runMultipleRowArray($readOnlyDB);

			return $this->runRawSelectSQLArray($sql, [$dateFrom, $dateTo]);

		}else{

			$readOnlyDB->select(
				'total_bet,total_win,total_loss,total_payout,summary_date,total_deposit,total_withdrawal,'.
				'total_bonus,total_cashback,total_fee,total_bank_cash_amount,count_all_players,count_new_player,count_first_deposit,count_second_deposit'
			)->from('summary2_report_daily')
				->where('summary_date >=', $dateFrom)
				->where('summary_date <=', $dateTo)
				->order_by('summary_date', 'desc');
			return $this->runMultipleRowArray($readOnlyDB);
		}
	}

	/**
	 * detail: the sum/total of the win and loss payout
	 *
	 * @param string $type
	 * @param string $date
	 * @param array $selected_tag
	 * @return array
	 */
	public function sumBetWinLossPayout($type, $date, $selected_tag = null) {

		$this->load->model(array('game_logs', 'total_player_game_year', 'total_player_game_month', 'total_player_game_day'));

		$this->utils->debug_log('type', $type, 'date', $date);
		$readOnlyDB = $this->getReadOnlyDB();

		$from = $this->utils->getNowForMysql();
		$to = $this->utils->getNowForMysql();
		if ($type == 'YEAR') {
			//get year from $date
			$year = substr($date, 0, 4);
			$from = $year . '-01-01 ' . Utils::FIRST_TIME;
			$to = $year . '-12-31 ' . Utils::LAST_TIME;
			list($totalBet, $totalWin, $totalLoss) = $this->total_player_game_year->sumOperatorBetsWinsLossByDatetime($from, $to, null, null, $readOnlyDB, $selected_tag);

		} else if ($type == 'YEAR_MONTH') {
			//month
			$year_month = substr($date, 0, 7);
			$last_day = (new DateTime($date))->format('t');

			$from = $year_month . '-01 ' . Utils::FIRST_TIME;
			$to = $year_month . '-' . $last_day . ' ' . Utils::LAST_TIME;
			list($totalBet, $totalWin, $totalLoss) = $this->total_player_game_month->sumOperatorBetsWinsLossByDatetime($from, $to, null, null, $readOnlyDB, $selected_tag);

		} else {
			//day
			$from = $date . ' ' . Utils::FIRST_TIME;
			$to = $date . ' ' . Utils::LAST_TIME;
			list($totalBet, $totalWin, $totalLoss) = $this->total_player_game_day->sumOperatorBetsWinsLossByDatetime($from, $to, null, null, $readOnlyDB, $selected_tag);
		}

		$payout = $totalLoss - $totalWin;
		return array(
            'total_bet'     => $this->utils->roundCurrencyForShow($totalBet),
			'total_win'     => $this->utils->roundCurrencyForShow($totalWin),
			'total_loss'    => $this->utils->roundCurrencyForShow($totalLoss),
			'payout'        => $this->utils->roundCurrencyForShow($payout)
        );
	}

	/**
	 * Handle ajax from view, Report_management::conversion_rate_report().
	 *
	 * @param string $summaryBy ex, all, directplayer, affiliate, agency and referrer. default is "all".
	 * @param boolean|array $full_params queue_results.full_params from export_data only.
	 * @return json $return The json string, return from conversion_rate_report_summaryByXXX().
	 */
	public function conversion_rate_report($summaryBy = 'all', $full_params = false) {

		$extra_search = false;
		if( !empty($full_params) ){ // queue_results.full_params
			foreach($full_params['extra_search'] as $key => $value){
				if( ! empty($value['name']) ){
					$extra_search[$value['name']] = $value['value'];
				}
			}
			$this->utils->debug_log('1440.extra_search:', $extra_search );
		}

		$return = array();
		switch( strtolower($summaryBy) ){
			default:
			case 'all':{
				$return = $this->conversion_rate_report_summaryByAll($extra_search);
				break;
			}
			case 'directplayer':{
				$return = $this->conversion_rate_report_summaryByDirectPlayer($extra_search);
				break;
			}
			case 'affiliate':{
				$return = $this->conversion_rate_report_summaryByAffiliate($extra_search);
				break;
			}
			case 'agency':{
				$return = $this->conversion_rate_report_summaryByAgency($extra_search);
				break;
			}
			case 'referrer':{
				$return = $this->conversion_rate_report_summaryByReferrer($extra_search);
				break;
			}
		}

		return $return;
	} // EOF conversion_rate_report

	/**
	 * getWhere4first_deposit_datetime
	 *
	 * @param array $input Recommand  return from data_tables->extra_search().
	 * @example <code>
	 * $request = $this->input->post();
	 * $input = $this->data_tables->extra_search($request);
	 * </code>
	 * @param string $theField for first_deposit_datetime field alias.
	 * @return string $subSql4first_deposit_date
	 */
	public function getWhere4first_deposit_datetime($input, $theField='player_report_hourly.first_deposit_datetime'){
		$subSql4first_deposit_date = '';
		if( ! empty($input['first_deposit_date_from'])
			&& ! empty($input['first_deposit_date_to'])
		){
			$first_deposit_date_from = $input['first_deposit_date_from'];
			$first_deposit_date_to = $input['first_deposit_date_to'];
			$first_deposit_date_from .= ' 00:00:00';
			$first_deposit_date_to .= ' 23:59:59';
			// for player_report_hourly table.
			/* for search form field, first_deposit_date. */
			$subSql4first_deposit_date =<<<EOF
			$theField BETWEEN STR_TO_DATE('$first_deposit_date_from', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('$first_deposit_date_to', '%Y-%m-%d %H:%i:%s')
EOF;
			$subSql4first_deposit_date = $this->data_tables->clearBlankSpaceAfterHeredoc($subSql4first_deposit_date);
		}
		return $subSql4first_deposit_date;
	}// EOF getWhere4first_deposit_datetime

	/**
	 * Assemble To Where Codition Sentence
	 *
	 * The sentence will  like,
	 * player.playerId = "123" OR player.playerId = "123" OR ...
	 *
	 *
	 * @param [type] $playerIdRowList
	 * @param string $aliasFieldname
	 * @return void
	 */
	public function getPlayerIdCoditionStr($playerIdRowList, $aliasFieldname=''){
		$playerIdCoditionStr = '';
		$playerIdCoditionList = [];

		$formatStr =<<<EOF
		%s = "%s"
EOF;
		$formatStr = $this->data_tables->clearBlankSpaceAfterHeredoc($formatStr);

		if( empty($playerIdRowList ) ){
			$playerIdRowList = array();
		}

		foreach($playerIdRowList as $keyInteger => $currRow){
			if( empty($aliasFieldname) ){
				$playerIdFieldName = key($currRow);
			}else{
				$playerIdFieldName = $aliasFieldname;
			}

			$playerIdFieldValue = $currRow[key($currRow)];
			$playerIdCoditionList[] =sprintf($formatStr, $playerIdFieldName, $playerIdFieldValue);
		}
		$playerIdCoditionStr = implode(' OR ', $playerIdCoditionList);
		return $playerIdCoditionStr;
	}// EOF getPlayerIdCoditionStr

	/**
	 * Get player.playerId With Where Condition
	 *
	 * The player.playerId is P.K is faster than other fields that Not set index.
	 *
	 * @param string $date_from The start datetime of range.
	 * @param string $date_to The end datetime of range.
	 * @return array $playerIdList The result of getMultipleRowArray().
	 */
	public function getPlayerIdWhereCreatedOnRange($date_from, $date_to){

		$readOnlyDB = $this->getReadOnlyDB();
		$sql = <<<EOF
		SELECT playerId
		FROM player
		WHERE createdOn BETWEEN STR_TO_DATE('$date_from', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('$date_to', '%Y-%m-%d %H:%i:%s')
EOF;
		$sql = $this->data_tables->clearBlankSpaceAfterHeredoc($sql);

		$result4PlayerIdList = $readOnlyDB->query( $sql );
		$playerIdList = $this->getMultipleRowArray($result4PlayerIdList);
		return $playerIdList;
	} // EOF getPlayerIdWhereCreatedOnRange

	/**
	 * getWhere4registration_datetime
	 * @param array $input Recommand  return from data_tables->extra_search().
	 * @example <code>
	 * $request = $this->input->post();
	 * $input = $this->data_tables->extra_search($request);
	 * </code>
	 * @param string $theField for createdOn field alias.
	 * @return string $subSql4registration_date
	 */
	public function getWhere4registration_datetime($input, $theField='player.createdOn'){
		$subSql4registration_date = '';

		$date_from_to = $this->convertInput2Date_from_to($input);
		if( ! empty($date_from_to) ){
			$registration_date_from = $date_from_to['date_from'];
			$registration_date_to = $date_from_to['date_to'];
			// for player table.
			/* for search form field, registration_date. */
			$subSql4registration_date =<<<EOF
			$theField BETWEEN STR_TO_DATE('$registration_date_from', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('$registration_date_to', '%Y-%m-%d %H:%i:%s')
EOF;
			$subSql4registration_date = $this->data_tables->clearBlankSpaceAfterHeredoc($subSql4registration_date);
		}
		return $subSql4registration_date;
	} // EOF getWhere4registration_datetime

	/**
	 * Get date_from and date_to from $input then append to time string
	 *
	 * @param array $input
	 * @return array $return The fromat,
	 * - $return[date_from] = $input['registration_date_from']. ' 00:00:00'
	 * - $return[date_to] = $input['registration_date_from']. ' 23:59:59'
	 *
	 */
	function convertInput2Date_from_to($input){
		$return = [];
		if( ! empty($input['registration_date_from'])
			&& ! empty($input['registration_date_to'])
		){
			$date_from = $input['registration_date_from'];
			$date_to = $input['registration_date_to'];
			$date_from .= ' 00:00:00';
			$date_to .= ' 23:59:59';

			$return['date_from'] = $date_from;
			$return['date_to'] = $date_to;
		}
		return $return;
	} // EOF convertInput2Date_from_to

	/**
	 * initial the attr. CRR_TIME_LIMIT
	 * Default the attr. is max_execution_time of ini.
	 *
	 * @return void
	 */
	function initialCrrExecutionTime(){
		$max_execution_time = ini_get('max_execution_time');
		if( empty($this->CRR_TIME_LIMIT) ){
			$this->CRR_TIME_LIMIT = $max_execution_time;
		}
	}

	/**
	 * Extend execution time for Conversion Rate Report.
	 *
	 * @return boolean|NULL The max_execution_time changed  success is true and no change is NULL else false.
	 */
	function setCrrExecutionTime(){
		$max_execution_time = ini_get('max_execution_time');
		if($max_execution_time != $this->CRR_TIME_LIMIT){
			return set_time_limit($this->CRR_TIME_LIMIT);
		}
		return NULL;
	}



	/**
	 * conversion_rate_report Summary By All
	 */
	public function conversion_rate_report_summaryByAll($is_export = false){
		$this->setCrrExecutionTime();

		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		// enable / disable first_deposit_date_from[to]
		$search_first_deposit_date_switch = 'off'; // default
		if( ! empty($input['search_first_deposit_date_switch']) ){
			$search_first_deposit_date_switch = $input['search_first_deposit_date_switch'];
		}
		if($search_first_deposit_date_switch == 'off'){ // ignore first_deposit_date_from and first_deposit_date_to
			unset($input['first_deposit_date_from']);
			unset($input['first_deposit_date_to']);
		}

		$this->data_tables->options['is_export'] = $is_export;


		/// The cols START
		$colIndex = 0;
		$columns = array();
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'Category';
		$columns[$colIndex]['select'] = 'Category';
		$columns[$colIndex]['name'] = lang('Category');
		$columns[$colIndex]['formatter'] = function ($d, $row) {
			return $d;
		};
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'counterOfTotalRegistered';
		$columns[$colIndex]['select'] = 'counterOfTotalRegistered';
		$columns[$colIndex]['name'] = lang('Total Registered Players');
		$columns[$colIndex]['formatter'] = function ($d, $row) {
			return $d;
		};
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'counterOfFirstDepositedOnRegistered';
		$columns[$colIndex]['select'] = 'counterOfFirstDepositedOnRegistered';
		$columns[$colIndex]['name'] = lang('Total First Deposited Player');
		$columns[$colIndex]['formatter'] = function ($d, $row) {
			return $d;
		};
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'percentageOfConversionRate';
		$columns[$colIndex]['select'] = 'percentageOfConversionRate';
		$columns[$colIndex]['name'] = lang('Conversion Rate');
		$columns[$colIndex]['formatter'] = function ($d, $row) {
			$return_d = $d;
			if( is_numeric($d) ){
				// To display to second decimal place.
				$return_d = $this->data_tables->percentageFormatter($d/100);
			}
			return $return_d;

		};
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'amountOfFirstDepositedOnRegistered';
		$columns[$colIndex]['select'] = 'amountOfFirstDepositedOnRegistered';
		$columns[$colIndex]['name'] = lang('Total First Deposit Amount');
		$columns[$colIndex]['formatter'] = function ($d, $row) {
			return $d;
		};
		/// EOF cols

		$sqls = array();
		$benchmarks = array();
		$totalOnly = true;

		// store amounts of report_summaryByXXX.
		// $results['directPlayer'] = $this->conversion_rate_report_summaryByAllWithSubSql('directPlayer', $input, $sqls['directPlayer']);
		$json = $this->conversion_rate_report_summaryByDirectPlayer($is_export, $totalOnly);

		$Catrgory = 'directPlayer';
		$results[$Catrgory][0]['Category'] = $Catrgory;
		$results[$Catrgory][0]['counterOfTotalRegistered'] = $json['summaryByAll']['totalRegisteredPlayers'];
		$results[$Catrgory][0]['counterOfFirstDepositedOnRegistered'] = $json['summaryByAll']['totalFirstDepositPlayers'];
		$results[$Catrgory][0]['percentageOfConversionRate'] = $this->data_tables->percentageFormatter($json['summaryByAll']['conversionRate']);
		$results[$Catrgory][0]['amountOfFirstDepositedOnRegistered'] = $json['summaryByAll']['totalFirstDepositedAmount'];
		$sqls[$Catrgory] = array_filter($json['sqls']);
		$benchmarks[$Catrgory] = $json['benchmarks'];

		$this->data_tables->last_query = NULL; // clear for next conversion_rate_report_summaryByXXX().

		$json = $this->conversion_rate_report_summaryByAffiliate($is_export, $totalOnly);

		$Catrgory = 'affiliatePlayer';
		$results[$Catrgory][0]['Category'] = $Catrgory;
		$results[$Catrgory][0]['counterOfTotalRegistered'] = $json['summaryByAll']['totalRegisteredPlayers'];
		$results[$Catrgory][0]['counterOfFirstDepositedOnRegistered'] = $json['summaryByAll']['totalFirstDepositPlayers'];
		$results[$Catrgory][0]['percentageOfConversionRate'] = $this->data_tables->percentageFormatter($json['summaryByAll']['conversionRate']);
		$results[$Catrgory][0]['amountOfFirstDepositedOnRegistered'] = $json['summaryByAll']['totalFirstDepositedAmount'];
		$sqls[$Catrgory] = array_filter($json['sqls']);
		$benchmarks[$Catrgory] = $json['benchmarks'];
		// $results['affiliatePlayer'] = $this->conversion_rate_report_summaryByAllWithSubSql('affiliatePlayer', $input, $sqls['affiliatePlayer']);

		$this->data_tables->last_query = NULL; // clear for next conversion_rate_report_summaryByXXX().

		$json = $this->conversion_rate_report_summaryByAgency($is_export, $totalOnly);
		$Catrgory = 'agencyPlayer';
		$results[$Catrgory][0]['Category'] = $Catrgory;
		$results[$Catrgory][0]['counterOfTotalRegistered'] = $json['summaryByAll']['totalRegisteredPlayers'];
		$results[$Catrgory][0]['counterOfFirstDepositedOnRegistered'] = $json['summaryByAll']['totalFirstDepositPlayers'];
		$results[$Catrgory][0]['percentageOfConversionRate'] = $this->data_tables->percentageFormatter($json['summaryByAll']['conversionRate']);
		$results[$Catrgory][0]['amountOfFirstDepositedOnRegistered'] = $json['summaryByAll']['totalFirstDepositedAmount'];
		$sqls[$Catrgory] = array_filter($json['sqls']);
		$benchmarks[$Catrgory] = $json['benchmarks'];
		// $results['agencyPlayer'] = $this->conversion_rate_report_summaryByAllWithSubSql('agencyPlayer', $input, $sqls['agencyPlayer']);

		$this->data_tables->last_query = NULL; // clear for next conversion_rate_report_summaryByXXX().

		$json = $this->conversion_rate_report_summaryByReferrer($is_export, $totalOnly);
		$Catrgory = 'refererPlayer';
		$results[$Catrgory][0]['Category'] = $Catrgory;
		$results[$Catrgory][0]['counterOfTotalRegistered'] = $json['summaryByAll']['totalRegisteredPlayers'];
		$results[$Catrgory][0]['counterOfFirstDepositedOnRegistered'] = $json['summaryByAll']['totalFirstDepositPlayers'];
		$results[$Catrgory][0]['percentageOfConversionRate'] = $this->data_tables->percentageFormatter($json['summaryByAll']['conversionRate']);
		$results[$Catrgory][0]['amountOfFirstDepositedOnRegistered'] = $json['summaryByAll']['totalFirstDepositedAmount'];
		$sqls[$Catrgory] = array_filter($json['sqls']);
		$benchmarks[$Catrgory] = $json['benchmarks'];
		// $results['refererPlayer'] = $this->conversion_rate_report_summaryByAllWithSubSql('refererPlayer', $input, $sqls['refererPlayer']);


		$rows = array();
		/// the row of Category ALL.
		$rowIndex = 0;
		$rows[$rowIndex]['Category'] = lang('All');
		$field = 'amountOfFirstDepositedOnRegistered';
		$rows[$rowIndex]['amountOfFirstDepositedOnRegistered'] = $this->helperSumFirstRowByField($results, $field);
		// $rows[$rowIndex]['amountOfFirstDepositedOnRegistered'] = array_sum( array_column($results, 'amountOfFirstDepositedOnRegistered') ) ;
		$field = 'counterOfFirstDepositedOnRegistered';
		$rows[$rowIndex]['counterOfFirstDepositedOnRegistered'] = $this->helperSumFirstRowByField($results, $field);
		// $rows[$rowIndex]['counterOfFirstDepositedOnRegistered'] = array_sum( array_column($results, 'counterOfFirstDepositedOnRegistered') ) ;
		$field = 'counterOfTotalRegistered';
		$rows[$rowIndex]['counterOfTotalRegistered'] = $this->helperSumFirstRowByField($results, $field);
		// $rows[$rowIndex]['counterOfTotalRegistered'] = array_sum( array_column($results, 'counterOfTotalRegistered') ) ;
		$percentageOfConversionRate = 0;
		if( ! empty($rows[$rowIndex]['counterOfTotalRegistered']) ){
			$percentageOfConversionRate = $rows[$rowIndex]['counterOfFirstDepositedOnRegistered'] / $rows[$rowIndex]['counterOfTotalRegistered'];
		}
		$percentageOfConversionRate = $this->data_tables->percentageFormatter($percentageOfConversionRate);
		$rows[$rowIndex]['percentageOfConversionRate'] = $percentageOfConversionRate;
		// $rows[$rowIndex]['percentageOfConversionRate'] = array_sum( array_column($results, 'percentageOfConversionRate') ) ;
		$rowIndex++; /// the row of Category directPlayer.
		$results['directPlayer'][0]['Category'] = lang('Direct Player'); // replace title of row.
		$rows[$rowIndex] = $results['directPlayer'][0];
		$rowIndex++; /// the row of Category affiliatePlayer.
		$results['affiliatePlayer'][0]['Category'] = lang('Affiliate'); // replace title of row.
		$rows[$rowIndex] = $results['affiliatePlayer'][0];
		$rowIndex++; /// the row of Category agencyPlayer.
		$results['agencyPlayer'][0]['Category'] = lang('Agency'); // replace title of row.
		$rows[$rowIndex] = $results['agencyPlayer'][0];
		$rowIndex++; /// the row of Category refererPlayer.
		$results['refererPlayer'][0]['Category'] = lang('Referrer'); // replace title of row.
		$rows[$rowIndex] = $results['refererPlayer'][0];

		$result = $this->data_tables->_prepareDataForLists($columns, $rows);
		$result['header_data'] = $this->data_tables->get_columns($columns);
		$result['providerMethod'] = __FUNCTION__;
		$result['input'] = $input;
		$result['request'] = $request;
		$result['sqls'] = $sqls;
		$result['benchmarks'] = $benchmarks;

		return $result;
		// $sqls for trace,
	} // EOF conversion_rate_report_summaryByAll

	/**
	 * SUM the $results[xxx][0][$field] for count.
	 *
	 * @param array $results The array for sum, format: $results[xxx][0][$field]
	 * @param string $field The field name.
	 * @return void integer $sum
	 */
	public function helperSumFirstRowByField($results, $field){
		// $field = 'amountOfFirstDepositedOnRegistered';
		$sum = 0;
		foreach($results as $key => $caseResult){
			$sum += $caseResult[0][$field];
		}
		return $sum;
	}// EOF helperSumFirstRowByField

	/**
	 * Get conversion_rate_report of summary By DirectPlayer
	 *
	 *
	 * Check SQL,
	 * - conversionRate.firstDepositCounter: 2301.conversion_rate_report_summaryByDirectPlayer.sql:
	 *
	 * @link datatables, https://datatables.net/reference/option/
	 *
	 * @param boolean|array $extra_search Check called from export. ( entry: Export_data::conversion_rate_report() )
	 *
	 * @param boolean $totalOnly Called from conversion_rate_report_summaryByAll(). For Summary By ALL of reporting.
	 *
	 * @return array $result Base on datatables, and extra elements,
	 * - $result['providerMethod'] This function name for upgrade or debug.
	 * - $result['conversionRate'] The percentage at bottom of list.
	 * - $result['request'] Base on ci::input->post()
	 * - $result['input'] Parsing $request['extra_search'].
	 * - $result['sqls'] The API query SQL(s).
	 * - $result['benchmarks'] The spend time pre query.
	 * - $result['summaryByAll'] for conversion_rate_report_summaryByAll().
	 *
	 */
	public function conversion_rate_report_summaryByDirectPlayer($extra_search = false, $totalOnly = false) {
		$this->setCrrExecutionTime();

		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		if( ! empty($extra_search) ){
			$input = $extra_search;
		}
		$is_export = ! empty($extra_search);

		// enable / disable first_deposit_date_from[to]
		$search_first_deposit_date_switch = 'off'; // default
		if( ! empty($input['search_first_deposit_date_switch']) ){
			$search_first_deposit_date_switch = $input['search_first_deposit_date_switch'];
		}
		if($search_first_deposit_date_switch == 'off'){ // ignore first_deposit_date_from and first_deposit_date_to
			unset($input['first_deposit_date_from']);
			unset($input['first_deposit_date_to']);
		}

		$this->data_tables->options['is_export'] = $is_export;
		// Default clause

		$sqls = []; // for trace SQL.

		$subtotals = array();
		$subtotals['firstDepositAmount'] = 0;

		/// The cols START
		$colIndex = 0;
		$columns = array();
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'username';
		$columns[$colIndex]['select'] = 'username';
		$columns[$colIndex]['name'] = lang('Direct Player Username');
		$columns[$colIndex]['formatter'] = function ($d, $row) {
			return $d;
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'createdOn';
		$columns[$colIndex]['select'] = 'createdOn';
		$columns[$colIndex]['name'] = lang('Registration Time');
		// Format: yyyy-mm-dd HH:MM:SS
		$columns[$colIndex]['formatter'] = 'dateTimeFormatter';
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'firstDepositTime';
		// $columns[$colIndex]['select'] = '`transactions`.`created_at`'; // ver. transactions
		$columns[$colIndex]['select'] = 'first_deposit_datetime'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('First Deposit Time');
		// Format: yyyy-mm-dd HH:MM:SS
		$columns[$colIndex]['formatter'] = 'dateTimeFormatter'; // directly assign to function.
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'firstDepositAmount';
		// $columns[$colIndex]['select'] = '`transactions`.`amount`'; // ver. transactions
		$columns[$colIndex]['select'] = 'first_deposit_amount'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('First Deposit Amount');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){ // 1,234,567.00
			$subtotals['firstDepositAmount'] += $d; // for sub total.
			return $this->data_tables->currencyFormatter($d);
		}; // EOF $columns[$colIndex]['formatter']

		$colIndex++; // just for count player.

		/// disable for OGP-13537 conversion rate report
		// 2. Summary by Direct player on 2019/07/16 -> Fail, the exported report player numbers are wrong, no matter the registered time range setting, the csv always showed all players, and there a column that is not necessary
		// $columns[$colIndex]['dt'] = $colIndex;

		$columns[$colIndex]['alias'] = 'playerId';
		// $columns[$colIndex]['select'] = '`transactions`.`amount`'; // ver. transactions
		$columns[$colIndex]['select'] = 'playerId'; // ver. player_report_hourly
		// $columns[$colIndex]['name'] = lang('First Deposit Amount');
		// $columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){ // 1,234,567.00
		// 	$subtotals['firstDepositAmount'] += $d; // for sub total.
		// 	return $this->data_tables->currencyFormatter($d);
		// }; // EOF $columns[$colIndex]['formatter']
		/// EOF cols

		/// disable for OGP-14393 Enhancement of Conversion Rate Report performance
		// $theField4registration_datetime ='createdOn';
		// $where4registration_datetime = $this->getWhere4registration_datetime($input, $theField4registration_datetime);
		// if( ! empty($where4registration_datetime) ){
		// 	$where4registration_datetime = ' AND '. $where4registration_datetime;
		// }

		// player_report_hourly.registered_date range limited for OGP-14393

		// PRH = player_report_hourly
		// RDT = registration_datetime
		$theFileName='player_report_hourly.registered_date';
		$where4PrhRd = $this->getWhere4registration_datetime($input, $theFileName);

		/// Patch for OGP-14393 Enhancement of Conversion Rate Report performance
		// Get the player who created on date range.
		$date_from_to = $this->convertInput2Date_from_to($input);
		$playerIdList = $this->getPlayerIdWhereCreatedOnRange($date_from_to['date_from'], $date_from_to['date_to']);
		// apply to player_report_hourly WHERE condition
		if( ! empty($playerIdList) ){
			// PRH = player_report_hourly
			$PRHplayerIdsCoditionStr = $this->getPlayerIdCoditionStr($playerIdList, 'player_report_hourly.player_id');
		}else{
			$PRHplayerIdsCoditionStr = 0;
		}

		$theField4first_deposit_datetime ='first_deposit_datetime';
		$where4first_deposit_datetime = $this->getWhere4first_deposit_datetime($input, $theField4first_deposit_datetime);
		if( ! empty($where4first_deposit_datetime) ){
			$where4first_deposit_datetime = ' AND '. $where4first_deposit_datetime;
		}

		/// OGP-14393 Enhancement of Conversion Rate Report performance
		$theField4registration_datetime ='created_on';
		$where4registration_datetime = $this->getWhere4registration_datetime($input, $theField4registration_datetime);
		if( ! empty($where4registration_datetime) ){
			$where4registration_datetime = ' AND '. $where4registration_datetime;
		}
		$table = <<<EOF
		( /* as reporting */
			SELECT player_id as playerId
			, username
			, created_on as createdOn
			, ( /* as first_deposit_amount */
				SELECT first_deposit_amount
				FROM player_relay
				WHERE deleted_at IS NULL
					AND agent_id=0 /* filter multi-registered */
					AND affiliate_id=0 /* filter multi-registered */
					AND referee_player_id=0 /* filter multi-registered */
					AND player_relay_list.player_id = player_relay.player_id
					/* $where4registration_datetime */ /* disable for player_relay_list.player_id = player_relay.player_id */
					$where4first_deposit_datetime
			) as first_deposit_amount
			,( /* as first_deposit_datetime */
				SELECT first_deposit_datetime
				FROM player_relay
				WHERE deleted_at IS NULL
					AND agent_id=0 /* filter multi-registered */
					AND affiliate_id=0 /* filter multi-registered */
					AND referee_player_id=0 /* filter multi-registered */
					AND player_relay_list.player_id = player_relay.player_id
					/* $where4registration_datetime */ /* disable for player_relay_list.player_id = player_relay.player_id */
					$where4first_deposit_datetime
			) as first_deposit_datetime
			FROM player_relay as player_relay_list
			WHERE deleted_at IS NULL
				AND agent_id=0 /* filter multi-registered */
				AND affiliate_id=0 /* filter multi-registered */
				AND referee_player_id=0 /* filter multi-registered */
				$where4registration_datetime
				/* $where4first_deposit_datetime */
		) as reporting
EOF;

/// disable for OGP-14393 Enhancement of Conversion Rate Report performance
// 		$table = <<<EOF
// 		( /* as reporting */
// 			SELECT playerId
// 			, username
// 			, agent_id
// 			, affiliateId
// 			, refereePlayerId
// 			, createdOn
// 			, ( /* as first_deposit_amount */
// 				SELECT first_deposit_amount
// 				FROM ( /* as relay_player_report_hourly4cols */
// 					SELECT player.playerId
// 					, relay_player_report_hourly.first_deposit_amount
// 					, player.affiliateId affiliate_id
// 					, IF(player.affiliateId IS NULL or player.affiliateId = "", "0", player.affiliateId) as int_affiliateId
// 					, player.agent_id
// 					, IF(player.agent_id IS NULL or player.agent_id = "", "0", player.agent_id) as int_agent_id /* convert to int while NULL or Zero.*/
// 					, player.refereePlayerId
// 					, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", "0", player.refereePlayerId) as int_refereePlayerId
// 					FROM player
// 					LEFT OUTER JOIN ( /* as relay_player_report_hourly  */
// 						/* wrapper for last player_report_hourly pre player */
// 						/* START get the last record of the player */
// 						SELECT player_report_hourly.first_deposit_amount
// 						, player_report_hourly.first_deposit_datetime
// 						, player_report_hourly.player_id
// 						, IF(player_report_hourly.agent_id IS NULL or player_report_hourly.agent_id = "", "0", player_report_hourly.agent_id) as int_agent_id
// 						, IF(player_report_hourly.affiliate_id IS NULL or player_report_hourly.affiliate_id = "", "0", player_report_hourly.affiliate_id) as int_affiliate_id
// 						FROM player_report_hourly
// 						INNER JOIN ( /* as last_update_player_report_hourly */
// 							SELECT max(id) as max_id /* get the lastest reocrd for the player.*/
// 							, player_report_hourly.player_id
// 							FROM player_report_hourly
// 							WHERE $PRHplayerIdsCoditionStr  /* Patch for OGP-14393 */
// 								AND first_deposit_amount > 0 /* Patch for OGP-14393 */
// 								AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 								AND (affiliate_id IS NULL OR affiliate_id = "") /* filter multi-registered */
// 								$where4first_deposit_datetime /* Patch for OGP-14393 */
// 							GROUP by player_id
// 						)as last_update_player_report_hourly
// 						ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 						AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 						WHERE first_deposit_amount > 0 /* Patch for OGP-14393 */
// 						$where4first_deposit_datetime /* Patch for OGP-14393 */
// 						/* EOF get the last record of the player */
// 					) as relay_player_report_hourly ON playerId = relay_player_report_hourly.player_id
// 															AND relay_player_report_hourly.first_deposit_amount > 0
// 					WHERE player.deleted_at IS NULL
// 					AND relay_player_report_hourly.first_deposit_amount > 0
// 					AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 					AND (player.affiliateId IS NULL OR player.affiliateId = "") /* filter multi-registered */
// 					AND (refereePlayerId IS NULL OR refereePlayerId = "") /* filter multi-registered */
// 					$where4registration_datetime
// 					$where4first_deposit_datetime
// 				) as relay_player_report_hourly4cols
// 				WHERE 0 = relay_player_report_hourly4cols.int_affiliateId /* filter multi-registered */
// 				AND 0 = relay_player_report_hourly4cols.int_agent_id /* filter multi-registered */
// 				AND 0 = relay_player_report_hourly4cols.int_refereePlayerId /* filter multi-registered */
// 				AND player4reporting.playerId = relay_player_report_hourly4cols.playerId
// 			) as first_deposit_amount
// 			, ( /* as first_deposit_datetime */
// 				SELECT first_deposit_datetime
// 				FROM ( /* as relay_player_report_hourly4cols */
// 					SELECT player.playerId
// 					, relay_player_report_hourly.first_deposit_datetime
// 					, player.affiliateId affiliate_id
// 					, IF(player.affiliateId IS NULL or player.affiliateId = "", "0", player.affiliateId) as int_affiliateId
// 					, player.agent_id
// 					, IF(player.agent_id IS NULL or player.agent_id = "", "0", player.agent_id) as int_agent_id /* convert to int while NULL or Zero.*/
// 					, player.refereePlayerId
// 					, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", "0", player.refereePlayerId) as int_refereePlayerId
// 					FROM player
// 					LEFT OUTER JOIN ( /* as relay_player_report_hourly  */
// 						/* wrapper for last player_report_hourly pre player */
// 						/* START get the last record of the player */
// 						SELECT player_report_hourly.first_deposit_amount
// 						, player_report_hourly.first_deposit_datetime
// 						, player_report_hourly.player_id
// 						, IF(player_report_hourly.agent_id IS NULL or player_report_hourly.agent_id = "", 0, player_report_hourly.agent_id) as int_agent_id
// 						, IF(player_report_hourly.affiliate_id IS NULL or player_report_hourly.affiliate_id = "", 0, player_report_hourly.affiliate_id) as int_affiliate_id
// 						FROM player_report_hourly
// 						INNER JOIN ( /* as last_update_player_report_hourly */
// 							SELECT max(id) as max_id /* get the lastest reocrd for the player.*/
// 							, player_report_hourly.player_id
// 							FROM player_report_hourly
// 							WHERE $PRHplayerIdsCoditionStr  /* Patch for OGP-14393 */
// 								AND first_deposit_amount > 0 /* Patch for OGP-14393 */
// 								AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 								AND (affiliate_id IS NULL OR affiliate_id = "") /* filter multi-registered */
// 								$where4first_deposit_datetime /* Patch for OGP-14393 */
// 							GROUP by player_id
// 						)as last_update_player_report_hourly
// 						ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 							AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 						WHERE first_deposit_amount > 0 /* Patch for OGP-14393 */
// 						$where4first_deposit_datetime /* Patch for OGP-14393 */
// 						/* EOF get the last record of the player */
// 					) as relay_player_report_hourly ON playerId = relay_player_report_hourly.player_id
// 															AND relay_player_report_hourly.first_deposit_amount > 0
// 					WHERE player.deleted_at IS NULL
// 					AND relay_player_report_hourly.first_deposit_amount > 0
// 					AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 					AND (player.affiliateId IS NULL OR player.affiliateId = "") /* filter multi-registered */
// 					AND (refereePlayerId IS NULL OR refereePlayerId = "") /* filter multi-registered */
// 					$where4registration_datetime
// 					$where4first_deposit_datetime
// 				) as relay_player_report_hourly4cols
// 				WHERE 0 = relay_player_report_hourly4cols.int_affiliateId /* filter multi-registered */
// 				AND 0 = relay_player_report_hourly4cols.int_agent_id /* filter multi-registered */
// 				AND 0 = relay_player_report_hourly4cols.int_refereePlayerId /* filter multi-registered */
// 				AND player4reporting.playerId = relay_player_report_hourly4cols.playerId
// 			 )as first_deposit_datetime
// 			FROM player as player4reporting
// 		) as reporting
// EOF;
		$table = $this->data_tables->clearBlankSpaceAfterHeredoc($table);

		$where = array();

		$theField4registration_datetime ='createdOn';
		$where[] = $this->getWhere4registration_datetime($input, $theField4registration_datetime);

		$values = array();
		$joins = array();
		$group_by = array();
		$having = array();
		$distinct = false;
		$external_order=[];
		$notDatatable = '';
		$countOnlyField = 'playerId';
		$innerJoins=[];
		$useIndex=[];

		$markName = 'list';
		$this->utils->markProfilerStart($markName); // list mark.
		// for count records contain "group by" and "having".
		$this->config->set_item('debug_data_table_sql', true);
		if( $totalOnly ) {
			$result = $this->data_tables->empty_data($request);
		}else{
			$result = $this->data_tables->get_data($request // #1
								, $columns // #2
								, $table // #3
								, $where // #4
								, $values // #5
								, $joins // #6
								, $group_by // #7
								, $having  // #8
								, $distinct // #9
								, $external_order // #10
								, $notDatatable // #11
								, $countOnlyField // #12
								, $innerJoins // #13
								, $useIndex // #14
							);
		}

		$sql = $this->data_tables->last_query; // for trace
		$this->utils->markProfilerEndAndPrint($markName, $benchmarks[$markName]); // total mark.
		$sqls[$markName] = $sql; // list mark.

		if( ! $is_export ){ // hidden the row in view, kw:"subTotalCol".
			/// subTotal append to $result['data'], rows.
			// MUST before getMultipleRowArray().Because getMultipleRowArray() will formatter data.
			$subTotalRowsIndex = count( $result['data'] );
			$result['data'][$subTotalRowsIndex][0] = '<span class="th subTotalCol">'.lang('Sub Total').'</span>';
			$result['data'][$subTotalRowsIndex][1] = '';
			$result['data'][$subTotalRowsIndex][2] = '';
			$result['data'][$subTotalRowsIndex][3] = '<span class="th subTotalCol">'.$this->data_tables->currencyFormatter($subtotals['firstDepositAmount']).'</span>';
		}

		// for Total, get orig SQL and sum(amount) from that.
		$this->data_tables->options['only_sql'] = true;
		// $request['start']
		$request['length'] = -1; // for ignore LIMIT.
		$result4sql = $this->data_tables->get_data($request // #1
										, $columns // #2
										, $table // #3
										, $where // #4
										, $values // #5
										, $joins // #6
										, $group_by // #7
										, $having  // #8
										, $distinct // #9
										, $external_order // #10
										, $notDatatable // #11
										, $countOnlyField // #12
										, $innerJoins // #13
										, $useIndex // #14
									);
// $this->utils->debug_log('1984.conversion_rate_report_summaryByDirectPlayer.result4sql:', $result4sql);
		$this->data_tables->options['only_sql'] = false;
		$tmpSQL = $result4sql['data'];

		$summary = [];
		$markName = 'total2calcRows'; // total mark.
		$this->utils->markProfilerStart($markName); // total mark.
		$result4Total2calcRows = $readOnlyDB->query( $tmpSQL );
		$this->utils->markProfilerEndAndPrint($markName, $benchmarks[$markName]); // total mark.

		$total2calcRows = $this->getMultipleRowArray($result4Total2calcRows);
		if( ! empty($total2calcRows) ){ // for NULL
			$totalFirstDepositedAmount = 0;
			$totalFirstDepositPlayers = 0;
			$totalRegisteredPlayers = 0;

			foreach($total2calcRows as $indexNumber => $result4Total2calcRow){
				$totalFirstDepositedAmount += $result4Total2calcRow['firstDepositAmount'];

				if($result4Total2calcRow['firstDepositAmount'] > 0){
					$totalFirstDepositPlayers++;
				}
				$totalRegisteredPlayers++;
			}
			$summary[0]['totalRegisteredPlayers'] = $totalRegisteredPlayers;
			$summary[0]['totalFirstDepositedAmount'] = $totalFirstDepositedAmount;
			$summary[0]['totalFirstDepositPlayers'] = $totalFirstDepositPlayers;
			$counterHasFirstDeposit[0]['totalFirstDepositPlayers']= $totalFirstDepositPlayers;
		}else{
			$summary[0]['totalRegisteredPlayers'] = 0;
			$summary[0]['totalFirstDepositedAmount'] = 0;
			$summary[0]['totalFirstDepositPlayers']=0;
		}

		$conversionRate = 0;
		$summary[0]['totalFirstDepositPlayers'] = 0;
		$summary[0]['conversionRate']= 0;
		if( ! empty($summary[0]['totalRegisteredPlayers']) ){
			$conversionRate = $counterHasFirstDeposit[0]['totalFirstDepositPlayers'] / $summary[0]['totalRegisteredPlayers'];
			// append totalFirstDepositPlayers,
			$summary[0]['totalFirstDepositPlayers'] = $counterHasFirstDeposit[0]['totalFirstDepositPlayers'];
			$summary[0]['conversionRate'] = $conversionRate;
		}
		$result['conversionRate'] = $this->data_tables->percentageFormatter($conversionRate);

		$result['providerMethod'] = __FUNCTION__;
		$result['input'] = $input;
		$result['request'] = $request;
		$result['sqls'] = $sqls;
		$result['benchmarks'] = $benchmarks;
		$result['summaryByAll'] = $summary[0]; // for conversion_rate_report_summaryByAll().
		return $result;
		// $sqls for trace,
	} // EOF conversion_rate_report_summaryByDirectPlayer
	/**
	 * Get conversion_rate_report of summary By Affiliate
	 *
	 *
	 * Check SQL,
	 * - list: The list of reporting.
	 * - total: The total row at bottom of reporting.
	 *
	 * @link datatables, https://datatables.net/reference/option/
	 *
	 * @param boolean|array $extra_search Check called from export. ( entry: Export_data::conversion_rate_report() )
	 *
	 * @param boolean $totalOnly Called from conversion_rate_report_summaryByAll(). For Summary By ALL of reporting.
	 *
	 * @return array $result Base on datatables, and extra elements,
	 * - $result['providerMethod'] This function name for upgrade or debug.
	 * - $result['conversionRate'] The percentage at bottom of list.
	 * - $result['request'] Base on ci::input->post()
	 * - $result['input'] Parsing $request['extra_search'].
	 * - $result['sqls'] The API query SQL(s).
	 * - $result['benchmarks'] The spend time pre query.
	 * - $result['summaryByAll'] for conversion_rate_report_summaryByAll().
	 *
	 */
	public function conversion_rate_report_summaryByAffiliate($extra_search = false, $totalOnly = false) {
		$this->setCrrExecutionTime();

		// $this->db->select('affiliateId')->from('affiliates')->where('username', $affiliate_username);
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		if( ! empty($extra_search) ){
			$input = $extra_search;
		}
		$is_export = ! empty($extra_search);

		$sqls = []; // for trace
		$benchmarks = [];

		// enable / disable first_deposit_date_from[to]
		$search_first_deposit_date_switch = 'off'; // default
		if( ! empty($input['search_first_deposit_date_switch']) ){
			$search_first_deposit_date_switch = $input['search_first_deposit_date_switch'];
		}
		if($search_first_deposit_date_switch == 'off'){ // ignore first_deposit_date_from and first_deposit_date_to
			unset($input['first_deposit_date_from']);
			unset($input['first_deposit_date_to']);
		}

		$this->data_tables->options['is_export'] = $is_export;

		$subtotals = array();
		$subtotals['conversionRate'] = 0;
		$subtotals['totalRegisteredPlayers'] = 0;
		$subtotals['totalFirstDepositPlayers'] = 0;
		$subtotals['totalFirstDepositedAmount'] = 0;

		/// The cols START
		$colIndex = 0;
		$columns = array();
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'affiliateUsername';
		$columns[$colIndex]['select'] = 'username';
		$columns[$colIndex]['name'] = lang('Affiliate Username');
		$columns[$colIndex]['formatter'] = function ($d, $row) {
			return $d;
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'totalRegisteredPlayers';
		$columns[$colIndex]['select'] = 'totalRegisteredPlayers';
		$columns[$colIndex]['name'] = lang('Total Registered Players');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ) {
			$subtotals['totalRegisteredPlayers'] += $d; // for sub total.
			return $d;
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'totalFirstDepositPlayers';
		// $columns[$colIndex]['select'] = '`transactions`.`created_at`'; // ver. transactions
		$columns[$colIndex]['select'] = 'totalFirstDepositPlayers'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('Total First Deposited Player');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ) {
			$subtotals['totalFirstDepositPlayers'] += $d; // for sub total.
			return $d;
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'conversionRate';
		// $columns[$colIndex]['select'] = '`transactions`.`amount`'; // ver. transactions
		$columns[$colIndex]['select'] = 'totalFirstDepositPlayers/totalRegisteredPlayers'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('Conversion Rate');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){ // 1,234,567.00
			$subtotals['conversionRate'] += $d; // for sub total.
			return $this->data_tables->percentageFormatter($d);
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'totalFirstDepositedAmount';
		// $columns[$colIndex]['select'] = '`transactions`.`amount`'; // ver. transactions
		$columns[$colIndex]['select'] = 'totalFirstDepositedAmount'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('Total First Deposit Amount');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){ // 1,234,567.00
			$subtotals['totalFirstDepositedAmount'] += $d; // for sub total.
			return $this->data_tables->currencyFormatter($d);
		}; // EOF $columns[$colIndex]['formatter']
		/// EOF cols


		/// Patch for OGP-14393 Enhancement of Conversion Rate Report performance
		// Get the player who created on date range.
		$date_from_to = $this->convertInput2Date_from_to($input);
		$playerIdList = $this->getPlayerIdWhereCreatedOnRange($date_from_to['date_from'], $date_from_to['date_to']);
		// apply to player_report_hourly WHERE condition
		if( ! empty($playerIdList) ){
			// PRH = player_report_hourly
			$PRHplayerIdsCoditionStr = $this->getPlayerIdCoditionStr($playerIdList, 'player_report_hourly.player_id');
		}else{
			$PRHplayerIdsCoditionStr = 0;
		}

		$theField4registration_datetime ='createdOn';
		$where4registration_datetime = $this->getWhere4registration_datetime($input, $theField4registration_datetime);
		if( ! empty($where4registration_datetime) ){
			$where4registration_datetime = ' AND '. $where4registration_datetime;
		}

		$theField4first_deposit_datetime ='first_deposit_datetime';
		$where4first_deposit_datetime = $this->getWhere4first_deposit_datetime($input, $theField4first_deposit_datetime);
		if( ! empty($where4first_deposit_datetime) ){
			$where4first_deposit_datetime = ' AND '. $where4first_deposit_datetime;
		}

		/// disable for OGP-14393 Enhancement of Conversion Rate Report performance
		// $where4affiliate_username = 'totalRegisteredPlayers > 0'; // for ignore row while affiliate_username of search field is empty.
		// if( ! empty($input['affiliate_username']) ){
		// 	$where4affiliate_username = ' username = "'. $input['affiliate_username']. '" ';
		// }

		$this->load->model(array('affiliatemodel'));
		$PRHaffiliateIdCoditionStr = '';
		$affiliatesAffiliateIdCoditionStr = '';
		if( ! empty($input['affiliate_username']) ){
			$affiliateId = $this->affiliatemodel->getAffiliateIdByUsername( $input['affiliate_username'] );
			$PRHaffiliateIdCoditionStr = ' AND affiliate_id = "'. $affiliateId. '"';

			$affiliatesAffiliateIdCoditionStr = ' AND affiliateId = "'. $affiliateId. '"';
		}


		// totalRegisteredPlayers : 註冊人數
		// totalFirstDepositPlayers : 首存人數
		// totalFirstDepositedAmount : 首存金额

		//filter multi-registered: agent_id,affiliateId and refereePlayerId.

		/// disable for OGP-14393 Enhancement of Conversion Rate Report performance
		// $idCondStr = '1';// @rbo =remove before online

		/// OGP-14393 Enhancement of Conversion Rate Report performance
		// ----- ver. player_relay
		$where4affiliate_username = ''; // for ignore row while affiliate_username of search field is empty.
		if( ! empty($input['affiliate_username']) ){
			$where4affiliate_username = ' AND  username = "'. $input['affiliate_username'].'" ';
		}else{
			$where4affiliate_username = '';
		}
		$theField4registration_datetime ='created_on';
		$where4registration_datetime = $this->getWhere4registration_datetime($input, $theField4registration_datetime);
		if( ! empty($where4registration_datetime) ){
			$where4registration_datetime = ' AND '. $where4registration_datetime;
		}
		/// filter agency that without player
		$dofilter4totalRegisteredPlayers = false;
		if( ! empty($input['affiliate_username']) ){
		}else{
			$dofilter4totalRegisteredPlayers = true;
		}
		$idCondStr = '1';
		if($dofilter4totalRegisteredPlayers){ // get registration_datetime condition.
			$sql = <<<EOF
			SELECT affiliate_id
			FROM player_relay
			WHERE deleted_at IS NULL
				AND 0 = player_relay.agent_id /* filter multi-registered */
				AND 0 = player_relay.referee_player_id /* filter multi-registered */
				$where4registration_datetime
			GROUP BY affiliate_id
EOF;
			$result = $this->db->query($sql);
			$rows = $this->getMultipleRowArray($result);
			if( ! empty($rows) ){
				$idCond = [];
				foreach($rows as $keyNumber => $row){
					$tmp4id =$row['affiliate_id'];
					$idCond[] =<<<EOF
					affiliateId = "$tmp4id"
EOF;
 // ref. affiliateList.affiliateId
				}
				$idCondStr = ' ( '. implode(' OR ', $idCond). ' ) ';
			}
		}
		$table = <<<EOF
		( 	/* as reporting */
			SELECT affiliateList.affiliateId
				, affiliateList.username
				,( /* as totalRegisteredPlayers */
					SELECT COUNT( player_id) as counter
					FROM player_relay
					WHERE player_relay.deleted_at IS NULL
					AND affiliateList.affiliateId = player_relay.affiliate_id
					AND 0 = player_relay.agent_id /* filter multi-registered */
					AND 0 = player_relay.referee_player_id /* filter multi-registered */
					$where4registration_datetime
					/* disable for B3.1, where4first_deposit_datetime $where4first_deposit_datetime EOF where4first_deposit_datetime */
				) as totalRegisteredPlayers
				,( /* as totalFirstDepositPlayers */
					SELECT COUNT( player_id) as counter
					FROM player_relay
					WHERE player_relay.deleted_at IS NULL
					AND player_relay.first_deposit_amount > 0
					AND affiliateList.affiliateId = player_relay.affiliate_id
					AND 0 = player_relay.agent_id /* filter multi-registered */
					AND 0 = player_relay.referee_player_id /* filter multi-registered */
					$where4registration_datetime
					$where4first_deposit_datetime
				) as totalFirstDepositPlayers
				,( /* as totalFirstDepositedAmount */
					SELECT SUM( first_deposit_amount ) as amount
					FROM player_relay
					WHERE player_relay.deleted_at IS NULL
					AND player_relay.first_deposit_amount > 0
					AND affiliateList.affiliateId = player_relay.affiliate_id
					AND 0 = player_relay.agent_id /* filter multi-registered */
					AND 0 = player_relay.referee_player_id /* filter multi-registered */
					$where4registration_datetime
					$where4first_deposit_datetime
				) as totalFirstDepositedAmount
			FROM affiliates as affiliateList
			WHERE $idCondStr

			$where4affiliate_username /* Patch for OGP-14393 */
		) as reporting
EOF;
/// disable for OGP-14393 Enhancement of Conversion Rate Report performance
// 		// ----- ver. player_report_hourly
// 		$table = <<<EOF
// 		( 	/* as reporting */
// 			SELECT *
// 			FROM ( /* as relay_affiliates_reporting */
// 				SELECT affiliateList.affiliateId
// 				, affiliateList.username
// 				,( /* as totalRegisteredPlayers */
// 					SELECT COUNT( playerId) as counter
// 					FROM ( /* as relay_player_report_hourly4cols */
// 						SELECT player.playerId
// 						, IF(player.agent_id IS NULL or player.agent_id = "", 0, player.agent_id) as int_agent_id
// 						, IF(player.affiliateId IS NULL or player.affiliateId = "", 0, player.affiliateId) as int_affiliateId
// 						, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", 0, player.refereePlayerId) as int_refereePlayerId
// 						FROM player
// 						LEFT OUTER JOIN ( /* as relay_player_report_hourly  */
// 							/* wrapper for last player_report_hourly pre player */
// 							/* START get the last record of the player */
// 							SELECT player_report_hourly.player_id
// 							, player_report_hourly.first_deposit_amount
// 							, player_report_hourly.first_deposit_datetime
// 							, IF(player_report_hourly.agent_id IS NULL or player_report_hourly.agent_id = "", 0, player_report_hourly.agent_id) as int_agent_id
// 							, IF(player_report_hourly.affiliate_id IS NULL or player_report_hourly.affiliate_id = "", 0, player_report_hourly.affiliate_id) as int_affiliate_id
// 							FROM player_report_hourly
// 							INNER JOIN (
// 								SELECT max(id) as max_id /* get the lastest reocrd for the player.*/
// 								, player_report_hourly.player_id
// 								FROM player_report_hourly
// 								WHERE $PRHplayerIdsCoditionStr  /* Patch for OGP-14393 */
// 								AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 								$PRHaffiliateIdCoditionStr /* Patch for OGP-14393 */
// 								GROUP by player_id
// 							)as last_update_player_report_hourly ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 																AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 							/* EOF get the last record of the player */
// 						) as relay_player_report_hourly ON playerId = relay_player_report_hourly.player_id
// 						WHERE player.deleted_at IS NULL
// 						AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 						/* where4registration_datetime */ $where4registration_datetime /* EOF where4registration_datetime */
// 						/* disable for B3.1, where4first_deposit_datetime $where4first_deposit_datetime EOF where4first_deposit_datetime */
// 					) as relay_player_report_hourly4cols
// 					WHERE affiliateList.affiliateId = relay_player_report_hourly4cols.int_affiliateId
// 					AND 0 = relay_player_report_hourly4cols.int_agent_id /* filter multi-registered */
// 					AND 0 = relay_player_report_hourly4cols.int_refereePlayerId /* filter multi-registered */
//
// 				) as totalRegisteredPlayers
// 				,( /* as totalFirstDepositPlayers */
// 					SELECT COUNT( playerId ) as counter
// 					FROM ( /* relay_player_report_hourly4cols */
// 						SELECT player.playerId
// 						, IF(player.agent_id IS NULL or player.agent_id = "", 0, player.agent_id) as int_agent_id
// 						, IF(player.affiliateId IS NULL or player.affiliateId = "", 0, player.affiliateId) as int_affiliateId
// 						, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", 0, player.refereePlayerId) as int_refereePlayerId
// 						FROM player
// 						LEFT OUTER JOIN ( /* as relay_player_report_hourly  */
// 							/* wrapper for last player_report_hourly pre player */
// 							/* START get the last record of the player */
// 							SELECT player_report_hourly.player_id
// 							, player_report_hourly.first_deposit_amount
// 							, player_report_hourly.first_deposit_datetime
// 							, IF(player_report_hourly.agent_id IS NULL or player_report_hourly.agent_id = "", "0", player_report_hourly.agent_id) as int_agent_id
// 							, IF(player_report_hourly.affiliate_id IS NULL or player_report_hourly.affiliate_id = "", "0", player_report_hourly.affiliate_id) as int_affiliate_id
// 							FROM player_report_hourly
// 							INNER JOIN (
// 								SELECT max(id) as max_id /* get the lastest reocrd for the player.*/
// 								, player_report_hourly.player_id
// 								FROM player_report_hourly
// 								WHERE $PRHplayerIdsCoditionStr  /* Patch for OGP-14393 */
// 								AND first_deposit_amount > 0 /* Patch for OGP-14393 */
// 								AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 								$where4first_deposit_datetime /* Patch for OGP-14393 */
// 								$PRHaffiliateIdCoditionStr /* Patch for OGP-14393 */
// 								GROUP by player_id
// 							)as last_update_player_report_hourly ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 																AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 							WHERE first_deposit_amount > 0
// 							$where4first_deposit_datetime /* Patch for OGP-14393 */
// 							/* EOF get the last record of the player */
// 						) as relay_player_report_hourly ON playerId = relay_player_report_hourly.player_id
// 														AND relay_player_report_hourly.first_deposit_amount > 0
// 														$where4first_deposit_datetime /* Patch for OGP-14393 */
// 						WHERE player.deleted_at IS NULL
// 						AND relay_player_report_hourly.first_deposit_amount > 0
// 						AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 						$where4registration_datetime
// 						$where4first_deposit_datetime
// 					) as relay_player_report_hourly4cols
// 					WHERE affiliateList.affiliateId = int_affiliateId
// 					AND 0 = relay_player_report_hourly4cols.int_agent_id /* filter multi-registered */
// 					AND 0 = relay_player_report_hourly4cols.int_refereePlayerId /* filter multi-registered */
// 				) as totalFirstDepositPlayers
// 				,( /* as totalFirstDepositedAmount */
// 					SELECT SUM( first_deposit_amount ) as amount
// 					FROM ( /* as relay_player_report_hourly4cols */
// 						SELECT player.playerId
// 						, relay_player_report_hourly.first_deposit_amount
// 						, player.affiliateId affiliate_id
// 						, IF(player.affiliateId IS NULL or player.affiliateId = "", 0, player.affiliateId) as int_affiliateId
// 						, player.agent_id
// 						, IF(player.agent_id IS NULL or player.agent_id = "", 0, player.agent_id) as int_agent_id /* convert to int while NULL or Zero.*/
// 						, player.refereePlayerId
// 						, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", 0, player.refereePlayerId) as int_refereePlayerId
// 						FROM player
// 						LEFT OUTER JOIN ( /* as relay_player_report_hourly  */
// 							/* wrapper for last player_report_hourly pre player */
// 							/* START get the last record of the player */
// 							SELECT player_report_hourly.player_id
// 							, player_report_hourly.first_deposit_amount
// 							, player_report_hourly.first_deposit_datetime
// 							, IF(player_report_hourly.agent_id IS NULL or player_report_hourly.agent_id = "", 0, player_report_hourly.agent_id) as int_agent_id
// 							, IF(player_report_hourly.affiliate_id IS NULL or player_report_hourly.affiliate_id = "", 0, player_report_hourly.affiliate_id) as int_affiliate_id
// 							FROM player_report_hourly
// 							INNER JOIN ( /* as last_update_player_report_hourly */
// 								SELECT max(id) as max_id /* get the lastest reocrd for the player.*/
// 								, player_report_hourly.player_id
// 								FROM player_report_hourly
// 								WHERE $PRHplayerIdsCoditionStr  /* Patch for OGP-14393 */
// 								AND first_deposit_amount > 0 /* Patch for OGP-14393 */
// 								AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 								$where4first_deposit_datetime /* Patch for OGP-14393 */
// 								$PRHaffiliateIdCoditionStr /* Patch for OGP-14393 */
// 								GROUP by player_id
// 							)as last_update_player_report_hourly ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 																AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 							WHERE first_deposit_amount > 0 /* Patch for OGP-14393 */
// 							$where4first_deposit_datetime /* Patch for OGP-14393 */
// 							/* EOF get the last record of the player */
// 						) as relay_player_report_hourly ON playerId = relay_player_report_hourly.player_id
// 																AND relay_player_report_hourly.first_deposit_amount > 0
// 																$where4first_deposit_datetime /* Patch for OGP-14393 */
// 						WHERE player.deleted_at IS NULL
// 						AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 						AND relay_player_report_hourly.first_deposit_amount > 0
// 						$where4registration_datetime
// 						$where4first_deposit_datetime
// 					) as relay_player_report_hourly4cols
// 					WHERE affiliateList.affiliateId = int_affiliateId
// 					AND 0 = relay_player_report_hourly4cols.int_agent_id /* filter multi-registered */
// 					AND 0 = relay_player_report_hourly4cols.int_refereePlayerId /* filter multi-registered */
// 				) as totalFirstDepositedAmount
// 				FROM affiliates as affiliateList
// 				WHERE 1 $affiliatesAffiliateIdCoditionStr /* Patch for OGP-14393 */
// 			) as relay_affiliates_reporting
// 			WHERE $where4affiliate_username
// 		) as reporting
// EOF;

		$table = $this->data_tables->clearBlankSpaceAfterHeredoc($table);

		$where = array();
		$values = array();
		$joins = array();
		$group_by = array();
		$having = array();
		$distinct = false;
		$external_order=[];
		$notDatatable = '';
		$countOnlyField = 'affiliateId';
		$innerJoins=[];
		$useIndex=[];

		$markName = 'list';
		$this->utils->markProfilerStart($markName); // list mark.
		$this->config->set_item('debug_data_table_sql', true);

		if( $totalOnly ) {// from SummaryByAll
			$result = $this->data_tables->empty_data($request);
		}else{
			$result = $this->data_tables->get_data($request // #1
						, $columns // #2
						, $table // #3
						, $where // #4
						, $values // #5
						, $joins // #6
						, $group_by // #7
						, $having  // #8
						, $distinct // #9
						, $external_order // #10
						, $notDatatable // #11
						, $countOnlyField // #12
						, $innerJoins // #13
						, $useIndex // #14
					);
		}

		$sql = $this->data_tables->last_query;
		$this->utils->markProfilerEndAndPrint($markName, $benchmarks[$markName]); // total mark.

		$sqls[$markName] = $sql; // list mark.

		if( ! $is_export ){
			/// subTotal append to $result['data'], rows.
			// DONT append before getMultipleRowArray().because getMultipleRowArray() will formatter data.
			$subTotalRowsIndex = count( $result['data'] );
			$result['data'][$subTotalRowsIndex][0] = '<span class="th subTotalCol">'.lang('Sub Total').'</span>';
			$result['data'][$subTotalRowsIndex][1] = '<span class="th subTotalCol">'.$this->data_tables->defaultFormatter($subtotals['totalRegisteredPlayers']).'</span>';
			$result['data'][$subTotalRowsIndex][2] = '<span class="th subTotalCol">'.$this->data_tables->defaultFormatter($subtotals['totalFirstDepositPlayers']).'</span>';
			$conversionRate = 0;
			if( $subtotals['totalRegisteredPlayers'] > 0 ){
				$conversionRate = $subtotals['totalFirstDepositPlayers']/$subtotals['totalRegisteredPlayers'];
			}
			$result['data'][$subTotalRowsIndex][3] = '<span class="th subTotalCol">'.$this->data_tables->percentageFormatter($conversionRate).'</span>';
			$result['data'][$subTotalRowsIndex][4] = '<span class="th subTotalCol">'.$this->data_tables->currencyFormatter($subtotals['totalFirstDepositedAmount']).'</span>';
		}

		// for Total, get orig SQL and sum(amount) from that.
		// $this->config->set_item('debug_data_table_sql', true);
		$this->data_tables->options['only_sql'] = true;
		// $request['start']
		$request['length'] = -1; // for ignore LIMIT.
		$result4sql = $this->data_tables->get_data($request // #1
										, $columns // #2
										, $table // #3
										, $where // #4
										, $values // #5
										, $joins // #6
										, $group_by // #7
										, $having  // #8
										, $distinct // #9
										, $external_order // #10
										, $notDatatable // #11
										, $countOnlyField // #12
										, $innerJoins // #13
										, $useIndex // #14
									);
		$this->data_tables->options['only_sql'] = false;
		$sql4total = $result4sql['data'];
		$sql = <<<EOF
		SELECT /* summaryByAffiliate */ SUM(totalRegisteredPlayers) as totalRegisteredPlayers
		, SUM(totalFirstDepositPlayers) as totalFirstDepositPlayers
		/* , SUM(totalFirstDepositPlayers)/SUM(totalRegisteredPlayers) as conversionRate */
		, SUM(totalFirstDepositedAmount) as totalFirstDepositedAmount
		FROM ($sql4total) as total;
EOF;
		$sql = $this->data_tables->clearBlankSpaceAfterHeredoc($sql);

		$markName = 'total'; // total mark.
		$this->utils->markProfilerStart($markName); // total mark.
		if( $totalOnly || true) {// from SummaryByAll - SummaryByAffiliate Need
			$result4Total = $readOnlyDB->query( $sql );
			$summary = $this->getMultipleRowArray($result4Total);
		}else{
			$summary = array();
		}
		$this->utils->markProfilerEndAndPrint($markName, $benchmarks[$markName]); // total mark.

		$sqls[$markName] = $sql; // total mark.

		// for Summary by ALL display Zero while NULL.
		if( empty($summary[0]['totalRegisteredPlayers']) ){ // for NULL
			$summary[0]['totalRegisteredPlayers'] = 0;
		}
		if( empty($summary[0]['totalFirstDepositPlayers']) ){ // for NULL
			$summary[0]['totalFirstDepositPlayers'] = 0;
		}
		if( empty($summary[0]['totalFirstDepositedAmount']) ){ // for NULL
			$summary[0]['totalFirstDepositedAmount'] = 0;
		}

		if( ! empty($summary[0]['totalRegisteredPlayers']) ){
			$summary[0]['conversionRate'] = $summary[0]['totalFirstDepositPlayers'] / $summary[0]['totalRegisteredPlayers'];
		}else{
			$summary[0]['conversionRate'] = 0;
		}

		// $this->utils->debug_log('1992.conversion_rate_report_summaryByDirectPlayer.summary:', $summary);
		$totalRowsIndex = count( $result['data'] );
		$result['data'][$totalRowsIndex][0] = '<span class="th totalCol">'.lang('Total').'</span>';
		$result['data'][$totalRowsIndex][1] = '<span class="th totalCol">'.$this->data_tables->defaultFormatter($summary[0]['totalRegisteredPlayers']).'</span>';
		$result['data'][$totalRowsIndex][2] = '<span class="th totalCol">'.$this->data_tables->defaultFormatter($summary[0]['totalFirstDepositPlayers']).'</span>';
		$result['data'][$totalRowsIndex][3] = '<span class="th totalCol">'.$this->data_tables->percentageFormatter($summary[0]['conversionRate']).'</span>';
		$result['data'][$totalRowsIndex][4] = '<span class="th totalCol">'.$this->data_tables->currencyFormatter($summary[0]['totalFirstDepositedAmount']).'</span>';

		if( $is_export ){ //  remove tags of html while export.
			foreach($result['data'][$totalRowsIndex] as $key => $val){
				$result['data'][$totalRowsIndex][$key] = strip_tags($val);
			}
		}

		$result['providerMethod'] = __FUNCTION__;
		$result['input'] = $input;
		$result['request'] = $request;
		$result['sqls'] = $sqls;
		$result['benchmarks'] = $benchmarks;
		$result['summaryByAll'] = $summary[0]; // for conversion_rate_report_summaryByAll().
		return $result;
		// $sqls for trace,

	} // EOF conversion_rate_report_summaryByAffiliate
	/**
	 * Get conversion_rate_report of summary By Agency
	 *
	 *
	 * Check SQL,
	 * - list: The list of reporting.
	 * - total: The total row at bottom of reporting.
	 *
	 * @link datatables, https://datatables.net/reference/option/
	 *
	 * @param boolean|array $extra_search Check called from export. ( entry: Export_data::conversion_rate_report() )
	 *
	 * @param boolean $totalOnly Called from conversion_rate_report_summaryByAll(). For Summary By ALL of reporting.
	 *
	 * @return array $result Base on datatables, and extra elements,
	 * - $result['providerMethod'] This function name for upgrade or debug.
	 * - $result['conversionRate'] The percentage at bottom of list.
	 * - $result['request'] Base on ci::input->post()
	 * - $result['input'] Parsing $request['extra_search'].
	 * - $result['sqls'] The API query SQL(s).
	 * - $result['benchmarks'] The spend time pre query.
	 * - $result['summaryByAll'] for conversion_rate_report_summaryByAll().
	 *
	 */
	public function conversion_rate_report_summaryByAgency( $extra_search = false, $totalOnly = false) {

		$result4SCET = $this->setCrrExecutionTime();

		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		if( ! empty($extra_search) ){
			$input = $extra_search;
		}
		$is_export = ! empty($extra_search);

		$sqls = []; // for trace
		// enable / disable first_deposit_date_from[to]
		$search_first_deposit_date_switch = 'off'; // default
		if( ! empty($input['search_first_deposit_date_switch']) ){
			$search_first_deposit_date_switch = $input['search_first_deposit_date_switch'];
		}
		if($search_first_deposit_date_switch == 'off'){ // ignore first_deposit_date_from and first_deposit_date_to
			unset($input['first_deposit_date_from']);
			unset($input['first_deposit_date_to']);
		}

		$this->data_tables->options['is_export'] = $is_export;

		$subtotals = array();
		$subtotals['totalRegisteredPlayers'] = 0;
		$subtotals['totalFirstDepositPlayers'] = 0;
		$subtotals['conversionRate'] = 0;
		$subtotals['totalFirstDepositedAmount'] = 0;


		/// The cols START
		$colIndex = 0;
		$columns = array();
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'agent_name';
		$columns[$colIndex]['select'] = 'agent_name';
		$columns[$colIndex]['name'] = lang('Agency Username');
		$columns[$colIndex]['formatter'] = function ($d, $row) {
			return $d;
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'totalRegisteredPlayers';
		$columns[$colIndex]['select'] = 'totalRegisteredPlayers';
		$columns[$colIndex]['name'] = lang('Total Registered Players');
		// // Format: yyyy-mm-dd HH:MM:SS
		// $columns[$colIndex]['formatter'] = 'dateTimeFormatter';
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){
			$subtotals['totalRegisteredPlayers'] += $d;
			return $d;
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'totalFirstDepositPlayers';
		// $columns[$colIndex]['select'] = '`transactions`.`created_at`'; // ver. transactions
		$columns[$colIndex]['select'] = 'totalFirstDepositPlayers'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('Total First Deposited Player');
		// // Format: yyyy-mm-dd HH:MM:SS
		// $columns[$colIndex]['formatter'] = 'dateTimeFormatter'; // directly assign to function.
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){
			$subtotals['totalFirstDepositPlayers'] += $d;
			return $d;
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'conversionRate';
		// $columns[$colIndex]['select'] = '`transactions`.`amount`'; // ver. transactions
		$columns[$colIndex]['select'] = 'totalFirstDepositPlayers/totalRegisteredPlayers'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('Conversion Rate');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){ // 1,234,567.00
			$subtotals['conversionRate'] += $d; // for sub total.
			return $this->data_tables->percentageFormatter($d);
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'totalFirstDepositedAmount';
		// $columns[$colIndex]['select'] = '`transactions`.`amount`'; // ver. transactions
		$columns[$colIndex]['select'] = 'totalFirstDepositedAmount'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('Total First Deposit Amount');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){ // 1,234,567.00
			$subtotals['totalFirstDepositedAmount'] += $d; // for sub total.
			return $this->data_tables->currencyFormatter($d);
		}; // EOF $columns[$colIndex]['formatter']
		/// EOF cols


		/// Patch for OGP-14393 Enhancement of Conversion Rate Report performance
		// Get the player who created on date range.
		$date_from_to = $this->convertInput2Date_from_to($input);
		$playerIdList = $this->getPlayerIdWhereCreatedOnRange($date_from_to['date_from'], $date_from_to['date_to']);
		// apply to player_report_hourly WHERE condition
		if( ! empty($playerIdList) ){
			// PRH = player_report_hourly
			$PRHplayerIdsCoditionStr = $this->getPlayerIdCoditionStr($playerIdList, 'player_report_hourly.player_id');
		}else{
			$PRHplayerIdsCoditionStr = 0;
		}

		$theField4registration_datetime ='created_on'; // OGP-14393 Enhancement of Conversion Rate Report performance
		$where4registration_datetime = $this->getWhere4registration_datetime($input, $theField4registration_datetime);
		if( ! empty($where4registration_datetime) ){
			$where4registration_datetime = ' AND '. $where4registration_datetime;
		}

		$theField4first_deposit_datetime ='first_deposit_datetime';
		$where4first_deposit_datetime = $this->getWhere4first_deposit_datetime($input, $theField4first_deposit_datetime);
		if( ! empty($where4first_deposit_datetime) ){
			$where4first_deposit_datetime = ' AND '. $where4first_deposit_datetime;
		}

		/// disable for OGP-14393 Enhancement of Conversion Rate Report performance
		// $where4agency_username = 'totalRegisteredPlayers > 0';
		// if( ! empty($input['agency_username']) ){
		// 	$where4agency_username = ' agent_name = "'. $input['agency_username']. '" ';
		// }


		$this->load->model(array('agency_model'));
		// $PRHagent_idCoditionStr = ''; /// disable for OGP-14393 Enhancement of Conversion Rate Report performance
		$agency_agentsAgent_idCoditionStr = '';
		if( ! empty($input['agency_username']) ){
			$agent_id = $this->agency_model->get_agent_id_by_agent_name( $input['agency_username'] );
			// $PRHagent_idCoditionStr = ' AND agent_id = "'. $agent_id. '"'; /// disable for OGP-14393 Enhancement of Conversion Rate Report performance
			$agency_agentsAgent_idCoditionStr = ' AND agent_id = "'. $agent_id. '"';
		}

		// totalRegisteredPlayers : 註冊人數
		// totalFirstDepositPlayers : 首存人數
		// totalFirstDepositedAmount : 首存金额
		//filter multi-registered: agent_id, affiliateId and refereePlayerId.

		/// OGP-14393 Enhancement of Conversion Rate Report performance
		// ----- ver. player_relay
		/// filter agency that without player
		$dofilter4totalRegisteredPlayers = false;
		if( ! empty($input['agency_username']) ){
		}else{
			$dofilter4totalRegisteredPlayers = true;
		}
		// Get agent_id who has registered player.
		if($dofilter4totalRegisteredPlayers){
			$sql = <<<EOF
			SELECT agent_id
			FROM player_relay
			WHERE deleted_at IS NULL
				AND 0 < player_relay.agent_id /* filter multi-registered */
				AND 0 = player_relay.affiliate_id /* filter multi-registered */
				AND 0 = player_relay.referee_player_id /* filter multi-registered */
				$where4registration_datetime
			GROUP BY agent_id
EOF;
			$result = $this->db->query($sql);
			$rows = $this->getMultipleRowArray($result);
			if( ! empty($rows) ){
				$agent_idCond = [];
				foreach($rows as $keyNumber => $row){
					$tmp4agent_id =$row['agent_id'];
					$agent_idCond[] =<<<EOF
					agent_id = "$tmp4agent_id"
EOF;
				}
				$agent_idCondStr = ' ( '. implode(' OR ', $agent_idCond). ' ) ';
			}else{
				$agent_idCondStr ="0";
			}

		}else{
			$agent_idCondStr = ' agent_name = "'. $input['agency_username'].'" ';
		}
		$table=<<<EOF
		( /* as reporting */
				SELECT agencyAgentList.agent_id
				, agencyAgentList.agent_name
				, ( /* as totalRegisteredPlayers */
					SELECT COUNT( player_id ) as counter
					FROM player_relay
					WHERE deleted_at IS NULL
						AND agencyAgentList.agent_id = player_relay.agent_id
						AND 0 = player_relay.affiliate_id /* filter multi-registered */
						AND 0 = player_relay.referee_player_id /* filter multi-registered */
						$where4registration_datetime
						/* disable for B3.1, $where4first_deposit_datetime */
				) as totalRegisteredPlayers
				,( /* as totalFirstDepositPlayers */
					SELECT count(player_id) as counter
					FROM player_relay
					WHERE deleted_at IS NULL
						AND player_relay.first_deposit_amount > 0 /* Patch for OGP-14393 */
						AND agencyAgentList.agent_id = player_relay.agent_id
						AND 0 = player_relay.affiliate_id /* filter multi-registered */
						AND 0 = player_relay.referee_player_id /* filter multi-registered */
						$where4registration_datetime
						$where4first_deposit_datetime /* Patch for OGP-14393 */ /* first_deposit_datetime, first_deposit_amount */
				) as totalFirstDepositPlayers
				,(/* as totalFirstDepositedAmount */
					SELECT sum( first_deposit_amount ) as amount
					FROM player_relay
					WHERE deleted_at IS NULL
					AND player_relay.first_deposit_amount > 0 /* Patch for OGP-14393 */
						AND agencyAgentList.agent_id = player_relay.agent_id
						AND 0 = player_relay.affiliate_id /* filter multi-registered */
						AND 0 = player_relay.referee_player_id /* filter multi-registered */
						$where4registration_datetime
						$where4first_deposit_datetime /* Patch for OGP-14393 */ /* first_deposit_datetime, first_deposit_amount */
				)as totalFirstDepositedAmount
				FROM agency_agents as agencyAgentList
				WHERE $agent_idCondStr
				$agency_agentsAgent_idCoditionStr /* Patch for OGP-14393 */
		) as reporting
EOF;

/// disable for OGP-14393 Enhancement of Conversion Rate Report performance
//		// ----- ver. player_report_hourly
// 		$table = <<<EOF
// 		( /* as reporting */
// 			SELECT * FROM (  /* as relay_agency_reporting */
// 				SELECT agencyAgentList.agent_id
// 				, agencyAgentList.agent_name
// 				,( /* as totalRegisteredPlayers */
// 					SELECT COUNT( playerId) as counter
// 					FROM ( /* as relay_player_report_hourly4cols */
// 						SELECT player.*
// 						, IF(player.agent_id IS NULL or player.agent_id = "", 0, player.agent_id) as int_agent_id
// 						, IF(player.affiliateId IS NULL or player.affiliateId = "", 0, player.affiliateId) as int_affiliateId
// 						, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", 0, player.refereePlayerId) as int_refereePlayerId
// 						FROM player
// 						LEFT OUTER JOIN ( /* as relay_player_report_hourly  */
// 							/* wrapper for last player_report_hourly pre player */
// 							/* START get the last record of the player */
// 							SELECT player_report_hourly.player_id
// 							, player_report_hourly.first_deposit_amount
// 							, player_report_hourly.first_deposit_datetime
// 							, IF(player_report_hourly.agent_id IS NULL or player_report_hourly.agent_id = "", 0, player_report_hourly.agent_id) as int_agent_id
// 							, IF(player_report_hourly.affiliate_id IS NULL or player_report_hourly.affiliate_id = "", 0, player_report_hourly.affiliate_id) as int_affiliate_id
// 							FROM player_report_hourly
// 							INNER JOIN ( /* as last_update_player_report_hourly */
// 								SELECT max(id) as max_id /* get the lastest reocrd for the player.*/
// 								, player_report_hourly.player_id
// 								FROM player_report_hourly
// 								WHERE $PRHplayerIdsCoditionStr  /* Patch for OGP-14393 */
// 								AND (affiliate_id IS NULL OR affiliate_id = "") /* filter multi-registered */
// 								$PRHagent_idCoditionStr /* Patch for OGP-14393 */
// 								GROUP by player_id
// 							)as last_update_player_report_hourly
// 							ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 							AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 							/* EOF get the last record of the player */
// 						) as relay_player_report_hourly ON playerId = relay_player_report_hourly.player_id
// 						WHERE player.deleted_at IS NULL
// 						AND (player.affiliateId IS NULL OR player.affiliateId = "") /* filter multi-registered */
// 						AND (refereePlayerId IS NULL OR refereePlayerId = "") /* filter multi-registered */
// 						$where4registration_datetime
// 						/* disable for B3.1, $where4first_deposit_datetime */
// 					) as relay_player_report_hourly4cols
// 					WHERE agencyAgentList.agent_id = relay_player_report_hourly4cols.agent_id
// 					AND 0 = relay_player_report_hourly4cols.int_affiliateId /* filter multi-registered */
// 					AND 0 = relay_player_report_hourly4cols.int_refereePlayerId /* filter multi-registered */
// 				) as totalRegisteredPlayers
// 				,( /* as totalFirstDepositPlayers */
// 					SELECT count(playerId) as counter
// 					FROM ( /* as relay_player_report_hourly4cols */
// 						SELECT player.*
// 						, IF(player.agent_id IS NULL or player.agent_id = "", 0, player.agent_id) as int_agent_id
// 						, IF(player.affiliateId IS NULL or player.affiliateId = "", 0, player.affiliateId) as int_affiliateId
// 						, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", 0, player.refereePlayerId) as int_refereePlayerId
// 						FROM player
// 						LEFT OUTER JOIN ( /* as relay_player_report_hourly  */
// 							/* wrapper for last player_report_hourly pre player */
// 							/* START get the last record of the player */
// 							SELECT player_report_hourly.player_id
// 							, player_report_hourly.first_deposit_amount
// 							, player_report_hourly.first_deposit_datetime
// 							, IF(player_report_hourly.agent_id IS NULL or player_report_hourly.agent_id = "", 0, player_report_hourly.agent_id) as int_agent_id
// 							, IF(player_report_hourly.affiliate_id IS NULL or player_report_hourly.affiliate_id = "", 0, player_report_hourly.affiliate_id) as int_affiliate_id
// 							FROM player_report_hourly
// 							INNER JOIN (
// 								SELECT max(id) as max_id /* get the lastest reocrd for the player.*/
// 								, player_report_hourly.player_id
// 								FROM player_report_hourly
// 								WHERE $PRHplayerIdsCoditionStr  /* Patch for OGP-14393 */
// 								AND first_deposit_amount > 0 /* Patch for OGP-14393 */
// 								AND (affiliate_id IS NULL OR affiliate_id = "") /* filter multi-registered */
// 								$where4first_deposit_datetime /* Patch for OGP-14393 */
// 								$PRHagent_idCoditionStr /* Patch for OGP-14393 */
// 								GROUP by player_id
// 							)as last_update_player_report_hourly
// 							ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 								AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 							/* EOF get the last record of the player */
// 						) as relay_player_report_hourly ON playerId = relay_player_report_hourly.player_id
// 														AND relay_player_report_hourly.first_deposit_amount > 0
// 														$where4first_deposit_datetime /* Patch for OGP-14393 */
// 						WHERE player.deleted_at IS NULL
// 						AND relay_player_report_hourly.first_deposit_amount > 0
// 						AND (player.affiliateId IS NULL OR player.affiliateId = "") /* filter multi-registered */
// 						AND (refereePlayerId IS NULL OR refereePlayerId = "") /* filter multi-registered */
// 						$where4registration_datetime
// 						$where4first_deposit_datetime
// 					) as relay_player_report_hourly4cols
// 					WHERE  agencyAgentList.agent_id = relay_player_report_hourly4cols.agent_id
// 					AND 0 = relay_player_report_hourly4cols.int_affiliateId /* filter multi-registered */
// 					AND 0 = relay_player_report_hourly4cols.int_refereePlayerId /* filter multi-registered */
//
// 				) as totalFirstDepositPlayers
// 				,( /* as totalFirstDepositedAmount */
// 					SELECT sum( first_deposit_amount ) as amount
// 					FROM ( /* as relay_player_report_hourly4cols */
// 						SELECT player.playerId
// 						, relay_player_report_hourly.first_deposit_amount
// 						, player.agent_id
// 						, IF(player.agent_id IS NULL or player.agent_id = "", 0, player.agent_id) as int_agent_id /* convert to int while NULL or Zero.*/
// 						, player.refereePlayerId
// 						, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", 0, player.refereePlayerId) as int_refereePlayerId
// 						, IF(player.affiliateId IS NULL or player.affiliateId = "", 0, player.affiliateId) as int_affiliateId
// 						FROM player
// 						LEFT OUTER JOIN ( /* as relay_player_report_hourly  */
// 							/* wrapper for last player_report_hourly pre player */
// 							/* START get the last record of the player */
// 							SELECT player_report_hourly.player_id
// 							, player_report_hourly.first_deposit_amount
// 							, player_report_hourly.first_deposit_datetime
// 							, IF(player_report_hourly.agent_id IS NULL or player_report_hourly.agent_id = "", 0, player_report_hourly.agent_id) as int_agent_id
// 							, IF(player_report_hourly.affiliate_id IS NULL or player_report_hourly.affiliate_id = "", 0, player_report_hourly.affiliate_id) as int_affiliate_id
// 							FROM player_report_hourly
// 							INNER JOIN (
// 								SELECT max(id) as max_id /* get the lastest reocrd for the player.*/
// 								, player_report_hourly.player_id
// 								FROM player_report_hourly
// 								WHERE $PRHplayerIdsCoditionStr  /* Patch for OGP-14393 */
// 								AND first_deposit_amount > 0 /* Patch for OGP-14393 */
// 								AND (affiliate_id IS NULL OR affiliate_id = "") /* filter multi-registered */
// 								$where4first_deposit_datetime /* Patch for OGP-14393 */
// 								$PRHagent_idCoditionStr /* Patch for OGP-14393 */
// 								GROUP by player_id
// 							)as last_update_player_report_hourly
// 							ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 								AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 							/* EOF get the last record of the player */
// 							WHERE first_deposit_amount > 0 /* Patch for OGP-14393 */
// 							$where4first_deposit_datetime /* Patch for OGP-14393 */
// 						) as relay_player_report_hourly ON playerId = relay_player_report_hourly.player_id
// 														AND relay_player_report_hourly.first_deposit_amount > 0
// 														$where4first_deposit_datetime /* Patch for OGP-14393 */
// 						WHERE player.deleted_at IS NULL
// 						AND relay_player_report_hourly.first_deposit_amount > 0
// 						AND (player.affiliateId IS NULL OR player.affiliateId = "") /* filter multi-registered */
// 						AND (refereePlayerId IS NULL OR refereePlayerId = "") /* filter multi-registered */
// 						$where4registration_datetime
// 						$where4first_deposit_datetime
// 					) as relay_player_report_hourly4cols
// 					WHERE agencyAgentList.agent_id = relay_player_report_hourly4cols.agent_id
// 					AND 0 = relay_player_report_hourly4cols.int_affiliateId /* filter multi-registered */
// 					AND 0 = relay_player_report_hourly4cols.int_refereePlayerId /* filter multi-registered */
// 				) as totalFirstDepositedAmount
// 				FROM agency_agents as agencyAgentList
// 				WHERE 1 $agency_agentsAgent_idCoditionStr /* Patch for OGP-14393 */
// 			) as relay_agency_reporting
// 			WHERE $where4agency_username
// 		) as reporting
// EOF;

		$table = $this->data_tables->clearBlankSpaceAfterHeredoc($table);

		$where = array();
		$values = array();
		$joins = array();
		$group_by = array();
		$having = array();
		$distinct = false;
		$external_order=[];
		$notDatatable = '';
		$countOnlyField = 'agent_id';
		$innerJoins=[];
		$useIndex=[];

		$markName = 'list';
		$this->utils->markProfilerStart($markName); // list mark.
		$this->config->set_item('debug_data_table_sql', true);
		if( $totalOnly ) {
			$result = $this->data_tables->empty_data($request);
		}else{
			$result = $this->data_tables->get_data($request // #1
				, $columns // #2
				, $table // #3
				, $where // #4
				, $values // #5
				, $joins // #6
				, $group_by // #7
				, $having  // #8
				, $distinct // #9
				, $external_order // #10
				, $notDatatable // #11
				, $countOnlyField // #12
				, $innerJoins // #13
				, $useIndex // #14
			);
		}

		$this->utils->markProfilerEndAndPrint($markName, $benchmarks[$markName] ); // total mark.

		$sqls[$markName] = $this->data_tables->last_query; // total mark.

		if( ! $is_export ){

			/// subTotal append to $result['data'], rows.
			// MUST before getMultipleRowArray().because getMultipleRowArray() will formatter data.
			$subTotalRowsIndex = count( $result['data'] );
			$result['data'][$subTotalRowsIndex][0] = '<span class="th subTotalCol">'.lang('Sub Total').'</span>';
			$result['data'][$subTotalRowsIndex][1] = '<span class="th subTotalCol">'.$this->data_tables->defaultFormatter($subtotals['totalRegisteredPlayers']).'</span>';
			$result['data'][$subTotalRowsIndex][2] = '<span class="th subTotalCol">'.$this->data_tables->defaultFormatter($subtotals['totalFirstDepositPlayers']).'</span>';
			$conversionRate = 0;
			if( $subtotals['totalRegisteredPlayers'] > 0 ){
				$conversionRate = $subtotals['totalFirstDepositPlayers']/$subtotals['totalRegisteredPlayers'];
			}
			$result['data'][$subTotalRowsIndex][3] = '<span class="th subTotalCol">'.$this->data_tables->percentageFormatter($conversionRate).'</span>';
			$result['data'][$subTotalRowsIndex][4] = '<span class="th subTotalCol">'.$this->data_tables->currencyFormatter($subtotals['totalFirstDepositedAmount']).'</span>';
		}

		// for Total, get orig SQL and sum(amount) from that.
		// $this->config->set_item('debug_data_table_sql', true);
		$this->data_tables->options['only_sql'] = true;
		// $request['start']
		$request['length'] = -1; // for ignore LIMIT.
		$result4sql = $this->data_tables->get_data($request // #1
										, $columns // #2
										, $table // #3
										, $where // #4
										, $values // #5
										, $joins // #6
										, $group_by // #7
										, $having  // #8
										, $distinct // #9
										, $external_order // #10
										, $notDatatable // #11
										, $countOnlyField // #12
										, $innerJoins // #13
										, $useIndex // #14
									);
		$this->data_tables->options['only_sql'] = false;
		// $this->utils->debug_log('1984.conversion_rate_report_summaryByDirectPlayer.result4sql:', $result4sql);
		// $sql = 'SELECT SUM(totalRegisteredPlayers) as totalRegisteredPlayers FROM ('. $result4sql['data']. ') as tmp';

		$sql4total = $result4sql['data'];
		$sql = <<<EOF
		SELECT  /* summaryByAgency */ SUM(totalRegisteredPlayers) as totalRegisteredPlayers
		, SUM(totalFirstDepositPlayers) as totalFirstDepositPlayers
		/* , SUM(totalFirstDepositPlayers)/SUM(totalRegisteredPlayers)  as conversionRate */
		, SUM(totalFirstDepositedAmount) as totalFirstDepositedAmount
		FROM ($sql4total) as total;
EOF;
		$sql = $this->data_tables->clearBlankSpaceAfterHeredoc($sql);

		$markName = 'total'; // total mark.
		$this->utils->markProfilerStart($markName); // total mark.
		if( $totalOnly || true) {// from SummaryByAll
			$result4Total = $readOnlyDB->query( $sql );
			$summary = $this->getMultipleRowArray($result4Total);
		}else{
			$summary = array();
		}
		$this->utils->markProfilerEndAndPrint($markName, $benchmarks[$markName]); // total mark.

		$sqls[$markName] = $sql;

		// for Summary by ALL display Zero while NULL.
		if( empty($summary[0]['totalRegisteredPlayers']) ){ // for NULL
			$summary[0]['totalRegisteredPlayers'] = 0;
		}
		if( empty($summary[0]['totalFirstDepositPlayers']) ){ // for NULL
			$summary[0]['totalFirstDepositPlayers'] = 0;
		}
		if( empty($summary[0]['totalFirstDepositedAmount']) ){ // for NULL
			$summary[0]['totalFirstDepositedAmount'] = 0;
		}

		if( ! empty($summary[0]['totalRegisteredPlayers']) ){
			$summary[0]['conversionRate'] = $summary[0]['totalFirstDepositPlayers'] / $summary[0]['totalRegisteredPlayers'];
		}else{
			$summary[0]['conversionRate'] = 0;
		}

		// $this->utils->debug_log('1992.conversion_rate_report_summaryByDirectPlayer.summary:', $summary);
		$totalRowsIndex = count( $result['data'] );
		$result['data'][$totalRowsIndex][0] = '<span class="th totalCol">'.lang('Total').'</span>';
		$result['data'][$totalRowsIndex][1] = '<span class="th totalCol">'.$this->data_tables->defaultFormatter($summary[0]['totalRegisteredPlayers']).'</span>';
		$result['data'][$totalRowsIndex][2] = '<span class="th totalCol">'.$this->data_tables->defaultFormatter($summary[0]['totalFirstDepositPlayers']).'</span>';
		$result['data'][$totalRowsIndex][3] = '<span class="th totalCol">'.$this->data_tables->percentageFormatter($summary[0]['conversionRate']).'</span>';
		$result['data'][$totalRowsIndex][4] = '<span class="th totalCol">'.$this->data_tables->currencyFormatter($summary[0]['totalFirstDepositedAmount']).'</span>';

		if( $is_export ){ //  remove tags of html while export.
			foreach($result['data'][$totalRowsIndex] as $key => $val){
				$result['data'][$totalRowsIndex][$key] = strip_tags($val);
			}
		}

		$result['providerMethod'] = __FUNCTION__;
		$result['input'] = $input;
		$result['request'] = $request;
		$result['sqls'] = $sqls;
		$result['benchmarks'] = $benchmarks;
		$result['summaryByAll'] = $summary[0]; // for conversion_rate_report_summaryByAll().
		return $result;
		// $sqls for trace,
	} // EOF conversion_rate_report_summaryByAgency
	/**
	 * Get conversion_rate_report of summary By Referrer
	 *
	 *
	 * Check SQL,
	 * - list: The list of reporting.
	 * - total: The total row at bottom of reporting.
	 *
	 * @link datatables, https://datatables.net/reference/option/
	 *
	 * @param boolean|array $extra_search Check called from export. ( entry: Export_data::conversion_rate_report() )
	 *
	 * @param boolean $totalOnly Called from conversion_rate_report_summaryByAll(). For Summary By ALL of reporting.
	 *
	 * @return array $result Base on datatables, and extra elements,
	 * - $result['providerMethod'] This function name for upgrade or debug.
	 * - $result['conversionRate'] The percentage at bottom of list.
	 * - $result['request'] Base on ci::input->post()
	 * - $result['input'] Parsing $request['extra_search'].
	 * - $result['sqls'] The API query SQL(s).
	 * - $result['benchmarks'] The spend time pre query.
	 * - $result['summaryByAll'] for conversion_rate_report_summaryByAll().
	 */
	public function conversion_rate_report_summaryByReferrer($extra_search = false, $totalOnly = false) {
		$this->setCrrExecutionTime();

		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));


		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		if( ! empty($extra_search) ){
			$input = $extra_search;
		}
		$is_export = ! empty($extra_search);

		$sqls = []; // for trace

		// enable / disable first_deposit_date_from[to]
		$search_first_deposit_date_switch = 'off'; // default
		if( ! empty($input['search_first_deposit_date_switch']) ){
			$search_first_deposit_date_switch = $input['search_first_deposit_date_switch'];
		}
		if($search_first_deposit_date_switch == 'off'){ // ignore first_deposit_date_from and first_deposit_date_to
			unset($input['first_deposit_date_from']);
			unset($input['first_deposit_date_to']);
		}

		$this->data_tables->options['is_export'] = $is_export;

		$subtotals = array();
		$subtotals['conversionRate'] = 0;
		$subtotals['totalFirstDepositedAmount'] = 0;

		$subtotals = array();
		$subtotals['totalRegisteredPlayers'] = 0;
		$subtotals['totalFirstDepositPlayers'] = 0;
		$subtotals['conversionRate'] = 0;
		$subtotals['totalFirstDepositedAmount'] = 0;



		/// The cols START
		$colIndex = 0;
		$columns = array();
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'orefereeUsername';
		$columns[$colIndex]['select'] = 'orefereeUsername';
		$columns[$colIndex]['name'] = lang('Referrers Username');
		$columns[$colIndex]['formatter'] = function ($d, $row) {
			return $d;
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'totalRegisteredPlayers';
		$columns[$colIndex]['select'] = 'totalRegisteredPlayers';
		$columns[$colIndex]['name'] = lang('Total Registered Players');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){
			$subtotals['totalRegisteredPlayers'] += $d;
			return $d;
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'totalFirstDepositPlayers';
		// $columns[$colIndex]['select'] = '`transactions`.`created_at`'; // ver. transactions
		$columns[$colIndex]['select'] = 'totalFirstDepositPlayers'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('Total First Deposited Player');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){
			$subtotals['totalFirstDepositPlayers'] += $d;
			return $d;
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'conversionRate';
		// $columns[$colIndex]['select'] = '`transactions`.`amount`'; // ver. transactions
		$columns[$colIndex]['select'] = 'totalFirstDepositPlayers/totalRegisteredPlayers'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('Conversion Rate');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){ // 1,234,567.00
			$subtotals['conversionRate'] += $d; // for sub total.
			return $this->data_tables->percentageFormatter($d);
		}; // EOF $columns[$colIndex]['formatter']
		$colIndex++;
		$columns[$colIndex]['dt'] = $colIndex;
		$columns[$colIndex]['alias'] = 'totalFirstDepositedAmount';
		// $columns[$colIndex]['select'] = '`transactions`.`amount`'; // ver. transactions
		$columns[$colIndex]['select'] = 'totalFirstDepositedAmount'; // ver. player_report_hourly
		$columns[$colIndex]['name'] = lang('Total First Deposit Amount');
		$columns[$colIndex]['formatter'] = function ($d, $row) use ( &$subtotals ){ // 1,234,567.00
			$subtotals['totalFirstDepositedAmount'] += $d; // for sub total.
			return $this->data_tables->currencyFormatter($d);
		}; // EOF $columns[$colIndex]['formatter']
		/// EOF cols

		/// Patch for OGP-14393 Enhancement of Conversion Rate Report performance
		// Get the player who created on date range.
		$date_from_to = $this->convertInput2Date_from_to($input);
		$playerIdList = $this->getPlayerIdWhereCreatedOnRange($date_from_to['date_from'], $date_from_to['date_to']);
		// apply to player_report_hourly WHERE condition
		if( ! empty($playerIdList) ){
			// PRH = player_report_hourly
			$PRHplayerIdsCoditionStr = $this->getPlayerIdCoditionStr($playerIdList, 'player_report_hourly.player_id');
		}else{
			$PRHplayerIdsCoditionStr = 0;
		}

		/// disable for OGP-14393 Enhancement of Conversion Rate Report performance
		// $theField4registration_datetime ='player.createdOn';
		// $where4registration_datetime = $this->getWhere4registration_datetime($input, $theField4registration_datetime);
		// if( ! empty($where4registration_datetime) ){
		// 	$where4registration_datetime = ' AND '. $where4registration_datetime;
		// }

		$theField4first_deposit_datetime ='first_deposit_datetime';
		$where4first_deposit_datetime = $this->getWhere4first_deposit_datetime($input, $theField4first_deposit_datetime);
		if( ! empty($where4first_deposit_datetime) ){
			$where4first_deposit_datetime = ' AND '. $where4first_deposit_datetime;
		}

		$where4referrers_username = ' totalRegisteredPlayers > 0';
		if( ! empty($input['referrers_username']) ){
			$where4referrers_username =  ' orefereeUsername = "'. $input['referrers_username']. '" ';
		}

		// ----- ver. player_relay
		// reporting = refereePlayer+ [player+relay_player_report_hourly]
		$theField4registration_datetime ='player_relay.created_on';
		$where4registration_datetime = $this->getWhere4registration_datetime($input, $theField4registration_datetime);
		if( ! empty($where4registration_datetime) ){
			$where4registration_datetime = ' AND '. $where4registration_datetime;
		}
		if( empty($input['referrers_username']) ){
			$where4referrers_username = ''; // change default
		}else{
			$where4referrers_username =  ' refereePlayer.username = "'. $input['referrers_username']. '" ';
			$where4referrers_username = ' AND '. $where4referrers_username; // change default
		}
		/// filter agency that without player
		$dofilter4totalRegisteredPlayers = false;
		if( ! empty($input['referrers_username']) ){
		}else{
			$dofilter4totalRegisteredPlayers = true;
		}
		$idCondStr = '';
		if($dofilter4totalRegisteredPlayers){
			$sql = <<<EOF
			SELECT referee_player_id
			FROM player_relay
			WHERE deleted_at IS NULL
				AND 0 < player_relay.referee_player_id
				AND 0 = player_relay.agent_id /* filter multi-registered */
				AND 0 = player_relay.affiliate_id /* filter multi-registered */
				AND referee_player_id > 0
				$where4registration_datetime
			GROUP BY referee_player_id
EOF;
			$result = $this->db->query($sql);
			$rows = $this->getMultipleRowArray($result);
			if( ! empty($rows) ){
				$idCond = [];
				foreach($rows as $keyNumber => $row){
					$tmp4id =$row['referee_player_id'];
					$idCond[] =<<<EOF
					refereePlayer.playerId = "$tmp4id"
EOF;
 // ref. refereePlayer.playerId
				}
				$idCondStr = ' AND ( '. implode(' OR ', $idCond). ' ) ';
			}else{
				$idCondStr = ' AND 0';
			}

		}
		// ----- ver. player_relay
		$table = <<<EOF
		( /* as reporting */
			SELECT refereePlayer.playerId as orefereePlayerId
				, refereePlayer.username as orefereeUsername
				, ( /* as totalRegisteredPlayers */
					SELECT COUNT( player_id) as counter
					FROM player_relay
					WHERE player_relay.deleted_at IS NULL
					AND player.playerId = player_relay.referee_player_id
					AND player_relay.agent_id = 0 /* filter multi-registered */
					AND player_relay.affiliate_id = 0 /* filter multi-registered */
					$where4registration_datetime
					/* disable for B3.1, $where4first_deposit_datetime */
				) as totalRegisteredPlayers /* 註冊人數 */
				,( /* as totalFirstDepositPlayers */
					SELECT COUNT( player_id) as counter
					FROM player_relay
					WHERE player_relay.deleted_at IS NULL
					AND player_relay.first_deposit_amount > 0
					AND player.playerId = player_relay.referee_player_id
					AND player_relay.agent_id = 0 /* filter multi-registered */
					AND player_relay.affiliate_id = 0 /* filter multi-registered */
					$where4registration_datetime
					$where4first_deposit_datetime
				) as totalFirstDepositPlayers /* 首儲玩家數 */
				,( /* as totalFirstDepositedAmount */
					SELECT SUM( first_deposit_amount) as amount
					FROM player_relay
					WHERE player_relay.deleted_at IS NULL
					AND player_relay.first_deposit_amount > 0
					AND player.playerId = player_relay.referee_player_id
					AND player_relay.agent_id = 0 /* filter multi-registered */
					AND player_relay.affiliate_id = 0 /* filter multi-registered */
					$where4registration_datetime
					$where4first_deposit_datetime
				) as totalFirstDepositedAmount /* 首儲玩家總金額 */
			FROM player
			INNER JOIN player as refereePlayer ON refereePlayer.playerId = player.playerId /* player.refereePlayerId */
			WHERE player.deleted_at IS NULL
			$idCondStr
			$where4referrers_username
		) as reporting
EOF;

/// disable for OGP-14393 Enhancement of Conversion Rate Report performance
// ----- ver. player_report_hourly
// 		$table = <<<EOF
// 		( /* as reporting */
// 			SELECT *
// 			FROM ( /* as relay_referer_reporting */
// 				SELECT refereePlayer.playerId as orefereePlayerId
// 				, refereePlayer.username as orefereeUsername
// 				,( /* as totalRegisteredPlayers */
// 					SELECT COUNT( playerId) as counter
// 					FROM ( /* as relay_player_report_hourly4cols */
// 						SELECT player.playerId
// 						, player.agent_id
// 						, IF(player.agent_id IS NULL or player.agent_id = "", "0", player.agent_id) as int_agent_id
// 						, player.refereePlayerId
// 						, IF(player.affiliateId IS NULL or player.affiliateId = "", "0", player.affiliateId) as int_affiliateId
// 						, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", "0", player.refereePlayerId) as int_refereePlayerId
// 						FROM player
// 						LEFT OUTER JOIN ( /* as relay_player_report_hourly  */
// 							/* wrapper for last player_report_hourly pre player */
// 							/* START get the last record of the player */
// 							SELECT player_report_hourly.*
// 							, IF(player_report_hourly.agent_id IS NULL or player_report_hourly.agent_id = "", "0", player_report_hourly.agent_id) as int_agent_id
// 							, IF(player_report_hourly.affiliate_id IS NULL or player_report_hourly.affiliate_id = "", "0", player_report_hourly.affiliate_id) as int_affiliate_id
// 							FROM player_report_hourly
// 							INNER JOIN (
// 								SELECT max(id) as max_id /* get the lastest reocrd for the player.*/
// 								, player_report_hourly.player_id
// 								FROM player_report_hourly
// 								WHERE $PRHplayerIdsCoditionStr /* Patch for OGP-14393 */
// 								AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 								AND (affiliate_id IS NULL OR affiliate_id = "") /* filter multi-registered */
// 								GROUP by player_id
// 							)as last_update_player_report_hourly
// 							ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 							AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 							/* EOF get the last record of the player */
// 						) as relay_player_report_hourly ON playerId = relay_player_report_hourly.player_id
// 						WHERE player.deleted_at IS NULL
// 						AND (player.agent_id IS NULL OR player.agent_id = "") /* filter multi-registered */
// 						AND (player.affiliateId IS NULL OR player.affiliateId = "") /* filter multi-registered */
// 						$where4registration_datetime
// 						/* disable for B3.1, $where4first_deposit_datetime */
// 					) as relay_player_report_hourly4cols
// 					WHERE refereePlayer.playerId = relay_player_report_hourly4cols.int_refereePlayerId
// 					AND 0 = relay_player_report_hourly4cols.int_agent_id /* filter multi-registered */
// 					AND 0 = relay_player_report_hourly4cols.int_affiliateId /* filter multi-registered */
// 				) as totalRegisteredPlayers /* 註冊人數 */
// 				, ( /* as totalFirstDepositPlayers */
// 					SELECT COUNT( playerId) as counter
// 					FROM ( /* as relay_player_report_hourly4cols */
// 						SELECT player.playerId
// 						, player.agent_id
// 						, IF(player.agent_id IS NULL or player.agent_id = "", "0", player.agent_id) as int_agent_id
// 						, player.affiliateId affiliate_id
// 						, IF(player.affiliateId IS NULL or player.affiliateId = "", "0", player.affiliateId) as int_affiliateId
// 						, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", "0", player.refereePlayerId) as int_refereePlayerId
// 						FROM player
// 						INNER JOIN player as irefereePlayer ON irefereePlayer.playerId = player.refereePlayerId
// 						LEFT OUTER JOIN (
// 							/* get the last record of the player */
// 							SELECT player_report_hourly.*
// 							FROM player_report_hourly
// 							INNER JOIN (
// 								SELECT max(id) as max_id
// 								, player_report_hourly.player_id
// 								FROM player_report_hourly
// 								WHERE $PRHplayerIdsCoditionStr /* Patch for OGP-14393 */
// 								AND first_deposit_amount > 0 /* Patch for OGP-14393 */
// 								AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 								AND (affiliate_id IS NULL OR affiliate_id = "") /* filter multi-registered */
// 								GROUP by player_id
// 							)as last_update_player_report_hourly
// 							ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 							AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 							/* EOF get the last record of the player */
// 						) as relay_player_report_hourly	ON player.playerId = relay_player_report_hourly.player_id
// 														AND relay_player_report_hourly.first_deposit_amount > 0
// 						WHERE relay_player_report_hourly.first_deposit_amount > 0
// 						AND player.deleted_at IS NULL
// 						AND (player.agent_id IS NULL OR player.agent_id = "") /* filter multi-registered */
// 						AND (player.affiliateId IS NULL OR player.affiliateId = "") /* filter multi-registered */
// 						$where4registration_datetime
// 						$where4first_deposit_datetime
// 					) as relay_player_report_hourly4cols
// 					WHERE refereePlayer.playerId = relay_player_report_hourly4cols.int_refereePlayerId
// 					AND 0 = relay_player_report_hourly4cols.int_agent_id /* filter multi-registered */
// 					AND 0 = relay_player_report_hourly4cols.int_affiliateId /* filter multi-registered */
// 				) as totalFirstDepositPlayers /* 首儲玩家數 */
// 				, ( /* as totalFirstDepositedAmount */
// 					SELECT SUM( first_deposit_amount) as counter
// 					FROM ( /* as relay_player_report_hourly4cols */
// 						SELECT player.playerId
// 						, relay_player_report_hourly.first_deposit_amount
// 						, player.affiliateId affiliate_id
// 						, IF(player.affiliateId IS NULL or player.affiliateId = "", "0", player.affiliateId) as int_affiliateId
// 						, player.agent_id
// 						, IF(player.agent_id IS NULL or player.agent_id = "", "0", player.agent_id) as int_agent_id /* convert to int while NULL or Zero.*/
// 						, player.refereePlayerId
// 						, IF(player.refereePlayerId IS NULL or player.refereePlayerId = "", "0", player.refereePlayerId) as int_refereePlayerId
// 						FROM player
//
// 						INNER JOIN player as irefereePlayer ON irefereePlayer.playerId = player.refereePlayerId
// 						LEFT OUTER JOIN ( /* get the last record of the player */
// 							SELECT player_report_hourly.*
// 							FROM player_report_hourly
// 							INNER JOIN (
// 								SELECT max(id) as max_id
// 								, player_report_hourly.player_id
// 								FROM player_report_hourly
// 								WHERE $PRHplayerIdsCoditionStr /* Patch for OGP-14393 */
// 								AND first_deposit_amount > 0 /* Patch for OGP-14393 */
// 								AND (agent_id IS NULL OR agent_id = "") /* filter multi-registered */
// 								AND (affiliate_id IS NULL OR affiliate_id = "") /* filter multi-registered */
// 								GROUP by player_id
// 							)as last_update_player_report_hourly
// 							ON last_update_player_report_hourly.player_id = player_report_hourly.player_id
// 							AND last_update_player_report_hourly.max_id = player_report_hourly.id
// 							/* EOF get the last record of the player */
// 						) as relay_player_report_hourly	ON player.playerId = relay_player_report_hourly.player_id
// 														AND relay_player_report_hourly.first_deposit_amount > 0
// 						WHERE relay_player_report_hourly.first_deposit_amount > 0
// 						AND player.deleted_at IS NULL
// 						AND (player.agent_id IS NULL OR player.agent_id = "") /* filter multi-registered */
// 						AND (player.affiliateId IS NULL OR player.affiliateId = "") /* filter multi-registered */
// 						$where4registration_datetime
// 						$where4first_deposit_datetime
// 					) as relay_player_report_hourly4cols
// 					WHERE refereePlayer.playerId = relay_player_report_hourly4cols.int_refereePlayerId
// 					AND 0 = relay_player_report_hourly4cols.int_agent_id /* filter multi-registered */
// 					AND 0 = relay_player_report_hourly4cols.int_affiliateId /* filter multi-registered */
// 				) as totalFirstDepositedAmount /* 首儲玩家總金額 */
// 				FROM player
// 				INNER JOIN player as refereePlayer ON refereePlayer.playerId = player.refereePlayerId
// 				WHERE player.deleted_at IS NULL
// 			) as relay_referer_reporting
// 			WHERE $where4referrers_username
// 			GROUP BY orefereePlayerId /*refereePlayer.playerId*/
// 		) as reporting
// EOF;

		$table = $this->data_tables->clearBlankSpaceAfterHeredoc($table);

		$where = array();
		$values = array();
		$joins = array();
		$group_by = array();
		$having = array();
		$distinct = false;
		$external_order=[];
		$notDatatable = '';
		$countOnlyField = 'orefereePlayerId';
		$innerJoins=[];
		$useIndex=[];

		$markName = 'list';
		$this->utils->markProfilerStart($markName); // list mark.
		$orig4debug_data_table_sql =$this->config->item('debug_data_table_sql');
		$this->config->set_item('debug_data_table_sql', true);
		if( $totalOnly ) {
			$result = $this->data_tables->empty_data($request);
		}else{
			$result = $this->data_tables->get_data($request // #1
				, $columns // #2
				, $table // #3
				, $where // #4
				, $values // #5
				, $joins // #6
				, $group_by // #7
				, $having  // #8
				, $distinct // #9
				, $external_order // #10
				, $notDatatable // #11
				, $countOnlyField // #12
				, $innerJoins // #13
				, $useIndex // #14
			);
		}


		$sql = $this->data_tables->last_query; // for trace
		$this->config->set_item('debug_data_table_sql', $orig4debug_data_table_sql); // revert debug_data_table_sql
		$this->utils->markProfilerEndAndPrint($markName, $benchmarks[$markName]); // total mark.

		$sqls[$markName] = $sql; // list mark.

		if( ! $is_export ){
			/// subTotal append to $result['data'], rows.
			// MUST before getMultipleRowArray().because getMultipleRowArray() will formatter data.
			$subTotalRowsIndex = count( $result['data'] );
			$result['data'][$subTotalRowsIndex][0] = '<span class="th subTotalCol">'.lang('Sub Total').'</span>';
			$result['data'][$subTotalRowsIndex][1] = '<span class="th subTotalCol">'.$this->data_tables->defaultFormatter($subtotals['totalRegisteredPlayers']).'</span>';
			$result['data'][$subTotalRowsIndex][2] = '<span class="th subTotalCol">'.$this->data_tables->defaultFormatter($subtotals['totalFirstDepositPlayers']).'</span>';
			$conversionRate = 0; // default
			if( $subtotals['totalRegisteredPlayers'] > 0 ){ // avoid division by zero
				$conversionRate = $subtotals['totalFirstDepositPlayers']/$subtotals['totalRegisteredPlayers'];
			}
			$result['data'][$subTotalRowsIndex][3] = '<span class="th subTotalCol">'.$this->data_tables->percentageFormatter($conversionRate).'</span>';
			$result['data'][$subTotalRowsIndex][4] = '<span class="th subTotalCol">'.$this->data_tables->currencyFormatter($subtotals['totalFirstDepositedAmount']).'</span>';
		}

		// for Total, get orig SQL and sum(amount) from that.
		// $this->config->set_item('debug_data_table_sql', true);
		$this->data_tables->options['only_sql'] = true;
		// $request['start']
		$request['length'] = -1; // for ignore LIMIT.
		$result4sql = $this->data_tables->get_data($request // #1
										, $columns // #2
										, $table // #3
										, $where // #4
										, $values // #5
										, $joins // #6
										, $group_by // #7
										, $having  // #8
										, $distinct // #9
										, $external_order // #10
										, $notDatatable // #11
										, $countOnlyField // #12
										, $innerJoins // #13
										, $useIndex // #14
									);
		$this->data_tables->options['only_sql'] = false;
		$sql4total = $result4sql['data'];
		$sql = <<<EOF
		SELECT  /* summaryByReferrer */ SUM(totalRegisteredPlayers) as totalRegisteredPlayers
		, SUM(totalFirstDepositPlayers) as totalFirstDepositPlayers
		/* , SUM(totalFirstDepositPlayers)/SUM(totalRegisteredPlayers) as conversionRate */
		, SUM(totalFirstDepositedAmount) as totalFirstDepositedAmount
		FROM ($sql4total) as total;
EOF;
		$sql = $this->data_tables->clearBlankSpaceAfterHeredoc($sql);

		$markName = 'total'; // total mark.
		$this->utils->markProfilerStart($markName); // total mark.
		if( $totalOnly || true) {// from SummaryByAll
			$result4Total = $readOnlyDB->query( $sql );
			$summary = $this->getMultipleRowArray($result4Total);
		}else{
			$summary = array();
		}
		$this->utils->markProfilerEndAndPrint($markName, $benchmarks[$markName]); // total mark.

		$sqls[$markName] = $sql; // total mark.

		// for Summary by ALL display Zero while NULL.
		if( empty($summary[0]['totalRegisteredPlayers']) ){ // for NULL
			$summary[0]['totalRegisteredPlayers'] = 0;
		}
		if( empty($summary[0]['totalFirstDepositPlayers']) ){ // for NULL
			$summary[0]['totalFirstDepositPlayers'] = 0;
		}
		if( empty($summary[0]['totalFirstDepositedAmount']) ){ // for NULL
			$summary[0]['totalFirstDepositedAmount'] = 0;
		}

		if( ! empty($summary[0]['totalRegisteredPlayers']) ){ // for conversionRate
			$summary[0]['conversionRate'] = $summary[0]['totalFirstDepositPlayers'] / $summary[0]['totalRegisteredPlayers'];
		}else{
			$summary[0]['conversionRate'] = 0;
		}

		// $this->utils->debug_log('1992.conversion_rate_report_summaryByDirectPlayer.summary:', $summary);
		$totalRowsIndex = count( $result['data'] );
		$result['data'][$totalRowsIndex][0] = '<span class="th totalCol">'.lang('Total').'</span>';
		$result['data'][$totalRowsIndex][1] = '<span class="th totalCol">'.$this->data_tables->defaultFormatter($summary[0]['totalRegisteredPlayers']).'</span>';
		$result['data'][$totalRowsIndex][2] = '<span class="th totalCol">'.$this->data_tables->defaultFormatter($summary[0]['totalFirstDepositPlayers']).'</span>';
		$result['data'][$totalRowsIndex][3] = '<span class="th totalCol">'.$this->data_tables->percentageFormatter($summary[0]['conversionRate']).'</span>';
		$result['data'][$totalRowsIndex][4] = '<span class="th totalCol">'.$this->data_tables->currencyFormatter($summary[0]['totalFirstDepositedAmount']).'</span>';

		if( $is_export ){ //  remove tags of html while export.
			foreach($result['data'][$totalRowsIndex] as $key => $val){
				$result['data'][$totalRowsIndex][$key] = strip_tags($val);
			}
		}

		$result['providerMethod'] = __FUNCTION__;
		$result['input'] = $input;
		$result['request'] = $request;
		$result['sqls'] = $sqls;
		$result['benchmarks'] = $benchmarks;
		$result['summaryByAll'] = $summary[0]; // for conversion_rate_report_summaryByAll().
		return $result;

		// $sqls for trace,
	} // EOF conversion_rate_report_summaryByReferrer

	/**
	 * detail: get new added and total number of players
	 *
	 * @param string $type
	 * @param string $date
	 * @param array $selected_tag
	 *
	 * @return array
	 */
	public function get_new_and_total_players($type = 'YEAR', $date = null, $selected_tag = null) {
		$readOnlyDB = $this->getReadOnlyDB();

		$readOnlyDB->select("COUNT(player.playerId) AS total_players");
		$readOnlyDB->from('player');

        if(!empty($selected_tag)){
            $readOnlyDB->join('playertag', 'playertag.playerId = player.playerId', 'left');
            $readOnlyDB->where('(playertag.tagId NOT IN ('.implode(',', $selected_tag).") OR playertag.tagId is NULL)");
        }

		$readOnlyDB->where('deleted_at IS NULL');

		if ($type == 'DATE') {
			$readOnlyDB->select_sum(sprintf("IF(DATE(player.createdOn) = '%s', 1, 0)", $date), 'new_players');
			$readOnlyDB->where("DATE(player.createdOn) <=", $date);
		} else {
			$readOnlyDB->select_sum(sprintf("IF(EXTRACT(%s FROM player.createdOn) = '%s', 1, 0)", $type, $date), 'new_players');
			$readOnlyDB->where(sprintf("EXTRACT(%s FROM player.createdOn) <= '%s'", $type, $date));
		}

//        $this->utils->debug_log('the count ----->', $readOnlyDB->_compile_select());
		$query = $readOnlyDB->get();
		return $query->row_array();
	}

	/**
	 * detail: get all new players
	 *
	 * @param string $type
	 * @param string $date
	 *
	 * @return array
	 */
	public function get_new_players($type = 'YEAR', $date = null) {
		$readOnlyDB = $this->getReadOnlyDB();

		$readOnlyDB->select("playerId");
		$readOnlyDB->select("username");
		$readOnlyDB->select("createdOn");
		$readOnlyDB->from('player');
		$readOnlyDB->where('deleted_at IS NULL');

		if ($type == 'DATE') {
			$readOnlyDB->where("DATE(createdOn)", $date);
		} else {
			$readOnlyDB->where(sprintf("EXTRACT(%s FROM createdOn) = '%s'", $type, $date));
		}

		$query = $readOnlyDB->get();
		return $query->result_array();
	}

	/**
	 * detail: get total number of players
	 *
	 * @param string $type
	 * @param string $date
	 *
	 * @return array
	 */
	public function get_total_players($type = 'YEAR', $date = null) {
		$readOnlyDB = $this->getReadOnlyDB();

		$readOnlyDB->select("playerId");
		$readOnlyDB->select("username");
		$readOnlyDB->select("createdOn");
		$readOnlyDB->from('player');
		$readOnlyDB->where('deleted_at IS NULL');

		if ($type == 'DATE') {
			$readOnlyDB->where("DATE(createdOn) <=", $date);
		} else {
			$readOnlyDB->where(sprintf("EXTRACT(%s FROM createdOn) <= '%s'", $type, $date));
		}

		$query = $readOnlyDB->get();
		return $query->result_array();
	}

	/**
	 * detail: get total deposit players
	 *
	 * @param string $type
	 * @param string $date
	 *
	 * @return array
	 */
	public function get_total_deposit_players($type = 'YEAR', $date = null) {
		$readOnlyDB = $this->getReadOnlyDB();
		$readOnlyDB->distinct();
		$readOnlyDB->select('player.playerId, player.username');
		$readOnlyDB->from('transactions');
		$readOnlyDB->join('player', 'transactions.to_id = player.playerId');
		$readOnlyDB->where('transactions.to_type', Transactions::PLAYER);
		$readOnlyDB->where('transactions.transaction_type', Transactions::DEPOSIT);
		$readOnlyDB->where('transactions.status', Transactions::APPROVED);
		if ($type == 'DATE') {
			$readOnlyDB->where('DATE(transactions.created_at) =', $date);
		} else {
			$readOnlyDB->where(sprintf("EXTRACT(%s FROM transactions.created_at) = '%s'", $type, $date));
		}

		$query = $readOnlyDB->get();
		return $query->result_array();
	}

	/**
	 * detail: get the first and second deposit record
	 *
	 * @param string $type
	 * @param string $date
	 * @param array $selected_tag
	 * @return array
	 */
	public function get_first_and_second_deposit($type = 'YEAR', $date = null, $selected_tag = null) {
		$readOnlyDB = $this->getReadOnlyDB();

		$date_str = $type == 'DATE' ? "DATE(created_at)" : "EXTRACT({$type} FROM created_at)";

        $playerTagJoin = '';
        $playerTagWhere = '';
        if(!empty($selected_tag)){
            $playerTagJoin = 'LEFT JOIN playertag ON playertag.playerId = player.playerId';
            $playerTagWhere = 'AND (playertag.tagId NOT IN ('.implode(',', $selected_tag).') OR playertag.tagId is NULL)';
        }

		$sql = <<<EOD
SELECT
	to_id,
    COUNT(DISTINCT common_date) number_of_dates,
    SUM(number_of_deposits) number_of_deposits,
	MIN(CONCAT(common_date,'_',number_of_deposits)) min_deposit_date_count,
	MAX(CONCAT(common_date,'_',number_of_deposits)) max_deposit_date_count
FROM
    (SELECT
        to_id,
		{$date_str} common_date,
        COUNT(id) number_of_deposits
    FROM
        transactions
    JOIN
        player ON player.playerId = transactions.to_id AND player.deleted_at IS NULL
        {$playerTagJoin}
    WHERE
        transaction_type = 1 AND
        to_type = 2 AND
        transactions.status = 1 AND
        {$date_str} <= '{$date}'
        {$playerTagWhere}
    GROUP BY
		to_id,
        {$date_str}
    ) a
GROUP BY
	to_id
HAVING
	number_of_dates <= 2 OR number_of_deposits <= 2
EOD;

		$query = $readOnlyDB->query($sql);

		$result = $query->result_array();
		// echo $readOnlyDB->last_query();exit();
		$data = array(
			'first_deposit' => 0,
			'second_deposit' => 0,
		);
		if(!empty($result)){
			foreach ($result as $row) {
				$min_deposit_date_count = explode('_', $row['min_deposit_date_count']);
				$max_deposit_date_count = explode('_', $row['max_deposit_date_count']);
				$min_deposit_date = $min_deposit_date_count[0];
				$min_deposit_count = $min_deposit_date_count[1];
				$max_deposit_date = $max_deposit_date_count[0];
				$max_deposit_count = $max_deposit_date_count[1];

				if ($date == $min_deposit_date) {

					$data['first_deposit']++;

					if ($row['number_of_deposits'] > 1) {
						$data['second_deposit']++;
					}
				} else if ($max_deposit_date == $date && $min_deposit_count == 1) {
					$data['second_deposit']++;
				}
			}
		}
		return $data;
	}

	/**
	 * detail: get first deposit record
	 *
	 * @param string $type
	 * @param string $date
	 *
	 * @return array
	 */
	public function get_first_deposit($type = 'YEAR', $date = null) {
		$readOnlyDB = $this->getReadOnlyDB();

		$date_str = $type == 'DATE' ? "DATE(created_at)" : "EXTRACT({$type} FROM created_at)";

		$sql = <<<EOD
SELECT
	to_id playerId,
	username,
	a.first_deposit
FROM
    (SELECT
        to_id,
		{$date_str} common_date,
		MIN(CONCAT(created_at,'_',id,'_',amount)) first_deposit,
        COUNT(id) number_of_deposits

    FROM
        transactions
    WHERE
        transaction_type = 1 AND
        to_type = 2 AND
        status = 1 AND
        {$date_str} <= '{$date}'
    GROUP BY
		to_id,
        {$date_str}
    ) a
LEFT JOIN
	player ON player.playerId = a.to_id
WHERE
	player.deleted_at IS NULL
GROUP BY
	to_id
HAVING
	MIN(common_date) = '{$date}' AND (COUNT(DISTINCT common_date) <= 2 OR SUM(number_of_deposits) <= 2)
EOD;
		$data = array();
		$query = $readOnlyDB->query($sql);
		$result = $query->result_array();
		// echo $readOnlyDB->last_query();exit();
		if(!empty($result)){
			foreach ($result as $row) {
				$first_deposit = explode('_', $row['first_deposit']);
				$data[] = array(
					'playerId' => $row['playerId'],
					'username' => $row['username'],
					'date' => $first_deposit[0],
					'amount' => $first_deposit[2],
					'transactionId' => $first_deposit[1],
				);
			}
		}
		return $data;
	}

	/**
	 * detail: get the second deposit record
	 *
	 * @param string $type
	 * @param string $date
	 *
	 * @return array
	 */
	public function get_second_deposit($type = 'YEAR', $date = null) {

		$readOnlyDB = $this->getReadOnlyDB();

		$date_str = $type == 'DATE' ? "DATE(%s)" : "EXTRACT({$type} FROM %s)";

		$this->db->select('player.playerId');
		$this->db->select('player.username');
		$this->db->select("SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(CONCAT_WS('_',transactions.created_at,transactions.id,transactions.amount) order by transactions.created_at asc, transactions.id asc separator ','),',',2),',', -1) second_deposit", false);
		$this->db->from('transactions');
		$this->db->join('player', 'player.playerId = transactions.to_id');
		$this->db->where('transactions.transaction_type', Transactions::DEPOSIT);
		$this->db->where('transactions.to_type', Transactions::PLAYER);
		$this->db->where('transactions.status', Transactions::APPROVED);
		$this->db->where(sprintf("{$date_str} <=", 'transactions.created_at'), $date);
		$this->db->where('player.deleted_at IS NULL');
		$this->db->group_by('transactions.to_id');
		$this->db->having('COUNT(transactions.id) >=', 2);
		$this->db->having(sprintf("$date_str =", 'second_deposit'), $date);
		$query = $this->db->get();

		$result = $query->result_array();

		foreach ($result as $row) {
			$second_deposit = explode('_', $row['second_deposit']);
			$data[] = array(
				'playerId' => $row['playerId'],
				'username' => $row['username'],
				'date' => $second_deposit[0],
				'amount' => $this->utils->formatCurrencyNoSym($second_deposit[2]),
				'transactionId' => $second_deposit[1],
			);
		}

		return $data;
	}

	/**
	 * detail: get certain payments report
	 *
	 * @param array $param
	 *
	 * @return array
	 */
	public function getPaymentsReport($params) {
		$readOnlyDB = $this->getReadOnlyDB();

		$sql = <<<EOD
SELECT * FROM
(SELECT
	player.username `username`,
	CONCAT(player.groupName, ' ', player.levelName) `member_level`,
	playerlevel.playerGroupId `playerGroupId`,
	'withdrawal' as `transaction`,
	walletaccount.dwStatus as `status`,
	walletaccount.amount as `amount`,
	walletaccount.dwMethod as `payment_method`,
	adminusers.username as `processed_by`,
	walletaccount.processDatetime as `process_time`,
	walletaccount.notes as `notes`
FROM
	walletaccount
LEFT JOIN
	adminusers ON adminusers.userId = walletaccount.processedBy
LEFT JOIN
	playeraccount ON playeraccount.playerAccountId = walletaccount.playerAccountId
LEFT JOIN
	player ON player.playerId = playeraccount.playerId
LEFT JOIN
	playerlevel ON playerlevel.playerId = player.playerId

UNION

SELECT
	player.username `username`,
	CONCAT(player.groupName, ' ', player.levelName) `member_level`,
	playerlevel.playerGroupId `playerGroupId`,
	'deposit' as `transaction`,
	sale_orders.status as `status`,
	sale_orders.amount as `amount`,
	payment_account.flag as `payment_method`,
	adminusers.username as `processed_by`,
	sale_orders.process_time as `process_time`,
	sale_orders.notes as `notes`
FROM
	sale_orders
LEFT JOIN
	player ON player.playerId = sale_orders.player_id
LEFT JOIN
	payment_account ON payment_account.id = sale_orders.payment_account_id
LEFT JOIN
	adminusers ON adminusers.userId = sale_orders.processed_by
LEFT JOIN
	playerlevel ON playerlevel.playerId = player.playerId) a
EOD;

		$where = array();
		$values = array();

		if ($params['dateRangeValueStart'] != null) {
			$where[] = 'process_time >= ?';
			$values[] = $params['dateRangeValueStart'];
		}

		if ($params['dateRangeValueEnd'] != null) {
			$where[] = 'process_time <= ?';
			$values[] = $params['dateRangeValueEnd'];

		}

		if ($params['paymentReportsortByUsername'] != null) {
			$where[] = 'username = ?';
			$values[] = $params['paymentReportsortByUsername'];
		}

		if ($params['paymentReportSortByPlayerLevel'] != null) {
			$where[] = 'playerGroupId = ?';
			$values[] = $params['paymentReportSortByPlayerLevel'];
		}

		if ($params['paymentReportSortByTransaction'] != null) {
			$where[] = 'transaction = ?';
			$values[] = $params['paymentReportSortByTransaction'];
		}

		if ($params['paymentReportSortByTransactionStatus'] != null) {
			switch ($params['paymentReportSortByTransactionStatus']) {
			case 'approved':
				$where[] = "status IN(?,?,?)";
				$values[] = 'approved';
				$values[] = Sale_order::STATUS_BROWSER_CALLBACK;
				$values[] = Sale_order::STATUS_SETTLED;
				break;
			case 'declined':
				$where[] = "status IN(?,?,?)";
				$values[] = 'declined';
				$values[] = Sale_order::STATUS_DECLINED;
				$values[] = Sale_order::STATUS_FAILED;
				break;
			case 'cancelled':
				$where[] = "status IN(?,?)";
				$values[] = 'cancelled';
				$values[] = Sale_order::STATUS_CANCELLED;
				break;
			default:
				break;
			}
		}

		if ($params['paymentReportSortByDWAmountGreaterThan'] != null) {
			$where[] = 'amount >= ?';
			$values[] = $params['paymentReportSortByDWAmountGreaterThan'];
		}

		if ($params['paymentReportSortByDWAmountLessThan'] != null) {
			$where[] = 'amount <= ?';
			$values[] = $params['paymentReportSortByDWAmountLessThan'];
		}

		if ($where) {
			$sql .= ' WHERE ' . implode(' AND ', $where);
		}

		$query = $readOnlyDB->query($sql, $values);
		return $query->result_array();
	}

	//===payment report=========================================================================================

	/**
	 * detail: get payment reports
	 *
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function payment_report($request, $is_export = false) {

		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		// $this->load->library([ 'player_manager' ]);
		$this->load->model(array('transactions', 'player_model'));

		$this->data_tables->is_export = $is_export;

		$input = $this->data_tables->extra_search($request);
		// $this->benchmark->mark('pre_processing_start');
		$where = array();
		$values = array();
		$having = array();
		$group_by = [];


		// if (isset($input['group_by']) && !empty($input['group_by'])) {
		if (!isset($input['group_by']) ) {
			// default group by and where
			$group_by = array('transactions.trans_date', 'transactions.transaction_type', 'transactions.payment_account_id');
			$where[] = "banktype.bankName IS NOT NULL";
		}
		else {
			$this->utils->debug_log('group_by', $input['group_by']);

			switch ($input['group_by']) {
				// case 'by_promotion_category' :
				// 	$group_by = [ 'transactions.trans_date', 'transactions.transaction_type', 'transactions.promo_category' ];
				// 	break;
				case 'by_player' :
					$group_by = [ 'transactions.trans_date', 'transactions.transaction_type', 'transactions.to_id' ];
					break;
				case 'by_level' :
					$group_by = [ 'transactions.trans_date', 'transactions.transaction_type', 'player.levelId' ];
					$where[] = "player.levelName IS NOT NULL";
					break;
				case 'by_admuser' :
					$group_by = [ 'transactions.trans_date', 'transactions.transaction_type', 'transactions.from_id' ];
					$where[] = "from_username IS NOT NULL";
					break;
				case 'by_aff' :
					$group_by = [ 'transactions.trans_date', 'transactions.transaction_type', 'player.affiliateId' ];
					$where[] = "affiliates.username IS NOT NULL";
					break;
				case 'by_agency' :
					$group_by = [ 'transactions.trans_date', 'transactions.transaction_type', 'player.agent_id' ];
					$where[] = "agency_agents.agent_name IS NOT NULL";
					break;
				case 'by_ref' :
					$group_by = [ 'transactions.trans_date', 'transactions.transaction_type', 'playerfriendreferral.playerId' ];
					$where[] = "playerfriendreferral.playerId IS NOT NULL";
					break;
				case 'by_payment_type' : default :
					$group_by = [ 'transactions.trans_date', 'transactions.transaction_type', 'transactions.payment_account_id' ];
					$where[] = "banktype.bankName IS NOT NULL";
					$where[] = "payment_account.payment_account_number IS NOT NULL";
					break;
			}
		}
		$this->utils->debug_log('group_by sql --->', $group_by);

		# START DEFINE COLUMNS #################################################################################################################################################
		// $i = 0;
		// $date_col = 0;
		// $member_col = 1;
		// $user_col = 2;
		// $group_level_col = 3;
		// $trans_type_col = 4;
		// $payment_type_col = 5;
		// $promo_cat_col = 6;
		// $amount_col = 7;

		$col = 0;
		$na = $is_export ? lang('lang.norecyet') : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';

		$columns = array(
			array(
				'alias' => 'to_id',
				'select' => 'transactions.to_id',
			),
			array(
				'alias' => 'from_id',
				'select' => 'transactions.from_id',
			),
			array(
				'alias' => 'to_type',
				'select' => 'transactions.to_type',
			),
			array(
				'alias' => 'from_type',
				'select' => 'transactions.from_type',
			),
			// 0 - date
			array(
				'dt' => $col++,
				'alias' => 'date',
				'select' => 'transactions.trans_date',
				// 'formatter' => 'dateFormatter',
				'name' => lang('Date'),
			),
			array(
				'alias' => 'player_levelName',
				'select' => 'player.levelName'
			),
			array(
				'alias' => 'player_groupName',
				'select' => 'player.groupName'
			),
			// 1 - member (player-username)
			array(
				'dt' => $col++,
				'alias' => 'member',
				'select' => 'transactions.to_username',
				'formatter' => function ($d, $row) use ($is_export, $group_by, $na) {

					$affiliate = ( ! empty( $row['affiliate'] ) ) ? $row['affiliate'] : '';

					if (in_array('transactions.to_id', $group_by)) {

						if ($is_export) {
							return ($d ? $d : lang('lang.norecyet'));
						} else {

							$to_type_lang = lang('transaction.from.to.type.' . $row['to_type']);
							switch ($row['to_type']) {
							case Transactions::ADMIN:
								// return '<i class="fa fa-user-secret" title="' . lang('transaction.from.to.type.' . $row['to_type']) . '"></i> ' . ($d . '('.$affiliate.')' ?: '<i>' . lang('lang.norecyet') . '</i>');
								return '<i class="fa fa-user-secret" title="' . $to_type_lang . '"></i> ' . ($d ?: $na);
								break;
							case Transactions::PLAYER:
								return '<i class="fa fa-user" title="' . $to_type_lang . '"></i> ' . ($d ? '<a href="/player_management/userInformation/' . $row['to_id'] . '" target="_blank">' . $d .  '</a>' : $na);
								break;
							case Transactions::AFFILIATE:
								return '<i class="fa fa-users" title="' . $to_type_lang . '"></i> ' . ($d  ?: $na);
								break;
							default:
								return $na;
							}
						}
					}else{
						if ($is_export) {
							return $row['member'];
						} else {
							return '<i class="fa fa-user" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ? '<a href="/player_management/userInformation/' . $row['from_id'] . '" target="_blank">' . $row['member'] . '</a>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>');
						}
					}
					return '';
				},
				'name' => lang('Player Username'),
			),
			// 2 - affiliate username
			[
				'dt' => $col++,
				'alias' => 'Affiliate',
				'select' => 'affiliates.username',
				'formatter' => function ($d, $row) use ($na) {
					return $d ?: $na;
				},
				'name' => lang('Affiliate Username'),
			],
			// 3 - agency username
			[
				'dt' => $col++,
				'alias' => 'Agency',
				'select' => 'agency_agents.agent_name',
				'formatter' => function ($d, $row) use ($na) {
					// $a = $this->agency_model->get_agent_by_id($d);
					// return $a['agent_name'] ?: $na;
					return $d ?: $na;
				},
				'name' => lang('Agent Username'),
			],
			// 4 - referrer username
			[
				'dt' => $col++,
				'alias' => 'Referrer',
				'select' => 'playerfriendreferral.playerId',
				'formatter' => function ($d, $row) use ($na) {
					// $referrer = $this->player_model->getReferrerByPlayerId($d);
					// return isset($referrer['username']) ? $referrer['username'] : $na;
					$referrer_username = $this->player_model->getUsernameById($d);
					return $referrer_username ?: $na;
				},
				'name' => lang('pay_report.referrer_username'),
			],
			// 5 - adm-user
			array(
				'dt' => $col++,
				'alias' => 'user',
				'select' => 'from_username',
				'formatter' => function ($d, $row) use ($is_export, $group_by, $na) {
					//echo "<pre>";print_r($row);exit;
					$affiliate = ( ! empty( $row['affiliate'] ) ) ? $row['affiliate'] : '';

					if (in_array('transactions.from_id', $group_by)) {
						if ($is_export) {
							return $d;
						} else {

							switch ($row['from_type']) {
							case Transactions::ADMIN:
								return '<i class="fa fa-user-secret" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ?: $na);
							case Transactions::PLAYER:
								return '<i class="fa fa-user" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ? '<a href="/player_management/userInformation/' . $row['from_id'] . '" target="_blank">' . $d . '</a>' : $na);
							case Transactions::AFFILIATE:
								return '<i class="fa fa-users" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ?: $na);
							default:
								return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
							}
						}
					}else{
						if ($is_export) {
							return $row['user'];
						} else {
							return '<i class="fa fa-user" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ?: $na);
						}
					}
					return '';
				},
				'name' => lang('Admin Username'),
			),
			// 6 - group level (vip-level)
			array(
				'dt' => $col++,
				'alias' => 'player_level',
				'select' => 'player.levelName',
				'formatter' => function ($d, $row) use ($is_export, $group_by, $input) {
					if (in_array('player.levelId', $group_by) || isset($input['by_player_level']) ) {
						if($d != 'N/A') {
							return lang($row['player_groupName']).' - '.lang($row['player_levelName']);
						}
						return $d;
					}
					return '';
				},
				// 'formatter' => function ($d, $row) use ($is_export) {
				// 	if ($is_export) {
				// 		return ($d ? $d : lang('lang.norecyet'));
				// 	} else {
				// 		switch ($row['from_type']) {
				// 		case Transactions::ADMIN:
				// 			return '<i class="fa fa-user-secret" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>');
				// 		case Transactions::PLAYER:
				// 			return '<i class="fa fa-user" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ? '<a href="/player_management/userInformation/' . $row['from_id'] . '" target="_blank">' . $d . '</a>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>');
				// 		case Transactions::AFFILIATE:
				// 			return '<i class="fa fa-users" title="' . lang('transaction.from.to.type.' . $row['from_type']) . '"></i> ' . ($d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>');
				// 		default:
				// 			return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
				// 		}
				// 	}
				// },
				'name' => lang('Player Level'),
			),
			// 7 - pay type (coll-account)
			array(
				'dt' => $col++,
				'alias' => 'payment_type',
				'select' => 'payment_account.payment_account_name',
				'formatter' => function ($d, $row) use ($is_export, $group_by) {
					if (in_array('transactions.payment_account_id', $group_by)) {
						return lang($row['bankname']) . ' - ' . $d . (!empty($row['payment_account_number']) ? ' - ******'.substr($row['payment_account_number'], -4) : '');
					}
					return '';
				},
				'name' => lang('Payment Type'),
			),
			// 8 - promo-cat
			array(
				'dt' => $col++,
				'alias' => 'promoTypeName',
				'select' => 'promotype.promoTypeName',
				'formatter' => function ($d, $row) use ($is_export, $group_by) {
					if (in_array('transactions.promo_category', $group_by)) {
						return lang($d);
					}
					return '';
				},
				'name' => lang('Promotion Category'),
			),
			// 9 - tx-type
			array(
				'dt' => $col++,
				'alias' => 'transaction_type',
				'select' => 'transactions.transaction_type',
				'formatter' => function ($d, $row) use ($is_export, $group_by) {
					if (in_array('transactions.transaction_type', $group_by)) {
						if ($is_export) {
							return lang('transaction.transaction.type.' . $d) ?: lang('lang.norecyet');
						} else {
							return lang('transaction.transaction.type.' . $d) ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}
					}
					return '';
				},
				'name' => lang('Transaction Type'),
			),
			array(
				'alias' => 'bankname',
				'select' => 'banktype.bankName',
			),
			array(
				'alias' => 'payment_account_number',
				'select' => 'payment_account.payment_account_number',
			),
			// 10 - amount
			array(
				'dt' => $col++,
				'alias' => 'amount',
				'select' => 'sum(transactions.amount)',
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return $this->utils->formatCurrencyNoSym($d);
					} else {
						return $d == 0 ? '<span class="text-muted">' . $this->utils->formatCurrencyNoSym($d) . '</span>' : '<strong>' . $this->utils->formatCurrencyNoSym($d) . '</strong>';
					}
					//return $d;
				},
				'name' => lang('Amount'),
			),
			array(
				'alias' => 'affiliate',
				'select' => 'affiliates.username'
			)
		);
		# END DEFINE COLUMNS #################################################################################################################################################
		#
		if ($is_export) {
			// Exclude unwanted columns for export
			// (Hidden by javascript in html output; In export we exclude columns physically)
			$hide_cols = [];
			$group_by_opt = isset($input['group_by']) ? $input['group_by'] : null;
			switch ($group_by_opt) {
				case 'by_player':
					$hide_cols = [ 5, 6, 7, 8 ];
					break;
				case 'by_level':
					$hide_cols = [ 1, 2, 3, 4, 5 ,7, 8 ];
					break;
				case 'by_admuser':
					$hide_cols = [ 1, 2, 3, 4, 6, 7, 8 ];
					break;
				case 'by_aff' :
					$hide_cols = [ 1, 3, 4, 5, 6, 7, 8 ];
					break;
				case 'by_agency' :
					$hide_cols = [ 1, 2, 4, 5, 6, 7, 8 ];
					break;
				case 'by_ref' :
					$hide_cols = [ 1, 2, 3, 5, 6, 7, 8 ];
					break;
				case 'by_payment_type':
				default:
					$hide_cols = [ 1, 2, 3, 4, 5, 6, 8 ];
					break;
			}

			$hide_cols = array_flip($hide_cols);

			foreach ($columns as $key => & $col) {
				$this->utils->debug_log(__METHOD__, 'key', $key, 'col', $col);
				if (isset($col['dt']) && isset($hide_cols[$col['dt']])) {
					unset($columns[$key]);
				}
			}
		}

		$this->utils->debug_log(__METHOD__, 'columns', $columns);

		$table = 'transactions';
		$joins = array(
			'promotype' => "promotype.promotypeId = transactions.promo_category",
			'payment_account' => "payment_account.id = transactions.payment_account_id",
			'banktype' => "payment_account.payment_type_id = banktype.bankTypeId",
			'player' => "player.playerId = transactions.to_id and transactions.to_type=" . Transactions::PLAYER,
			'affiliates' => 'affiliates.affiliateId = player.affiliateId',
			'agency_agents' => 'agency_agents.agent_id = player.agent_id',
			'playerfriendreferral' => 'playerfriendreferral.invitedPlayerId = player.playerId'
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		// $this->utils->debug_log('input', $input);

		// if (isset($input['from_type'])) {
		// 	$where[] = "transactions.from_type = ?";
		// 	$values[] = $input['from_type'];
		// }

		//if (isset($input['fromUsername'])) {
		// if (isset($input['fromUsernameId'])) {
		// 	//$where[] = "(CASE transactions.from_type WHEN ? THEN fromUser.username WHEN ? THEN fromPlayer.username WHEN ? THEN fromAffiliate.username ELSE NULL END) LIKE ?";
		// 	$where[] = "(CASE transactions.from_type WHEN ? THEN fromUser.userId WHEN ? THEN fromPlayer.playerId WHEN ? THEN fromAffiliate.affiliateId ELSE NULL END) = ?";
		// 	$values[] = Transactions::ADMIN;
		// 	$values[] = Transactions::PLAYER;
		// 	$values[] = Transactions::AFFILIATE;
		// 	//$values[] = $input['fromUsername'];
		// 	$values[] = $input['fromUsernameId'];
		// }

		// if (isset($input['no_affiliate']) && $input['no_affiliate'] == true) {
		// 	$where[] = "fromPlayer.affiliateId IS NULL OR toPlayer.affiliateId IS NULL";
		// }

		// if (isset($input['to_type'])) {
		// 	$where[] = "transactions.to_type = ?";
		// 	$values[] = $input['to_type'];
		// }

		//if (isset($input['toUsername'])) {
		// if (isset($input['toUsernameId'])) {
		// 	//$where[] = "(CASE transactions.to_type WHEN ? THEN toUser.username WHEN ? THEN toPlayer.username WHEN ? THEN toAffiliate.username ELSE NULL END) LIKE ?";
		// 	$where[] = "(CASE transactions.to_type WHEN ? THEN toUser.userId WHEN ? THEN toPlayer.playerId WHEN ? THEN toAffiliate.affiliateId ELSE NULL END) = ? ";
		// 	$values[] = Transactions::ADMIN;
		// 	$values[] = Transactions::PLAYER;
		// 	$values[] = Transactions::AFFILIATE;
		// 	//$values[] = '%' . $input['toUsername'] . '%';
		// 	//$values[] = $input['toUsername'];
		// 	$values[] = $input['toUsernameId'];
		// }

		if (isset($input['by_amount_greater_than'])) {
			$having["amount >= "] = $input['by_amount_greater_than'];
			// $values[] = $input['by_amount_greater_than'];
		}

		if (isset($input['by_amount_less_than'])) {
			$having["amount <= "] = $input['by_amount_less_than'];
			// $values[] = $input['by_amount_less_than'];
		}

		$where[]="transactions.status = ?";
		$values[]=Transactions::APPROVED;

		if (isset($input['by_transaction_type'])) {
			if ($input['by_transaction_type'] == -1) {
				$where[] = "transactions.transaction_type in (?,?,?,?)";
				$values[] = Transactions::ADD_BONUS;
				$values[] = Transactions::MEMBER_GROUP_DEPOSIT_BONUS;
				$values[] = Transactions::PLAYER_REFER_BONUS;
				$values[] = Transactions::RANDOM_BONUS;
			} else {
				$where[] = "transactions.transaction_type = ?";
				$values[] = $input['by_transaction_type'];
			}
		} else {
			$where[] = "transactions.transaction_type in (?,?,?,?,?,?,?)";
			$values[] = Transactions::ADD_BONUS;
			$values[] = Transactions::MEMBER_GROUP_DEPOSIT_BONUS;
			$values[] = Transactions::PLAYER_REFER_BONUS;
			$values[] = Transactions::RANDOM_BONUS;
			$values[] = Transactions::DEPOSIT;
			$values[] = Transactions::WITHDRAWAL;
			$values[] = Transactions::AUTO_ADD_CASHBACK_TO_BALANCE;
		}

		if (isset($input['enable_date']) && $input['enable_date']) {
			if (isset($input['by_date_from'], $input['by_date_to'])) {
				$where[] = "transactions.trans_date >=?";
				$where[] = "transactions.trans_date <=?";
				$values[] = $input['by_date_from'];
				$values[] = $input['by_date_to'];
			}
		}

		if (isset($input['by_player_level'])) {
			$where[] = "player.levelId = ?";
			$values[] = $input['by_player_level'];
		}

		// if (isset($input['flag'])) {
		// 	$where[] = "transactions.flag = ?";
		// 	$values[] = $input['flag'];
		// }

		// if (isset($input['promo_category'])) {
		// 	$where[] = "transactions.promo_category = ?";
		// 	$values[] = $input['promo_category'];
		// }

		$player_id = null;
		//convert memberUsername to player id
		// $this->utils->debug_log('memberUsername', $input['memberUsername']);
		// if (isset($input['by_username']) && !empty($input['by_username'])) {
		// 	$memberUsername = $input['by_username'];
		// 	$playerId = $this->player_model->getPlayerIdByUsername($memberUsername);
		// 	// $this->utils->debug_log('memberUsername', $memberUsername, $playerId);
		// 	if (!empty($playerId)) {
		// 		$player_id = $playerId;
		// 	}
		// }

		// if ($player_id) {
		// 	$where[] = "(transactions.to_type = ? AND transactions.to_id = ?)";
		// 	$values[] = Transactions::PLAYER;
		// 	$values[] = $player_id;
		// }

		if (isset($input['by_username'], $input['search_by'])) {
			if ($input['search_by'] == 1) {
				$where[] = "transactions.to_username LIKE ?";
				$values[] = '%' . $input['by_username'] . '%';
			} else if ($input['search_by'] == 2) {
				$where[] = "transactions.to_username = ?";
				$values[] = $input['by_username'];
			}

			$where[] = "(transactions.to_type = ?)";
			$values[] = Transactions::PLAYER;
		}

		if (isset($input['agent_name'])) {
            $show_game_platform = true;
            $show_game_type = true;
            $show_game = true;
            $show_player = true;
            $this->load->model(array('agency_model'));
			$agent_detail = $this->agency_model->get_agent_by_name($input['agent_name']);

			if (isset($input['include_all_downlines']) && $input['include_all_downlines'] == 'on' && !empty($agent_detail)) {
				$joins['agency_agents'] = 'player.agent_id = agency_agents.agent_id';
				$parent_ids = array($agent_detail['agent_id']);
				$sub_ids = array();
				$all_ids = $parent_ids;
				while (!empty($sub_ids = $this->agency_model->get_sub_agent_ids_by_parent_id($parent_ids))) {
					//$this->utils->debug_log('sub_ids', $sub_ids);
					$all_ids = array_merge($all_ids, $sub_ids);
					$parent_ids = $sub_ids;
					$sub_ids = array();
				}
				foreach ($all_ids as $i => $id) {
					if ($i == 0) {
						$w = "(player.agent_id = ?";
					} else {
						$w .= " OR player.agent_id = ?";
					}
					$values[] = $id;
				}
				$w .= ")";
				$where[] = $w;
			} else {
				$where[] = "player.agent_id = ?";
				$values[] = $agent_detail['agent_id'];
			}
		}

		// for affiliate
		if (isset($input['affiliate_username']) && ! empty($input['affiliate_username'])) {
			$where[] = "affiliates.username = ?";
			$values[] = $input['affiliate_username'];
			$show_player = true;
		}

		// referrer
		if (isset($input['referrer_username'])) {
			$referrer_player_id = $this->player_model->getPlayerIdByUsername($input['referrer_username']);
			if (!empty($referrer_player_id)) {
				$where[] = "playerfriendreferral.playerId = ?";
				$values[] = $referrer_player_id;
			}
		}

		// adm-user
		if (isset($input['admin_username']) && !empty($input['admin_username'])) {
			$where[] = 'transactions.from_username = ?';
			$values[] = $input['admin_username'];
		}

		// Default clause
		$where[] = "player.deleted_at IS NULL";


		// $this->benchmark->mark('pre_processing_end');

		# END PROCESS SEARCH FORM #################################################################################################################################################
		if($is_export){
            $this->data_tables->options['is_export']=true;
			// $this->data_tables->options['only_sql']=true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename']=$csv_filename;
		}
		$mark = 'data_sql';
		$this->utils->markProfilerStart($mark);
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having);

		if($is_export){
		    //drop result if export
			return $csv_filename;
		}
		$this->utils->markProfilerEndAndPrint($mark);

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(transactions.amount) total_amount', null, $columns, $where, $values);

		$result['summary'][0]['total_amount'] = $this->utils->formatCurrencyNoSym($summary[0]['total_amount']);

		return $result;
	}

     /**
      * detail: get payment status history
      *
      * @param array $request
      * @param Boolean $is_export
      *
      * @return array
      */
     public function payment_status_history_report($request, $is_export = false) {

         $readOnlyDB = $this->getReadOnlyDB();

         $this->load->library('data_tables', array("DB" => $readOnlyDB));
         $this->load->model(array('sale_order'));

         $this->data_tables->is_export = $is_export;

         $input = $this->data_tables->extra_search($request);
         $this->utils->debug_log('search sql --->', $input);
         // $this->benchmark->mark('pre_processing_start');
         $where = array();
         $values = array();
         $having = array();
         $group_by = array("DATE_FORMAT(sale_orders_status_history.created_at,'%Y-%m-%d')", "sale_orders.payment_account_name");
         $this->utils->debug_log('group_by sql --->', $group_by);
         # START DEFINE COLUMNS #################################################################################################################################################
         $i = 0;
         $redirect_or_response_success_status = implode(',' , array(Sale_order::DEPOSIT_STATUS_RERIERCT_URL,Sale_order::DEPOSIT_STATUS_RERIERCT_FORM,Sale_order::DEPOSIT_STATUS_RERIERCT_DIRECT_PAY,Sale_order::DEPOSIT_STATUS_RERIERCT_QRCODE,Sale_order::DEPOSIT_STATUS_RERIERCT_QRCODE_MODAL,Sale_order::DEPOSIT_STATUS_RERIERCT_STATIC));
         $response_fail_status = implode(',', array(Sale_order::DEPOSIT_STATUS_RERIERCT_ERROR,Sale_order::DEPOSIT_STATUS_RERIERCT_ERROR_MODAL));

         $columns = array(
             array(
                 'dt' => $i++,
                 'alias' => 'created_at',
                 'select' => 'DATE_FORMAT(sale_orders_status_history.created_at, \'%Y-%m-%d\')',
                 'name' => lang('Date'),
             ),
             array(
                 'dt' => $i++,
                 'alias' => 'payment_account_name',
                 'select' => 'sale_orders.payment_type_name',
                 'formatter' => function ($d) use ($is_export) {
                     if ($is_export) {
                         return lang($d);
                     } else {
                         return lang($d);
                     }
                 },
                 'name' => lang("pay.payment_account"),
             ),
             array(
                 'dt' => $i++,
                 'alias' => 'request_num',
                 'select' => 'SUM( CASE WHEN sale_orders_status_history.status = ' . Sale_order::DEPOSIT_STATUS_CREATE_ORDER . ' THEN 1 ELSE 0 END )',
                 'formatter' => function ($d) use ($is_export) {
                     if ($is_export) {
						return $d;
					} else {
						return $d ? $d : 0;
					}
                 },
                 'name' => lang("report.p59"),
             ),
             array(
                 'dt' => $i++,
                 'alias' => 'submit_num',
                 'select' => 'SUM( CASE WHEN sale_orders_status_history.status = ' . Sale_order::DEPOSIT_STATUS_SUBMIT_ORDER . ' THEN 1 ELSE 0 END )',
                 'formatter' => function ($d) use ($is_export) {
                     if ($is_export) {
						return $d;
					} else {
						return $d ? $d : 0;
					}
                 },
                 'name' => lang("report.p60"),
             ),
             array(
                 'dt' => $i++,
                 'alias' => 'redirect_or_response_success_num',
                 'select' => 'SUM( CASE WHEN sale_orders_status_history.status IN (' . $redirect_or_response_success_status . ') THEN 1 ELSE 0 END )',
                 'formatter' => function ($d) use ($is_export) {
                     if ($is_export) {
						return $d;
					} else {
						return $d ? $d : 0;
					}
                 },
                 'name' => lang("report.p61"),
             ),
             array(
                 'dt' => $i++,
                 'alias' => 'response_fail_num',
                 'select' => 'SUM( CASE WHEN sale_orders_status_history.status IN (' . $response_fail_status . ') THEN 1 ELSE 0 END )',
                 'formatter' => function ($d) use ($is_export) {
                     if ($is_export) {
						return $d;
					} else {
						return $d ? $d : 0;
					}
                 },
                 'name' => lang("report.p62"),
             ),
             array(
                 'dt' => $i++,
                 'alias' => 'get_callback_num',
                 'select' => 'SUM( CASE WHEN sale_orders_status_history.status = ' . Sale_order::DEPOSIT_STATUS_GET_CALLBACK . ' THEN 1 ELSE 0 END )',
                 'formatter' => function ($d) use ($is_export) {
                     if ($is_export) {
						return $d;
					} else {
						return $d ? $d : 0;
					}
                 },
                 'name' => lang("report.p63"),
             ),
             array(
                 'dt' => $i++,
                 'alias' => 'process_callback_fail_num',
                 'select' => 'SUM( CASE WHEN sale_orders_status_history.status = ' . Sale_order::DEPOSIT_STATUS_CHECK_CALLBACK_ORDER_FAILED . ' THEN 1 ELSE 0 END )',
                 'formatter' => function ($d) use ($is_export) {
                     if ($is_export) {
						return $d;
					} else {
						return $d ? $d : 0;
					}
                 },
                 'name' => lang("report.p64"),
             ),
             array(
                 'dt' => $i++,
                 'alias' => 'approve_sale_order_num',
                 'select' => 'SUM( CASE WHEN sale_orders_status_history.status = ' . Sale_order::DEPOSIT_STATUS_APPROVE_SALE_ORDER . ' THEN 1 ELSE 0 END )',
                 'formatter' => function ($d) use ($is_export) {
                     if ($is_export) {
						return $d;
					} else {
						return $d ? $d : 0;
					}
                 },
                 'name' => lang("report.p65"),
             ),
             array(
                 'dt' => $i++,
                 'alias' => 'success_rate', //(成功上分 / 导页成功 or 即时返回成功)
                 'select' => '(SUM( CASE WHEN sale_orders_status_history.status = ' . Sale_order::DEPOSIT_STATUS_APPROVE_SALE_ORDER . ' THEN 1 ELSE 0 END ) / SUM( CASE WHEN sale_orders_status_history.status IN ('. $redirect_or_response_success_status . ') THEN 1 ELSE 0 END ))*100',
                 'formatter' => function ($d) use ($is_export) {
                     if ($is_export) {
                         return $this->data_tables->percentageFormatter($d/100);
					} else {
						return $this->data_tables->percentageFormatter($d/100);
					}
                 },
                 'name' => lang("report.p66"),
             ),
             array(
                 'dt' => $i++,
                 'alias' => 'failed_rate', //(处理回调失败 / 导页成功 or 即时返回成功)
                 'select' => '(SUM( CASE WHEN sale_orders_status_history.status = ' . Sale_order::DEPOSIT_STATUS_CHECK_CALLBACK_ORDER_FAILED . ' THEN 1 ELSE 0 END ) / SUM( CASE WHEN sale_orders_status_history.status IN ('. $redirect_or_response_success_status . ') THEN 1 ELSE 0 END ))*100',
                 'formatter' => function ($d) use ($is_export) {
                     if ($is_export) {
                         return $this->data_tables->percentageFormatter($d/100);
					} else {
						return $this->data_tables->percentageFormatter($d/100);
					}
                 },
                 'name' => lang("report.p67"),
             )
         );
         # END DEFINE COLUMNS #################################################################################################################################################

         $table = 'sale_orders_status_history';
         $joins = array(
             'sale_orders' => "sale_orders_status_history.order_id = sale_orders.id",
         );

         if (isset($input['enable_date']) && $input['enable_date']) {
             if (isset($input['by_date_from'], $input['by_date_to'])) {
                 $where[] = "sale_orders_status_history.created_at >=?";
                 $where[] = "sale_orders_status_history.created_at <=?";
                 $values[] = $input['by_date_from'];
                 $values[] = $input['by_date_to'];
             }
         }

         if (isset($input['by_accountname'])) {
             $accountname = $input['by_accountname'];
             if (mb_strlen($accountname,"utf-8") != strlen($accountname)){
                 //turn utf8 to unicode
                 $unicode = 0;
                 $unicode = (ord($accountname[0]) & 0x1F) << 12;
                 $unicode |= (ord($accountname[1]) & 0x3F) << 6;
                 $unicode |= (ord($accountname[2]) & 0x3F);
                 $accountname = dechex($unicode);
             }

             $where[] = "sale_orders.payment_type_name LIKE ?";
             $values[] = '%' . $accountname . '%';
         }

         if (isset($input['by_success_rate_greater_than'])) {
             $having_key = ($input['by_success_rate_greater_than'] == 0) ? 'success_rate >= ' : 'success_rate >= ' ;
             $having[$having_key] = (float)$input['by_success_rate_greater_than'];
         }

         if (isset($input['by_success_rate_less_than'])) {
             $having_key = ($input['by_success_rate_less_than'] == 0) ? 'success_rate <= ' : 'success_rate <= ' ;
             $having[$having_key] = (float)$input['by_success_rate_less_than'];
         }

         if (isset($input['by_failed_rate_greater_than'])) {
             $having_key = ($input['by_failed_rate_greater_than'] == 0) ? 'failed_rate >= ' : 'failed_rate >= ' ;
             $having[$having_key] = (float)$input['by_failed_rate_greater_than'];
         }

         if (isset($input['by_failed_rate_less_than'])) {
             $having_key = ($input['by_failed_rate_less_than'] == 0) ? 'failed_rate <= ' : 'failed_rate <= ' ;
             $having[$having_key] = (float)$input['by_failed_rate_less_than'];
         }
         # END PROCESS SEARCH FORM #################################################################################################################################################
         $mark = 'data_sql';
         $this->utils->markProfilerStart($mark);
         $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having);
         $this->utils->markProfilerEndAndPrint($mark);
         return $result;
     }

	/**
	 * detail: get balance history of a certain player
	 *
	 * @param int $player_id balance_history player_id
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function balance_history($player_id, $request, $is_export = false) {

		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('transactions', 'player_model', 'affiliatemodel'));

		$this->data_tables->is_export = $is_export;

		$this->benchmark->mark('pre_processing_start');

		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'alias' => 'id',
				'select' => 'balance_history.id',
			),
			array(
				'alias' => 'player_id',
				'select' => 'balance_history.player_id',
			),
			array(
				'dt' => $i++,
				'alias' => 'date',
				'select' => 'balance_history.created_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('player.ut01'),
			),
			array(
				'dt' => $i++,
				'alias' => 'action_type',
				'select' => 'balance_history.action_type',
				'name' => lang('player.ut01'),
				'formatter' => function ($d, $row) use ($is_export) {

					if ($d < 1000) {
						return lang('transaction.transaction.type.' . $d);
					} else {
						return lang('balance.action.type.' . $d);
					}

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'main_wallet',
				'select' => 'balance_history.main_wallet',
				'formatter' => 'currencyFormatter',
				'name' => lang('Main Wallet'),
			),
			array(
				'dt' => $i++,
				'alias' => 'total_balance',
				'select' => 'balance_history.total_balance',
				'formatter' => 'currencyFormatter',
				'name' => lang('Total Balance'),
			),
			array(
				'dt' => $i++,
				'alias' => 'big_wallet',
				'select' => 'balance_history.big_wallet',
				'formatter' => function($d, $row) use($is_export){
					if($is_export){
						return '';
					}else{
						return '<button class="btn btn-success btn-xs show_bigwallet_details btn-scooter" data-playerid="'.$row['player_id'].'">'.lang('Details').'</button>';
					}
				},
				'name' => lang('Wallet Details'),
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'balance_history';
		$joins = array(
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		// $request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		// $this->utils->debug_log('input', $input);

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "balance_history.created_at >= ?";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "balance_history.created_at <= ?";
			$values[] = $input['dateRangeValueEnd'];
		}

		//convert memberUsername to player id
		// $this->utils->debug_log('memberUsername', $input['memberUsername']);
		if (isset($input['memberUsername']) && !empty($input['memberUsername'])) {
			$memberUsername = $input['memberUsername'];
			$playerId = $this->player_model->getPlayerIdByUsername($memberUsername);
			// $this->utils->debug_log('memberUsername', $memberUsername, $playerId);
			if (!empty($playerId)) {
				$player_id = $playerId;
			}
		}

		if ($player_id) {
			$where[] = "balance_history.player_id =?";
			$values[] = $player_id;
		}

		$this->benchmark->mark('pre_processing_end');

		# END PROCESS SEARCH FORM #################################################################################################################################################

		$this->benchmark->mark('data_sql_start');
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->benchmark->mark('data_sql_end');

		return $result;
	}

  	/**
  	 * detail: get deposit list of a certain player
  	 *
  	 * @param int $playerId
  	 * @param array $request
  	 * @param Boolean $is_export
  	 *
  	 * @return array
  	 */
  	public function depositList($playerId=null, $request, $is_export = false, $is_locked = false, $inPlayerCenter = '', $notDatatable = '', $_where = '', $csv_filename=null, $allow_multiple_select = false) {

		$this->load->model(array('sale_order', 'payment_account','player_attached_proof_file_model','sale_orders_notes'));
  		$this->load->library(array('data_tables'));
  		$player_attached_proof_file_model = $this->player_attached_proof_file_model;
  		$permDepositDetail = false;
  		if( empty($inPlayerCenter) && !$is_export ){

  			$this->load->library(array('permissions'));
  			$this->permissions->setPermissions();

  			$permDepositDetail = $this->permissions->checkPermissions('approve_decline_deposit');
  		}

  		$input = $this->data_tables->extra_search($request);
  		$dwStatus = isset($input['dwStatus'])?$input['dwStatus']:'';

  		# START DEFINE COLUMNS #################################################################################################################################################
  		$i = 0;
  		$columns = array(
  			array(
  				'alias' => 'geo_location',
  				'select' => 'sale_orders.geo_location',
  			),
  			array(
  				'alias' => 'playerId',
  				'select' => 'player.playerId',
  			),
			array(
				'dt' => (!$is_export) ? $i++ : NULL,
  				'alias' => 'id',
  				'select' => 'sale_orders.id',
				'name' => lang('column.id'),
				'formatter' => function ($d) use ($permDepositDetail, $is_export, $is_locked, $allow_multiple_select) {
					if($is_locked) {
						return '<input type="checkbox" name="sales_order_id" value="'.$d.'">';

					}
  					if (!$is_export) {
						if ($permDepositDetail) {
  							$action = '<div class="clearfix" style="width:65px;">';
  							if ($allow_multiple_select) {
  								$action .= '<div class="col-md-3" style="padding:5px 1px 0 2px"><input type="checkbox" name="sales_order_id" class="chk-order-id" value="'.$d.'"></div>';
  							}
  							$action .= '<div class="col-md-9" style="padding:0 2px 0 2px"><span class="btn btn-xs btn-info review-btn" onclick="getDepositDetail(' . $d . ')" data-toggle="modal" >' . lang("lang.details") . '</span></div>';
  							$action .= "</div>";

  							return $action;	// data-target="#depositDetailsModal"
  						} else {
  							return '';
  						}
  				    }else{
						return $d;
  				    }
  				},
  			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'sale_orders.status',
				'name' => lang('lang.status'),
				'formatter' => function ($d) use ($is_export) {
					if(!empty($d)){
						if (!$is_export) {
							switch ($d) {
								case Sale_order::STATUS_DECLINED:
									return '<i class="text-danger">' . lang('sale_orders.status.' . $d) . '</i>';
									break;
								case Sale_order::STATUS_SETTLED:
									return '<i class="text-success">' . lang('sale_orders.status.' . $d) . '</i>';
									break;
								default:
									return '<i class="text-default">' . lang('sale_orders.status.' . $d) . '</i>';
							}
							return $payment_flag;
						} else {
							return lang('sale_orders.status.' . $d);
						}
					} else {
						if (!$is_export) {
							return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						} else {
							return lang('lang.norecyet');
						}
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'secure_id',
				'select' => 'sale_orders.secure_id',
				'name' => lang('deposit_list.order_id'),
  				'formatter' => function ($d, $row) use ($is_export,$dwStatus)  {
  					if (!$is_export) {
  						if($this->utils->isEnabledFeature('highlight_deposit_id_in_list')) {
  							if(!empty($d)){
  								$deposit_id_highlight_sequence = $this->utils->getConfig('deposit_id_highlight_sequence');
	  							if(!empty($deposit_id_highlight_sequence)){
	  								switch ($dwStatus) {
						  				case Sale_order::VIEW_STATUS_REQUEST_ALL:
						  				case Sale_order::VIEW_STATUS_REQUEST_TODAY:
						  				case Sale_order::VIEW_STATUS_REQUEST:
					  						foreach ($deposit_id_highlight_sequence as $key => $value) {
					  							if(isset($value['timeout']) && isset($value['background_color'])){
					  								if($this->utils->isTimeoutNow($row['created_at'], $value['timeout'])){
					  									return '<span style="background-color: ' . $value['background_color'] . '; color:white">' . $d . '</span>';
					  								}
					  							}
					  						}
						  					break;
						  				default:
						  					return '<span>' . $d . '</span>';
						  					break;
						  			}
	  							}
  							}
						}
  						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return $d ?: lang('lang.norecyet');
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'username',
  				'select' => '( CASE WHEN affiliates.username IS NULL THEN player.username ELSE CONCAT(player.username, \' (\', affiliates.username,  \')\' ) END )',
  				'name' => lang('system.word38'),
  				'formatter' => function ($d, $row) use ($is_export)  {
  					if (!$is_export) {
  						return sprintf('<a href="/player_management/userInformation/%s">%s</a>', $row['playerId'], $d);
  					}else{
  						return $d;
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'payment_flag',
  				'select' => 'payment_account.flag',
  				'name' => lang('pay.payment_account_flag'),
  				'formatter' => function ($d, $row) {

  					$payment_flag = lang('pay.auto_online_payment');

  					switch ($row['payment_flag']) {
  					case Payment_account::FLAG_MANUAL_LOCAL_BANK:
  						$payment_flag = lang('pay.local_bank_offline');
  						break;
  					case Payment_account::FLAG_MANUAL_ONLINE_PAYMENT:
  						$payment_flag = lang('pay.manual_online_payment');
  						break;
  					case Payment_account::FLAG_AUTO_ONLINE_PAYMENT:
  						$payment_flag = lang('pay.auto_online_payment');
  						break;
  					}

  					return $payment_flag;

  				},

  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'created_at',
  				'select' => 'sale_orders.created_at',
  				'name' => lang('pay.reqtime'),
  				'formatter' => 'dateTimeFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'deposit_date',
  				'select' => 'sale_orders.player_submit_datetime',
  				'name' => lang('Deposit Datetime'),
  				'formatter' => function ($d,$row)  use ($is_export)  {

  					if($row['status'] == Sale_order::STATUS_DECLINED){
        				if (!$is_export) {
  							return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
	  					}else{
	  						return lang('lang.norecyet');
	  					}
  					}else{
  						if (!$is_export) {
  							return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
	  					}else{
	  						return $d;
	  					}
  					}
 				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'real_name',
  				'select' => 'CONCAT(ifnull(playerdetails.firstName, ""), \' \', ifnull(playerdetails.lastName, "") )',
  			    'name' => lang('system.word39'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return  ($d == " ") ?  '<i class="text-muted">' . lang('lang.norecyet') . '</i>' : $d;
  					}else{
  						return ($d == " ") ? lang('lang.norecyet')  :  $d ;
  					}
  				},
  			),
  			array(
				'dt' => (!$is_export) ? $i++ : NULL,
  				'alias' => 'affiliate',
  				'select' => 'affiliates.username',
  				'formatter' => function ($d)  use ($is_export)  {
  					if (!$is_export) {
  						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return $d;
  					}
  				},
  			),
  			array(
                'dt' => $i++,
                'alias' => 'group_level',
                'select' => 'player.levelName',
                'name' => lang('pay.playerlev'),
                'formatter' => function ($d, $row) {
		            $player_level = $this->player_model->getPlayerCurrentLevel($row['playerId']);
		            if($player_level){
		                $groupName = lang($player_level[0]['groupName']);
		                $levelName = lang($player_level[0]['vipLevelName']);
		            	return $groupName . ' - ' .$levelName;
		            }
		            else{
		            	return null;
		            }
                },
            ),
  			array(
  				'dt' => $i++,
  				'alias' => 'amount',
  				'select' => 'sale_orders.amount',
  				'name' => lang('Deposit Amount'),
  				'formatter' => function ($d) use ($is_export) {
  					if (!$is_export) {
  						return '<strong>'.$this->utils->formatCurrencyNoSym($d).'</strong>';
  					}else{
  						return $this->utils->formatCurrencyNoSym($d) ;
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'payment_type_name',
  				'select' => 'sale_orders.payment_type_name',
  				'name' => lang('pay.collection_name'),
  				'formatter' => 'languageFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'ip',
  				'select' => 'sale_orders.ip',
  				'name' => lang('pay.ip'),
  				'formatter' => function ($d, $row)  use ($is_export)  {
  					if (!$is_export) {
  						return trim($d . ' ' . trim($row['geo_location'], ',')) ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return trim($d . ' ' . trim($row['geo_location'], ',')) ?: lang('lang.norecyet');
  					}

  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'updated_at',
  				'select' => 'sale_orders.updated_at',
				'name' => lang('pay.updatedon'),
  				'formatter' => 'dateTimeFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'timeout_at',
  				'select' => 'sale_orders.timeout_at',
  				'name' => lang('cms.timeoutAt'),
  				'formatter' => 'dateTimeFormatter',
  			),
			array(
				'dt' => $i++,
				'alias' => 'process_time',
				'select' => 'sale_orders.process_time',
				'name' => lang('pay.procsson'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ?: lang('lang.norecyet');
					}
				},
			),
			// array(
			// 	'dt' => $i++,
			// 	'alias' => 'player_deposit_slip_path',
			// 	'select' => 'sale_orders.id',
			// 	'name' => lang('pay.depslip'),
			// 	'formatter' => function ($d, $row)  use ($is_export, $player_attached_proof_file_model) {
			// 		if($is_export) return '';

			// 		if (!$this->utils->isEnabledFeature('enable_deposit_upload_documents')) return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';

			// 		if(!$d) return lang('lang.norecyet');

			// 		$proof_slips = $player_attached_proof_file_model->getAttachementRecordInfo($row['playerId'],null,self::Deposit_Attached_Document,null,null,$d,null,false);

			// 		if(empty($proof_slips)) return lang('lang.norecyet');

			// 		$output = '<a onclick="modal(\'/payment_management/show_proof_slip/' . $row['playerId'] .'/'.$d.'/'.self::Deposit_Attached_Document. '\',\'' . lang('Attached document') . '\')" href="javascript:void(0);" data-toggle="tooltip" title="View attached file"><i class="fa fa-search"></i></a>';

			// 		$output .= 'Y';//!empty($proof_slips) ? 'Y' : 'N';

			// 		return $output;
			// 	},
			// ),
  			array(
  				'dt' => $i++,
  				'alias' => 'payment_account_name',
  				'select' => 'payment_account.payment_account_name',
  				'name' => lang('pay.collection_account_name'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return $d ?: lang('lang.norecyet');
  					}
  				},
			  ),
			array(
				'dt' => $i++,
				'alias' => 'notes',
				'select' => 'payment_account.notes',
				'name' => lang('con.bnk20'),
				'formatter' => function ($d) {
					return $d ?: lang('lang.norecyet');
				},
			),
  			array(
  				'dt' => $i++,
  				'alias' => 'player_payment_type_name',
  				'select' => 'sale_orders.player_payment_type_name',
  				'name' => lang('pay.deposit_payment_name'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return lang($d) ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return lang($d) ?: lang('lang.norecyet');
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'player_payment_account_name',
  				'select' => 'sale_orders.player_payment_account_name',
  				'name' => lang('pay.deposit_payment_account_name'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return $d ?: lang('lang.norecyet');
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'player_payment_account_number',
  				'select' => 'sale_orders.player_payment_account_number',
  				'name' => lang('pay.deposit_payment_account_number'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return $d ?: lang('lang.norecyet');
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'player_deposit_transaction_code',
  				'select' => 'sale_orders.player_deposit_transaction_code',
  				'name' => lang('pay.deposit_transaction_code'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return $d ?: lang('lang.norecyet');
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'promoName',
  				'select' => 'promorules.promoName',
  				'name' => lang('pay.promoname'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return $d ?: lang('lang.norecyet');
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'bonusAmount',
  				'select' => 'playerpromo.bonusAmount',
  				'name' => lang('pay.promobonus'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return '<strong>'.$this->utils->formatCurrencyNoSym($d).'</strong>';
  					}else{
  						return $this->utils->formatCurrencyNoSym($d);
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'external_order_id',
  				'select' => 'sale_orders.external_order_id',
  				'name' => lang('External ID'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return $d ?: lang('lang.norecyet');
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'bank_order_id',
  				'select' => 'sale_orders.bank_order_id',
  				'name' => lang('Bank Order ID'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return $d ?: lang('lang.norecyet');
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'player_deposit_time',
  				'select' => 'sale_orders.player_deposit_time',
  				'name' => lang('Deposit Datetime From Player'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return ($d != "0000-00-00 00:00:00") ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return ($d != "0000-00-00 00:00:00") ? $d : lang('lang.norecyet');
  					}
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'player_mode_of_deposit',
  				'select' => 'sale_orders.player_mode_of_deposit',
  				'name' => lang('Mode of Deposit'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return $d ? lang($d) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
  					}else{
  						return $d ? lang($d) : lang('lang.norecyet');
  					}
  				},
  			),
			array(
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'sale_orders.id',
				'name' => lang('Player Deposit Note'),
				'formatter' => function ($sale_order_id) use ($is_export)  {
					$display_last_notes = false;
					$donotShowUsername = true;
					$allNotes = $this->sale_orders_notes->getNotesByNoteType(Sale_orders_notes::PLAYER_NOTES, $sale_order_id, $display_last_notes, $donotShowUsername);

					if (!$is_export) {
						return $allNotes ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return str_replace('</br>', "\n", $allNotes) ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'processed_by_name',
				'select' => 'adminusers.username',
				'name' => lang('pay.procssby'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'sale_orders.id',
				'name' => lang('External Note'),
				'formatter' => function ($sale_order_id) use ($is_export)  {
					$display_last_notes = false;
					$limit_the_number_of_words_displayed = true;
					$allNotes = $this->sale_orders_notes->getNotesByNoteType(Sale_orders_notes::EXTERNAL_NOTE, $sale_order_id, $display_last_notes, false, $limit_the_number_of_words_displayed);
					$exportNotes = $this->sale_orders_notes->getNotesByNoteType(Sale_orders_notes::EXTERNAL_NOTE, $sale_order_id, $display_last_notes, false, false);

					if (!$is_export) {
						return $allNotes ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return str_replace('</br>', "\n", $exportNotes) ?: lang('lang.norecyet');
					}
  				},
  			),
			array(
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'sale_orders.id',
				'name' => lang('Internal Note'),
				'formatter' => function ($sale_order_id) use ($is_export)  {
					$display_last_notes = false;
					$limit_the_number_of_words_displayed = true;
					$allNotes = $this->sale_orders_notes->getNotesByNoteType(Sale_orders_notes::INTERNAL_NOTE, $sale_order_id, $display_last_notes, false, $limit_the_number_of_words_displayed);
					$exportNotes = $this->sale_orders_notes->getNotesByNoteType(Sale_orders_notes::INTERNAL_NOTE, $sale_order_id, $display_last_notes, false,false);

					if (!$is_export) {
						return $allNotes ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return str_replace('</br>', "\n", $exportNotes) ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'sale_orders.id',
				'name' => lang('Action Log'),
				'formatter' => function ($sale_order_id) use ($is_export)  {
					$display_last_notes = false;

					$allNotes = $this->sale_orders_notes->getNotesByNoteType(Sale_orders_notes::ACTION_LOG, $sale_order_id, $display_last_notes);

					if (!$is_export) {
						return $allNotes ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return str_replace('</br>', "\n", $allNotes) ?: lang('lang.norecyet');
					}
				},
			),
  		);
  		# END DEFINE COLUMNS #################################################################################################################################################

  		$table = 'sale_orders';
  		$joins = array(
  			'player' => 'player.playerId = sale_orders.player_id',
  			'playerdetails' => 'playerdetails.playerId = sale_orders.player_id',
  			'adminusers' => 'sale_orders.processed_by = adminusers.userId',
  			'playerpromo' => 'playerpromo.playerpromoId = sale_orders.player_promo_id',
  			'promorules' => 'playerpromo.promorulesId = promorules.promorulesId',
  			'transactions' => 'transactions.id = sale_orders.transaction_id',
  			'payment_account' => 'payment_account.id = sale_orders.payment_account_id',
  			'affiliates' => 'affiliates.affiliateId = player.affiliateId',
  		);

  		# START PROCESS SEARCH FORM #################################################################################################################################################
  		$where = array();
  		$values = array();

  		if( ! empty( $notDatatable ) ) $input = $request;
  		// $this->utils->debug_log('deposit search input', $input);

  		$where[] = 'sale_orders.payment_kind = ?';
  		$values[] = Sale_order::PAYMENT_KIND_DEPOSIT;

  		if (!empty($playerId)) {
  			$where[] = "sale_orders.player_id = ?";
  			$values[] = $playerId;
  		}

  		if (isset($input['username'])) {
  			$where[] = "player.username LIKE ?";
  			$values[] = '%' . $input['username'] . '%';
  		}

  		if (isset($input['payment_flag_1']) || isset($input['payment_flag_2']) || isset($input['payment_flag_3'])) {
  			$payment_flag=[];
			if(@$input['payment_flag_1']=='1'){
				$payment_flag[]=1;
			}
			if(@$input['payment_flag_2']=='1'){
				$payment_flag[]=2;
			}
			if(@$input['payment_flag_3']=='1'){
				$payment_flag[]=3;
			}
			if( ! empty($payment_flag) ){ /// for OGP-15228 Cannot export CSV under Payment (Deposit List)
				$where[] = "payment_account.flag IN (".implode(',', $payment_flag).")";
			}
  		}

  		if (isset($input['realname'])) {
  			$where[] = "CONCAT_WS(' ', playerdetails.firstName, playerdetails.lastName) LIKE ?";
  			$values[] = '%' . $input['realname'] . '%';
  		}

  		if (isset($input['affiliate'])) {
  			$where[] = "affiliates.username LIKE ?";
  			$values[] = '%' . $input['affiliate'] . '%';
  		}

  		if (isset($input['amount'])) {
  			$where[] = "sale_orders.amount = ?";
  			$values[] = $input['amount'];
  		}

  		if (isset($input['amount_from'])) {
  			$where[] = "sale_orders.amount >= ?";
  			$values[] = $input['amount_from'];
  		}

  		if (isset($input['amount_to'])) {
  			$where[] = "sale_orders.amount <= ?";
  			$values[] = $input['amount_to'];
  		}

		if (!empty($input['locked_user_id'])) {
			$where[] = "sale_orders.locked_user_id != ?";
			$values[] = '';
		}

		if (!empty($input['processed_by'])) {
			$where[] = "sale_orders.processed_by = ?";
			$values[] = $input['processed_by'];
		}


		if ($this->safeGetParam($input, 'enable_date') == '1') {
			if (isset($input['deposit_date_from'], $input['deposit_date_to'], $input['search_time'])) {
				if($input['search_time'] =='1') {
					$where[] = "sale_orders.created_at >= ?";
					$where[] = "sale_orders.created_at <= ?";
				}
				else if($input['search_time'] =='2'){
					$where[] = "sale_orders.updated_at >= ?";
					$where[] = "sale_orders.updated_at <= ?";
				}
				$values[] = $input['deposit_date_from'];
				$values[] = $input['deposit_date_to'];
			}else{
				if (isset($input['deposit_date_from'], $input['deposit_date_to'])) {
					$where[] = "sale_orders.created_at >= ?";
					$where[] = "sale_orders.created_at <= ?";
					$values[] = $input['deposit_date_from'];
					$values[] = $input['deposit_date_to'];
				}
			}
  	    }

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "sale_orders.created_at >= ?";
			$where[] = "sale_orders.created_at <= ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		if( ! empty( $_where ) ) $where[] = $_where;

		if (isset($input['search_status'])) {
			if($input['searchBtn'] == '1'){
				if ($input['search_status'] != 'allStatus') {
					$multiDwStatus = $input['search_status'];
					$where[] = "sale_orders.status = ?";
					$values[] = $multiDwStatus;
				}

			}
		}

		if(isset($input['dwStatus'])){
			switch ($input['dwStatus']) {
				case Sale_order::VIEW_STATUS_REQUEST_ALL:
				case Sale_order::VIEW_STATUS_REQUEST_TODAY:
				case Sale_order::VIEW_STATUS_REQUEST:
				case Sale_order::VIEW_STATUS_REQUEST_BANKDEPOSIT:
				case Sale_order::VIEW_STATUS_REQUEST_3RDPARTY:
					$where[] = "sale_orders.status IN (?,?)";
					$values[] = Sale_order::STATUS_PROCESSING;
					$values[] = Sale_order::STATUS_CHECKING;
					break;
				case Sale_order::VIEW_STATUS_APPROVED_ALL:
				case Sale_order::VIEW_STATUS_APPROVED_TODAY:
				case Sale_order::VIEW_STATUS_APPROVED:
					$where[] = "sale_orders.status IN (?,?)";
					$values[] = Sale_order::STATUS_BROWSER_CALLBACK;
					$values[] = Sale_order::STATUS_SETTLED;
					break;
				case Sale_order::VIEW_STATUS_DECLINED_ALL:
				case Sale_order::VIEW_STATUS_DECLINED_TODAY:
				case Sale_order::VIEW_STATUS_DECLINED:
					$where[] = "sale_orders.status = ?";
					$values[] = Sale_order::STATUS_DECLINED;
					break;
			}
		}

		if (isset($input['excludeTimeout'], $input['search_status'])) {
			if($input['search_status'] == Sale_order::STATUS_PROCESSING) {
				$where[] = "(sale_orders.timeout_at > ? OR sale_orders.timeout_at IS NULL)";
				$values[] = $this->utils->getNowForMysql();
			}
  		}

  		if (isset($input['secure_id'])) {
  			$where[] = "sale_orders.secure_id = ?";
  			$values[] = $input['secure_id'];
  		}

  		if (isset($input['external_order_id']) && !empty($input['external_order_id'])) {
  			$where[] = "sale_orders.external_order_id = ?";
  			$values[] = $input['external_order_id'];
  		}

  		if (isset($input['bank_order_id']) && !empty($input['bank_order_id'])) {
  			$where[] = "sale_orders.bank_order_id = ?";
  			$values[] = $input['bank_order_id'];
  		}

  		$select_all_payment_account=@$input['select_all']=='true';

  		if(!$select_all_payment_account){

	  		$payment_account_id_arr=[];

			$payment_account_list = $this->payment_account->getAllPaymentAccountDetails();
			if(is_array($payment_account_list)){
				foreach ($payment_account_list as $payment_account) {
					$id=$payment_account->payment_account_id;
					$key='payment_account_id_'.$id;
					if($select_all_payment_account || @$input[$key]=='true'){
						$payment_account_id_arr[]=intval($id);
					}
				}
			}

			if(!empty($payment_account_id_arr)){
				$where[] = "sale_orders.payment_account_id in (".implode(',',$payment_account_id_arr).")";
			}
  		}


        // to see test player deposit on their user information page
        if(empty($playerId)){
        	$where[] = "player.deleted_at IS NULL";
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
		$distinct=false;
		$external_order=[];
		$countOnlyField='sale_orders.id';

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins,
			$group_by, $having, $distinct, $external_order, $notDatatable, $countOnlyField);

		if($is_export){
		    //drop result if export
			return $csv_filename;
		}

  		return $result;
  	}

	/**
	 * detail: get withdraw lists of certain player
	 *
	 * @param int $playerId walletaccount playerId
	 * @param Boolean $enabledAction
	 * @param array $request
	 * @param Boolean $is_export
	 * @return  array
	 */
	public function withdrawList($playerId = null, $enabledAction = 'true', $request, $is_export = false, $is_locked = false, $csv_filename=null) {

		$this->load->model(array('wallet_model', 'transaction_notes', 'payment_account', 'common_category', 'operatorglobalsettings', 'walletaccount_notes','walletaccount_timelog','users','player_model'));
		$this->load->library(array('data_tables'));
		$this->load->helper(['player_helper']);

		$input = $this->data_tables->extra_search($request);
		$common_category = $this->common_category;
		$permWithdrawDetail=false;
		if(!$is_export ){
			$this->load->library('permissions');
			$this->permissions->setPermissions();
            if($this->utils->isEnabledFeature('separate_approve_decline_withdraw_pending_review_and_request_permission')){
                $permWithdrawDetail = true;
            }else{
                $permWithdrawDetail = $this->permissions->checkPermissions('approve_decline_withdraw');
            }
		}
		$dwStatus = isset($input['dwStatus']) ? $input['dwStatus'] : '';
		$enabledAction = $enabledAction == 'true';

		//use the currency settings.
		$currency_settings = $this->utils->getCurrentCurrency()['currency_code'];

		$customStage = [];
        $stages = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();
        foreach ($stages as $key => $value) {
            if(is_array($value) && array_key_exists('enabled', $value) && $value['enabled']){
                if(is_int($key)){
                    $customStage['CS'.$key] = $value['name'];
                }
            }
        }

		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'alias' => 'playerId',
				'select' => 'player.playerId',
			),
			array(
				'alias' => 'is_checking',
				'select' => 'walletaccount.is_checking',
			),
			array(
				'alias' => 'aff_username',
				'select' => 'affiliates.username',
			),
			array(
				'dt' => ($enabledAction && ($permWithdrawDetail || $dwStatus != 'request' && $dwStatus != 'pending_review')) ? $i++ : NULL,
				'alias' => 'walletAccountId',
				'select' => 'walletaccount.walletAccountId',
				'name' => lang('column.id'),
				'formatter' => function ($d, $row) use ($permWithdrawDetail, $enabledAction, $is_export, $is_locked) {
					if($is_locked) {
						return '<input type="checkbox" name="wallet_account_id" value="'.$d.'">';
					}

					if (!$is_export) {
						if ($permWithdrawDetail && $enabledAction) {
							$output = '<div class="clearfix" style="width:65px;">';
							$output_checckbox = $this->utils->isEnabledFeature("enable_batch_withdraw_process_apporve_decline") ?
							'<div class="col-md-3" style="padding:5px 1px 0 2px"><input type="checkbox" class="chk-order-id" data-player_id="' .$row['playerId']. '" data-dwstatus="' .$row['dwStatus']. '" data-withdrawcode="' .$row['withdrawCode']. '" name="wallet_account_id" value="'.$d.'"></div>' : '';

							$output .= $output_checckbox;

							$output .= '<div class="col-md-9" style="padding:0 2px 0 2px"><span class="btn btn-xs btn-info review-btn" data-toggle="modal" onclick="';
							if ($row['dwStatus'] == Wallet_model::APPROVED_STATUS || $row['dwStatus'] == Wallet_model::PAY_PROC_STATUS) {
								$output .= 'getWithdrawalApproved(' . $row['walletAccountId'] .',' .APPROVED_MODAL.','.$row['playerId'].')" >';		// data-target="#approvedDetailsModal"
							} else if ($row['dwStatus'] == Wallet_model::DECLINED_STATUS || $row['dwStatus'] == Wallet_model::PAID_STATUS) {
								# Decline and Paid use a same modal
								$output .= 'getWithdrawalDeclined(' . $row['walletAccountId'] . ',' . $row['playerId'] . ',' .DECLINED_MODAL.')" >';		// data-target="#declinedDetailsModal"
								// } else if ($row['dwStatus'] == Wallet_model::REQUEST_STATUS) {
								// 	$output .= 'getWithdrawalRequest(' . $row['walletAccountId'] . ',' . $row['playerId'] . ')" data-target="#requestDetailsModal">';
							} else {
								// Determines the target modal to be showed
								$customWithdrawalSetting = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();
								$useApprovedModal = false;

								if ($customWithdrawalSetting['maxCSIndex'] == -1) {
									$useApprovedModal = true;
								} else if (substr($row['dwStatus'], 0, 2) == 'CS') {
									$currentCSIndex = intval(substr($row['dwStatus'], 2));
									$useApprovedModal = $currentCSIndex >= $customWithdrawalSetting['maxCSIndex'];
								}

								if ($useApprovedModal) {
									#$output .= 'getWithdrawalRequest(' . $row['walletAccountId'] . ',' . $row['playerId'] . ',' .APPROVED_MODAL.')" >';	// data-target="#approvedDetailsModal"
									$output .= 'getWithdrawalRequest(' . $row['walletAccountId'] . ',' . $row['playerId'] . ',' .APPROVED_MODAL.')" >'; // data-target="#approvedDetailsModal"
								} else {
									$output .= 'getWithdrawalRequest(' . $row['walletAccountId'] . ',' . $row['playerId'] . ',' .REQUEST_MODAL.')" >'; // data-target="#requestDetailsModal"
								}
							}
							$output .= lang("lang.details");
							$output .= '</span></div></div>';
							return $output;
						} else {
							return '';
						}

				}else{
					return $d;
				}
			},
			),
			array(
                'dt' => $i++,
                'alias' => 'dwStatus',
                'select' => 'walletaccount.dwStatus',
                'name' => lang('lang.status'),
                'formatter' => function ($d, $row) use ($is_export, $customStage) {
                    if (!$is_export) {
						if ($d == Wallet_model::PENDING_REVIEW_STATUS) {
							if($this->utils->isEnabledFeature("enable_withdrawal_pending_review") && $this->permissions->checkPermissions('withdraw_pending_review_list')){
								return '<strong class="text-muted">' . lang('st.pendingreview') . '</strong>';
							}
						}
                        if ($d == Wallet_model::PAY_PROC_STATUS) {
                            return '<span class="text-success">' . lang('st.processing') . '</span>';
						}

						if ($d == Wallet_model::LOCK_API_UNKNOWN_STATUS) {
                            return '<span class="text-success">' . lang('st.lockedapirequest') . '</span>';
                        }
						if ($d == Wallet_model::REQUEST_STATUS ) {
							return lang('st.pending');
                        } else if ($d == Wallet_model::PAID_STATUS || $d == Wallet_model::APPROVED_STATUS) {
                            return '<span class="text-success">' . lang('st.paid') . '</span>';
                        } else if ($d == Wallet_model::DECLINED_STATUS) {
                            return '<span class="text-danger">' . lang('st.declined') . '</span>';
                        } else {
                            return $customStage[$d];
                        }
                    }else{
						if ($d == Wallet_model::PENDING_REVIEW_STATUS) {
							if($this->utils->isEnabledFeature("enable_withdrawal_pending_review") && $this->permissions->checkPermissions('withdraw_pending_review_list')){
								return lang('st.pendingreview');
							}
						}
                        if ($d == Wallet_model::PAY_PROC_STATUS) {
                            return lang('st.processing');
						}

						if ($d == Wallet_model::LOCK_API_UNKNOWN_STATUS) {
                            return lang('st.lockedapirequest');
						}

                        if ($d == Wallet_model::REQUEST_STATUS) {
                            return lang('st.pending');
                        } else if ($d == Wallet_model::PAID_STATUS || $d == Wallet_model::APPROVED_STATUS) {
                            return lang('st.paid');
                        } else if ($d == Wallet_model::DECLINED_STATUS) {
                            return lang('st.declined');
                        } else {
                            return $customStage[$d];
                        }
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'withdrawCode',
                'select' => 'walletaccount.transactionCode',
                'name' => lang('Withdraw Code'),
                'formatter' => function ($d,$row) use ($is_export,$dwStatus)  {
                    if (!$is_export) {
                    	if($this->utils->isEnabledFeature('highlight_withdrawal_code_in_list')) {
  							if(!empty($d)){
  								$withdraw_code_highlight_sequence = $this->utils->getConfig('withdraw_code_highlight_sequence');
	  							if(!empty($withdraw_code_highlight_sequence)){
	  								switch ($dwStatus) {
						  				case Wallet_model::REQUEST_STATUS:
					  						foreach ($withdraw_code_highlight_sequence as $key => $value) {
					  							if(isset($value['timeout']) && isset($value['background_color'])){
					  								if($this->utils->isTimeoutNow($row['createdOn'], $value['timeout'])){
					  									return '<span style="background-color: ' . $value['background_color'] . '; color:white">' . $d . '</span>';
					  								}
					  							}
					  						}
						  					break;
						  				default:
						  					return '<span>' . $d . '</span>';
						  					break;
						  			}
	  							}
  							}
						}
                        return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }else{
                        return $d ?: lang('lang.norecyet');
                    }
                },
            ),

            array(
                'dt' => $i++,
                'alias' => 'lockedStatus',
                'select' => 'ad.username',
                'name' => lang('Locked Status'),
                'formatter' => function($d){
                    return !empty($d) ? $d : 'N/A';
                }
            ),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'player.username',
				'name' => lang('pay.username'),
				'formatter' => function ($d, $row) use ($is_export)  {
					if (!$is_export) {
						if(!empty($row['aff_username'])){
							$d=$d.' ('.$row['aff_username'].')';
						}
						return sprintf('<a href="/player_management/userInformation/%s">%s</a>', $row['playerId'], $d);
					}else{
						return $d;
					}

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'createdOn',
				'select' => 'walletaccount.dwDatetime',
				'name' => lang('pay.reqtime'),
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'processTime',
				'select' => 'walletaccount.walletAccountId',
				'name' => lang('pay.proctime'),
				'formatter' => function ($walletAccountId) use ($is_export) {

					$note = $this->walletaccount_timelog->getWalletAccountTimeLogByWalletAccountId($walletAccountId, Wallet_model::PAY_PROC_STATUS);

					if(!empty($note)){
						return date('Y-m-d H:i:s', strtotime($note['create_date']));
					}
					else{
						if (!$is_export) {
							return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}else{
							return lang('lang.norecyet');
						}
					}
				},

			),
			array(
				'dt' => $i++,
				'alias' => 'paidTime',
				'select' => 'walletaccount.walletAccountId',
				'name' => lang('pay.paidtime'),
				'formatter' => function ($walletAccountId) use ($is_export) {

					$note = $this->walletaccount_timelog->getWalletAccountTimeLogByWalletAccountId($walletAccountId, Wallet_model::PAID_STATUS);

					if(!empty($note)){
						return date('Y-m-d H:i:s', strtotime($note['create_date']));
					}
					else{
						if (!$is_export) {
							return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}else{
							return lang('lang.norecyet');
						}
					}
				},

			),
			array(
				'dt' => $i++,
				'alias' => 'spentTime',
				'select' => 'walletaccount.walletAccountId',
				'name' => lang('pay.spenttime'),
				'formatter' => function ($walletAccountId) use ($is_export) {
					$request = $this->wallet_model->getWalletAccountBy($walletAccountId);
					//$paid = $this->transaction_notes->getTransactionNotesByTransactionId($walletAccountId, Transaction_notes::TRANS_WITHDRAWAL, Wallet_model::PAID_STATUS);
					$request_time = strtotime($request->dwDateTime);
					$paid_time    = strtotime($request->processDatetime);
					$spent_time   = $paid_time - $request_time;

					if($request->dwStatus == Wallet_model::PAID_STATUS){
						$day    = floor($spent_time/86400);
						$hour   = floor($spent_time%86400/3600);
						$minute = floor($spent_time%86400%3600/60);
						$second = floor($spent_time%86400%3600%60);
						$timeString = sprintf("%d s", $second);
						if($minute > 0){
							$timeString = sprintf("%d m %d s", $minute, $second);
						}
						if($hour > 0){
							$timeString = sprintf("%d h %d m %d s", $hour, $minute, $second);
						}
						if($day > 0){
							$timeString = sprintf("%d d %d h %d m %d s", $day, $hour, $minute, $second);
						}
						return $timeString;
					}
					else{
						if (!$is_export) {
							return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}else{
							return lang('lang.norecyet');
						}
					}
				},

			),
			array(
  				'dt' => $i++,
  				'alias' => 'real_name',
  				'select' => 'CONCAT(ifnull(playerdetails.firstName, ""), \' \', ifnull(playerdetails.lastName, "") )',
  			    'name' => lang('sys.vu40'),
  				'formatter' => function ($d) use ($is_export)  {
  					if (!$is_export) {
  						return  ($d == " ") ?  '<i class="text-muted">' . lang('lang.norecyet') . '</i>' : $d;
  					}else{
  						return ($d == " ") ? lang('lang.norecyet')  :  $d ;
  					}
  				},
  			),
  			array(
                'dt' => $i++,
                'alias' => 'group_level',
                'select' => 'player.levelName',
                'name' => lang('pay.playerlev'),
                'formatter' => function ($d, $row) {
		            $player_level = $this->player_model->getPlayerCurrentLevel($row['playerId']);

		            if($player_level){
		                $groupName = lang($player_level[0]['groupName']);
		                $levelName = lang($player_level[0]['vipLevelName']);
		            	return $groupName . ' - ' .$levelName;
		            }
		            else{
		            	return null;
		            }
                },
            ),
			array(
				'dt' => $i++,
				'alias' => 'tag',
				'select' => 'player.playerId',
				'name' => lang('Tag'),
				'formatter' => function ($d, $row) use ($is_export)  {
					return player_tagged_list($row['playerId'], $is_export);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'walletaccount.amount',
				'name' => lang('pay.withamt'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return '<strong>'.$d.'</strong>';
					}else{
						return $d;
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'bankName',
				'select' => 'IF(ISNULL(walletaccount.bankName), banktype.bankName, walletaccount.bankName)',
				'name' => lang('pay.bankname'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return $d ? lang($d) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ? lang($d) : lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'bankAccountFullName',
				'select' => 'IF(ISNULL(walletaccount.bankAccountFullName), playerbankdetails.bankAccountFullName, walletaccount.bankAccountFullName)',
				'name' => lang('pay.acctname'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'bankAccountNumber',
				'select' => 'IF(ISNULL(walletaccount.bankAccountNumber), playerbankdetails.bankAccountNumber, walletaccount.bankAccountNumber)',
				'name' => lang('pay.acctnumber'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'default_payment_flag',
				'select' => 'banktype.default_payment_flag',
				'name' => lang('pay.payment_account_flag'),
				'formatter' => function ($d) {
					$payment_flag = lang('pay.manual_online_payment');

  					switch ($d) {
	  					case Payment_account::FLAG_MANUAL_LOCAL_BANK:
	  						$payment_flag = lang('pay.local_bank_offline');
	  						break;
	  					case Payment_account::FLAG_MANUAL_ONLINE_PAYMENT:
	  						$payment_flag = lang('pay.manual_online_payment');
	  						break;
	  					case Payment_account::FLAG_AUTO_ONLINE_PAYMENT:
	  						$payment_flag = lang('pay.auto_online_payment');
	  						break;
  					}

  					return $payment_flag;
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'branch',
				'select' => 'IF(ISNULL(walletaccount.bankBranch), playerbankdetails.branch, walletaccount.bankBranch)',
				'name' => lang('pay.acctbranch'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'declined_category',
				'select' => 'walletaccount.withdrawal_declined_category_id',
				'name' => lang('Withdrawal Declined Category'),
				'formatter' => function ($d) use ($is_export,$common_category)  {
					if (!$is_export) {
						return $d ? lang($common_category->getCategoryInfoById($d)['category_name']): '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ? lang($common_category->getCategoryInfoById($d)['category_name']): lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'province',
				'select' => 'IF(ISNULL(walletaccount.bankProvince), playerbankdetails.province, walletaccount.bankProvince)',
				'name' => lang('Province'),
			),
			array(
				'dt' => $i++,
				'alias' => 'city',
				'select' => 'IF(ISNULL(walletaccount.bankCity), playerbankdetails.city, walletaccount.bankCity)',
				'name' => lang('City'),
			),
			array(
				'dt' => $i++,
				'alias' => 'dwIp',
				'select' => 'walletaccount.dwIp',
				'name' => lang('pay.withip'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'dwLocation',
				'select' => 'walletaccount.dwLocation',
				'name' => lang('pay.withlocation'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'processedByName',
				'select' => 'adminusers.username',
				'name' => lang('pay.procssby'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'processDatetime',
				'select' => 'walletaccount.processDatetime',
				'name' => lang('pay.updatedon'),
				'formatter' => function ($d, $row) use ($is_export)  {

					if (!$d || strtotime($d) < 0) {
						if (!$is_export) {
							return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}else{
							return $d ?: lang('lang.norecyet');
						}
					} else {
						return date('Y-m-d H:i:s', strtotime($d));
					}

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'withdrawalId',
				'select' => 'walletaccount.walletAccountId',
				'name' => lang('pay.withdrawalId'),
				'formatter' => function ($d) use ($is_export)  {
					if (!$is_export) {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return $d ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'walletaccount.walletAccountId', #'walletaccount_notes.content' EXTERNAL_NOTE,
				'name' => lang('External Note'),
				'formatter' => function ($walletAccountId) use ($is_export)  {
 					$display_last_notes = false;
 					$limit_the_number_of_words_displayed = true;
					$allNotes = $this->walletaccount_notes->getNotesByNoteType(Walletaccount_notes::EXTERNAL_NOTE, $walletAccountId, $display_last_notes, false, $limit_the_number_of_words_displayed);

					$exportNotes = $this->walletaccount_notes->getNotesByNoteType(Walletaccount_notes::EXTERNAL_NOTE, $walletAccountId, $display_last_notes, false, false);

					if (!$is_export) {
						return $allNotes ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return str_replace('</br>', "\n", $exportNotes) ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'walletaccount.walletAccountId', #'walletaccount_notes.content' INTERNAL_NOTE,
				'name' => lang('Internal Note'),
				'formatter' => function ($walletAccountId) use ($is_export)  {
					$display_last_notes = false;
					$limit_the_number_of_words_displayed = true;
					$allNotes = $this->walletaccount_notes->getNotesByNoteType(Walletaccount_notes::INTERNAL_NOTE, $walletAccountId, $display_last_notes, false, $limit_the_number_of_words_displayed);

					$exportNotes = $this->walletaccount_notes->getNotesByNoteType(Walletaccount_notes::INTERNAL_NOTE, $walletAccountId, $display_last_notes, false, false);

					if (!$is_export) {
						return $allNotes ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return str_replace('</br>', "\n", $exportNotes) ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'walletaccount.walletAccountId', #'walletaccount_notes.content' ACTION_LOG,
				'name' => lang('Action Log'),
				'formatter' => function ($walletAccountId) use ($is_export)  {
					$display_last_notes = false;
					$allNotes = $this->walletaccount_notes->getNotesByNoteType(Walletaccount_notes::ACTION_LOG, $walletAccountId, $display_last_notes);

					if (!$is_export) {
						return $allNotes ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{
						return str_replace('</br>', "\n", $allNotes) ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'timeLog',
				'select' => 'walletaccount.walletAccountId',
				'name' => lang('pay.timelog'),
				'formatter' => function ($walletAccountId) use ($is_export) {

					$timelogs   = $this->walletaccount_timelog->getWalletAccountTimeLogByWalletAccountId($walletAccountId);
					$timeString = '';
					if(!empty($timelogs)){
						$setting = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();

						$statusMap = array();
						for ($i=0; $i < 6; $i++) {
							$stage = 'CS'.$i;
							$statusMap[$stage] = $setting[$i]['name'];
						}
						$statusMap[Wallet_model::REQUEST_STATUS]          = lang('pay.penreq');
						$statusMap[Wallet_model::PENDING_REVIEW_STATUS]   = lang('pay.penreview');
						$statusMap[Wallet_model::PAY_PROC_STATUS]         = lang('pay.processing');
						$statusMap[Wallet_model::PAID_STATUS]             = lang('pay.paid');
						$statusMap[Wallet_model::DECLINED_STATUS]         = lang('pay.decreq');
						$statusMap[Wallet_model::LOCK_API_UNKNOWN_STATUS] = lang('pay.lockapiunknownreq');

						foreach ($timelogs as $timelog) {
							$getUserName = 'N/A';
							if(!empty($timelog['created_by'])){
								if($timelog['create_type'] == Walletaccount_timelog::ADMIN_USER){
									$getUserName = $this->users->getUsernameById($timelog['created_by']);
								}else{
									$getUserName = $this->player_model->getUsernameById($timelog['created_by']);
								}
							}

							if(!empty($timelog['after_status'])){
								$timeString .= sprintf("[%s] %s:%s</br>", $statusMap[$timelog['after_status']], $getUserName, $timelog['create_date']);
							}else{
								$timeString .= sprintf("[%s] %s:%s</br>", $statusMap[$timelog['before_status']], $getUserName, $timelog['create_date']);
							}
						}
					}

					if (!$is_export) {
						return $timeString;
					}else{
						return str_replace('</br>', "\n", $timeString);
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'currency',
				'select' => 'playeraccount.currency',
				'name' => lang('pay.curr'),
				'formatter' => function ($d) use ($is_export,$currency_settings)  {
					$d = (!empty($currency_settings)) ? $currency_settings : $d;
					if (!$is_export) {
						return $d ? '<strong>' . $d . '</strong>': '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}else{

						return $d ? : lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'system_code',
				'select' => 'external_system.system_code',
				'name' => lang('sys.ga.systemcode'),
				'formatter' => function ($d, $row)  use ($is_export) {
					if($row['dwStatus'] == Wallet_model::PAID_STATUS) {
						if(!$row['paymentAPI']) {
							return lang('Manual Payment');
						}
						else {
							return $d;
						}
					}
					else {
						if ($is_export) {
							return $d ?: lang('lang.norecyet');
						} else {
							return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'paymentAPI',
				'select' => 'walletaccount.paymentAPI',
				'name' => lang('lang.withdrawal_payment_api'),
				'formatter' => 'defaultFormatter',
			)
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		# START CUSTOM COLUMNS #################################################################################################################################################
		if($this->utils->isEnabledFeature('separate_approve_decline_withdraw_pending_review_and_request_permission')){
			if($dwStatus == 'request'){
				$columns = $this->checkIfEnabled($this->permissions->checkPermissions('approve_decline_withdraw_pending_request'), array('walletAccountId'), $columns);
			}

			if($dwStatus == 'pending_review'){
				$columns = $this->checkIfEnabled($this->permissions->checkPermissions('approve_decline_withdraw_pending_review'), array('walletAccountId'), $columns);
			}
		}else{

        }
		# END CUSTOM COLUMNS #################################################################################################################################################

		$table = 'walletaccount';
		$joins = array(
			'playeraccount' => 'playeraccount.playerAccountId = walletaccount.playerAccountId',
			'player' => 'player.playerId = walletaccount.playerId',
			'playerdetails' => 'playerdetails.playerId = player.playerId',
			'localbankwithdrawaldetails' => 'localbankwithdrawaldetails.walletAccountId = walletaccount.walletAccountId',
			'playerbankdetails' => 'playerbankdetails.playerBankDetailsId = localbankwithdrawaldetails.playerBankDetailsId',
			'banktype' => 'banktype.bankTypeId = playerbankdetails.bankTypeId',
			'adminusers' => 'adminusers.userId = walletaccount.processedBy',
			'adminusers ad' => 'ad.userId = walletaccount.locked_user_id',
			'affiliates' => 'affiliates.affiliateId = player.affiliateId',
			'external_system' => 'walletaccount.paymentAPI = external_system.id'
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		//$request = $this->input->post();

		//$input = $this->data_tables->extra_search($request);

		$where[] = 'walletaccount.transactionType = ?';
		$where[] = 'walletaccount.status = ?';
		$values[] = 'withdrawal';
		$values[] = 0;

		if (isset($input['withdraw_code'])) {
			$where[] = "walletaccount.transactionCode = ?";
			$values[] = $input['withdraw_code'];
		}

		if (!empty($playerId)) {
			$where[] = "walletaccount.playerId = ?";
			$values[] = $playerId;
		}

		if (isset($input['username'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['username'] . '%';
		}

		if (isset($input['realname'])) {
			$where[] = "CONCAT_WS(' ', playerdetails.firstName, playerdetails.lastName) LIKE ?";
			$values[] = '%' . $input['realname'] . '%';
		}

		if (isset($input['amount_from'])) {
			$where[] = "walletaccount.amount >= ?";
			$values[] = $input['amount_from'];
		}

		if (isset($input['amount_to'])) {
			$where[] = "walletaccount.amount <= ?";
			$values[] = $input['amount_to'];
		}

		if (!empty($input['enable_date'])) {
			if (isset($input['withdrawal_date_from'], $input['withdrawal_date_to'], $input['search_time'])) {
                if($input['search_time'] == '1'){
                    $where[] = "walletaccount.dwDatetime >= ?";
                    $values[] = $input['withdrawal_date_from'];
                    $where[] = "walletaccount.dwDatetime <= ?";
                    $values[] = $input['withdrawal_date_to'];
                } else if($input['search_time'] == '2'){
                	$where[] = "walletaccount.processDatetime >= ?";
                    $values[] = $input['withdrawal_date_from'];
                    $where[] = "walletaccount.processDatetime <= ?";
                    $values[] = $input['withdrawal_date_to'];
                }
			}else{
		        if (isset($input['dwStatus']) && $input['dwStatus'] == Wallet_model::PAID_STATUS) {
		            $where[] = "(walletaccount.processDatetime BETWEEN ? AND ?)";
		            $values[] = $input['withdrawal_date_from'];
		            $values[] = $input['withdrawal_date_to'];
		        } else {
		            $where[] = "((walletaccount.dwDateTime BETWEEN ? AND ?) OR (walletaccount.processDatetime BETWEEN ? AND ?))";
		            $values[] = $input['withdrawal_date_from'];
		            $values[] = $input['withdrawal_date_to'];
		            $values[] = $input['withdrawal_date_from'];
		            $values[] = $input['withdrawal_date_to'];
		        }
		    }
		}

		if (isset($input['dwStatus'], $input['search_status'])) {
			if($input['dwStatus'] != $input['search_status']){
				if ($input['search_status'] != 'allStatus') {
					$multiDwStatus = $input['search_status'];
					$where[] = "walletaccount.dwStatus = ?";
					$values[] = $multiDwStatus;
				}
			}else{
				if (!is_array($input['dwStatus'])) {
					$multiDwStatus = $input['dwStatus'];
					$where[] = "walletaccount.dwStatus = ?";
					$values[] = $multiDwStatus;
				} else {
					$multiDwStatus = join('\',\'', $input['dwStatus']);
					$where[] = "walletaccount.dwStatus IN ('$multiDwStatus')";
				}
			}
		}else if(isset($input['dwStatus'])){
			if (!is_array($input['dwStatus'])) {
				$multiDwStatus = $input['dwStatus'];
				$where[] = "walletaccount.dwStatus = ?";
				$values[] = $multiDwStatus;
			} else {
				$multiDwStatus = join('\',\'', $input['dwStatus']);
				$where[] = "walletaccount.dwStatus IN ('$multiDwStatus')";
			}
		}

		if (isset($input['withdrawAPI'])) {
			if (!is_array($input['withdrawAPI'])) {
				$multiWithdrawAPI = $input['withdrawAPI'];
				$where[] = "walletaccount.paymentAPI = ?";
				$values[] = $multiWithdrawAPI;
			} else {
				$multiWithdrawAPI = implode(',', $input['withdrawAPI']);
				$where[] = "walletaccount.paymentAPI IN ($multiWithdrawAPI)";
			}
		}

		if (!empty($input['locked_user_id'])) {
			$where[] = "walletaccount.locked_user_id != ?";
			$values[] = '';

			// display only request and custom stage withdrawal in locked withdrawal list
			$where[] = "walletaccount.dwStatus NOT IN (?, ?, ?)";
			$values[] = Wallet_model::APPROVED_STATUS;
			$values[] = Wallet_model::DECLINED_STATUS;
			$values[] = Wallet_model::PAID_STATUS;
		}

		if (!empty($input['processed_by'])) {
			$where[] = "processedBy = ?";
			$values[] = $input['processed_by'];
		}


		// to see test player withdrawlist  on their user information page
        if(empty($playerId)){
        	$where[] = "player.deleted_at IS NULL";
        }


		# END PROCESS SEARCH FORM #################################################################################################################################################
		if($is_export){
            $this->data_tables->options['is_export']=true;
			// $this->data_tables->options['only_sql']=true;
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
		$countOnlyField='walletaccount.walletAccountId';

     //    $columns = $this->checkIfEnabled($this->utils->isEnabledFeature('display_locked_status_column'), array('lockedStatus'), $columns);

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins,
			$group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);

		if($is_export){
		    //drop result if export
			return $csv_filename;
		}

		// $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		return $result;
	}

	/**
	 * detail: get deposit checking report
	 *
	 * @return json
	 */
	public function depositCheckingReport($request, $is_export = false) {
		$this->load->model('sale_order');
		$this->load->library(array('data_tables'));
		$input = $this->data_tables->extra_search($request);
		if(!$is_export ){
			$this->load->library('permissions');
			$this->permissions->setPermissions();
		}
		$this->utils->debug_log('=========depositCheckingReport input',$input);


		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'alias' => 'playerId',
				'select' => 'player.playerId',
			),
			array(
				'dt' => $i++,
				'alias' => 'secure_id',
				'select' => 'sale_orders.secure_id',
				'name' => lang('deposit_list.order_id'),
				'formatter' => function ($d, $row) use ($is_export) {
					if($is_export){
						return $d;
					}
					return sprintf('<a href="/payment_management/deposit_list?secure_id=%s">%s</a>', $d, $d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'player.username',
				'name' => lang('player.01'),
				'formatter' => function ($d, $row) use ($is_export) {
					if($is_export){
						return $d;
					}
					return sprintf('<a href="/player_management/userInformation/%s">%s</a>', $row['playerId'], $d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'real_name',
				'select' => 'CONCAT(ifnull(playerdetails.firstName,""), \' \', ifnull(playerdetails.lastName,"") )',
				'name' => lang('sys.vu19'),
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'sale_orders.amount',
				'name' => lang('Deposit Amount'),
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'request_time',
				'select' => 'sale_orders.created_at',
				'name' => lang('pay.reqtime'),
				'formatter' => 'dateTimeFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'approved_time',
				'select' => 'sale_orders.processed_approved_time',
				'name' => lang('payment.lastUpdatedTime'),
				'formatter' => 'dateTimeFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'processed_time',
				'select' => 'sale_orders.processed_approved_time',
				'name' => lang('payment.totalProcessingTime'),
				'formatter' => function ($d, $row) use ($is_export) {
					if($is_export){
						if (!empty($row['request_time']) && !empty($row['approved_time'])) {
							return $this->utils->dateDiff($row['request_time'], $row['approved_time']);
						} else {
							return lang('lang.norecyet');
						}
					}

					if (!empty($row['request_time']) && !empty($row['approved_time'])) {
						return '<span class="text-info"><b>' . $this->utils->dateDiff($row['request_time'], $row['approved_time']) . '</b></span>';
					} else {
						return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'sale_orders.status',
				'name' => lang('lang.status'),
				'formatter' => function ($d, $row) use ($is_export) {
					switch ($d) {
						case Sale_order::STATUS_PROCESSING:
						case Sale_order::STATUS_BROWSER_CALLBACK:
							if($is_export){
								return lang('deposit_list.st.pending');
							}
							return '<span class="text-default"><b>' . lang('deposit_list.st.pending'). '</b></span>';
							break;
						case Sale_order::STATUS_SETTLED:
							if($is_export){
								return lang('deposit_list.st.approved');
							}
							return '<span class="text-success"><b>' . lang('deposit_list.st.approved'). '</b></span>';
							break;
						case Sale_order::STATUS_CANCELLED:
						case Sale_order::STATUS_FAILED:
						case Sale_order::STATUS_DECLINED:
							if($is_export){
								return lang('deposit_list.st.declined');
							}
							return '<span class="text-danger"><b>' . lang("deposit_list.st.declined"). '</b></span>';
							break;
						default:
							if($is_export){
								return lang($d);
							}
							return '<span class="text-default"><b>' . lang($d) . '</b></span>';
							break;
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'processed_by',
				'select' => 'adminusers.username',
				'name' => lang('pay.procssby'),
				'formatter' => 'defaultFormatter',
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'sale_orders';
		$joins = array(
			'player' => 'player.playerId = sale_orders.player_id',
			'playerdetails' => 'playerdetails.playerId = sale_orders.player_id',
			'vipsettingcashbackrule' => 'vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId',
			'adminusers' => 'sale_orders.processed_by = adminusers.userId',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		// $request = $this->input->post();


		if (isset($input['deposit_date_from'], $input['deposit_date_to'])) {
            $where[] = "sale_orders.processed_approved_time >= ?";
            $values[] = $input['deposit_date_from'];
            $where[] = "sale_orders.processed_approved_time <= ?";
            $values[] = $input['deposit_date_to'];
		}else{
		    $where[] = "(sale_orders.processed_approved_time BETWEEN ? AND ?)";
		    $values[] = $input['deposit_date_from'];
		    $values[] = $input['deposit_date_to'];
		}


		if (isset($input['username'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['username'] . '%';
		}

		if (!empty($input['processed_by'])) {
			$where[] = "adminusers.username LIKE ?";
			$values[] ='%' . $input['processed_by']. '%';
		}


		if (isset($input['search_status'])) {
			 if ($input['search_status'] != 'allStatus') {
				 $where[] = "sale_orders.status = ?";
				 $values[] = $input['search_status'];

			}
		}


		# END PROCESS SEARCH FORM #################################################################################################################################################

		if($is_export){
            $this->data_tables->options['is_export'] = true;
            $this->data_tables->options['csv_filename'] = $this->utils->create_csv_filename(__FUNCTION__);
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		$this->utils->debug_log('=========depositCheckingReport result',$result);

		return $result;
	}


	/**
	 * detail: get withdraw checking report
	 *
	 * @return json
	 */
	public function withdrawCheckingReport($request, $is_export = false) {
		$this->load->model('wallet_model');
		$this->load->library(array('data_tables'));

		$input = $this->data_tables->extra_search($request);

		if(!$is_export ){
			$this->load->library('permissions');
			$this->permissions->setPermissions();
		}
		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'alias' => 'playerId',
				'select' => 'player.playerId',
			),
			array(
				'dt' => $i++,
				'alias' => 'transactionCode',
				'select' => 'walletaccount.transactionCode',
				'name' => lang('Withdrawal Code'),
				'formatter' => function ($d, $row) use ($is_export) {
					if($is_export){
						return $d;
					}
					return sprintf('<a href="/payment_management/viewWithdrawalRequestList?withdraw_code=%s">%s</a>', $d, $d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'player.username',
				'name' => lang('player.01'),
				'formatter' => function ($d, $row) use ($is_export) {
					if($is_export){
						return $d;
					}
					return sprintf('<a href="/player_management/userInformation/%s">%s</a>', $row['playerId'], $d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'real_name',
				'select' => 'CONCAT(ifnull(playerdetails.firstName,""), \' \', ifnull(playerdetails.lastName,"") )',
				'name' => lang("sys.vu19"),
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'walletaccount.amount',
				'name' => lang('Withdraw Amount'),
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'request_time',
				'select' => 'walletaccount.dwDatetime',
				'name' => lang('pay.reqtime'),
				'formatter' => 'dateTimeFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'approved_time',
				'select' => 'walletaccount.processDatetime',
				'name' => lang('payment.lastUpdatedTime'),
				'formatter' => 'dateTimeFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'processed_time',
				'select' => 'walletaccount.processDatetime',
				'name' => lang('payment.totalProcessingTime'),
				'formatter' => function ($d, $row) use ($is_export) {
					if($is_export){
						if ($row['request_time'] && $row['approved_time']) {
							if ($row['status'] == Wallet_model::REQUEST_STATUS || $row['status'] == Wallet_model::PENDING_REVIEW_STATUS ) {
								return lang('lang.norecyet') ;
							} else {
								return $this->utils->dateDiff($row['request_time'], $row['approved_time']);
							}
						} else {
							return lang('lang.norecyet');
						}
					}


					if ($row['request_time'] && $row['approved_time']) {
						if ($row['status'] == Wallet_model::REQUEST_STATUS || $row['status'] == Wallet_model::PENDING_REVIEW_STATUS ) {
							return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						} else {
							return '<span class="text-info"><b>' . $this->utils->dateDiff($row['request_time'], $row['approved_time']) . '</b></span>';
						}
					} else {
						return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'walletaccount.dwStatus',
				'name' => lang('lang.status'),
				'formatter' => function ($d, $row) use ($is_export) {
				switch ($d) {
					case Wallet_model::REQUEST_STATUS:
						if($is_export){
							return lang('st.pending');
						}
						return '<span class="text-default"><b>' . lang('st.pending'). '</b></span>';
						break;
					case Wallet_model::PENDING_REVIEW_STATUS:
						if($is_export){
							return lang('st.pending_review');
						}
						return '<span class="text-warning"><b>' . lang('st.pending_review'). '</b></span>';
						break;
					case Wallet_model::PAY_PROC_STATUS:
						if($is_export){
								return lang('st.processing');
						}
						return '<span class="text-primary"><b><i>' . lang('st.processing'). '</i></b></span>';
						break;
					case Wallet_model::PAID_STATUS:
					case Wallet_model::APPROVED_STATUS:
						if($is_export){
							return lang('st.paid');
						}
						return '<span class="text-success"><b>' . lang('st.paid'). '</b></span>';
						break;
					case Wallet_model::DECLINED_STATUS:
						if($is_export){
							return lang('st.declined');
						}
						return '<span class="text-danger"><b>' . lang('st.declined'). '</b></span>';
						break;
					case Wallet_model::LOCK_API_UNKNOWN_STATUS:
						if($is_export){
							return lang('pay.lockapiunknownreq');
						}
						return '<span class="text-default"><b>' . lang('pay.lockapiunknownreq'). '</b></span>';
						break;
					default:
						if($is_export){
							return lang($d);
						}
						return '<span class="text-default"><b>' . lang($d) . '</b></span>';
						break;
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'processed_by',
				'select' => 'adminusers.username',
				'name' => lang('pay.procssby'),
				'formatter' => 'defaultFormatter',
			)

		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'walletaccount';
		$joins = array(
			'player' => 'player.playerId = walletaccount.playerId',
			'playerdetails' => 'playerdetails.playerId =  walletaccount.playerId',
			'vipsettingcashbackrule' => 'vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId',
			'adminusers' => 'adminusers.userId = walletaccount.processedBy',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();

		if (isset($input['withdrawal_date_from'], $input['withdrawal_date_to'])) {
            $where[] = "walletaccount.processDatetime >= ?";
            $values[] = $input['withdrawal_date_from'];
            $where[] = "walletaccount.processDatetime <= ?";
            $values[] = $input['withdrawal_date_to'];
		}else{
		    $where[] = "(walletaccount.processDatetime BETWEEN ? AND ?)";
		    $values[] = $input['withdrawal_date_from'];
		    $values[] = $input['withdrawal_date_to'];
		}


		if (isset($input['username'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['username'] . '%';
		}

		if (!empty($input['processed_by'])) {
			$where[] = "adminusers.username LIKE ?";
			$values[] ='%' . $input['processed_by']. '%';
		}


		if (isset($input['search_status'])) {
			 if ($input['search_status'] != 'allStatus') {
				 $where[] = "walletaccount.dwStatus = ?";
				 $values[] = $input['search_status'];

			}
		}

		##############################################################################################################################################

		if($is_export){
            $this->data_tables->options['is_export'] = true;
            $this->data_tables->options['csv_filename'] = $this->utils->create_csv_filename(__FUNCTION__);
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		return $result;
	}


	public function exception_order_list($request, $is_export=false){

  		$this->load->model(array('sale_order', 'payment_account'));
  		$this->load->library(array('data_tables'));
  		$this->load->library(array('permissions'));
  		$this->permissions->setPermissions();

  		$this->data_tables->is_export=$is_export;

  		# START DEFINE COLUMNS #################################################################################################################################################
  		$i = 0;
  		$columns = array(
  			array(
  				'alias' => 'response_content',
  				'select' => 'exception_order.response_content',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'id',
  				'select' => 'exception_order.id',
  				'name' => lang('ID'),
  				'formatter' => function ($d, $row) {
  					return '<a href="javascript:;" onclick="showResponseContent('.$d.')">'.lang('Details').'</a><span class="hidden" id="content_'.$d.'"><pre>'.$row['response_content'].'</pre></span>';
  				},
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'created_at',
  				'select' => 'exception_order.created_at',
  				'name' => lang('Create Date'),
  				'formatter' => 'dateTimeFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'external_system_code',
  				'select' => 'external_system.system_code',
  				'name' => lang('Payment API'),
  				'formatter' => 'languageFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'amount',
  				'select' => 'exception_order.amount',
  				'name' => lang('Amount'),
  				'formatter' => 'currencyFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'external_order_id',
  				'select' => 'exception_order.external_order_id',
  				'name' => lang('External Order'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'sale_order_id',
  				'select' => 'sale_orders.secure_id',
  				'name' => lang('Deposit Order'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'withdrawal_order_id',
  				'select' => 'walletaccount.transactionCode',
  				'name' => lang('Withdrawal Order'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'player_bank_name',
  				'select' => 'exception_order.player_bank_name',
  				'name' => lang('Player Bank Name'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'player_bank_account_name',
  				'select' => 'exception_order.player_bank_account_name',
  				'name' => lang('Player Bank Account Name'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'player_bank_account_number',
  				'select' => 'exception_order.player_bank_account_number',
  				'name' => lang('Player Bank Account Number'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'player_bank_address',
  				'select' => 'exception_order.player_bank_address',
  				'name' => lang('Player Bank Address'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'collection_bank_name',
  				'select' => 'exception_order.collection_bank_name',
  				'name' => lang('Collection Bank'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'collection_bank_account_name',
  				'select' => 'exception_order.collection_bank_account_name',
  				'name' => lang('Collection Bank Account Name'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'collection_bank_account_number',
  				'select' => 'exception_order.collection_bank_account_number',
  				'name' => lang('Collection Bank Account Number'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'collection_bank_address',
  				'select' => 'exception_order.collection_bank_address',
  				'name' => lang('Collection Bank Address'),
  				'formatter' => 'defaultFormatter',
  			),
  			array(
  				'dt' => $i++,
  				'alias' => 'remarks',
  				'select' => 'exception_order.remarks',
  				'name' => lang('Remarks'),
  				'formatter' => 'defaultFormatter',
  			),
  		);
  		# END DEFINE COLUMNS #################################################################################################################################################

  		$table = 'exception_order';
  		$joins = array(
  			'external_system'=>'exception_order.external_system_id=external_system.id',
  			'sale_orders'=>'exception_order.sale_order_id=sale_orders.id',
  			'walletaccount'=>'exception_order.withdrawal_order_id=walletaccount.walletAccountId',
  			// 'player' => 'player.playerId = sale_orders.player_id',
  			// 'playerdetails' => 'playerdetails.playerId = sale_orders.player_id',
  			// 'adminusers' => 'sale_orders.processed_by = adminusers.userId',
  			// 'playerpromo' => 'playerpromo.playerpromoId = sale_orders.player_promo_id',
  			// 'promorules' => 'playerpromo.promorulesId = promorules.promorulesId',
  			// 'transactions' => 'transactions.id = sale_orders.transaction_id',
  			// 'payment_account' => 'payment_account.id = sale_orders.payment_account_id',
  			// 'affiliates' => 'affiliates.affiliateId = player.affiliateId'
  		);

  		# START PROCESS SEARCH FORM #################################################################################################################################################
  		$where = array();
  		$values = array();
  		//$request = $this->input->post();
  		$input = $this->data_tables->extra_search($request);

  		if (isset($input['player_bank_account_name'])) {
  			$where[] = "player_bank_account_name LIKE ?";
  			$values[] = '%' . $input['player_bank_account_name'] . '%';
  		}

  		if (isset($input['player_bank_account_number'])) {
  			$where[] = "player_bank_account_number LIKE ?";
  			$values[] = '%' . $input['player_bank_account_number'] . '%';
  		}

  		if (isset($input['collection_bank_account_name'])) {
  			$where[] = "collection_bank_account_name LIKE ?";
  			$values[] = '%' . $input['collection_bank_account_name'] . '%';
  		}

  		if (isset($input['order_secure_id'])) {
  			$where[] = "sale_orders.secure_id = ?";
  			$values[] = $input['order_secure_id'];
  		}

  		// if ($this->safeGetParam($input, 'enable_date') == 'true' ) {
  			if (isset($input['by_date_from'], $input['by_date_to'])) {
  				$where[] = "exception_order.created_at >= ?";
  				$where[] = "exception_order.created_at <= ?";
  				$values[] = $input['by_date_from'];
  				$values[] = $input['by_date_to'];
  			}
  	    // }
  		if (isset($input['withdrawal_order_id'])) {
  			$where[] = "walletaccount.transactionCode = ?";
  			$values[] = $input['withdrawal_order_id'];
  		}
  		# END PROCESS SEARCH FORM #################################################################################################################################################

  		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
  		return $result;
	}

	public function transactionsByGetRebate($player_id, $request, $not_datatable = ''){
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('player_model'));
		$this->benchmark->mark('pre_processing_start');
		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'receivedOn',
				'select' => 'created_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('Transfer time'),
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => "amount",
				'name' => lang('From'),
				'formatter' => function ($d) {
					return $this->utils->toCurrencyNumber($d);
				},
			)

		);
		$table = 'transactions';

		# END DEFINE COLUMNS #################################################################################################################################################

		//define conset  from admin/application/models/transactions.php
		$tPlayer =2;
		$auto_add_cashback_to_balance =13;
		$tstatus = 1;

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$group_by=[];
		$having=[];
		$distinct = true;
		$external_order=[]; //[['column'=>'id', 'dir'=> 'desc']];

		$input = $this->data_tables->extra_search($request);
		$where[] = "transactions.to_id = ? ";
		$values[] = $player_id;
		$where[] = "transactions.to_type = ? ";
		$values[] = $tPlayer;
		$where[] = "transactions.transaction_type = ? ";
		$values[] = $auto_add_cashback_to_balance;
		$where[] = "transactions.status = ? ";
		$values[] = $tstatus;

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "transactions.created_at BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		$this->utils->debug_log('where', $where, 'values', $values);
		$this->benchmark->mark('pre_processing_end');

		# END PROCESS SEARCH FORM #################################################################################################################################################
		$this->benchmark->mark('data_sql_start');
		#set empty

		$group_by =array();
		$having = array();
		$distinct = true;
		$external_order=array();
		$not_datatable='';
		$joins = array();
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, $distinct, $external_order, $not_datatable);
 		$this->utils->printLastSQL();
		$this->benchmark->mark('data_sql_end');
		$this->utils->debug_log($result);
		return $result;
	}

	public function playerCashbackHistory($player_id, $request){
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('player_model'));
		$this->benchmark->mark('pre_processing_start');

		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'receivedOn',
				'select' => 'paid_date',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('Transfer time'),
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => "paid_amount",
				'name' => lang('From'),
				'formatter' => function ($d) {
					return $this->utils->toCurrencyNumber($d);
				},
			)
		);

		if($this->utils->isEnabledFeature('enable_friend_referral_cashback')) {
			$cashback_type = array(
				array(
					'dt' => $i++,
					'alias' => 'cashback_type',
					'select' => "cashback_type",
					'name' => lang('Cashback Type'),
					'formatter' => function ($d) {
                        switch((int)$d){
                            case Group_level::FRIEND_REFERRAL_CASHBACK:
                                return lang('Friend Referral Cashback');
                                break;
                            case Group_level::NORMAL_CASHBACK:
                            default:
                                return lang('Normal Cashback');
                                break;
                        }
					},
				),
				array(
					'dt' => $i++,
					'alias' => 'invited_player_id',
					'select' => "invited_player_id",
					'name' => lang('Referred Player for Cashback'),
					'formatter' => function ($d, $row)  use ($readOnlyDB) {
						if($row['cashback_type'] == Group_level::FRIEND_REFERRAL_CASHBACK) {
							$qry = $readOnlyDB->query("SELECT username FROM player where playerId= ?", array($d));
						if ($qry && $qry->num_rows() > 0) {
							$res = $qry->row_array();
							return !empty($res['username']) ? $res['username'] : 'N/A';
						}
							return 'N/A';
						}
							return 'N/A';
					},
				),
		 	);
		 	$columns = array_merge($columns, $cashback_type);
		}

		$table = 'total_cashback_player_game_daily';

		$where = array();
		$values = array();

		$input = $this->data_tables->extra_search($request);
		$where[] = "total_cashback_player_game_daily.paid_flag = ? ";
		$values[] = Group_level::DB_TRUE;

		$where[] = "total_cashback_player_game_daily.paid_date BETWEEN ? AND ?";
		$values[] = $input['dateRangeValueStart'];
		$values[] = $input['dateRangeValueEnd'];

		if(!empty($player_id)) {
			$where[] = "total_cashback_player_game_daily.player_id = ? ";
			$values[] = $player_id;
		}

		if(!$this->utils->isEnabledFeature('enable_friend_referral_cashback')) {
			$where[] = "total_cashback_player_game_daily.cashback_type = ?";
			$values[] = Group_level::NORMAL_CASHBACK;
		}

		$this->benchmark->mark('pre_processing_end');
		$this->benchmark->mark('data_sql_start');

		$group_by =array();
		$having = array();
		$distinct = true;
		$external_order=array();
		$not_datatable='';
		$joins = array();
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, $distinct, $external_order, $not_datatable);
		$this->utils->printLastSQL();
		$this->benchmark->mark('data_sql_end');
		$this->utils->debug_log($result);
		return $result;
	}
}
////END OF FILE/////////
