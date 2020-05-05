<?php
require_once dirname(__FILE__) . '/base_model.php';

require_once dirname(__FILE__) . '/modules/report_module_player.php';
require_once dirname(__FILE__) . '/modules/report_module_player_center.php';
require_once dirname(__FILE__) . '/modules/report_module_transaction.php';
require_once dirname(__FILE__) . '/modules/report_module_game.php';
require_once dirname(__FILE__) . '/modules/report_module_aff.php';
require_once dirname(__FILE__) . '/modules/super_report_module.php';
require_once dirname(__FILE__) . '/modules/report_module_generate.php';
require_once dirname(__FILE__) . '/modules/kingrich_summary_report_generate.php';
require_once dirname(__FILE__) . '/modules/kingrich_send_data_scheduler_generate.php';

/**
 *
 * General behaviors include
 * * duplicate account report
 * * get promotion report
 * * get cashback report
 *
 * @category report_model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Report_model extends BaseModel {

	public function __construct() {
		parent::__construct();

		$this->initialCrrExecutionTime();
	}

	const EXPORT_FORMAT_EXCEL = 'excel';
	const EXPORT_FORMAT_CSV = 'csv';

	const EXPORT_TYPE_DIRECT = 'direct';
	const EXPORT_TYPE_QUEUE = 'queue';

	const FILTER_EMPTY_DATA = 1;
	const ONLY_EMPTY_DATA = 2;

	public $CRR_TIME_LIMIT = 480; // 8* 60 sec

	public $export_token;
	public $export_pid;


	use report_module_player;
	use report_module_player_center;
	use report_module_transaction;
	use report_module_game;
	use report_module_aff;
	use super_report_module;
	use report_module_generate;
	use kingrich_summary_report_generate;
	use kingrich_send_data_scheduler_generate;

	protected function safeGetParam($input, $name) {
		return isset($input[$name]) ? $input[$name] : null;
	}

	/**
	 * detail: duplicate account report
	 *
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function duplicateAccountReport($request, $is_export = false) {
		$this->load->library(array('duplicate_account', 'data_tables'));
		$this->load->model(array('player_model'));

		$input = $this->data_tables->extra_search($request);
		$this->utils->debug_log('input', $input);

		$player_id = null;
		if (isset($input['by_username'])) {
			$player_id = $this->player_model->getPlayerIdByUsername($input['by_username']);
		}

		if ($player_id != null) {
			$data = $this->duplicate_account->getDuplicateAccountsJSON($player_id, 1);
		} else {
			$data = array();
		}
		$result = array(
			"draw" => '',
			"recordsFiltered" => count($data),
			"recordsTotal" => count($data),
			"data" => $data,
			"header_data" => '',
		);
		return $result;
	}

	/**
	 * Datatable.js datasource for table duplicate_account_total
	 *
	 * @param	array	$request	Datatable search form
	 * @param	boolean	$is_export	export flag, used by export data methods (see below)
	 * @see		/api/duplicate_account_total
	 * @see		/export_data/duplicate_account_total
	 * @return	array	server-side data pack for datatable.js
	 */
	public function duplicateAccountTotal($request, $is_export = false) {
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->data_tables->is_export = $is_export;

		$request['draw'] = '';
		// Show only top 2000 players (That should be enough)
		$request['start'] = 0;
		$request['length'] = 2000;
		$external_order = 'total_rate DESC';

		$input 		= $this->data_tables->extra_search($request);
		$table 		= 'duplicate_account_total';
		$joins		= [];
		$where		= [];
		$values		= [];
		$group_by	= [];
		$having		= [];

		$columns = [
			[ 'dt' => 0 , 'alias' => 'username'	, 'select' => 'username' , 'name' => lang('player.01') ,
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) { return $d; }
					$title = lang('Click to show duplicate account details for player');
					return "<a href='/report_management/duplicate_account_detail_by_username/{$d}' title='{$title}' class='dup_total dup_details'>{$d}</a>";
				}
			] ,
			[ 'dt' => 1 , 'alias' => 'total_rate' , 'select' => 'total_rate' , 'name' => lang('Total Rate')] ,

			[ 'dt' => 2 , 'alias' => 'updated_at' , 'select' => 'updated_at' , 'name' => lang('Last Updated On')],
		];

		if (isset($input['by_username'])) {
			$where[]	= 'username LIKE ?';
			$values[]	= "%{$input['by_username']}%";
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, $external_order);

		return $result;
	}

	public function usernameExistsInDuplicateAccountInfo($username) {
		if (empty($username)) {
			return false;
		}
		$this->db->from('duplicate_account_info')
			->where('userName', $username);

		return $this->runExistsResult();
	}

	public function duplicateAccountInfo($username, $for_player_info = false, $is_request_for_modal=false, $is_count_related_total_rate=false) {
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->library('player_manager');

		$request['draw'] = '';

		$table 		= 'duplicate_account_info';
		$joins		= [];
		$where		= [];
		$values		= [];
		$group_by	= [];
		$having		= [];

		$i = 0;
		$columns = [];
		if ($for_player_info == true) {
			$columns[] = [ 'dt' => $i++ , 'alias' => 'setTag', 'select' => 'dup_userName' ,
				'formatter' => function ($d, $row) {
					$player_id = $this->player_manager->getPlayerIdByUsername($d);
					return "<input type='checkbox' id='tags' name='set_tag[]' class='checktags' value='{$player_id}' onclick='countTagBoxes()'>";

				}
			];

			# OGP-3524
			if($this->utils->isEnabledFeature('link_account_in_duplicate_account_list')){
				$columns[] = [ 'dt' => $i++ , 'alias' => 'setLinkAcct', 'select' => 'dup_userName' ,
					'formatter' => function ($d, $row) use($username) {
						$player_id = $this->player_manager->getPlayerIdByUsername($d);
						$this->load->model('linked_account_model');
                        if (!empty($this->linked_account_model->getPlayerLinkedAccountLinkIdByUsername($username))) {
                            $linkId = $this->linked_account_model->getPlayerLinkedAccountLinkIdByUsername($username)['link_id'];
                            $isAcctLinkAlready = $this->linked_account_model->isPlayerAcctWasLinkedAccountAlready($d, $linkId) ? "checked" : "";
                            return "<input type='checkbox' id='linkaccts' name='link_acct[]' class='checkaccts' value='{$player_id}' username_val='{$d}' {$isAcctLinkAlready} onclick='countLinkBoxes()'>";
                        }
					}
				];
			}
		}
		$columns = array_merge($columns,
		[
			[ 'dt' => $i++ , 'alias' => 'userName'             , 'select' => 'userName'           ] ,
			[ 'dt' => $i++ , 'alias' => 'total_rate'           , 'select' => 'total_rate'         ] ,
			[ 'dt' => $i++ , 'alias' => 'dup_userName'         , 'select' => 'dup_userName',
				'formatter' => function ($d, $row) use ($is_request_for_modal) {
					$title = lang('Click to show duplicate account details for player');
					if($is_request_for_modal) {
						return $d;
					}
					else {
						return "<a href='#' title='{$title}' class='dup_total dup_details dup_modal_trigger' data-username={$d}>{$d}</a>";
					}
				}
			]
		]);

		if($is_count_related_total_rate) {
			$columns = array_merge($columns,
			[
				[ 'dt' => $i++ , 'alias' => 'related_total_rate'         , 'select' => 'dup_userName',
					'formatter' => function ($dup_userName, $row) use ($username) {
						$this->db->select('SUM(total_rate) as related_total_rate');
						$this->db->from('duplicate_account_info')
							->where('userName', $dup_userName)
							->where('dup_userName != ', $username);
						return $this->runOneRowOneField('related_total_rate');
					}
				]
			]);
		}

		$dupSearchColumn = [
            ['alias' => 'dup_regIp'            , 'select' => 'dup_regIp'          ] ,
            ['alias' => 'dup_loginIp'          , 'select' => 'dup_loginIp'        ] ,
            ['alias' => 'dup_depositIp'        , 'select' => 'dup_depositIp'      ] ,
            ['alias' => 'dup_withDrawIp'       , 'select' => 'dup_withDrawIp'     ] ,
            ['alias' => 'dup_TranMain2SubIp'   , 'select' => 'dup_TranMain2SubIp' ] ,
            ['alias' => 'dup_TranSub2MainIp'   , 'select' => 'dup_TranSub2MainIp' ] ,
            ['alias' => 'dup_realName'         , 'select' => 'dup_realName'       ] ,
            ['alias' => 'dup_passwd'           , 'select' => 'dup_passwd'         ] ,
            ['alias' => 'dup_email'            , 'select' => 'dup_email'          ] ,
            ['alias' => 'dup_mobile'           , 'select' => 'dup_mobile'         ] ,
            ['alias' => 'dup_address'          , 'select' => 'dup_address'        ] ,
            ['alias' => 'dup_city'             , 'select' => 'dup_city'           ] ,
            ['alias' => 'dup_country'          , 'select' => 'dup_country'        ] ,
            ['alias' => 'dup_cookie'           , 'select' => 'dup_cookie'         ] ,
            ['alias' => 'dup_referrer'         , 'select' => 'dup_referrer'       ] ,
            ['alias' => 'dup_device'           , 'select' => 'dup_device'         ] ,
        ];
        $dupEnabledColumn = $this->utils->getConfig('duplicate_account_info_enalbed_condition');

        foreach ($dupSearchColumn as $key => $row) {
            $_del = true;
            foreach ($dupEnabledColumn as $column) {
                $_rowName = $row['select'];
                if ($column == 'ip' && strpos($_rowName, 'Ip') !== false) {
                    $_del = false; break;
                }
                if ($column == 'password' && strpos($_rowName, 'passwd') !== false) {
                    $_del = false; break;
                }
                if ($column == 'realname' && strpos($_rowName, 'realName') !== false) {
                    $_del = false; break;
                }
                $_dupColumn = 'dup_' . $column;
                if ($_dupColumn == $_rowName) {
                    $_del = false; break;
                }
            }
            if ($_del) {
                unset($dupSearchColumn[$key]);
                continue;
            }
            $dupSearchColumn[$key]['dt'] = $i++;
        }

        $columns = array_merge($columns, $dupSearchColumn);
		$where[]	= 'userName = ?';
		$values[]	= $username;

        $is_exclude_ip_enabled = $this->utils->isEnabledFeature('exclude_ips_in_duplicate_account');
        if($is_exclude_ip_enabled){
            $list_of_ips_to_be_excluded_in_duplicate_account = $this->utils->getConfig('list_of_ips_to_be_excluded_in_duplicate_account');
            $list_of_ips_to_be_excluded_in_duplicate_account = implode(',', $list_of_ips_to_be_excluded_in_duplicate_account);

            $this->utils->debug_log("Duplicate account excluded IP's ===========>", $list_of_ips_to_be_excluded_in_duplicate_account);
            if(!empty($list_of_ips_to_be_excluded_in_duplicate_account)){
                $where[]    = '(dup_regIp NOT IN (?) AND dup_loginIp NOT IN (?) AND dup_depositIp NOT IN (?) AND dup_withDrawIp NOT IN (?) AND dup_TranMain2SubIp NOT IN (?) AND dup_TranSub2MainIp NOT IN (?))';
                $values[]   = $list_of_ips_to_be_excluded_in_duplicate_account;
                $values[]   = $list_of_ips_to_be_excluded_in_duplicate_account;
                $values[]   = $list_of_ips_to_be_excluded_in_duplicate_account;
                $values[]   = $list_of_ips_to_be_excluded_in_duplicate_account;
                $values[]   = $list_of_ips_to_be_excluded_in_duplicate_account;
                $values[]   = $list_of_ips_to_be_excluded_in_duplicate_account;
            }
        }

        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having);

		return $result;
	}

	/**
	 * detail: get promotion report
	 *
	 * @param array $request
	 * @param Boolean $is_export
	 * @return array
	 */
	public function promotionReport($request, $is_export = false) {

		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('transactions', 'player_model', 'player_promo', 'promorules'));
		$promorules = $this->promorules;
		$this->data_tables->is_export = $is_export;

		$transaction_types = array(Transactions::ADD_BONUS, Transactions::MEMBER_GROUP_DEPOSIT_BONUS,
			Transactions::PLAYER_REFER_BONUS, Transactions::RANDOM_BONUS);

		# START DEFINE COLUMNS #################################################################################################################################################
		// $i = 0;
		$date_column = 0;
		$username_column = 1;
		$group_level_column = 2;
		$promotion_column = 3;
		$promo_status_column = 4;
		$amount_column = 5;
		$columns = array(
			array(
				'alias' => 'player_id',
				'select' => 'playerpromo.playerId',
			),
			array(
				'alias' => 'promoTypeName',
				'select' => 'promotype.promoTypeName',
			),
            array(
                'select' => 'playerpromo.playerpromoId',
                'alias' => 'playerpromoId',
            ),
			array(
				'alias' => 'transaction_type',
				'select' => 'transactions.transaction_type',
			),
			array(
				'dt' => $date_column,
				'alias' => 'created_at',
				'select' => 'transactions.created_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('player.ut01'),
			),
			array(
				'dt' => $username_column,
				'alias' => 'username',
				'select' => '( CASE WHEN affiliates.username IS NULL THEN player.username ELSE CONCAT(player.username, \' (\', affiliates.username,  \')\' ) END )',
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return ($d ? $d : lang('lang.norecyet'));
					} else {
						return '<i class="fa fa-user" ></i> ' . ($d ? '<a href="/player_management/userInformation/' . $row['player_id'] . '" target="_blank">' . $d . '</a>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>');
					}
				},
				'name' => lang('report.pr01'),
			),
			array(
				'dt' => $group_level_column,
				'alias' => 'group_level',
				'select' => 'CONCAT(vipsetting.groupName, \'|\', vipsettingcashbackrule.vipLevelName )',
				'formatter' => function ($d) use ($is_export) {
                    $d = (explode("|",$d));
                    if(isset($d[1])){
                        return lang($d[0]).' - '.lang($d[1]);
                    }else{
                        return lang($d[0]);
                    }
				},
				'name' => lang('Member Level'),
			),
			array(
				'alias' => 'transPromoTypeName',
				'select' => 'transpromotype.promoTypeName',
			),
			array(
				'alias' => 'promorulesId',
				'select' => 'promorules.promorulesId',
			),
			array(
				'dt' => $promotion_column,
				'alias' => 'promotion',
				'select' => 'promorules.promoName',
				'formatter' => function ($d, $row) use ($is_export, $promorules) {
					list($promoName, $promoType, $promoDetails) = $promorules->getPromoNameAndType($row['transaction_type'], $row['transPromoTypeName'],
						$row['promoTypeName'], $d, null, $row['group_level']);

					$fullPromoDesc = implode(' - ', array(lang($promoType), $promoName));
					if ($is_export) {
						return $fullPromoDesc;
					} else {
						$showPromoRuleDetailsText = lang('cms.showPromoRuleDetails');
						$promolink = <<<EOD
<a href='###' data-toggle='modal' data-target='#promoDetails' onclick='return viewPromoRuleDetails({$row['promorulesId']})'>
<span data-toggle='tooltip' data-original-title='$showPromoRuleDetailsText' data-placement="top">
   {$fullPromoDesc}
</span>
</a>
EOD;
						return !empty($fullPromoDesc) ? $promolink : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
					//return $d;
				},
				'name' => lang('report.p44'),
			),
			array(
				'dt' => $promo_status_column,
				'alias' => 'promo_status',
				'select' => 'playerpromo.transactionStatus',
				'name' => lang('Promotion Status'),
				'formatter' => function ($d, $row) use ($is_export) {
					if ($d == Player_promo::TRANS_STATUS_APPROVED) {
						$d = lang('Approved');
					} else if ($d == Player_promo::TRANS_STATUS_DECLINED) {
						$d = lang('Declined');
					// if the status is 9
					} else if ($d == Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION) {
						$d = lang('Finished Withdraw Condition');
					}

					if ($is_export) {
						return $d;
					} else {
						return $d ? $d : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $amount_column,
				'alias' => 'amount',
				'select' => 'transactions.amount',
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return $this->utils->formatCurrencyNoSym($d);
					} else {
						return $d == 0 ? '<span class="text-muted">' . $this->utils->formatCurrencyNoSym($d) . '</span>' : '<strong>' . $this->utils->formatCurrencyNoSym($d) . '</strong>';
					}
				},
				'name' => lang('player.ut05'),
			),

		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'playerpromo';
		$joins = array(
			'transactions' => "playerpromo.playerpromoId = transactions.player_promo_id and transactions.transaction_type IN (" . implode(',', $transaction_types) . ")",
			'player' => "playerpromo.playerId = player.playerId",
			'promorules' => "promorules.promorulesId = playerpromo.promorulesId",
			'promotype as transpromotype' => "transpromotype.promotypeId = transactions.promo_category",
			'promotype' => "promotype.promotypeId = promorules.promoCategory",
			'affiliates' => 'affiliates.affiliateId = player.affiliateId',
			'vipsettingcashbackrule' => 'vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId',
			'vipsetting' => 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		// $request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		$this->utils->debug_log('input', $input);

		if (isset($input['byBonusAmountLessThan'])) {
			$where[] = "transactions.amount <= ?";
			$values[] = $input['byBonusAmountLessThan'];
		}

		if (isset($input['byBonusAmountGreaterThan'])) {
			$where[] = "transactions.amount >= ?";
			$values[] = $input['byBonusAmountGreaterThan'];
		}

		if (isset($input['byPlayerLevel'])) {
			$where[] = "player.levelId = ?";
			$values[] = $input['byPlayerLevel'];
		}

		if (isset($input['byPromotionType'])) {
			$where[] = "transactions.promo_category = ?";
			$values[] = $input['byPromotionType'];
		}

		if (isset($input['byPromotionId'])) {
			$where[] = "promorules.promorulesId = ?";
			$values[] = $input['byPromotionId'];
		}

		if (isset($input['byPromotionStatus'])) {
			$where[] = "playerpromo.transactionStatus = ?";
			$values[] = $input['byPromotionStatus'];
		}

		if ($this->safeGetParam($input, 'enableDate') == 'true') {

			if (isset($input['byBonusPeriodJoinedFrom'])) {
				$where[] = "transactions.created_at >= ?";
				$values[] = $input['byBonusPeriodJoinedFrom'] . ' 00:00:00';
			}

			if (isset($input['byBonusPeriodJoinedTo'])) {
				$where[] = "transactions.created_at <= ?";
				$values[] = $input['byBonusPeriodJoinedTo'] . ' 23:59:59';
			}
		}

		if (isset($input['byUsername'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['byUsername'] . '%';
		}


		$where[] = "player.deleted_at IS NULL";

		//==where condition=================================


		# END PROCESS SEARCH FORM #################################################################################################################################################
		if($is_export){
            $this->data_tables->options['is_export']=true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename']=$csv_filename;
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

        if($is_export){
		    //drop result if export
			return $csv_filename;
		}

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(transactions.amount) total_amount', null, $columns, $where, $values);

		$result['summary'][0]['total_amount'] = $this->utils->formatCurrencyNoSym($summary[0]['total_amount']);

		return $result;
	}

	/**
	 * detail: get cashback report
	 *
	 * @param array $request
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function cashbackReport($request, $is_export = false) {

		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('transactions', 'player_model', 'group_level'));

		$this->data_tables->is_export = $is_export;

		$player_model = $this->player_model;

		// $this->benchmark->mark('pre_processing_start');

		# START DEFINE COLUMNS #################################################################################################################################################
		// $i = 0;
		$date_column = 0;
		$username_column = 1;
		$group_level_column = 2;
		$amount_column = 3;
		$bet_amount_column = 4;
		$origin_bet_amount_column = 5;
		$paid_flag_column = 6;
		$game_platform_column = 7;
		$game_type_column = 8;
		$game_description_column = 9;
		$updated_at_column = 10;
		$paid_date_column = 11;
		$paid_amount_column = 12;
		$withdraw_condition_amount_column = 13;
		$cashback_type = 14;
		$friend_referral_cashback = 15;
		$columns = array(
			array(
				'alias' => 'player_id',
				'select' => 'total_cashback_player_game_daily.player_id',
			),
			array(
				'alias' => 'id',
				'select' => 'total_cashback_player_game_daily.id',
			),
			array(
				'alias' => 'cashback_percentage',
				'select' => 'total_cashback_player_game_daily.cashback_percentage',
			),
			array(
				'dt' => $date_column,
				'alias' => 'total_date',
				'select' => 'total_cashback_player_game_daily.total_date',
				'formatter' => 'dateFormatter',
				'name' => lang('Date'),
			),
			array(
				'dt' => $username_column,
				'alias' => 'username',
				'select' => '( CASE WHEN affiliates.username IS NULL THEN player.username ELSE CONCAT(player.username, \' (\', affiliates.username,  \')\' ) END )',
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return ($d ? $d : lang('N/A'));
					} else {
						$str = '<i class="fa fa-user" ></i> ' . ($d ? '<a href="/player_management/userInformation/' . $row['player_id'] . '" target="_blank">' . $d . '</a>' : '<i class="text-muted">' . lang('N/A') . '</i>');
						if ($row['paid_flag'] != '1') {
							$str = '<input type="checkbox" class="chk_row" value="' . $row['id'] . '" onchange="changeSelectRow()"> ' . $str;
						}
						return $str;
					}
				},
				'name' => lang('Player Username'),
			),
			array(
				'dt' => $group_level_column,
				'alias' => 'group_level',
				'select' => 'concat_ws("|", vipsetting.groupName, vipsettingcashbackrule.vipLevelName, concat(ifnull(total_cashback_player_game_daily.cashback_percentage, "0"), "%"))',
				'formatter' => function ($d) {
					$d = (explode("|",$d));
					return lang($d[0]).' - '.lang($d[1]).' - '.$d[2];
				},
				'name' => lang('VIP Level'),
			),
			array(
				'dt' => $amount_column,
				'alias' => 'amount',
				'select' => 'total_cashback_player_game_daily.amount',
				'name' => lang('Amount'),
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $bet_amount_column,
				'alias' => 'bet_amount',
				'select' => 'total_cashback_player_game_daily.bet_amount',
				'name' => lang('Bet Amount'),
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $origin_bet_amount_column,
				'alias' => 'original_bet_amount',
				'select' => 'total_cashback_player_game_daily.original_bet_amount',
				'name' => lang('Original Bet Amount'),
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $paid_flag_column,
				'alias' => 'paid_flag',
				'select' => 'total_cashback_player_game_daily.paid_flag',
				'name' => lang('Paid'),
				'formatter' => function ($d, $row) use ($is_export) {
					return $d == '1' ? lang('Paid') : lang('Not pay');
				},
			),
			array(
				'dt' => $game_platform_column,
				'alias' => 'game_platform',
				'select' => 'external_system.system_code',
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return $d;
					} else {
						return $d ? $d : '<i class="text-muted">' . lang('N/A') . '</i>';
					}
				},
				'name' => lang('Game Platform'),
			),
			array(
				'dt' => $game_type_column,
				'alias' => 'game_type',
				'select' => 'game_type.game_type_lang',
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return $d ? lang($d) : lang('N/A');
					} else {
						return $d ? lang($d) : '<i class="text-muted">' . lang('N/A') . '</i>';
					}
				},
				'name' => lang('Game Type'),
			),
			array(
				'dt' => $game_description_column,
				'alias' => 'game_description',
				'select' => 'game_description.game_name',
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return $d ? lang($d) : lang('N/A');
					} else {
						return $d ? lang($d) : '<i class="text-muted">' . lang('N/A') . '</i>';
					}
				},
				'name' => lang('Game'),
			),
			array(
				'dt' => $updated_at_column,
				'alias' => 'updated_at',
				'select' => 'total_cashback_player_game_daily.updated_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('Updated at'),
			),
			array(
				'dt' => $paid_date_column,
				'alias' => 'paid_date',
				'select' => 'total_cashback_player_game_daily.paid_date',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('Paid date'),
			),
			array(
				'dt' => $paid_amount_column,
				'alias' => 'paid_amount',
				'select' => 'total_cashback_player_game_daily.paid_amount',
				'formatter' => 'currencyFormatter',
				'name' => lang('Paid amount'),
			),
			array(
				'dt' => $withdraw_condition_amount_column,
				'alias' => 'withdraw_condition_amount',
				'select' => 'total_cashback_player_game_daily.withdraw_condition_amount',
				'formatter' => 'currencyFormatter',
				'name' => lang('Withdraw Condition amount'),
			)
		);

		if($this->utils->isEnabledFeature('enable_friend_referral_cashback')) {
			$cashback_type = array(
				array(
					'dt' => $cashback_type,
					'alias' => 'cashback_type',
					'select' => 'total_cashback_player_game_daily.cashback_type',
					'formatter' => function($d, $row) {
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
					'name' => lang('Cashback Type')
				),
				array(
					'dt' => $friend_referral_cashback,
					'alias' => 'invited_player_id',
					'select' => 'total_cashback_player_game_daily.invited_player_id',
					'formatter' => function($d, $row) use ($readOnlyDB, $player_model) {
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
					'name' => lang('Referred Player for Cashback')
				),
			);
			$columns = array_merge($columns, $cashback_type);
		}



		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'total_cashback_player_game_daily';
		$joins = array(
			'player' => "total_cashback_player_game_daily.player_id = player.playerId",
			'game_description' => "game_description.id = total_cashback_player_game_daily.game_description_id",
			'game_type' => "game_type.id = total_cashback_player_game_daily.game_type_id",
			'external_system' => "external_system.id = total_cashback_player_game_daily.game_platform_id",
			'vipsettingcashbackrule' => "vipsettingcashbackrule.vipsettingcashbackruleId=player.levelId",
			'affiliates' => 'affiliates.affiliateId = player.affiliateId',
			'vipsetting' => 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId',
		);

		if($this->utils->isEnabledFeature('enable_friend_referral_cashback')) {
		#	$joins['playerfriendreferral'] = 'playerfriendreferral.invitedPlayerId = player.playerId';
		}

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		// $request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		// $this->utils->debug_log('input', $input);

		if (isset($input['by_amount_less_than'])) {
			$where[] = "total_cashback_player_game_daily.amount <= ?";
			$values[] = $input['by_amount_less_than'];
		}

		if (isset($input['by_amount_greater_than'])) {
			$where[] = "total_cashback_player_game_daily.amount >= ?";
			$values[] = $input['by_amount_greater_than'];
		}

		if (isset($input['by_player_level'])) {
			$where[] = "player.levelId = ?";
			$values[] = $input['by_player_level'];
		}

		if (isset($input['by_paid_flag'])) {
			$where[] = "total_cashback_player_game_daily.paid_flag = ?";
			$values[] = $input['by_paid_flag'];
		}

		if (isset($input['by_cashback_type'])) {
			$where[] = "total_cashback_player_game_daily.cashback_type = ?";
			$values[] = $input['by_cashback_type'];
		}

		// only default to normal if not enable referral cashback
		if(!$this->utils->isEnabledFeature('enable_friend_referral_cashback')) {
			$where[] = "total_cashback_player_game_daily.cashback_type = ?";
			$values[] = Group_level::NORMAL_CASHBACK;
		}

		// if (isset($input['byPromotionId'])) {
		// 	$where[] = "promorules.promorulesId = ?";
		// 	$values[] = $input['byPromotionId'];
		// }

		if ($this->safeGetParam($input, 'enable_date') == 'true') {

			if (isset($input['by_date_from'])) {
				$where[] = "total_cashback_player_game_daily.total_date >= ?";
				$values[] = $input['by_date_from'];
			}

			if (isset($input['by_date_to'])) {
				$where[] = "total_cashback_player_game_daily.total_date <= ?";
				$values[] = $input['by_date_to'];
			}
		}

		//convert memberUsername to player id
		// $this->utils->debug_log('memberUsername', $input['memberUsername']);

		if (isset($input['by_username'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['by_username'] . '%';
		}

		$where[] = "player.deleted_at IS NULL";

		// $transaction_type = array(Transactions::ADD_BONUS, Transactions::MEMBER_GROUP_DEPOSIT_BONUS, Transactions::PLAYER_REFER_BONUS, Transactions::RANDOM_BONUS);
		// $where[] = "transactions.transaction_type IN (" . implode(',', array_fill(0, count($transaction_type), '?')) . ")";
		// $values = array_merge($values, $transaction_type);

		//==where condition=================================

		// $this->benchmark->mark('pre_processing_end');

		# END PROCESS SEARCH FORM #################################################################################################################################################
		// $this->benchmark->mark('data_sql_start');
		if($is_export){
            $this->data_tables->options['is_export']=true;
			// $this->data_tables->options['only_sql']=true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename']=$csv_filename;
		}
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		// $this->benchmark->mark('data_sql_end');

		// $this->utils->debug_log($result);
		if($is_export){
		    //drop result if export
			return $csv_filename;
		}

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(total_cashback_player_game_daily.amount) total_amount', null, $columns, $where, $values);

		$result['summary'][0]['total_amount'] = $this->utils->formatCurrencyNoSym($summary[0]['total_amount']);

		return $result;

	}

	public function agency_get_logs($request, $is_export) {

		$this->load->model(['agency_model']);
		$result = $this->agency_model->get_logs($request, $is_export);

		return $result;
	}

	public function agency_get_transactions($request, $is_export) {

		$this->load->model(['agency_model']);
		$result = $this->agency_model->get_transactions($request, $is_export);

		return $result;
	}

	public function get_agent_list($request, $is_export) {

		$this->load->model(['agency_model']);
		$result = $this->agency_model->get_agent_list($request, $is_export);

		return $result;
	}

	public function get_structure_list($request, $is_export) {

		$this->load->model(['agency_model']);
		$result = $this->agency_model->get_structure_list($request, $is_export);

		return $result;
	}

	public function currencyFormatter($d, $is_export = null) {
		if ($is_export) {
			return $this->utils->formatCurrencyNoSym($d);
		} else {
			return $d == 0 ? '<span class="text-muted">' . $this->utils->formatCurrencyNoSym($d) . '</span>' : '<strong>' . $this->utils->formatCurrencyNoSym($d) . '</strong>';
		}
	}

	//===agency========================================================

	/**
	 *  return player rolling comm info
	 *
	 *  @param
	 *  @return
	 */
	public function player_rolling_comm($request, $logged_agent_id, $is_export = false) {
		$this->load->library(array('data_tables'));
		$this->load->model(['agency_model']);

		$input = $this->data_tables->extra_search($request);
		// $this->utils->debug_log('get_settlement request', $request);
		$table = 'agency_player_rolling_comm';
		$where = array();
		$values = array();
		$joins = array();
		$joins['player'] = 'player.playerId = agency_player_rolling_comm.player_id';
		// $joins['agency_settlement'] = 'agency_settlement.settlement_id = agency_player_rolling_comm.settlement_id';

		// $settlement_id = $input['settlement_id'];
		// $where[] = "agency_settlement.settlement_id = ?";
		// $values[] = $settlement_id;

		if (isset($input['status']) && !empty($input['status'])) {
			$where[] = "agency_player_rolling_comm.payment_status = ?";
			$values[] = $input['status'];
		}

		if (isset($input['search_on_date']) && $input['search_on_date'] == 'true') {
			$where[] = "agency_player_rolling_comm.start_at >= ? and agency_player_rolling_comm.start_at <= ?";
			$values[] = $input['date_from'];
			$values[] = $input['date_to'];
		}

		if (isset($input['player_username']) && !empty($input['player_username'])) {
			$where[] = "player.username like ?";
			$values[] = '%' . $input['player_username'] . '%';
		}

		$search_agent_id = $logged_agent_id;

		$search_agent_id_arr = [$search_agent_id];

		if (isset($input['sub_agent_username']) && !empty($input['sub_agent_username'])) {
			$subagent = $this->agency_model->get_agent_by_name($input['sub_agent_username']);
			if (!empty($subagent)) {
				$search_agent_id = $subagent['agent_id'];
				$search_agent_id_arr = [$search_agent_id];
			}
		}

		if (isset($input['include_all_downlines']) && $input['include_all_downlines'] == 'true') {
			//search sub agent ids by search_agent_id
			$search_agent_id_arr = $this->agency_model->get_all_downline_arr($search_agent_id);
			$search_agent_id_arr[] = $search_agent_id;
			$search_agent_id_arr = array_unique($search_agent_id_arr);
		}

		$where[] = "agency_player_rolling_comm.agent_id in (" . implode(',', $search_agent_id_arr) . ")";
		// $values[] = '';

		$i = 0;
		$columns = array(
			array(
				'alias' => 'player_id',
				'select' => 'player.playerId',
			),
			array(
				'alias' => 'agency_player_rolling_comm_id',
				'select' => 'agency_player_rolling_comm.id',
			),
			array(
				'alias' => 'agent_id',
				'select' => 'agency_player_rolling_comm.agent_id',
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'player.username',
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return $d;
					} else {
						$player_id = $row['player_id'];
						$ret = "<i class='fa fa-user' ></i> ";
						$title = lang('Show Player Info');
						$ret .= "<a href='javascript:void(0)' class='agent-oper' data-toggle='tooltip' title='$title' onclick='show_player_info($player_id)'>$d</a> ";
						return $ret;
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'payment_status',
				'select' => 'agency_player_rolling_comm.payment_status',
				'name' => lang('Status'),
			),
			array(
				'dt' => $i++,
				'alias' => 'start_at',
				'select' => 'agency_player_rolling_comm.start_at',
				'name' => lang('Date From'),
			),
			array(
				'dt' => $i++,
				'alias' => 'end_at',
				'select' => 'agency_player_rolling_comm.end_at',
				'name' => lang('Date To'),
			),
			array(
				'dt' => $i++,
				'alias' => 'real_bets',
				'select' => 'agency_player_rolling_comm.real_bets',
				'name' => lang('Real Bet'),
			),
			array(
				'dt' => $i++,
				'alias' => 'total_bets',
				'select' => 'agency_player_rolling_comm.total_bets',
				'name' => lang('Total Bet'),
			),
			array(
				'dt' => $i++,
				'alias' => 'rolling_rate',
				'select' => 'agency_player_rolling_comm.rolling_rate',
				'name' => lang('Rolling Rate'),
			),
			array(
				'dt' => $i++,
				'alias' => 'rolling_comm_amt',
				'select' => 'agency_player_rolling_comm.rolling_comm_amt',
				'name' => lang('Rolling Comm Amt'),
			),
			array(
				'dt' => $i++,
				'alias' => 'notes',
				'select' => 'agency_player_rolling_comm.notes',
				'name' => lang('Notes'),
			),
			array(
				'dt' => $i++,
				'alias' => 'action',
				'select' => '""',
				'name' => lang('Action'),
				'formatter' => function ($d, $row) use ($is_export, $logged_agent_id) {
					if ($is_export || $logged_agent_id != $row['agent_id']) {
						return '';
					} else {

						//by status
						$status = $row['payment_status'];
						$id = $row['agency_player_rolling_comm_id'];
						$amount = $row['rolling_comm_amt'];
						$username = $row['username'];
						$show_settle = false;
						$show_pending = false;
						if ($status == 'pending') {
							$show_settle = true;
						} elseif ($status == 'settled') {
							//none
						} elseif ($status == 'current') {
							$show_settle = true;
							$show_pending = true;
						}
						$str = '';
						if ($show_settle) {
							$str .= ' <input type="button" class="settle btn btn-sm btn-primary" '
							. 'onclick="settle_rolling(' . $id . ', \'' . $amount . '\', \'' . $username . '\')" value="' . lang('Settle') . '">';
						}
						if ($show_pending) {
							$str .= ' <input type="button" class="pending btn btn-sm btn-success" '
							. 'onclick="pending_rolling(' . $id . ', \'' . $amount . '\', \'' . $username . '\')" value="' . lang('Pending') . '">';
						}

						return $str;

					}
				},
			),
		);
		//order by date from
		$external_order = [['column' => 2, 'dir' => 'desc']];
		//$request, $columns, $table, $form_search = array(), $values = array(), $joins = array(), $group_by = array(), $having = array(), $distinct = true, $external_order
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, null, null, true, $external_order);
		// $this->utils->debug_log('GET_PLAYER_ROLLING_COMM_INFO result', $result);
		$summary = $this->data_tables->summary($request, $table, $joins,
			'SUM( real_bets ) total_real_bet, SUM(total_bets) total_avail_bet, SUM(rolling_comm_amt) total_amount',
			null, $columns, $where, $values);

		$result['summary'][0]['total_real_bet'] = $this->utils->formatCurrencyNoSym($summary[0]['total_real_bet']);
		$result['summary'][0]['total_avail_bet'] = $this->utils->formatCurrencyNoSym($summary[0]['total_avail_bet']);
		$result['summary'][0]['total_amount'] = $this->utils->formatCurrencyNoSym($summary[0]['total_amount']);

		$result['sub_summary'][0]['sub_total_real_bet'] = $this->utils->formatCurrencyNoSym(array_sum(array_map(function ($row) {return str_replace(',', '', $row[4]);}, $result['data'])));
		$result['sub_summary'][0]['sub_total_avail_bet'] = $this->utils->formatCurrencyNoSym(array_sum(array_map(function ($row) {return str_replace(',', '', $row[5]);}, $result['data'])));
		$result['sub_summary'][0]['sub_total_amount'] = $this->utils->formatCurrencyNoSym(array_sum(array_map(function ($row) {return str_replace(',', '', $row[7]);}, $result['data'])));

		return $result;
	}

	//===agency========================================================


	public function task_list($request, $user_id, $isAdminUser, $is_export=false){
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('queue_result','users'));
		// $request = $this->input->post();
		$use_export_csv_with_progress_template =$this->utils->getConfig('use_export_csv_with_progress');
		$input = $this->data_tables->extra_search($request);

		$table = 'queue_results';

		$joins = array();
		$where = array();
		$values = array();
		$group_by = array();
		$having = array();


		# DEFINE TABLE COLUMNS ########################################################################################################################################################################
		$i = 0;
		$columns = [
			[
				'alias' => 'id',
				'select' => 'queue_results.id'
			],
			[
				'alias' => 'status',
				'select' => 'queue_results.status'
			],
			[
				'alias' => 'created_at',
				'select' => 'queue_results.created_at'
			],
			[
				'alias' => 'func_name',
				'select' => 'queue_results.func_name'
			],
			[
				'dt' => $i++,
				'alias' => 'updated_at',
				'select' => 'queue_results.updated_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('Date'),
			],
			[
				'dt' => $i++,
				'alias' => 'token',
				'select' => 'queue_results.token',
				'formatter' => function($d, $row) use ($is_export,$use_export_csv_with_progress_template) {
					if(!$is_export) {

						$funcName = $row['func_name'];
						$use_export_csv_with_progress  = false;
						$is_remote_export = $funcName == 'remote_export_csv';
						if(in_array($funcName, $use_export_csv_with_progress_template['admin'])){
					    	$use_export_csv_with_progress =  true;
				     	}

                        if($use_export_csv_with_progress || $is_remote_export){
                        	$d='<a href="'.site_url('/export_data/queue/'.$d).'" target="_blank">'.$d.'</a>';
						}else{
							$d='<a href="'.site_url('/system_management/common_queue/'.$d).'" target="_blank">'.$d.'</a>';
						}
					}
					return $d;
				},
				'name' => lang('ID'),
			],
			[
				'dt' => $i++,
				'alias' => 'func_name',
				'select' => 'queue_results.func_name',
				'formatter' => 'languageFormatter',
				'name' => lang('Type'),
			],
			[
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'queue_results.status',
				'formatter' => function($d, $row) use ($is_export) {

					$queue_row =  $this->queue_result->getResult($row['token']);
					$rlt = (array)json_decode($queue_row['result']);
					$use_export_csv_with_progress_template =$this->utils->getConfig('use_export_csv_with_progress');
					$use_export_csv_with_progress = false;
					$funcName = $queue_row['func_name'];
					$is_remote_export = $funcName == 'remote_export_csv';
					$progress = '';
					if(in_array($funcName, $use_export_csv_with_progress_template['admin']) || $is_remote_export){
						$use_export_csv_with_progress =  true;
						$progress_stat = isset($rlt['progress']) ? $rlt['progress'] : '0';
						$written = isset($rlt['written']) ? $rlt['written'] : '';
						$total_count = isset($rlt['total_count']) ? $rlt['total_count'] : '';
						$progress .='<span data-toggle="tooltip" title="Written: '.number_format((int)$written).' Total rows: '.number_format((int)$total_count).'">('.$progress_stat.'%)</span>';
						// $progress .='<div class="progress" data-toggle="tooltip" title="Written: '.number_format((int)$written).' Total rows: '.number_format((int)$total_count).'">';
						// $progress .='<div class="progress-bar progress-bar-striped active" style="width:'.$progress_stat.'%;min-width:40%" role="progressbar" aria-valuenow="1" aria-valuemin="20" aria-valuemax="100" >';
						// $progress .='<span class="progresslabel">'.$progress_stat.'%</span>';
						// $progress .='</div>';
						// $progress .='</div>';
					}

					switch ($d) {
						case Queue_result::STATUS_NEW_JOB:
							if($use_export_csv_with_progress || $is_remote_export){
								//$d=lang('Processing')."(".$rlt['progress']."%)".$progress;
								$d=lang('Processing').$progress;
							}else{
								$d=lang('Processing');
							}
							break;

						case Queue_result::STATUS_DONE:
							$d=lang('Done');
							break;

						case Queue_result::STATUS_ERROR:
							$d=lang('Failed');
							break;

						case Queue_result::STATUS_STOPPED:
							$d=lang('Stopped');
							break;
					}
					return $d;
				},
				'name' => lang('Status'),
			],
			[
				'dt' => $i++,
				'alias' => 'result',
				'select' => 'queue_results.token',
				'formatter' => function ($d, $row)  use ($is_export)  {
					if(!$is_export) {
						$d='<a href="'.site_url('/system_management/common_queue/'.$d).'" target="_blank">'.lang('Show').'</a>';
						// $id="_result_".$row['id'];
						// return "<a class='btn btn-xs btn-primary' onclick='showContent(\"".$id."\")'>".lang('Show')."</a><div style='display:none;' id='".$id."'><pre>".$this->utils->encodeJson($this->utils->decodeJson($d), true)."</pre></div>";
						// return "<pre>".$this->utils->encodeJson($this->utils->decodeJson($d), true)."</pre>";
					}
					return $d;
				},
				'name' => lang('Result'),
			],
			[
				'dt' => $i++,
				'alias' => 'created_at',
				'select' => 'queue_results.created_at',
				'name' => lang('Created At'),
			],
			[
				'dt' => $i++,
				'alias' => 'admin_user',
				'select' => 'adminusers.username',
				'name' => lang('Admin User'),
			],
			[
				'dt' => $i++,
				'alias' => 'player_username',
				'select' => 'player.username',
				'name' => lang('Player'),
			],
			[
				'dt' => $i++,
				'alias' => 'language',
				'select' => 'queue_results.lang',
				'formatter' => function ($d, $row)  use ($is_export)  {
					if($d=='2'){
						$d=lang('Chinese');
					}else{
						$d=lang('English');
					}

					return $d;
				},
				'name' => lang('Language'),
			],
		];

		$joins['adminusers'] = 'adminusers.userId = queue_results.caller and queue_results.caller_type='.Queue_result::CALLER_TYPE_ADMIN;
		$joins['player'] = 'player.playerId = queue_results.caller and queue_results.caller_type='.Queue_result::CALLER_TYPE_PLAYER;
		// $joins['game_type'] = 'game_type.id = total_player_game_hour.game_type_id';
		// $joins['game_description'] = 'game_description.id = total_player_game_hour.game_description_id';
		// $joins['external_system'] = 'external_system.id = total_player_game_hour.game_platform_id';
		// $joins['player'] = 'player.playerId = total_player_game_hour.player_id';
		// $joins['affiliates'] = 'affiliates.affiliateId = player.affiliateId';

		# FILTER ######################################################################################################################################################################################

		if($isAdminUser){

			$user_id = null;
			if (isset($input['username'])) {
				$user_id = $this->users->getIdByUsername($input['username']);

				$where[] = "queue_results.caller = ? and queue_results.caller_type=?";
				$values[] = $user_id;
				$values[] = Queue_result::CALLER_TYPE_ADMIN;
			}

		}


		if (isset($input['datetime_from'], $input['datetime_to'])) {

			$where[] ='queue_results.updated_at >= ?';
			$where[] ='queue_results.updated_at <= ?';

			$values[] = $input['datetime_from'];
			$values[] = $input['datetime_to'];

		}

		# OUTPUT ######################################################################################################################################################################################
        // $this->utils->debug_log('GAME_REPORT where values', $where, $values, $group_by, $having, $joins);
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, false);

		return $result;
	}

	public function response_result_list($request, $is_export=true){
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('response_result','player_model'));

		$input = $this->data_tables->extra_search($request);
		$table = 'response_results';

		$joins = array();
		$where = array();
		$values = array();
		$group_by = array();
		$having = array();

		$apiMap=$this->utils->getAllSystemMap();
		$abstractApi=$this->utils->loadAnyGameApiObject();

		# DEFINE TABLE COLUMNS ########################################################################################################################################################################
		$i = 0;
		$columns = [
			[
				'dt' => $i++,
				'alias' => 'created_at',
				'select' => 'response_results.created_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('Date'),
			],
			[
				'dt' => $i++,
				'alias' => 'id',
				'select' => 'response_results.id',
				'name' => lang('ID'),
			],
			[
				'dt' => $i++,
				'alias' => 'system_type_id',
				'select' => 'response_results.system_type_id',
				'formatter' => function($d, $row) use ($is_export, $apiMap) {
					if(isset($apiMap[$d])){
						return $apiMap[$d];
					}
					else if($d == SMS_API){
						return 'SMS';
					}

					return $d;
				},
				'name' => lang('API ID'),
			],
			[
				'dt' => $i++,
				'alias' => 'related_id2',
				'select' => 'response_results.related_id2',
				'formatter' => function($d, $row) use ($is_export) {
					if(!$is_export) {
						$resentFlag = ''; // append resend flag
						if($row['extra']){
							$resentFlag .= '<div class="glyphicon glyphicon-repeat resendFlag" data-toggle="tooltip" data-placement="right" title="resent"></div>';
						}

						if(substr($d, 0, 1) == 'W') {
							return empty($d) ? '' : '<a href="'.site_url('/payment_management/viewWithdrawalRequestList?dwStatus=payProc&withdraw_code='.$d).'" target="_blank">'.$d.'</a>'. $resentFlag;
						} else {
							return empty($d) ? '' : '<a href="'.site_url('/payment_management/deposit_list/?secure_id='.$d).'" target="_blank">'.$d.'</a>'. $resentFlag;
						}
					}
					return $d;
				},
				'name' => lang('Secure Id'),
			],
			[
				'dt' => $i++,
				'alias' => 'related_id3',
				'select' => 'response_results.related_id3',
				'formatter' => function($d, $row) use ($is_export) {
					if($row['request_api'] == 'SMS' || $row['request_api'] == 'telesales_response')
					{
						if(!$is_export) {
							return empty($d) ? '' : '<a href="'.site_url('/player_management/searchAllPlayer?contactNumber='.$d).'" target="_blank">'.$d.'</a>';
						}
						return $d;
					}
					else
						return '';
				},
				'name' => lang('Mobile'),
			],
			[
				'dt' => $i++,
				'alias' => 'related_id3',
				'select' => 'response_results.related_id3',
				'formatter' => function($d, $row) use ($is_export) {

					return $row['system_type_id'] == SMTP_API ? $d : '';

				},
				'name' => lang('Email Address'),
			],
			[
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'response_results.filepath',
				'formatter' => function($d, $row) use ($is_export) {
					if(!$is_export) {
						return empty($d) ? '' : '<a href="'.site_url('/system_management/show_response_content/'.$d).'" target="_blank">'.lang('Show').'</a>';
					}
					return $d;
				},
				'name' => lang('Content'),
			],
			[
				'dt' => $i++,
				'alias' => 'request_api',
				'select' => 'response_results.request_api',
				'name' => lang('Method'),
			],
			[
				'dt' => $i++,
				'alias' => 'status_code',
				'select' => 'response_results.status_code',
				'name' => lang('Status Code'),
			],
			[
				'dt' => $i++,
				'alias' => 'status_text',
				'select' => 'response_results.status_text',
				'name' => lang('Status Text'),
			],
			[
				'dt' => $i++,
				'alias' => 'player_id',
				'select' => 'response_results.player_id',
				'name' => lang('Player'),
				'formatter' => function($d, $row) use ($is_export) {
					if($is_export){
						return $d;
					}
					if(!empty($d)){
						return '<a href="/player_management/userInformation/'.$d.'" target="_blank">'.$d.'</a>';
					}

					return '';
				},
			],
			[
				'dt' => $i++,
				'alias' => 'flag',
				'select' => 'response_results.flag',
				'formatter' => function($d, $row) use ($is_export) {

					switch ($d) {
						case 1:
							return lang('Normal');
							break;

						case 2:
							return lang('Error');
							break;

						default:
							return lang('Unknown');
							break;
					}
				},
				'name' => lang('Flag'),
			],
			[
				'dt' => $i++,
				'alias' => 'filepath',
				'select' =>'response_results.filepath',
				'formatter' => function($d, $row) use ($is_export) {
					if(!$is_export) {
						return empty($d) ? '' : '<a href="'.site_url('/system_management/download_response_result/'.$d).'" target="_blank">'.lang('Download').'</a>';
					}
					return $d;
				},
				'name' => lang('Result'),
			],
			[ // for resend flag referenced.
				'dt' => $i++,
				'alias' => '',
				'select' =>'response_results.extra',
				'formatter' => function($d, $row) use ($is_export) {
					// if(!$is_export) {
					// 	return empty($d) ? '' : '<a href="'.site_url('/system_management/download_response_result/'.$d).'" target="_blank">'.lang('Download').'</a>';
					// }
					return $d;
				},
				'name' => lang('Result'),
			],
		];

		// $joins['adminusers'] = 'adminusers.userId = queue_results.caller and queue_results.caller_type='.Queue_result::CALLER_TYPE_ADMIN;
		// $joins['player'] = 'player.playerId = queue_results.caller and queue_results.caller_type='.Queue_result::CALLER_TYPE_PLAYER;
		// $joins['game_type'] = 'game_type.id = total_player_game_hour.game_type_id';
		// $joins['game_description'] = 'game_description.id = total_player_game_hour.game_description_id';
		// $joins['external_system'] = 'external_system.id = total_player_game_hour.game_platform_id';
		// $joins['player'] = 'player.playerId = total_player_game_hour.player_id';
		// $joins['affiliates'] = 'affiliates.affiliateId = player.affiliateId';

		# FILTER ######################################################################################################################################################################################

		if (isset($input['username'])) {
			$player_id = $this->player_model->getPlayerIdByUsername($input['username']);
			if(!empty($player_id)){
				$where[]='response_results.player_id = ?';
				$values[]=$player_id;
			}
		}

		if (isset($input['order_id']) && !empty($input['order_id'])) {
			$where[]='response_results.related_id2 = ?';
			$values[]=$input['order_id'];
		}

		if(isset($input['mobile']) && !empty($input['mobile']) && isset($input['email']) && !empty($input['email'])){
			$where[]='response_results.related_id3 = ? || response_results.related_id3 = ?';
			$values[]=$input['mobile'];
			$values[]=$input['email'];
		}
		else
		{
			if (isset($input['mobile']) && !empty($input['mobile'])) {
				$where[]='response_results.related_id3 = ?';
				$values[]=$input['mobile'];

				if (!isset($input['api_id'])) {
					$where[]='response_results.system_type_id = ?';
					$values[]=SMS_API;
				}
			}

			if (isset($input['email']) && !empty($input['email'])) {
				$where[]='response_results.related_id3 = ?';
				$values[]=$input['email'];

				if (!isset($input['api_id'])) {
					$where[]='response_results.system_type_id = ?';
					$values[]=SMTP_API;
				}
			}

		}

		if (isset($input['api_id']) && isset($input['show_gamegateway_api']) && $input['show_gamegateway_api']=='true') {
			$where[]='response_results.system_type_id IN (?,?)';
			$values[]=$input['api_id'];
			$values[]=GAMEGATEWAY_API;
		} elseif (isset($input['api_id'])) {
			$where[]='response_results.system_type_id = ?';
			$values[]=$input['api_id'];
		} elseif (isset($input['show_gamegateway_api']) && $input['show_gamegateway_api']=='true') {
			$where[]='response_results.system_type_id = ?';
			$values[]=GAMEGATEWAY_API;
		}

		if (isset($input['flag'])) {
			$where[]='response_results.flag = ?';
			$values[]=$input['flag'];
		}

		if (isset($input['method']) && !empty($input['method'])) {
			$where[]='response_results.request_api = ?';
			$values[]=$input['method'];
		}else{
			if(isset($input['no_query_balance']) && $input['no_query_balance']=='true'){
				//ignore function
				$where[]='response_results.request_api != ?';
				$values[]=Abstract_game_api::API_queryPlayerBalance;
			}
			if(!isset($input['show_sync_data']) || $input['show_sync_data']!='true'){
				//ignore function
				$where[]='response_results.request_api != ?';
				$where[]='response_results.request_api != ?';
				$where[]='response_results.request_api != ?';
				$where[]='response_results.request_api != ?';
				$values[]=Abstract_game_api::API_syncGameRecords;
				$values[]=Abstract_game_api::API_syncBalance;
				$values[]=Abstract_game_api::API_syncGameRecordsByPlayer;
				$values[]='syncMultiplePlatformGameRecords';
			}
		}

		if (isset($input['result_id']) && !empty($input['result_id'])) {
			$where[]='response_results.id = ?';
			$values[]=$input['result_id'];
		}else{
			if (isset($input['datetime_from'], $input['datetime_to'])) {

				$where[] ='response_results.created_at >= ?';
				$where[] ='response_results.created_at <= ?';

				$values[] = $input['datetime_from'];
				$values[] = $input['datetime_to'];

			}
		}

		# OUTPUT ######################################################################################################################################################################################
        // $this->utils->debug_log('GAME_REPORT where values', $where, $values, $group_by, $having, $joins);
		$distinct=false;
        $external_order=[];
        $not_datatable='';
        $countOnlyField='response_results.id';
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by,
			$having, $distinct, $external_order, $not_datatable, $countOnlyField);
		return $result;
	} // EOF response_result_list

	public function sms_report_list($request, $is_export=true){
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model('response_result');

		$input = $this->data_tables->extra_search($request);
		$table = 'response_results';

		$joins = array();
		$where = array();
		$values = array();
		$group_by = array();
		$having = array();

		# DEFINE TABLE COLUMNS ########################################################################################################################################################################
		$i = 0;
		$columns = [
			[
				'dt' => $i++,
				'alias' => 'created_at',
				'select' => 'response_results.created_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('Date'),
			],
			[
				'dt' => $i++,
				'alias' => 'id',
				'select' => 'response_results.id',
				'name' => lang('ID'),
			],
			[
				'dt' => $i++,
				'alias' => 'system_type_id',
				'select' => 'response_results.system_type_id',
				'formatter' => function($d, $row){
					if($d == SMS_API){
						return 'SMS';
					}
					return $d;
				},
				'name' => lang('API ID'),
			],
			[
				'dt' => $i++,
				'alias' => 'related_id3',
				'select' => 'response_results.related_id3',
				'formatter' => function($d, $row) use ($is_export) {
					if(!$is_export) {
						return empty($d) ? '' : '<a href="'.site_url('/player_management/searchAllPlayer?contactNumber='.$d).'" target="_blank">'.$d.'</a>';
					}
					return $d;
				},
				'name' => lang('Mobile'),
			],
			[
				'dt' => $i++,
				'alias' => 'status_code',
				'select' => 'response_results.status_code',
				'name' => lang('Status Code'),
			],
			[
				'dt' => $i++,
				'alias' => 'status_text',
				'select' => 'response_results.status_text',
				'name' => lang('Status Text'),
			],
			[
				'dt' => $i++,
				'alias' => 'flag',
				'select' => 'response_results.flag',
				'formatter' => function($d, $row) use ($is_export) {

					switch ($d) {
						case 1:
							return lang('Normal');
							break;

						case 2:
							return lang('Error');
							break;

						default:
							return lang('Unknown');
							break;
					}
				},
				'name' => lang('Flag'),
			],
			[
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'response_results.filepath',
				'formatter' => function($d, $row) use ($is_export) {
					if(!$is_export) {
						return empty($d) ? '' : '<a href="'.site_url('/system_management/show_sms_content/'.$d).'" target="_blank">'.lang('Show').'</a>';
					}
					return $d;
				},
				'name' => lang('Content'),
			],
		];

		# FILTER ######################################################################################################################################################################################
		$where[]='response_results.system_type_id = ?';
		$values[]=SMS_API;


		if (isset($input['mobile']) && !empty($input['mobile'])) {
			$where[]='response_results.related_id3 = ?';
			$values[]=$input['mobile'];
		}

		if (isset($input['flag'])) {
			$where[]='response_results.flag = ?';
			$values[]=$input['flag'];
		}

		if (isset($input['result_id']) && !empty($input['result_id'])) {
			$where[]='response_results.id = ?';
			$values[]=$input['result_id'];
		}else{
			if (isset($input['datetime_from'], $input['datetime_to'])) {

				$where[] ='response_results.created_at >= ?';
				$where[] ='response_results.created_at <= ?';

				$values[] = $input['datetime_from'];
				$values[] = $input['datetime_to'];

			}
		}

		# OUTPUT ######################################################################################################################################################################################
		$distinct=false;
        $external_order=[];
        $not_datatable='';
        $countOnlyField='response_results.id';
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by,
			$having, $distinct, $external_order, $not_datatable, $countOnlyField);

		return $result;
	}

	public function smtp_report_list($request, $is_export=true){
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model('response_result');

		$input = $this->data_tables->extra_search($request);
		$table = 'response_results';

		$joins = array();
		$where = array();
		$values = array();
		$group_by = array();
		$having = array();

		# DEFINE TABLE COLUMNS ########################################################################################################################################################################
		$i = 0;
		$columns = [
			[
				'dt' => $i++,
				'alias' => 'created_at',
				'select' => 'response_results.created_at',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('Date'),
			],
			[
				'dt' => $i++,
				'alias' => 'id',
				'select' => 'response_results.id',
				'name' => lang('ID'),
			],
			[
				'dt' => $i++,
				'alias' => 'system_type_id',
				'select' => 'response_results.system_type_id',
				'formatter' => function($d, $row){
					if($d == SMTP_API){
						return 'SMTP';
					}
					return $d;
				},
				'name' => lang('API ID'),
			],
			[
				'dt' => $i++,
				'alias' => 'player_id',
				'select' => 'response_results.player_id',
				'formatter' => function($d, $row) use ($is_export) {

					$username = '';

					if(!empty($d))
					{
						$this->load->model('player_model');
						$username = $this->player_model->getUsernameById($d);
					}

					if(!$is_export) {
						return empty($d) || empty($username) ? '' : '<a href="'.site_url('/player_management/userInformation/'.$d).'" target="_blank">'.$username.'</a>';
					}

					return $username;
				},
				'name' => lang('Username'),
			],
			[
				'dt' => $i++,
				'alias' => 'related_id3',
				'select' => 'response_results.related_id3',
				'formatter' => function($d, $row) use ($is_export) {
					return $d;
				},
				'name' => lang('Email Address'),
			],
			[
				'dt' => $i++,
				'alias' => 'status_code',
				'select' => 'response_results.status_code',
				'name' => lang('Status Code'),
			],
			[
				'dt' => $i++,
				'alias' => 'status_text',
				'select' => 'response_results.status_text',
				'name' => lang('Status Text'),
			],
			[
				'dt' => $i++,
				'alias' => 'flag',
				'select' => 'response_results.flag',
				'formatter' => function($d, $row) use ($is_export) {

					switch ($d) {
						case 1:
							return lang('Normal');
							break;

						case 2:
							return lang('Error');
							break;

						default:
							return lang('Unknown');
							break;
					}
				},
				'name' => lang('Flag'),
			],
			[
				'dt' => $i++,
				'alias' => 'content',
				'select' => 'response_results.filepath',
				'formatter' => function($d, $row) use ($is_export) {
					if(!$is_export) {
						return empty($d) ? '' : '<a href="'.site_url('/system_management/show_smtp_api_content/'.$d).'" target="_blank">'.lang('Show').'</a>';
					}
					return $d;
				},
				'name' => lang('Content'),
			],
		];

		# FILTER ######################################################################################################################################################################################
		$where[]='response_results.system_type_id = ?';
		$values[]=SMTP_API;


		if (isset($input['email']) && !empty($input['email'])) {
			$where[]='response_results.related_id3 = ?';
			$values[]=$input['email'];
		}

		if (isset($input['username']) && !empty($input['username'])) {

			$this->load->model('player_model');
			$player_id = $this->player_model->getPlayerIdByUsername($input['username']);

			$where[]='response_results.player_id = ?';
			$values[]=$player_id;
		}

		if (isset($input['flag'])) {
			$where[]='response_results.flag = ?';
			$values[]=$input['flag'];
		}

		if (isset($input['result_id']) && !empty($input['result_id'])) {
			$where[]='response_results.id = ?';
			$values[]=$input['result_id'];
		}else{
			if (isset($input['datetime_from'], $input['datetime_to'])) {

				$where[] ='response_results.created_at >= ?';
				$where[] ='response_results.created_at <= ?';

				$values[] = $input['datetime_from'];
				$values[] = $input['datetime_to'];

			}
		}

		# OUTPUT ######################################################################################################################################################################################
		$distinct=false;
        $external_order=[];
        $not_datatable='';
        $countOnlyField='response_results.id';
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by,
			$having, $distinct, $external_order, $not_datatable, $countOnlyField);

		return $result;
	}

	public function responsibleGamingReport($request, $is_export = false){
        $readOnlyDB = $this->getReadOnlyDB();

        $this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->library('player_manager');

		$this->load->model(array('responsible_gaming','player_model','transactions','game_logs','risk_score_model'));

        $this->load->helper(['player_helper']);
        $rgArr = $this->responsible_gaming->getAllTypeReport();
        //$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);


		$table =<<<EOF
(  /* START as responsible_gaming_reporting */
	SELECT
		R.player_id player_id,
		R.player_id id,
		R.id request_id,
		P.username username, /* sol */

		P.levelId,
        P.groupName,
        P.levelName,

		R.type `TYPE`,
		R.status as `status`,
		R.created_at created_at,
		R.amount amount,
		R.date_from date_from,
		R.date_to date_to,
		DATEDIFF(R.date_to, R.date_from) days_total,
		DATEDIFF(R.date_to, NOW()) days_left,

        P.playerId tag, /* will be return of player_tagged_list(player_id as tag) */
		P.playerId playerId,

		R.player_id risk_score, /* will be return of generate_total_risk_score(player_id as risk_score) */
		R.player_id kyc_lvl, /* will be return of getPlayerCurrentRiskLevel(player_id as kyc_lvl) */
		IFNULL(R.updated_at, R.created_at) updated_at
	FROM `responsible_gaming` AS R
	LEFT OUTER JOIN `player` AS P ON P.playerId = R.player_id
) as responsible_gaming_reporting
EOF;
		$table = $this->data_tables->clearBlankSpaceAfterHeredoc($table);

        $where = array();
        $values = array();
        $group_by = array();
        $having = array();

        // JOIN /////
		$joins = [];

        // DEFINE TABLE COLUMNS /////
        $i = 0;
        $columns = [
            // Checkbox
            [
                'dt' => $i++, // #1
                'alias' => 'id',
                'select' => 'player_id',
                'formatter' => function ($d, $row) use($is_export)  {
                    if($is_export){
                        return $row['id'];
                    }else{
                        return '<input type="checkbox" id ="tag-user-id-'. $row['id'] .'"  class="clickTag" onclick="chkTag();"  value="' . $row['id'] . ' "/>';
                    }
                },
                'name' => lang('#'),
            ],
            // Checkbox
            [
                'dt' => $i++, // #2
                'alias' => 'request_id',
                'select' => 'request_id',
                'name' => lang('#'),
            ],
            // Player username
            [
                'dt' => $i++, // #3
                'alias' => 'player_username',
                'select' => 'username',
                'formatter' => function ($d, $row) use($is_export)  {
                    if($is_export){
                        return $row['player_username'];
                    }else{
                        return '<a href="/player_management/userInformation/'.$row['id'].'">'.$row['player_username'].'</a>';
                    }
                },
                'name' => lang('Username'),
            ],
            // Player level
            [
                'dt' => $i++, // #4
                'alias' => 'group_level',
                'select' => 'CONCAT(groupName, \'|\', levelName )',
				'formatter' => function ($d, $row) {
                    // $this->utils->debug_log('the data ---->', $d);
                    if(!empty($d)){
					    $d = (explode("|",$d));
                        return lang($d[0]).' - '.lang($d[1]);
                    }else{
                        return 'N/A';
                    }
				},
                'name' => lang('Player Level'),
            ],
            // Type
            [
            	'dt'		=> $i++ , // #5
                'alias'		=> 'type',
                'select'	=> 'type',
                'formatter'	=> function ($d, $row) { return $this->responsible_gaming->type_to_string($d); },
                'name'      => lang('Type of Exclusion')
            ],
            // Status
            [
            	'dt'		=> $i++ , // #6
            	'alias'		=> 'responsible_gaming_status' ,
            	'select'	=> 'status' ,
            	'formatter'	=> function ($d, $row) { return $this->responsible_gaming->status_to_string($d); },
                'name'      => lang('Status')
            ],
            // Created At
            [
                'dt'		=> $i++ , // #7
                'alias'		=> 'created_at' ,
                'select'	=> 'created_at' ,
                'formatter'	=> function ($d, $row) { return date('Y-m-d H:i', strtotime($d)); },
                'name'      => lang('Date of Request')
            ],
            // Requested Amount
            [
                'dt'		=> $i++ , // #8
                'alias'		=> 'amount' ,
                'select'	=> 'amount',
                'formatter'	=> function ($d, $row) {
                    $allow_type = [Responsible_gaming::DEPOSIT_LIMITS,Responsible_gaming::WAGERING_LIMITS];
                    if(in_array($row['type'],$allow_type)){
                        return $d;
                    }else{
                        return lang('N/A');
                    }

                },
                'name'      => lang('Requested Amount')
            ],
            // Start time
			[
            	'dt'		=> $i++ , // #9
            	'alias'		=> 'date_from' ,
            	'select'	=> 'date_from' ,
            	'formatter'	=> function ($d, $row) { return date('Y-m-d H:i', strtotime($d)); },
                'name'      => lang('Start Time')
            ],
            // End time
            [
            	'dt'		=> $i++ , // #10
            	'alias'		=> 'date_to' ,
            	'select'	=> 'date_to' ,
            	'formatter'	=> function ($d, $row) {
            		if ($row['type'] == Responsible_gaming::SELF_EXCLUSION_PERMANENT) {
            			return '—';
            		}
            		return date('Y-m-d H:i', strtotime($d));
            	},
                'name'      => lang('End Time')
            ],
            // Total days
            [
            	'dt'		=> $i++ , // #11
            	'alias'		=> 'days_total' ,
            	'select'	=> 'DATEDIFF(date_to, date_from)' ,
            	'formatter'	=> function ($d, $row) use($is_export){
            		if ($d < 0)
            			{ $d = 0; }

            		if ($row['type'] == Responsible_gaming::SELF_EXCLUSION_PERMANENT) {
                        return '—';
            		}
            		return sprintf('%d %s', $d, lang('day'));
            	},
                'name'      => lang('Total Days')
            ],
            // Days left
            [
            	'dt'		=> $i++ , // #12
            	'alias'		=> 'days_left' ,
            	'select'	=> 'DATEDIFF(date_to, NOW())' ,
            	'formatter'	=> function ($d, $row) use($is_export) {
            		if ($d > $row['days_total'])
            			{ $d = $row['days_total']; }
            		if ($d < 0)
            			{ $d =  0; }
            		if ($row['type'] == Responsible_gaming::SELF_EXCLUSION_PERMANENT) {
                        return '—';
            		}
            		return $d == 0 ? '—' : sprintf('%d %s', $d, lang('Day'));
            	},
                'name'      => lang('Days Left')
            ],
            // Tag
            [
                'dt' => $i++, // #13
                'alias' => 'tag',
                'select' => 'tag',
                'formatter' => function ($d, $row) use($is_export){
					if ( ! empty($row['tag']) ){
						if($is_export){
							return player_tagged_list($row['tag'], true);
						}else{
							return player_tagged_list($row['tag']);
						}
					}
                },
                'name' => lang('Tag'),
            ],
            // Risk Score
            [
                'dt' => $i++, // #14
                'alias' => 'risk_score',
                'select' => 'risk_score',
                'formatter' => function ($d, $row) {
					if ( ! empty($row['risk_score']) ){
						return $this->risk_score_model->generate_total_risk_score($row['risk_score']);
					}
                },
                'name' => lang('risk score'),
            ],
            // KYC Level
            [
                'dt' => $i++, // #15
                'alias' => 'kyc_lvl',
                'select' => 'kyc_lvl',
                'formatter' => function ($d, $row) {
					if ( ! empty($row['kyc_lvl']) ){
						return $this->risk_score_model->getPlayerCurrentRiskLevel($row['kyc_lvl']);
					}
                },
                'name' => lang('KYC Level'),
            ],
            // Updated At
            [
                'dt' => $i++, // #16
                'alias' => 'updated_at',
                'select' => 'IFNULL(updated_at, created_at)',
                'name' => lang('Updated at'),
            ],

        ];

        // FILTER /////

		$this->utils->debug_log('RG Report ----->', 'input', $input);

		/// Patch for OGP-14359 : [Responsible Gaming Report]Inconsistent number of rows in export csv.
		if( isset($input['username'])
			&& $input['username'] ==''
		){
			unset($input['username']);
		}

		if( isset($input['player_level'])
			&& $input['player_level'] ==''
		){
			unset($input['player_level']);
		}

		if( isset($input['tag_id'])
			&& $input['tag_id'] ==''
		){
			unset($input['tag_id']);
		}

		if( isset($input['days_left_min'])
			&& $input['days_left_min'] ==''
		){
			unset($input['days_left_min']);
		}

		if( isset($input['days_left_max'])
			&& $input['days_left_max'] ==''
		){
			unset($input['days_left_max']);
		}

        // Player username
        if (isset($input['username'], $input['search_by'])) {
        	switch ($input['search_by']) {
        		case 1 :
        			$where[] = "username LIKE ?";
        			$values[] = "%{$input['username']}%";
        			break;
        		case 2 :
        			$where[] = "username = ?";
        			$values[] = $input['username'];
        			break;
        		default :
        	}
		}

        // Start Time
        if (isset($input['search_start_time_date'], $input['start_at_from'], $input['start_at_to']) && $input['search_start_time_date']=='on') {
            $where[] = "date_from >= ?";
            $values[] = $input['start_at_from'];
            $where[] = "date_from <= ?";
            $values[] = $input['start_at_to'];
		}

        // Updated_at
        if (isset($input['search_reg_date'], $input['update_at_from'], $input['update_at_to']) && $input['search_reg_date']=='on') {
            $where[] = 'IF(updated_at IS NULL, created_at, updated_at) BETWEEN ? AND ? ';
            $values[] = $input['update_at_from'];
            $values[] = $input['update_at_to'];
        }



        // Player level
        if (isset($input['player_level'])) {
            $where[] = "levelId = ?";
            $values[] = $input['player_level'];
        }

        // Days_left max/min
        if (isset($input['days_left_min'])) {
        	$where[] = 'DATEDIFF(date_to, NOW()) >= ?';
        	$values[] = $input['days_left_min'];
        }

        if (isset($input['days_left_max'])) {
        	$where[] = 'DATEDIFF(date_to, NOW()) <= ?';
        	$values[] = $input['days_left_max'];
        }

        // Types
        if (isset($input['rg_type'])) {
			$wherein_rg_type = is_array($input['rg_type']) ? implode(',', $input['rg_type']) : $input['rg_type'];
        	$where[] = "type IN ( {$wherein_rg_type} )";
        } else { // Return nothing when no type is selected
        	$where[] = "type < 0";
        }

        // Statuses
        if (isset($input['rg_status'])) {
			$wherein_rg_status = is_array($input['rg_status']) ? implode(',', $input['rg_status']) : $input['rg_status'];
        	$where[] = "`status` IN ( {$wherein_rg_status} )";
        } else { // Return nothing when no status is selected
        	$where[] = "status < 0";
		}

		/// tag_id // Tag
		if( ! empty($input['tag_id']) ){
			$this->player_manager->getPlayerTagById($input['tag_id'], $query);
			$clause = [];
			// foreach ($rows as $indexNumber => $currRow){
			if ($query->num_rows() > 0) {
				foreach ($query->result_array() as $currRow){
					$currPlayerId = $currRow['playerId'];
					$currClause =<<<EOF
					player_id = "$currPlayerId"
EOF;
					$clause[] = $this->data_tables->clearBlankSpaceAfterHeredoc($currClause);
				}
			}

			if( ! empty($clause) ){
				$where[] = '( /* START as tag_id */'
						. implode('||', $clause)
						. ' /* EOF as tag_id */ )';
			}else{ // for no player has the tag.
				$where[] = '( /* START as tag_id */ player_id = 0 /* EOF as tag_id */ )';
			}
		}

        $having=[];
        $distinct=false;
        //$external_order = [['column' => 2, 'dir' => 'desc']];
        $external_order =[];
        $not_datatable='';
        $countOnlyField='request_id';

		/// for $this->data_tables->last_query to debug.
		// $this->config->set_item('debug_data_table_sql',true);

		$result = $this->data_tables->get_data(	$request // #1
												, $columns // #2
												, $table // #3
												, $where // #4
												, $values // #5
												, $joins // #6
												, $group_by // #7
												, $having // #8
												, $distinct // #9
												, $external_order // #10
												, $not_datatable // #11
												, $countOnlyField // #12
											);
		/// for debug
		// $sql = $this->data_tables->last_query;
		// $this->utils->debug_log('OGP-14359:$sql:', $sql);
		// $result['sql'] = $sql;

        return $result;

    } // EOF responsibleGamingReport

    /**
     * Data provider for api/bonus_games_report (api_report_module::bonus_games_report())
     *
     * @param	array 	$request	full post array sent by Datatable client.
     * @param	boolean	$is_export	Output mode, remove html and extra formatting if true.
     * @return	JSON	JSON array,custom format for Datatable.
     */
    public function bonus_games_report($request, $is_export = false) {
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->data_tables->is_export = $is_export;
		$this->load->model(['player_model']);

		$request['draw'] = '';
		$external_order = '';

		$input 		= $this->data_tables->extra_search($request);
		$this->utils->debug_log('bonus_games_report-input', $input);
		$table 		= 'promo_game_player_game_history AS H';
		$joins		= [
			'player AS P'				=> 'H.player_id = P.playerId' ,
			'promo_game_games AS G'		=> 'H.game_id = G.id' ,
			'promo_game_gametypes AS T'	=> 'G.gametype_id = T.id' ,
			'promorules AS PR'			=> 'H.promorule_id = PR.promorulesId' ,
			'promotype AS PT'			=> 'PR.promoCategory = PT.promotypeId'
		];
		$where		= [];
		$values		= [];
		$group_by	= [];
		$having		= [];

		$i = 0;
		$columns = [
			[  'alias' => 'promorule_id'	, 'select' => 'H.promorule_id'	] ,
			[  'alias' => 'player_id'		, 'select' => 'H.player_id'	] ,
			[  'alias' => 'game_id'			, 'select' => 'H.game_id'	] ,
			[ 'dt' => $i++ , 'alias' => 'date'			, 'select' => 'IF (H.realized_at IS NULL, H.updated_at, H.realized_at)' ] ,
			[ 'dt' => $i++ , 'alias' => 'player'		, 'select' => 'P.username'	,
				'formatter' => function ($d, $row) {
					return "<a href='/player_management/userInformation/{$row['player_id']}'>$d</a>";
			} ] ,
			[ 'dt' => $i++ , 'alias' => 'group_level'	, 'select' => 'P.username'	,
				'formatter' => function ($d, $row) {
					$player_group_level = $this->player_model->getPlayerCurrentLevel($row['player_id']);
		            if($player_group_level){
		            	$player_group = lang($player_group_level[0]['groupName']);
		            	$player_level = lang($player_group_level[0]['vipLevelName']);
		                return "{$player_group} - {$player_level}";
		            }
		            return '$mdash;';
			} ] ,
			[ 'dt' => $i++ , 'alias' => 'gametype'		, 'select' => 'T.gametype'	,
				'formatter' => function ($d, $row) {
					return lang($d);
			} ] ,
			[ 'dt' => $i++ , 'alias' => 'gamename'		, 'select' => 'G.gamename'	,
				'formatter' => function ($d, $row) {
					return "<a href='/marketing_management/bonusGameSettings/{$row['game_id']}'>$d</a>";
			} ] ,
			[ 'dt' => $i++ , 'alias' => 'promo_type'	, 'select' => 'PT.promoTypeName'	,
				'formatter' => function ($d, $row) {
					if (substr($d, 0, 5) == '_json') {
						$langs = json_decode(substr($d, 6), 'as_array');
						return $langs[2];
					}
					return $d;
			} ] ,
			[ 'dt' => $i++ , 'alias' => 'promo_rule'	, 'select' => 'PR.promoName'	,
				'formatter' => function ($d, $row) {
					return "<a href='/marketing_management/editPromoRule/{$row['promorule_id']}'>$d</a>";
			} ] ,
			[ 'dt' => $i++ , 'alias' => 'bonus_type_raw', 'select' => 'H.bonus_type'	] ,
			[ 'dt' => $i++ , 'alias' => 'bonus_type'	, 'select' => 'H.bonus_type'	,
				'formatter' => function ($d, $row) {
					return lang($d);
			} ] ,
			[ 'dt' => $i++ , 'alias' => 'bonus_amount'	, 'select' => 'H.bonus_amount'	,
				'formatter' => function ($d, $row) {
					return $row['bonus_type'] == 'nothing' ? '&mdash;' : $d;
			} ] ,
		];

		// Default clause: show only closed games
		$where[] = "H.status = ?";
		$values[] = 'closed';

		// Date from/Date to
		if (isset($input['enable_date'], $input['date_from'], $input['date_to'])) {
			$where[] = 'IF(H.realized_at IS NULL, H.updated_at, H.realized_at) >= ?';
			$values[] = $input['date_from'];
			$where[] = 'IF(H.realized_at IS NULL, H.updated_at, H.realized_at) <= ?';
			$values[] = $input['date_to'];
		}

		// Player username
		if (isset($input['player_username'])) {
			$match = isset($input['player_match']) ? $input['player_match'] : 'similar';

			switch ($match) {
				case 'exact' :
					$where[] = 'P.username = ?';
					$values[] = $input['player_username'];
					break;
				case 'similar' : default :
					$where[] = 'P.username LIKE ?';
					$values[] = "%{$input['player_username']}%";
					break;
			}
		}

		// Player Level
		// TODO: implement clause
		if (isset($input['player_level_id'])) {
			$where[] = 'P.levelId = ?';
			$values[] = $input['player_level_id'];
		}

		// Gametype
		if (isset($input['game_type'])) {
			$where[] = 'G.gametype_id = ?';
			$values[] = $input['game_type'];
		}

		// Promo type
		if (isset($input['promo_type'])) {
			$where[] = 'PR.promoCategory = ?';
			$values[] = $input['promo_type'];
		}

		// Promo rule
		if (isset($input['promo_rule'])) {
			$where[] = 'H.promorule_id = ?';
			$values[] = $input['promo_rule'];
		}

		// Bonus type
		if (isset($input['bonus_type'])) {
			$where[] = 'H.bonus_type = ?';
			$values[] = $input['bonus_type'];
		}

		// Min amount
		if (isset($input['amount_min'])) {
			$where[] = 'H.bonus_amount >= ?';
			$values[] = $input['amount_min'];
		}

		// Max amount
		if (isset($input['amount_max']) && $input['amount_max'] > 0) {
			$where[] = 'H.bonus_amount <= ?';
			$values[] = $input['amount_max'];
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, $external_order);

		// Generate summary fields (DISUSED)
		// For this only generates grandtotal of all rows, not by-page subtotals.

		// $summary_selects = "
		// 	SUM(IF(H.bonus_type = 'cash', bonus_amount, 0)) AS sum_bonus_cash ,
		// 	SUM(IF(H.bonus_type = 'vip_exp', bonus_amount, 0)) AS sum_bonus_vip_exp ,
		// 	SUM(IF(H.bonus_type = 'cash', 1, 0)) AS count_rounds_bonus ,
		// 	SUM(IF(H.bonus_type = 'vip_exp', 1, 0)) AS count_rounds_vip_exp ,
		// 	SUM(IF(H.bonus_type = 'nothing', 1, 0)) AS count_rounds_nothing ,
		// ";

		// $summary = $this->data_tables->summary($request, $table, $joins, $summary_selects, null, $columns, $where, $values);

		// $sumrow = $summary[0];
		// $sumrow['sum_bonus_cash'] = $this->utils->formatCurrencyNoSym($sumrow['sum_bonus_cash']);
		// $sumrow['sum_bonus_vip_exp'] = $this->utils->formatCurrencyNoSym($sumrow['sum_bonus_vip_exp']);
		// $sumrow['count_rounds_bonus'] = $this->utils->formatInt($sumrow['count_rounds_bonus']);
		// $sumrow['count_rounds_vip_exp'] = $this->utils->formatInt($sumrow['count_rounds_vip_exp']);
		// $sumrow['count_rounds_nothing'] = $this->utils->formatInt($sumrow['count_rounds_nothing']);

		// $result['summary'][0] = $sumrow;

		return $result;
	}

	/**
     * Data provider for api/player_analysis_report (api_report_module::player_analysis_report())
     *
     * @param	array 	$request	full post array sent by Datatable client.
     * @param	boolean	$is_export	Output mode, remove html and extra formatting if true.
     * @return	JSON	JSON array,custom format for Datatable.
     */
    public function player_analysis_report($request, $permissions, $is_export = false) {
    	$session_timeout =  $this->utils->getConfig('session_timeout');
		$dateTimeNow =  $this->utils->getNowDateTime();
		$dateTimeNow = $dateTimeNow->modify($session_timeout);
		$dateTimeNow = $dateTimeNow->getTimestamp();
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->helper(['player_helper']);

		$viewVerifiedStatus = false;
		// $this->load->library(array('permissions'));
		$this->load->model(array('player_model', 'agency_model', 'wallet_model', 'game_logs','risk_score_model','player_kyc','player_attached_proof_file_model','kyc_status_model','transactions','game_provider_auth'));

		$this->data_tables->is_export = $is_export;
		$wallet_model = $this->wallet_model;
		$risk_score_model = $this->risk_score_model;
		$player_attached_proof_file_model = $this->player_attached_proof_file_model;
		$player_kyc = $this->player_kyc;
		$kyc_status_model = $this->kyc_status_model;
		$game_provider_auth = $this->game_provider_auth;
		$game_logs = $this->game_logs;
		$model = $this;

		$getGameSystemMap = $this->utils->getGameSystemMap();
		# START DEFINE COLUMNS #################################################################################################################################################

		$input = $this->data_tables->extra_search($request);

		$i = 0;
		$columns = array();
			$columns[] =  array(
				'select' => 'player.playerId',
				'alias' => 'playerId',
				'name' => lang('lang.action'),
			);
			$columns[] =  array(
				'select' => 'player.verified_email',
				'alias' => 'verified_email',
				'name' => lang("player.06"),
			);
			$columns[] =  array(
				'select' => 'player.verified_phone',
				'alias' => 'verified_phone',
				'name' => lang("Phone / mobile number"),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'player.username',//'( CASE WHEN affiliates.username IS NULL THEN player.username ELSE CONCAT(player.username, \' (\', affiliates.username,  \')\' ) END )',
				'formatter' => ($is_export) ? 'defaultFormatter' : function ($d, $row) {
					return '<a href="/player_management/userInformation/' . $row['playerId'] . '">' . $d . '</a>';
				},
				'name' => lang('player.01'),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'real_name',
				'select' => 'CONCAT(ifnull(playerdetails.firstName,""), \' \', ifnull(playerdetails.lastName,"") )',
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang("sys.vu19"),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'birthdate',
				'select' => 'playerdetails.birthdate',
				'name' => lang("date.of.birth"),
				'formatter' => function ($d, $row) use ($is_export) {
					if($is_export){
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					}else{
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'email',
				'select' => 'player.email',
				'name' => lang("player.06"),
				'formatter' => function ($d, $row) use ($permissions, $viewVerifiedStatus, $is_export) {
					$str = $d;
					if ($permissions['view_player_detail_contactinfo_em'] ) {

						if ($is_export) {
							$str = $row['verified_email'] == self::DB_TRUE ? $str . '  ' . lang('Verified') : $str . '  ' . lang('Not verified');
						} else {
							if ($viewVerifiedStatus) {
								if ($row['verified_email'] == self::DB_TRUE) {
									$str = '<span class="text-success"><i class="fa fa-envelope"></i> ' . $str . '</a></span>';
								} else {
									$str = '<span class="text-danger"><i class="fa fa-envelope"></i> ' . $str . '</a></span>';
								}
							} else {
								$str = '<span class="text-default"><i class="fa fa-envelope"></i> ' . $str . '</a></span>';
							}
						}

					} else {

						$str = $this->utils->maskMiddleString($str,0,strlen($str)-4, 4);
						if (!$is_export) {
							$str = '<span title="' . lang('con.aff01') . '">' . $str . '</span>';
						}
					}

					return $str;
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'contactNumber',
				'select' => 'playerdetails.contactNumber',
				'name' => lang("Phone / mobile number"),
				'formatter' => function ($d, $row) use ($permissions, $viewVerifiedStatus, $is_export) {

					$str = $d;
					if ($permissions['view_player_detail_contactinfo_cn'] ) {
                        if ($is_export) {
							$str = $row['verified_phone'] == self::DB_TRUE ? $str . '   ' . lang('Verified') : $str . '  ' . lang('Not verified');
						} else {
							if ($viewVerifiedStatus) {
								if ($row['verified_phone'] == self::DB_TRUE) {
									$str = '<span class="text-success"><i class="fa fa-phone"></i> ' . $str . '</a></span>';
								} else {
									$str = '<span class="text-danger"><i class="fa fa-phone"></i> ' . $str . '</a></span>';
								}
							} else {
								$str = '<span class="text-default"><i class="fa fa-phone"></i> ' . $str . '</a></span>';
							}
						}

					} else {
						$str = $this->utils->maskMiddleString($str,0,strlen($str)-4, 4);
						if (!$is_export) {
							if ($row['verified_phone'] == self::DB_TRUE) {
								$str = '<span class="text-success"><i class="fa fa-phone"></i><span title="' . lang('con.aff01') . '">' . $str . '</span>';
							} else {
								$str = '<span class="text-danger"><i class="fa fa-phone"></i><span title="' . lang('con.aff01') . '">' . $str . '</span>';
							}
						}

					}
					return $str;
				},

			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'group_level',
				'select' => 'CONCAT(player.groupName, \'|\',player.levelName )',
				'formatter' => function ($d) use ($is_export) {
					if(!empty($d)){
						$d = (explode("|",$d));
					}

					if ($is_export) {
						return !empty($d) ? lang($d[0]).' - '.lang($d[1]) : lang('lang.norecyet');
					} else {
						return !empty($d) ? lang($d[0]).' - '.lang($d[1]) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang("player.07"),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'tagName',
				'select' => 'player.playerId',
				'name' => lang("player.41"),
				'formatter' => function ($d, $row) use ($is_export) {
					return player_tagged_list($row['playerId'], $is_export);
				},
			);
			if ($this->utils->isEnabledFeature('ignore_player_analysis_permissions') || $permissions['reset_player_login_password'] ) {
	        	$columns[] = array(
	                'dt' => $i++,
	                'alias' => 'password',
	                'select' => 'player.password',
	                'name' => lang("Password"),
	            );
	        } if ($this->utils->isEnabledFeature('ignore_player_analysis_permissions') || $permissions['player_verification_question'] ) {
	        	 $columns[] = array(
	                'dt' => $i++,
	                'alias' => 'secretQuestion',
	                'select' => 'player.secretQuestion',
	                'name' => lang("Verification Question"),
	                'formatter' => function($d) use ($permissions){
	                	return lang($d);
	                }
	            );
	        } if ($this->utils->isEnabledFeature('ignore_player_analysis_permissions') || $permissions['player_verification_questions_answer'] ) {
            $columns[] = array(
                'dt' => $i++,
                'alias' => 'secretAnswer',
                'select' => 'player.secretAnswer',
                'name' => lang("Verification Answer"),
                );
        	}
			$columns[] = array(
				'dt' => $i++,
				'alias' => '',
				'select' => '',
				'name' => lang("Linked Accounts"),
				'formatter' => function ($d, $row) use ($is_export) {
					$this->load->model('linked_account_model');
					$linkedAccounts = $this->linked_account_model->getLinkedAccount(array("username"=>$row['username'],
																   "search_type"=>Linked_account_model::SEARCH_TYPE_EXACT_USERNAME,
																   "link_datetime"=>null));

					if($is_export || (!$this->utils->isEnabledFeature('ignore_player_analysis_permissions') && !$this->permissions->checkPermissions('linked_account')) || !$this->utils->isEnabledFeature('linked_account') || empty($linkedAccounts) || empty($linkedAccounts[0]['linked_accounts'])) return lang('lang.norecyet');

					$output = '<a onclick="modal(\'/player_management/showLinkedAccountsModal/' . $row['username'] . '\',\'' . lang('Linked Accounts') . '\')" href="javascript:void(0);" data-toggle="tooltip" title="'.lang('Linked Accounts').'"><i class="fa fa-search"></i></a>';

  					$output .= 'Y';

                    return $output;


				},

			);
			$columns[] = array(
                'dt' => $i++,
                'alias' => 'blocked',
                'select' => 'player.blocked' ,
                'name' => lang("lang.status"),
                'formatter' => function ($d, $row) use ($is_export){
                    $formatter = 1;
                    return  $this->utils->getPlayerStatus($row['playerId'],$formatter,$d,$is_export);
                },
            );
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'risk_level',
				'select' => 'playerdetails.playerId',
				'formatter' => function ($d) use ($is_export,$risk_score_model) {
					if ($is_export) {
						return $risk_score_model->getPlayerCurrentRiskLevel($d) && $risk_score_model->generate_total_risk_score($d) ? $risk_score_model->getPlayerCurrentRiskLevel($d) .' / '. $risk_score_model->generate_total_risk_score($d) : lang('lang.norecyet');
					}else{
						return $risk_score_model->getPlayerCurrentRiskLevel($d) && $risk_score_model->generate_total_risk_score($d) ? $risk_score_model->getPlayerCurrentRiskLevel($d) .' / '. $risk_score_model->generate_total_risk_score($d) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang("Risk Level/Score"),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'kyc_level',
				'select' => 'playerdetails.playerId',
				'formatter' => function ($d) use ($is_export,$player_kyc,$kyc_status_model) {
					if ($is_export) {
						return $player_kyc->getPlayerCurrentKycLevel($d) && $kyc_status_model->getPlayerCurrentStatus($d) ? $player_kyc->getPlayerCurrentKycLevel($d) .' / '. $kyc_status_model->getPlayerCurrentStatus($d): lang('lang.norecyet');
					}else{
						return $player_kyc->getPlayerCurrentKycLevel($d) && $kyc_status_model->getPlayerCurrentStatus($d) ? $player_kyc->getPlayerCurrentKycLevel($d) .' / '. $kyc_status_model->getPlayerCurrentStatus($d): '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang("KYC Level / Rate Code"),
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'ip_address',
				'select' => 'player_ip_last_request.ip',
				'name' => lang('player.10'),
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						$output = '<a onclick="modal(\'/player_management/showIPHistoryModal/' . $row['playerId'] . '\',\'' . lang('player.ui75') . '\')" href="javascript:void(0);" data-toggle="tooltip" title="'.lang('player.ui75').'"><i class="fa fa-search"></i></a>';

	  					$output .= 'Y';

	                    return $output;
					}
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_deposit_amount',
				'select' => 'player.totalDepositAmount',
				'name' => lang('report.in03'),
				'formatter' => 'currencyFormatter',
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_withdrawal_amount',
				'select' => 'player.approvedWithdrawAmount',
				'name' => lang('report.sum10'),
				'formatter' => function ($d, $row) use ($readOnlyDB, $is_export) {
					$query = $readOnlyDB->query("SELECT sum(transactions.amount) as total_withdrawal FROM transactions WHERE transactions.to_id = ? AND transactions.to_type = ? AND transactions.transaction_type = ? AND transactions.status = ?  ", array(
						$row['playerId'],
						Transactions::PLAYER,
						Transactions::WITHDRAWAL,
						Transactions::APPROVED,
					));
					$r = $query->row_array();

					return $this->data_tables->currencyFormatter(isset($r['total_withdrawal']) ? $r['total_withdrawal'] : 0);
				},
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_betting_amount',
				'select' => 'player.playerId',
				'name' => lang('player.ui31'),
				'formatter' => function ($d, $row) use ($is_export,$game_provider_auth,$game_logs,$getGameSystemMap,$input) {
					$total_bet = 0;
					if(isset($input['game_list_search'])){
						$game_platforms = $this->game_provider_auth->getGamePlatforms($d);
						$game_logs = $this->game_logs->getSummary($d);
						$game_list_search = $input['game_list_search'];

						foreach ($getGameSystemMap as $gameKey => $value) {
							foreach ($game_platforms as $game_platform) {
								if($game_platform['id'] == $gameKey){
									$game_platform_id = $game_platform['id'];
                                    if(in_array($game_platform_id, $game_list_search)){
                                        if (isset($game_logs[$game_platform_id])) {
                                            $total_bet += $game_logs[$game_platform_id]['bet']['sum'];
                                        }
                                    }
								}
							}
						}
					}
					return $this->data_tables->currencyFormatter(isset($total_bet) ? $total_bet : 0);
					}
			);
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'total_main_balance',
				'select' => 'player.playerId',
				'name' => lang("cashier.05"),
				'formatter' => function ($d, $row) use ($is_export,$wallet_model) {
					$totalBalance = 0;
					$balanceDetails = $this->wallet_model->getBalanceDetails($d);
					if(!empty($balanceDetails)){
						if(isset($balanceDetails['total_balance'])){
							$totalBalance = $balanceDetails['total_balance'];
						}
					}
					return $this->data_tables->currencyFormatter($totalBalance);
					}
			);

			foreach ($getGameSystemMap as $gameKey => $value) {
				$columns[] = array(
					'dt' => $i++,
					'alias' => 'total_total_nofrozen',
					'select' => 'player.playerId',
					'name' => lang("Overall Winloss ").$value,
					'formatter' => function ($d, $row) use ($is_export,$game_provider_auth,$game_logs,$gameKey,$input) {
						$gain_loss_sum = 0;
						if(isset($input['game_list_search'])){
							$game_platforms = $this->game_provider_auth->getGamePlatforms($d);
							$game_logs = $this->game_logs->getSummary($d);
							$game_list_search = is_array($input['game_list_search']) ? $input['game_list_search'] : array($input['game_list_search']);

							foreach ($game_platforms as &$game_platform) {
								if($game_platform['id'] == $gameKey){
									$game_platform_id = $game_platform['id'];
                                    if (in_array($game_platform_id, $game_list_search)) {
                                        if (isset($game_logs[$game_platform_id])) {
                                            $game_platform = array_merge($game_platform, $game_logs[$game_platform_id]);
                                            $gain_loss_sum = isset($game_logs[$game_platform_id]['gain_loss']['sum']) ? $game_logs[$game_platform_id]['gain_loss']['sum'] : 0;
                                        }
                                    }
								}
							}
						}
						return $this->data_tables->currencyFormatter($gain_loss_sum);
						}
				);

			}


		# END DEFINE COLUMNS #################################################################################################################################################
		$this->benchmark->mark('data_gathering_start');
		$table = 'player';
		$joins = array(
			'playerdetails' => 'playerdetails.playerId = player.playerId',
			'player_runtime' => 'player_runtime.playerId = player.playerId',
			'playertag' => 'playertag.playerId = player.playerId',
			'affiliates' => 'affiliates.affiliateId = player.affiliateId',
			'player_ip_last_request' => 'player_ip_last_request.player_id = player.playerId',
			'player_device_last_request' => 'player_device_last_request.player_id = player.playerId',
		);
		$group_by = []; // array('player.playerId');

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$usernames = null;
		if (isset($input['username'])) {
			$usernames = $input['username'];

			if(!empty($usernames)){
				if(is_array($usernames)){
					foreach ($usernames as $key => $value) {
						$usernames[$key] = $this->player_model->getUsernameById($value);
					}
					$where[] = "player.username IN ('". implode("','" , $usernames ) ."')";
				} else {
					$username = $this->player_model->getUsernameById($usernames);
					if(!empty($username)){
						$where[] = "player.username = ?";
						$values[] = $username;
					}
				}
			}
		}

		$where[] = "player.deleted_at IS NULL";

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
		$countOnlyField='player.playerId';
		$this->benchmark->mark('data_gathering_end');

		$this->benchmark->mark('playerList_result_start');

		$columns = $this->checkIfEnabled($this->utils->isEnabledFeature('show_risk_score'), array('risk_level'), $columns);
		$columns = $this->checkIfEnabled($this->utils->isEnabledFeature('show_kyc_status'), array('kyc_level'), $columns);
		$columns = $this->checkIfEnabled(!$this->utils->isEnabledFeature('close_aff_and_agent'), array('affiliate','agent'), $columns);
		if(!empty($values) || is_array($usernames)){
			$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins,
						$group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField);
		} else {
			$result = $this->data_tables->get_data($request, $columns, $table);
		}

		if($is_export){
		    //drop result if export
			return $csv_filename;
		}
		$this->benchmark->mark('playerList_result_end');

		$this->utils->debug_log(array(
			'player_analysis_report data gathering================================' => $this->benchmark->elapsed_time('data_gathering_start', 'data_gathering_end'),
		 	'player_analysis_report result=============================' => $this->benchmark->elapsed_time('playerList_result_start', 'playerList_result_end'),
		 	'player_analysis_report Summary============================' => $this->benchmark->elapsed_time('playerList_summary_start', 'playerList_summary_end'),
		));
		return $result;
    }

    public function getUserLogs( $request, $is_export = false ){

		# START DEFINE COLUMNS #################################################################################################################################################
    	$i = 0;
    	$columns = array(
    		array(
    			'dt' => $i++,
    			'alias' => 'logDate',
    			'select' => 'logs.logDate',
    			'name' => lang('report.log05'),
    			'formatter' => function($d, $row) use ($is_export){

    				if ($is_export) {
    					return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
    				} else {
    					return (!$d || strtotime($d) < 0) ? '<i>' . lang('lang.norecyet') . '</i>' : date('Y-m-d H:i:s', strtotime($d));
    				}

    			},
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'username',
    			'select' => 'logs.username',
    			'name' => lang('report.log02'),
    			'formatter' => 'languageFormatter'
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'userRole',
    			'select' => 'logs.userRole',
    			'name' => lang('report.log04'),
    			'formatter' => 'languageFormatter'
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'management',
    			'select' => 'logs.management',
    			'name' => lang('report.log03'),
    			'formatter' => 'languageFormatter'
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'action',
    			'select' => 'logs.action',
    			'name' => lang('lang.action'),
    			'formatter' => 'defaultFormatter'
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'referrer',
    			'select' => 'logs.referrer',
    			'name' => 'Referrer',
    			'formatter' => function ($d) use ($is_export) {
    				if( ! $is_export ){
    					return $d ? '<a href="' . $d . '">' . $d . '</a>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
    				} else{

    					return ( $d ) ? $d : lang('lang.norecyet');
    				}

    			},
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'uri',
    			'select' => 'logs.uri',
    			'name' => lang('link'),
    			'formatter' => function ($d) use ($is_export) {
    				if( ! $is_export ){
    					return $d ? '<a href="' . $d . '">' . $d . '</a>' : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
    				} else{

    					return ( $d ) ? $d : lang('lang.norecyet');
    				}

    			},
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'data',
    			'select' => 'logs.data',
    			'name' => lang('lang.details'),
    			'formatter' => function ($d) use ($is_export) {
    				$data = str_replace('\n', "\n", strip_tags($d));
    				if( ! $is_export ){
    					if ($data) {
    						return "<pre style='margin-bottom: 0;'>stripslashes($data)</pre>";
    					} else {
    						return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
    					}
    				}else{
    					return ($data) ? stripslashes($data) : lang('lang.norecyet');
    				}

    			},
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'ip',
    			'select' => 'logs.ip',
    			'name' => lang('sys.ip08'),
    			'formatter' => function($d, $row) use ($is_export){

    				if ($is_export) {
    					return trim(trim(strip_tags($d)), ',') ?: lang('lang.norecyet');
    				} else {
    					return trim(trim($d), ',') ?: '<i>' . lang('lang.norecyet') . '</i>';
    				}

    			},
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'status',
    			'select' => 'logs.status',
    			'name' => lang('lang.status'),
    			'formatter' => function ($d) use ($is_export) {
    				if( ! $is_export ){
    					return $d ? '<i class="glyphicon glyphicon-remove text-danger"></i>' : '<i class="glyphicon glyphicon-ok text-success"></i>';
    				}else{
    					return ( $d ) ? '✗' : '✓';
    				}

    			},
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'extra',
    			'select' => 'logs.extra',
    			'name' => lang('Data'),
    			'formatter' => function ($d) use ($is_export) {

    				$data = str_replace('\n', "\n", strip_tags($d));

    				if( ! $is_export ){
    					if ($data) {
    						return "<pre style='margin-bottom: 0;'>".stripslashes($data)."</pre>";
    					} else {
    						return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
    					}
    				}else{
    					return ($data) ? stripslashes($data) : lang('lang.norecyet');
    				}
    			},
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'logId',
    			'select' => 'logs.logId',
    			'name' => lang('File'),
    			'formatter' => function ($d, $row) use ($is_export){
    				if($is_export){
    					return ( $d ) ? strip_tags($d) : lang('lang.norecyet');
    				}else{
    					if(!empty($row['extra'])) {
    						return empty($d) ? ''
    						: '<a href="'.site_url('/user_management/download_user_logs/'.$d).'" target="_blank" data-placement="left"
    						data-toggle="tooltip" title="Download data"><i class="fa fa-download"></i></a>';
    					}
    				}
    			}
    		),
    		array(
    			'dt' => $i++,
    			'alias' => 'description',
    			'select' => 'logs.description',
    			'name' => lang('player.tm04'),
    		),
    	);
		# END DEFINE COLUMNS #################################################################################################################################################

	$table = 'logs';
	$joins = array();

			# START PROCESS SEARCH FORM #################################################################################################################################################
	$where = array();
	$values = array();

	$this->load->library('data_tables');
	$input = $this->data_tables->extra_search($request);

	if (isset($input['log_date_from'], $input['log_date_to'])) {
		$where[] = "logs.logDate BETWEEN ? AND ?";
		$values[] = $input['log_date_from'];
		$values[] = $input['log_date_to'];
	}

	if (isset($input['username'])) {
		$where[] = "logs.username LIKE ?";
		$values[] = '%' . $input['username'] . '%';
	}

	if (isset($input['userRole'])) {
		$where[] = "logs.userRole = ?";
		$values[] = $input['userRole'];
	}

	if (isset($input['management'])) {
		$where[] = "logs.management = ?";
		$values[] = $input['management'];
	}

	if (isset($input['action'])) {
		$where[] = "logs.action = ?";
		$values[] = $input['action'];
	}

	if (isset($input['extra'])) {
		if($input['extra'] == self::FILTER_EMPTY_DATA) {
			$where[] = "logs.extra != ?";
			$values[] = '';
		} elseif($input['extra'] == self::ONLY_EMPTY_DATA) {
			$where[] = "logs.extra IS NULL";
		}
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

	$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

	if($is_export){
			    //drop result if export
		return $csv_filename;
	}


	return $result;

	}

	/**
	 * detail: get Player Risk Score History Logs
	 *
	 * @param string player_id
	 * @param datetime create_date
	 * @return json
	 */
	public function risk_score_history_logs($request, $player_id = '', $is_export = false ) {

        $this->load->library(['data_tables']);
		$i = 0;

		$input = $this->data_tables->extra_search($request);

		$columns = array(
			array(
				'alias' => 'id',
				'select' => 'risk_score_history_logs.id',
				'formatter' => function($d, $row) use ($is_export){
						return $d;
				},
			),
			array(
				'alias' => 'category_description',
				'select' => 'risk_score.category_description',
				'formatter' => function($d, $row) use ($is_export){
						return $d;
				},
			),
			array(
				'alias' => 'result_changes_to',
				'select' => 'risk_score_history_logs.result_change_to'
			),
			array(
				'alias' => 'change_of_score_to',
				'select' => 'risk_score_history_logs.score_to',
			),
			array(
				'alias' => 'change_of_total_score_to',
				'select' => 'risk_score_history_logs.total_score_to',
			),
			array(
				'dt' => $i++,
				'alias' => 'created_at',
				'select' => 'risk_score_history_logs.created_at',
				'formatter' => function($d, $row) use ($is_export){
						return ($d) ? : lang('N/A');
				},
				'name' => lang('Date'),
			),
			array(
				'dt' => $i++,
				'alias' => 'risk_score_category',
				'select' => 'risk_score_history_logs.risk_score_category',
				'formatter' => function($d, $row) use ($is_export){
						return ($d) ? $d.' / '.$row['category_description'] : lang('N/A');
				},
				'name' => lang('Category / Description'),
			),
			array(
				'dt' => $i++,
				'alias' => 'result_changes_from',
				'select' => 'risk_score_history_logs.result_change_from',
				'formatter' => function($d, $row) use ($is_export){
						return sprintf(lang('risk_score_result_changes'), $d , $row['result_changes_to']);
				},
				'name' => lang('Result Changes'),
			),
			array(
				'dt' => $i++,
				'alias' => 'change_of_score_from',
				'select' => 'risk_score_history_logs.score_from',
				'formatter' => function($d, $row) use ($is_export){
						return sprintf(lang('risk_score_change_score'), $d , $row['change_of_score_to']);
				},
				'name' => lang('Change of Score'),
			),
			array(
				'dt' => $i++,
				'alias' => 'change_of_total_score_from',
				'select' => 'risk_score_history_logs.total_score_from',
				'formatter' => function($d, $row) use ($is_export){
						return sprintf(lang('risk_score_change_score'), $d , $row['change_of_total_score_to']);
				},
				'name' => lang('Change of Total Score'),
			),
			array(
				'dt' => $i++,
				'alias' => 'risk_score_level',
				'select' => 'risk_score_history_logs.risk_score_level_to',
				'formatter' => function($d, $row) use ($is_export){
						return ($d) ? : lang('N/A');
				},
				'name' => lang('Risk Score Level'),
			)
		);



		$table = 'risk_score_history_logs';

		$joins = array();
		$joins['risk_score'] = 'risk_score.category_name = risk_score_history_logs.risk_score_category';

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$group_by=[];
        $having=[];

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "risk_score_history_logs.created_at >= ?";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "risk_score_history_logs.created_at <= ?";
			$values[] = $input['dateRangeValueEnd'];
		}

		if (!empty($player_id)) {
			$where[] = "risk_score_history_logs.player_id = ?";
			$values[] = $player_id;
		}


		# END PROCESS SEARCH FORM #################################################################################################################################################

        $distinct=true;
        $external_order= 'risk_score_history_logs.id DESC';
        $not_datatable='';
		$countOnlyField='risk_score_history_logs.id';


		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having,	 $distinct, $external_order, $not_datatable, $countOnlyField);


		return $result;
	}
}

////END OF FILE

