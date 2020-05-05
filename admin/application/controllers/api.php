<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/APIBaseController.php';
require_once dirname(__FILE__) . '/payment_management.php';

require_once dirname(__FILE__) . '/modules/api_report_module.php';
require_once dirname(__FILE__) . '/modules/api_tree_module.php';
require_once dirname(__FILE__) . '/modules/api_agency_module.php';
require_once dirname(__FILE__) . '/modules/api_t1t_games_module.php';

require_once dirname(__FILE__) . '/modules/api_shopping_center_module.php';
require_once dirname(__FILE__) . '/modules/player_profile.php';
require_once dirname(__FILE__) . '/kyc_status.php';
require_once dirname(__FILE__) . '/modules/transfer_api_module.php';
/**
 * General behaviors include
 * * get all players
 * * get the (transaction history, deposit list, withdraw lists, game history, unsettle game history,ip history, duplicate accounts,
 *   duplicate account details, withdrawal bank list, main wallet balance, deposit limit, transfer request, balance history, promo history,
 *   adjustment history, cashback history, all withdrawals, all deposits, all transfer history, bank details, active promo details,
 *   eligible promo, available promo) of a certain player
 * * block or unblock game platform logs
 * * get adjustment history
 * * get friend referrals
 * * get first/second deposit
 * * get daily cashback
 * * get wallet balance of a certain affiliate
 * * get game description lists
 * * get promo history of a certain player
 * * get adjustment history of a certain player
 * * get cashback history of a certain player
 * * get all withdrawals of a certain player
 * * get all deposits of a certain player
 * * get all transfer history of a certain player
 * * get message history
 *
 * @category api
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Api extends APIBaseController {

	use api_report_module;
	use api_tree_module;
	use api_agency_module;
	use api_shopping_center_module;
	use player_profile;
	use kyc_status;
	// use api_t1t_games_module;
    use transfer_api_module;

	public function __construct() {
		parent::__construct();

		// $this->utils->debug_log('host from admin ',$this->utils->isFromHost('admin'), 'logged', $this->authentication->isLoggedIn());

		if($this->utils->isAdminSubProject()){
        // if($this->utils->isFromHost('admin') && $this->utils->isFromHost('l8b8mlgb8')) {
            if( !$this->authentication->isLoggedIn()){
           		show_error('No permissions', 403);
                exit;
            }
        }else if($this->utils->isAgencySubProject()) {
           if( !$this->isLoggedAgency()){
           		show_error('No permissions', 403);
                exit;
            }
        }else if($this->utils->isAffSubProject()) {
        	if(empty($this->session->userdata('affiliateId'))){
           		show_error('No permissions', 403);
        		exit;
        	}
        }

	}

	/**
	 * detail: get all players
	 *
	 * @return json
	 */
	public function playerList() {
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		//get post data
		$request = $this->input->post();

		$this->load->model(array('report_model'));

		$permissions = $this->getContactPermissions();
		$is_export = false;
		$result = $this->report_model->player_list_reports($request, $permissions, $is_export);

		/// OGP-14711 : Modify new layout of the "Player List": SBE_Player > All Player
		// 當月，則今天 11/6 ，只會計算 11/1~11/6 的會員喔。
		// In the current month, today 11/6, only 11/1~11/6 members will be counted.
		$currYearMonth = $this->utils->formatYearMonthForMysql(new DateTime());
		$whereData = 'EXTRACT(YEAR_MONTH FROM createdOn) = "'. $currYearMonth. '"';
		$result['total_registered_players'] = $this->player_model->totalRegisteredPlayers($whereData);
		$result['total_registered_players_today'] = $this->player_model->totalRegisteredPlayersByDate(date("Y-m-d"));

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get transaction history for a certain player
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function transactionHistory($player_id = null) {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->transaction_details($player_id, $request, $is_export);

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get deposit list of a certain player
	 *
	 * @param int $playerId
	 * @return json
	 */

	public function depositList($playerId = null, $allow_multiple_select = false) {
		if (!$this->isLoggedAdminUser()) {
			return;
		}

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$is_locked = false;
		$result = $this->report_model->depositList($playerId, $request, $is_export, $is_locked, '', '', '', null, $allow_multiple_select);

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get deposit list of a certain player
	 *
	 * @param int $playerId
	 * @return json
	 */

	public function withdrawList($playerId = null, $enabledAction = 'true') {
		if (!$this->isLoggedAdminUser()) {
			return;
		}

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$is_locked = false;
		$result = $this->report_model->withdrawList($playerId, $enabledAction, $request, $is_export);

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get game history of a certain player
	 *
	 * @param int $player_id player playerId
	 * @return json
	 */
	public function gamesHistory($player_id = null, $not_datatable = '') {

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;

		$result = $this->report_model->gamesHistory($request, $player_id, $is_export, $not_datatable);
		// $this->output->set_content_type('application/json')->set_output(json_encode($result));
		$this->returnJsonResult($result);
	}

	/**
	 * detail: get game history of a certain player
	 *
	 * @param int $player_id player playerId
	 * @return json
	 */
	public function gamesHistoryV2($player_id = null, $not_datatable = '') {

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;

		$result = $this->report_model->gamesHistoryV2($request, $player_id, $is_export, $not_datatable);
		$this->returnJsonResult($result);
	}

	/**
	 * detail: get unsettle game history of a certain player
	 *
	 * @param int $player_id player playerId
	 * @return json
	 */
	public function unsettlegamesHistory($player_id = null) {

		$is_export=false;

		$this->load->model('game_logs');

        $show_bet_detail_on_game_logs = $this->utils->isEnabledFeature('show_bet_detail_on_game_logs');
		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'alias' => 'id',
				'select' => 'game_logs.id',
			),
			array(
				'alias' => 'game_type',
				'select' => 'game_type.game_type',
			),
			array(
				'alias' => 'game_code',
				'select' => 'game_description.game_code',
			),
			array(
				'alias' => 'playerId',
				'select' => 'player.playerId',
			),
			array(
				'alias' => 'player_levelName',
				'select' => 'player.levelName'
			),
			array(
				'alias' => 'player_groupName',
				'select' => 'player.groupName'
			),
			array(
				'dt' => $i++,
				'alias' => 'end_at',
				'select' => 'game_logs.end_at',
                'name' => lang('player.ug01'),
				'formatter' => 'dateTimeFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'player_username',
				'select' => 'player.username',
				'formatter' => function ($d, $row) {
					return sprintf('<a target="_blank" href="/player_management/userInformation/%s">%s</a>', $row['playerId'], $d);
				},
			),
            array(
                'dt' => $this->utils->isEnabledFeature('aff_show_real_name_on_reports') ? $i++ : NULL,
                'alias' => 'realname',
                'select' => "CONCAT_WS(' ', playerdetails.firstName,playerdetails.lastName)",
                'formatter' => function ($d, $row) {
                    return trim($d);
                },
                'name' => lang('Real Name'),
            ),
			array(
				'dt' => $i++,
				'alias' => 'affiliate_username',
				'select' => 'affiliates.username',
				'name' => lang('Affiliate Username'),
				'formatter' => function ($d, $row) {
					if ($row['affiliate_username'] != '') {
						return sprintf('%s', $row['affiliate_username'], $d);
					} else {
						return 'NA';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'player_level',
				'select' => 'player.levelId',
				'formatter' => function($d, $row){
					if($d != 'N/A') {
						return lang($row['player_groupName']).' - '.lang($row['player_levelName']);
					}
		  			return $d;

				},
				'name' => lang('Player Level'),

			),
			array(
				'dt' => $i++,
				'alias' => 'game',
				'select' => 'external_system.system_code',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'game_type_lang',
				'select' => 'game_type.game_type_lang',
				'formatter' => function ($d, $row) {
					if ($row['flag'] == Game_logs::FLAG_TRANSACTION) {
						return lang('pay.transactions');
					}

					return $this->data_tables->languageFormatter($d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'game_name',
				'select' => 'game_description.game_name',
				'formatter' => function ($d, $row) {
					if ($row['flag'] == Game_logs::FLAG_TRANSACTION) {
						return lang('pay.transactions');
					}

					return $this->data_tables->languageFormatter($d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'real_bet_amount',
				'select' => 'game_logs.trans_amount',
				'formatter' => function ($d, $row) {
					return $this->utils->formatCurrencyNoSym($d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'bet_amount',
				'select' => 'game_logs.bet_amount',
				'formatter' => function ($d, $row) {
					return $this->utils->formatCurrencyNoSym($d);
				},
			),
            array(
                'dt' => $i++,
                'alias' => 'result_amount',
                'select' => 'game_logs.result_amount',
                'name' => lang('mark.resultAmount'),
                'formatter' => function ($d, $row) use ($is_export) {
                    if ($row['flag'] == Game_logs::FLAG_TRANSACTION) {
                        return lang('N/A');
                    } else {
                        if(!$is_export){
                            if($d <= 0){
                                //lose->green
                                return sprintf('<span style="font-weight:bold;" class ="text-success">%s</span>',$this->utils->formatCurrencyNoSym($d));
                            }else{
                                // win->red
                                return sprintf('<span style="font-weight:bold;" class ="text-danger">%s</span>',$this->utils->formatCurrencyNoSym($d));
                            }
                        }else{
                            return $this->utils->formatCurrencyNoSym($d);
                        }
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'bet_plus_result_amount',
                'select' => 'game_logs.bet_amount + game_logs.result_amount',
                'name' => lang('lang.bet.plus.result'),
                'formatter' => function ($d, $row) {
                    if ($row['flag'] == Game_logs::FLAG_TRANSACTION) {
                        return lang('N/A');
                    } else {
                        return $this->utils->formatCurrencyNoSym($d);
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'win_amount',
                'select' => 'game_logs.win_amount',
                'name' => lang('Win Amount'),
                'formatter' => function ($d, $row) {
                    if ($row['flag'] == Game_logs::FLAG_TRANSACTION) {
                        return lang('N/A');
                    } else {
                        return $this->utils->formatCurrencyNoSym($d);
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'loss_amount',
                'select' => 'game_logs.loss_amount',
                'name' => lang('Loss Amount'),
                'formatter' => function ($d, $row) {
                    if ($row['flag'] == Game_logs::FLAG_TRANSACTION) {
                        return lang('N/A');
                    } else {
                        return $this->utils->formatCurrencyNoSym($d);
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'after_balance',
                'select' => 'game_logs.after_balance',
                'name' => lang('mark.afterBalance'),
                'formatter' => function($d, $row) use ($is_export){
                    if ( $is_export ) {
                        return $this->utils->formatCurrencyNoSym($d);
                    } else {
                        return $d == 0 ? '<span class="text-muted">' . $this->utils->formatCurrencyNoSym($d) . '</span>' : '<strong>' . $this->utils->formatCurrencyNoSym($d) . '</strong>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'trans_amount',
                'select' => 'game_logs.trans_amount',
                'name' => lang('pay.transamount'),
                // 'formatter' => 'currencyFormatter',
                'formatter' => function ($d, $row) {
                    //only for game type
                    if ($row['flag'] != Game_logs::FLAG_TRANSACTION) {
                        return lang('N/A');
                    } else {
                        return $this->utils->formatCurrencyNoSym($d);
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'roundno',
                'select' => 'game_logs.table',
            ),
            array(
                'dt' => $i++,
                'alias' => 'note',
                'select' => 'game_logs.note',
                'name' => lang('Note'),
                'formatter' => function ($d, $row) {
                    if(!empty($d)){
                        return $d;
                    }else{
                        return "N/A";
                    }
                },
            ),
			array(
                'dt' => $i++,
                'alias' => 'betDetails',
                'select' => 'game_logs.bet_details',
                'name' => lang('Bet Detail'),
                'formatter' => function ($d, $row) use($show_bet_detail_on_game_logs) {
                    if (!empty($d)) {
                        $data = json_decode($d);
                       	$betDetailLink = "";
						if (!empty($data) && is_array($data)) {
							foreach ($data as $key => $value) {
								if (!empty($value)) {
									if (!empty($betDetailLink))
										$betDetailLink .= ", ";
									if(is_array($value)){
										$value=formatDebugMessage($value);
									}else{
										$value=lang($value);
									}
									if($key == 'sports_bet'){
										$key = 'Sports Bet';
										$res =  json_decode($value, true);
										$label = '';
										foreach($res as $k => $v){
											if(isset($v['yourBet'])) {
												$live = $v['isLive'] == true ? 'Live!' : 'Not Live';
												$htScore = $v['htScore'];
												if(is_array($htScore) ){
													$scoreDet = '';
													foreach($htScore as $n => $score){
														$scoreDet .= $htScore[$n]['score'].' ';
													}
													$htScore = "(".$scoreDet.")";
												}
												$label .= '<p>'.$v['yourBet'].', '.$v['odd'].', '.$live.', '.$htScore;
												$label .= (isset($v['eventName']) && isset($v['league']) ) ? ', '.$v['eventName'].', '.$v['league'] : '</p>';
											}
										}
										$value = $label;
									}

									$betDetailLink .= lang($key) . " : " .$value;

									if ($key == 'bet_details') {
										unset($betDetailLink);
										$details = json_decode($value, true);
										$label = '';

										# bet ids
										if (is_array($details) && !empty($details)) {
											foreach ($details as $details_id => $detail) {
												$bet_list = '';
												# bet list
												if (is_array($detail))  {

													foreach ($detail as $detail_key => $bets) {
														# list of not included in the display
														if (in_array($detail_key, array('odds', 'won_side', "win_amount"))) {
															continue;
														}
														$bet_list .=  lang($detail_key) . ":" . lang($bets) . ", ";
													}
												}

												$label .= "<div style='border-bottom:solid 0.1em #C0C0C0; margin-top:0.5em;'>" . lang('Bet ID') .": " . $details_id . "<br> (" . substr($bet_list, 0, -2) . ")</div>";
											}
										}
										$betDetailLink = $label;
									}
								}
							}
							if (!empty($platform_id)) {
								if ($platform_id == MG_API || $platform_id == QT_API)
								{
									if (!empty($betDetailLink))
										$betDetailLink .= "<br>";
									$betDetailLink .= '<a href="'.site_url('marketing_management/queryBetDetail/' . $row['game_platform_id'] . '/' . $row['playerId']).'" target="_blank" class="btn btn-info">'.lang('Bet Detail').'</a>';
								}
							}
						}else{
							$betDetailLink = lang($d) ;
						}
	                    return $betDetailLink;
                    }else{
                        return "N/A";
                    }
                },
            ),
			array(
				'dt' => $i++,
				'alias' => 'flag',
				'select' => 'game_logs.flag',
				'formatter' => 'defaultFormatter',
			),
            array(
                'dt' => $i++,
                'alias' => 'bet_type',
                'select' => 'game_logs.bet_type',
                'name' => lang('Bet Type'),
                'formatter' => function ($d, $row) use($is_export, $i) {
                    if( ! $is_export && (strpos(strtolower($d), 'single') != 'single' && !empty($d))){
                        $unique_id = $row['unique_id'];
						$is_unsettle = true;
                        $bets = json_decode($row['betDetails'], true);

                        $count = 0;
                        $count = !empty($bets['sports_bet']) ? count($bets['sports_bet']):0;
                        $count = !empty($bets['bet_details']) ? count($bets['bet_details']) + $count: $count;
                        $h = ($count > 1) ? ($count * 33) + 155 : 188;
						$link = "'/echoinfo/bet_details/$unique_id/0/$is_unsettle','Match Details','width=840,height=$h'";
                        return '<a href="javascript:void(window.open('.$link.'))">'.$d.'</a>';
                    }else{
                        return $d;
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'match_type',
                'select' => 'game_logs.match_type',
                'name' => lang('Match Type'),
                'formatter' => function ($d) use($is_export) {
                    return ($d == '0' || empty($d)) ? 'N/A' : 'Live';
                }
            ),
			array(
				'dt' => $i++,
				'alias' => 'match_details',
				'select' => 'game_logs.match_details',
				'name' => lang('Match Details'),
                'formatter' => 'defaultFormatter',
			),
            array(
                'dt' => $i++,
                'alias' => 'handicap',
                'select' => 'game_logs.handicap',
                'name' => lang('Handicap'),
                'formatter' => 'defaultFormatter',
            ),
			array(
				'dt' => $i++,
				'alias' => 'odds',
				'select' => 'game_logs.odds',
				'name' => lang('Odds'),
                'formatter' => 'defaultFormatter',
			),
            array(
                'alias' => 'unique_id',
                'select' => 'game_logs.external_uniqueid',
            )
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'game_logs_unsettle as game_logs';
		$joins = array(
			'player' => 'player.playerId = game_logs.player_id',
			'affiliates' => 'affiliates.affiliateId = player.affiliateId',
			'game_description' => 'game_description.id = game_logs.game_description_id',
			'game_type' => 'game_type.id = game_description.game_type_id',
			'external_system' => 'game_logs.game_platform_id = external_system.id',
            'playerdetails' => 'playerdetails.playerId = player.playerId'
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where[] = "player.playerId IS NOT NULL";

		if (isset($input['by_game_platform_id'])) {
			$where[] = "game_logs.game_platform_id = ?";
			$values[] = $input['by_game_platform_id'];
		}

		if (isset($input['by_game_flag'])) {
			$where[] = "game_logs.flag = ?";
			$values[] = $input['by_game_flag'];
		}

		if (isset($input['by_date_from'], $input['by_date_to'])) {
			$where[] = "game_logs.end_at BETWEEN ? AND ?";
			$values[] = $input['by_date_from'];
			$values[] = $input['by_date_to'];
		}

		if (isset($player_id)) {
			$where[] = "player.playerId = ?";
			$values[] = $player_id;
		}

		if (isset($input['by_no_affiliate']) && $input['by_no_affiliate'] == true) {
			$where[] = "player.affiliateId IS NULL";
		}

		if (isset($input['by_username'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['by_username'] . '%';
		}

		if (isset($input['by_game_code'])) {
			$where[] = "game_description.game_code = ?";
			$values[] = $input['by_game_code'];
		}

		if (isset($input['game_description_id'])) {
			$where[] = "game_description.id = ?";
			$values[] = $input['game_description_id'];
		}

		if (isset($input['by_group_level'])) {
			$where[] = "player.levelId  = ?";
			$values[] = $input['by_group_level'];
		}

		$all_game_types = isset($input['all_game_types']) ? ($input['all_game_types'] == 'true' || $input['all_game_types'] == 'on') : false;
		if (isset($input['game_type_id']) && !$all_game_types) {

			if (is_array($input['game_type_id'])) {
				if (isset($input['game_type_id_null'])) {
					$where[] = "(game_type.id IN (" . implode(',', array_fill(0, count($input['game_type_id']), '?')) . ") OR game_type.id IS NULL)";
				} else {
					$where[] = "game_type.id IN (" . implode(',', array_fill(0, count($input['game_type_id']), '?')) . ")";
				}
				$values = array_merge($values, $input['game_type_id']);
			} else {
				if (isset($input['game_type_id_null'])) {
					$where[] = "(game_type.id = ? OR game_type.id IS NULL)";
				} else {
					$where[] = "game_type.id = ?";
				}
				$values[] = $input['game_type_id'];
			}
		} else if (isset($input['game_type_id_null'])) {
			$where[] = "game_type.id IS NULL";
		}

		if (isset($input['by_amount_from'])) {
			$where[] = "game_logs.result_amount >= ?";
			$values[] = $input['by_amount_from'];
		}

		if (isset($input['by_amount_to'])) {
			$where[] = "game_logs.result_amount <= ?";
			$values[] = $input['by_amount_to'];
		}

		if (isset($input['by_bet_amount_from'])) {
			$where[] = "game_logs.bet_amount >= ?";
			$values[] = $input['by_bet_amount_from'];
		}

		if (isset($input['by_bet_amount_to'])) {
			$where[] = "game_logs.bet_amount <= ?";
			$values[] = $input['by_bet_amount_to'];
		}

		# END PROCESS SEARCH FORM #################################################################################################################################################

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(bet_amount) total_bet, SUM(result_amount) total_result, SUM(win_amount) total_win, SUM(loss_amount) total_loss', null, $columns, $where, $values);
		$sub_summary = $this->data_tables->summary($request, $table, $joins, 'external_system.system_code, SUM(bet_amount) total_bet, SUM(result_amount) total_result, SUM(win_amount) total_win, SUM(loss_amount) total_loss', 'external_system.system_code', $columns, $where, $values);
		$result['summary'][0]['total_bet'] = $this->utils->formatCurrencyNoSym($summary[0]['total_bet']);
		$result['summary'][0]['total_result'] = $this->utils->formatCurrencyNoSym($summary[0]['total_result']);
		$result['summary'][0]['total_win'] = $this->utils->formatCurrencyNoSym($summary[0]['total_win']);
		$result['summary'][0]['total_loss'] = $this->utils->formatCurrencyNoSym($summary[0]['total_loss']);
		$result['sub_summary'] = $sub_summary;
		$this->returnJsonResult($result);
	}

	/**
	 * detail: get user logs
	 *
	 * @return json
	 */
	public function userLogs() {
		$this->load->model('report_model');

		$request = $this->input->post();
		$result = $this->report_model->getUserLogs($request);
		$this->returnJsonResult($result);
	}

	/**
	 * detail: get withdraw condition of a certain player
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function withdrawCondition($player_id = null) {
		$this->load->model(array('promorules'));

		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'started_at',
				'select' => 'withdraw_conditions.started_at',
				'formatter' => 'dateTimeFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'source_type',
				'select' => 'withdraw_conditions.source_type',
				'formatter' => function ($d, $row) {
					return lang('withdraw_conditions.source_type.' . $d) ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'promotion_id',
				'select' => 'withdraw_conditions.promotion_id',
				'formatter' => function ($d, $row) {
					return !empty($d) ? $this->promorules->getPromoRule($d)['promoName'] : '<i class="text-muted">' . lang('pay.noPromo') . '</i>';
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'promotion_id',
				'select' => 'withdraw_conditions.promotion_id',
				'formatter' => function ($d, $row) {
					if (!$d) {
						return '<i class="text-muted">' . lang('pay.noPromo') . '</i>';
					}

					$promo_rule = $this->promorules->getPromoRule($d);

					$depositCondition = ($promo_rule['depositConditionNonFixedDepositAmount'] == 0)
					? lang('report.p20') . '(' . $promo_rule['nonfixedDepositMinAmount'] . ' - ' . $promo_rule['nonfixedDepositMaxAmount'] . ')'
					: lang('cms.anyAmt');

					if ($promo_rule['bonusReleaseRule'] == 0) {
						$promorulesBonusAmount = isset($promo_rule['promorulesBonusAmount']) ? $promo_rule['promorulesBonusAmount'] : 0;
						$bonusReleaseRule = lang('cms.fixedBonusAmount') . ' = ' . $promorulesBonusAmount;
					} else {
						$bonusReleaseRule = $promo_rule['depositPercentage'] . lang('cms.percentageOfDepositAmt') . ' ' . $promo_rule['maxBonusAmount'] . ' ' . lang('cms.maxbonusamt');
					}

					if ($promo_rule['withdrawRequirementRule'] == 0) {
						if ($promo_rule['withdrawRequirementConditionType'] == 0) {
							$withdrawRequirement = lang('cms.withBetAmtCond') . ' >= ' . $promo_rule['withdrawRequirementBetAmount'];
						} else {
							if ($promo_rule['promoType'] == 1) {
								$withdrawRequirement = lang('cms.betAmountCondition2') . ' ' . $promo_rule['withdrawRequirementBetCntCondition'];
							} else {
								$withdrawRequirement = lang('cms.betAmountCondition1') . ' ' . $promo_rule['withdrawRequirementBetCntCondition'];
							}
						}
					} else {
						$withdrawRequirement = lang('cms.noBetRequirement');
					}

					return '(' . lang('cms.depCon') . ')<br/>'
					. $depositCondition . '<br/>'
					. '(' . lang('cms.bonus') . ')<br/>'
					. $bonusReleaseRule . '<br/>'
					. '(' . lang('promo.betCondition') . ')<br/>'
						. $withdrawRequirement;
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'deposit_amount',
				'select' => 'withdraw_conditions.deposit_amount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'bonus_amount',
				'select' => 'withdraw_conditions.bonus_amount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'bet_times',
				'select' => 'withdraw_conditions.bet_times',
			),
			array(
				'dt' => $i++,
				'alias' => 'condition_amount',
				'select' => 'withdraw_conditions.condition_amount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'bet_amount',
				'select' => 'withdraw_conditions.bet_amount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'withdraw_conditions.status',
				'formatter' => function ($d, $row) {
					return $d == Promorules::STATUS_NORMAL ? lang('Unfinished') : lang('Finished');
				},
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'withdraw_conditions';
		$joins = array();

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if ($player_id) {
			$where[] = "withdraw_conditions.player_id = ?";
			$values[] = $player_id;
		}
		# END PROCESS SEARCH FORM #################################################################################################################################################

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);
	}


	public function promoStatus($player_id = null) {
		if (!$this->isLoggedAdminUser()) {
			return;
		}

		$request = $this->input->post();
		$result = $this->player_promo($player_id, $request);

		$this->returnJsonResult($result);
	}
	/**
	 * detail: get player promo of a certain player
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function player_promo($player_id , $request) {
		$this->load->model(array('promorules', 'player_promo'));

		$controller = $this;

		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'started_at',
				'select' => 'playerpromo.dateProcessed',
				'formatter' => 'dateTimeFormatter',
			),
			array(
				'alias' => 'promorulesId',
				'select' => 'promorules.promorulesId',
			),
            array(
                'dt' => $i++,
                'select' => 'playerpromo.playerpromoId',
                'alias' => 'promoId',
                'formatter' => 'defaultFormatter'
            ),
			array(
				'dt' => $i++,
				'alias' => 'promoType',
				'select' => 'promorules.promoType',
				'formatter' => function ($d, $row) {
					return $d == Promorules::PROMO_TYPE_NON_DEPOSIT ? lang('Non-deposit') : lang('Deposit');
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'promoName',
				'select' => 'promocmssetting.promoName',
				'formatter' => function ($d, $row) {
					if ($d == Promorules::SYSTEM_MANUAL_PROMO_RULE_NAME) {
						return lang('System Promotion');
					} elseif (!empty($d)) {
						return '<a href="#" data-toggle="modal" data-target="#promoDetails"  onclick="return viewPromoRuleDetails(' . $row['promorulesId'] . ');">' . $d . '</a>';
					} else {
						return lang('pay.noPromo');
					}

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'deposit_amount',
				'select' => 'playerpromo.depositAmount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'bonus_amount',
				'select' => 'playerpromo.bonusAmount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'condition_amount',
				'select' => 'playerpromo.withdrawConditionAmount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'bet_amount',
				'select' => 'withdraw_conditions.bet_amount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'current_bet_over_withdrawal_condition',
				'select' => 'withdraw_conditions.bet_amount',
				'formatter' => function ($d, $row) {
					return number_format($d, 2, '.', ',') . " / " . number_format($row['condition_amount'], 2, '.', ',');
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'playerpromo.transactionStatus',
				'formatter' => function ($d, $row) use ($controller) {
					return $controller->player_promo->statusToName($d);
				},
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'playerpromo';
		$joins = array(
			'promorules' => 'playerpromo.promorulesId=promorules.promorulesId',
			'promocmssetting' => 'playerpromo.promoCmsSettingId=promocmssetting.promoCmsSettingId',
			'withdraw_conditions' => 'playerpromo.playerpromoId=withdraw_conditions.player_promo_id',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if ($player_id) {
			$where[] = "playerpromo.playerId = ?";
			$values[] = $player_id;
		}

		if (isset($input['promo_process_date_from'], $input['promo_process_date_to'])) {
			$where[] = "playerpromo.dateProcessed BETWEEN ? AND ?";
			$values[] = $input['promo_process_date_from'];
			$values[] = $input['promo_process_date_to'];
		}

		if (isset($input['promo_status'])) {
			$where[] = "playerpromo.transactionStatus = ?";
			$values[] = $input['promo_status'];
		}

		if (isset($input['promoCmsSettingId'])) {
			$where[] = "promocmssetting.promoCmsSettingId = ?";
			$values[] = $input['promoCmsSettingId'];
		}

		# END PROCESS SEARCH FORM #################################################################################################################################################

		return $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
	}

	/**
	 * detail: get deposit checking report
	 *
	 * @return json
	 */
	public function depositCheckingReport($is_export = false) {
		if (!$this->isLoggedAdminUser()) {
			return;
		}

		$this->load->model(array('report_model'));
		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->depositCheckingReport($request,$is_export);

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get withdraw checking report
	 *
	 * @return json
	 */
	public function withdrawCheckingReport() {
		if (!$this->isLoggedAdminUser()) {
			return;
		}

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$result = $this->report_model->withdrawCheckingReport($request, $is_export);


		$this->returnJsonResult($result);
	}

	/**
	 * detail: get adjustment history
	 *
	 * @return json
	 */
	public function adjustment_history() {

		$this->load->model('transactions');

		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'transaction_time',
				'select' => 'transactions.created_at',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'transaction_type',
				'select' => 'transactions.transaction_type',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'to_username',
				'select' => '(CASE transactions.to_type WHEN 1 THEN adm1.username WHEN 2 THEN p1.username WHEN 3 THEN aff1.username END)',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'to_type',
				'select' => 'transactions.to_type',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'transactions.amount',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'before_balance',
				'select' => 'transactions.before_balance',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'after_balance',
				'select' => 'transactions.after_balance',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'subwallet',
				'select' => 'external_system.system_code',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'transactions.status',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'flag',
				'select' => 'transactions.flag',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'external_transaction_id',
				'select' => 'transactions.external_transaction_id',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'note',
				'select' => 'transactions.note',
				'formatter' => 'defaultFormatter',
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'transactions';
		$joins = array(
			'adminusers adm1' => 'adm1.userId = transactions.from_id',
			'player p1' => 'p1.playerId = transactions.from_id',
			'affiliates aff1' => 'aff1.affiliateId = transactions.from_id',
			'adminusers adm2' => 'adm2.userId = transactions.to_id',
			'player p2' => 'p2.playerId = transactions.to_id',
			'affiliates aff2' => 'aff2.affiliateId = transactions.to_id',
			// 'playeraccount' => 'playeraccount.playerAccountId = transactions.sub_wallet_id',
			'external_system' => 'external_system.id = transactions.sub_wallet_id',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		# END PROCESS SEARCH FORM #################################################################################################################################################

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		// $this->output->set_content_type('application/json')->set_output(json_encode($result));
		$this->returnJsonResult($result);
	}

	/**
	 * detail: get ip history of a certain player
	 *
	 * @param int $player_id http_request playerId
	 * @return json
	 */
	public function ip_history($player_id = null) {
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'ip',
				'select' => 'http_request.ip',
				'formatter' => function ($d, $row) {
					return $d ? $d . ' (' . implode(', ', $this->utils->getIpCityAndCountry($d)) . ')' : $this->data_tables->defaultFormatter($d, $row);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'type',
				'select' => 'http_request.type',
				'formatter' => function ($d) {
					return lang('http.type.' . $d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'referrer',
				'select' => 'http_request.referrer',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'device',
				'select' => 'http_request.device',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'createdat',
				'select' => 'http_request.createdat',
				'formatter' => 'defaultFormatter',
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'http_request';
		$joins = array();

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "http_request.createdat BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}
		if ($player_id) {
			$where[] = "http_request.playerId = ?";
			$values[] = $player_id;
		}

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		# END PROCESS SEARCH FORM #################################################################################################################################################

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);
	}

	/**
	 * detail: get duplicate accounts of a certain player
	 *
	 * @param int player id
	 * @return json
	 */
	public function dup_accounts($player_id = null) {
		$this->utils->debug_log($player_id);
		$this->load->library('duplicate_account');
		if ($player_id != null) {
			$this->utils->debug_log('before getDuplicateAccountsJSON');
			$data = $this->duplicate_account->getDuplicateAccountsJSON($player_id);
			$this->utils->debug_log('after getDuplicateAccountsJSON');
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
		$this->utils->debug_log($result);

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get duplicate account details of a certain player
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function dup_accounts_detail($player_id = null) {
		$this->load->library('duplicate_account');
		if ($player_id != null) {
			$data = $this->duplicate_account->getAllDuplicateAccountsJSON($player_id);
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
		$this->returnJsonResult($result);
	}

	/**
	 * detail: get friend referrals
	 *
	 * @return json
	 */
	public function friend_referral() {

		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;

		$result = $this->report_model->friendReferral($request, $is_export);
		$this->returnJsonResult($result);
	}

	# REPORTS #########################################################################################################################################################################################

	/**
	 * detail: check if the username is exist
	 *
	 * @param string $username
	 * @return json
	 */
	public function playerUsernameExist($username = null, $checkExistPlayer = false) {
        if(static::API_ACL_RESULT_SUCCESS !== $this->_check_api_acl(__FUNCTION__, 'default')){
            return $this->_show_last_check_acl_response('json');
        }

        $this->load->model(array('player_model'));

        $username = $username ?: $this->input->post('username');
		$checkExistPlayer = $checkExistPlayer ?: $this->input->post('checkExistPlayer');
		$result =false;

		if(!empty($username)){

			$result = (boolean) $this->is_exist($username, 'player.username');

			$this->utils->debug_log('check user if exist =====> ', $result);

            if (!$result && $this->utils->isEnabledFeature('enable_username_cross_site_checking')) {
                $result = $this->player_model->checkCrossSiteByUsername($username, false);
            }

			if ($result == true) {
				$username_length = strlen($username);
				$restrictUsername = !empty($this->utils->isRestrictUsernameEnabled()) ? 1 : 0;
				$min_username_length = $this->utils->getConfig('default_min_size_username');
				$max_username_length = $this->utils->getConfig('default_max_size_username');
				$regex_username = !empty($this->utils->isRestrictUsernameEnabled()) ? $this->utils->getConfig('restrict_regex_username') : $this->utils->getConfig('default_regex_username');
				$check = preg_match($regex_username, $username, $match);

                if( $this->input->post('newdeposit') == 1){
                    $check = true;
                }

				if (($username_length < $min_username_length || $username_length > $max_username_length) || (!$check)) {
					$result = false;
				}

				// check exist player to display withdraw/deposit amount
				if($checkExistPlayer == true) {
					$result = true;
				}
			}

		}
		$this->returnJsonResult($result);
	}

	/**
	 * detail: get withdrawal bank list of a certain player
	 *
	 * @param string $username
	 * @return json
	 */
	public function withdrawalBankList($username = null) {
		$this->load->model(array('player_model', 'playerbankdetails'));
		$username = $username ?: $this->input->post('username');
		$playerId = $this->player_model->getPlayerIdByUsername($username);

        # Overwrite playerId with the logged-in player id when called from player domain
        if(!$this->isLoggedAdminUser()) {
            $playerId = $this->authentication->getPlayerId();
        }

		if (!$playerId) {
			$result = array('list' => false);
			$this->returnJsonResult($result);
			return;
		}

		$list = $this->playerbankdetails->getPlayerWithdrawalBankList($playerId);

		$data['list'] = $list ? array_map(function ($item) {
			$item['bankName'] = lang($item['bankName']);
			return $item;
		}, $list) : array();

		$this->returnJsonResult($data);
	}

	/**
	 * detail: get deposit bank list of a certain player
	 *
	 * @param	string	$playerId
	 * @return	json
	 */
	public function depositBankList($playerId = null) {
		$this->load->model(array('player_model', 'playerbankdetails'));
		$playerId = intval($playerId);

        # Overwrite playerId with the logged-in player id when called from player domain
        if(!$this->isLoggedAdminUser()) {
            $playerId = $this->authentication->getPlayerId();
        }

		if (!$playerId) {
			$result = array('list' => false);
			$this->returnJsonResult($result);
			return;
		}

		$list = $this->playerbankdetails->getPlayerDepositBankList($playerId);

		$data['list'] = $list ? array_map(function ($item) {
			$item['bankName'] = lang($item['bankName']);
			return $item;
		}, $list) : array();

		$this->returnJsonResult($data);
	}

	/**
	 * detail: get main wallet balance of a certain player
	 *
	 * @param string $username
	 * @return jsonplayerDepositLimit
	 */
	public function playerMainWalletBalance($username = null) {
		$this->load->model(array('player_model'));
		$username = $username ?: $this->input->post('username');
		$playerId = $this->player_model->getPlayerIdByUsername($username);

        # Overwrite playerId with the logged-in player id when called from player domain
        if(!$this->isLoggedAdminUser()) {
            $playerId = $this->authentication->getPlayerId();
        }

		if (!$playerId) {
			$result = array('balance' => false);
			$this->returnJsonResult($result);
			return;
		}

		$balance = $this->player_model->getMainWalletBalance($playerId);
		$data['balance'] = $balance ? $this->utils->formatCurrencyNoSym($balance) : '0.00';
		$this->returnJsonResult($data);
	}

	/**
	 * detail: get deposit limit of a certain player
	 *
	 * @param string $username
	 * @return json
	 */
	public function playerDepositLimit($username = null) {
		$this->load->model(array('player_model', 'group_level','sale_order'));
		$username = $username ?: $this->input->post('username');
		$playerId = $this->player_model->getPlayerIdByUsername($username);

		if (!$playerId) {
			$result = array('balance' => false);
			$this->returnJsonResult($result);
			return;
		}

		$depositRule = $this->group_level->getPlayerDepositRule($playerId);
		$minDeposit = @$depositRule[0]['minDeposit'];
		$maxDeposit = @$depositRule[0]['maxDeposit'];
        if($this->utils->isEnabledFeature('responsible_gaming')){
            $this->load->library(array('player_responsible_gaming_library'));
            $resDeposit= $this->player_responsible_gaming_library->getDepositLimit($playerId);
            if($resDeposit['status']){
                if($resDeposit['value']<$maxDeposit){
                    $maxDeposit=$resDeposit['value'];
                }
            }

        }
		$data['minDeposit'] = $minDeposit ? $minDeposit : '0.00';
		$data['maxDeposit'] = $maxDeposit ? $maxDeposit : '0.00';
		$this->returnJsonResult($data);
	}

	/**
	 * detail: get withdraw limit of a certain player
	 *
	 * @param string $username
	 * @return json
	 */
	public function playerWithdrawLimit($username = null) {
		$this->load->model(array('player_model', 'group_level'));
		$username = $username ?: $this->input->post('username');
		$playerId = $this->player_model->getPlayerIdByUsername($username);

		if (!$playerId) {
			$result = array('success' => false, 'balance' => false);
			$this->returnJsonResult($result);
			return;
		}

		//get player total withdraw for today
		$playerTotalWithdrawForToday = $this->transactions->getPlayerTotalWithdrawals($playerId, true) ?: 0;

		//get player level withdraw rule
		$withdrawalRule = $this->group_level->getPlayerWithdrawRule($playerId);
		$dailyMaxWithdrawal = @$withdrawalRule[0]['dailyMaxWithdrawal'];

		if ($playerTotalWithdrawForToday < $dailyMaxWithdrawal) {
			$result['success'] = true;
		} else {
			$result = array('success' => false, 'message' => lang('Daily Maximum Withdrawal Reached'));
		}
		$result['dailyMaxWithdrawal'] = $dailyMaxWithdrawal ? $dailyMaxWithdrawal : '0.00';
		$result['playerTotalWithdrawForToday'] = $playerTotalWithdrawForToday ? $playerTotalWithdrawForToday : '0.00';

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get player name
	 *
	 * @param string $username
	 * @return json
	 */
	public function playerName($username = null) {
		$this->load->model(array('player_model', 'group_level'));
		$username = $username ?: $this->input->post('username');
		$playerId = $this->player_model->getPlayerIdByUsername($username);

		if (!$playerId) {
			$result = array('playerName' => false);
			$this->returnJsonResult($result);
			return;
		}

		$playerDetails = $this->player_model->getPlayerDetailsById($playerId);
		$data['name'] = trim($playerDetails->firstName . ' ' . $playerDetails->lastName);

		$this->returnJsonResult($data);
	}

	/**
	 * detail: check bank account number
	 *
	 * @param string $accountNumber
	 * @return json
	 */
	public function bankAccountNumber($accountNumber = null) {
		$this->load->model(array('playerbankdetails'));

        if(!$this->authentication->isLoggedIn()){
            $status = self::MESSAGE_TYPE_ERROR;
            $message = lang('Not Login');
            $this->returnCommon($status, $message);
        }

        $player_id = $this->authentication->getPlayerId();

        $bank_type = $this->input->get_post('bankType');
        $bank_type_id = $this->input->get_post('bankTypeId');
        $accountNumber = $accountNumber ?: $this->input->get_post('accountNumber');
		$accountNumber = $accountNumber ?: $this->input->get_post('input-acct-num');

		$result = $this->playerbankdetails->validate_bank_account_number($player_id, $accountNumber, $bank_type, null, $bank_type_id);
		if($result){
		    $status = self::MESSAGE_TYPE_SUCCESS;
		    $message = NULL;
        }else{
		    $status = self::MESSAGE_TYPE_ERROR;
		    $message = lang('account_number_can_not_be_duplicate');
        }
		$this->returnCommon($status, $message);
	}

	/**
	 * detail: get promo applications
	 *
	 * @return json
	 */
	public function promoApplicationList() {

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$result = $this->report_model->promoApplicationList($request, $is_export);

		$this->returnJsonResult($result);
	}
	public function daily_player_balance_report() {
		$filenames = $this->input->post('filename');
		$this->utils->recordAction(lang('export_data'), $this->router->fetch_method(), $filenames);
		return true;
	}
	/**
	 * detail: get summary report
	 *
	 * @param string $year
	 * @param string $month
	 *
	 * @return json
	 */
	public function report_summary($year = null, $month = null, $is_export = false) {

        if($is_export){
			$filenames = $this->input->post('filename');
			$this->utils->recordAction(lang('export_data'), $this->router->fetch_method(), $filenames);
			return true;
		}

		$selected_tags = $this->input->post('tag_list');
		$this->load->model(array('report_model'));

		if ($month) {
			$transaction_summary_list = $this->report_model->report_summary('DATE', $year . str_pad($month, 2, '0'), $selected_tags);
		} else if ($year) {
			$transaction_summary_list = $this->report_model->report_summary('YEAR_MONTH', $year, $selected_tags);
		} else {
			$transaction_summary_list = $this->report_model->report_summary('YEAR', null, $selected_tags);
		}

		$transaction_summary_list = array_combine(array_column($transaction_summary_list, 'common_date'), $transaction_summary_list);

		$data = null;
		if ($month) {
			//days of this month
			$month_start = strtotime('first day of this month', mktime(0, 0, 0, $month, 1, $year));
			$month_end = date('Y-m-d', strtotime('last day of this month', mktime(0, 0, 0, $month, 1, $year)));
			$i = 0;
			while (true) {
				$date = date('Y-m-d', strtotime('+' . $i++ . ' day', $month_start));
				$new_and_total_players = $this->report_model->get_new_and_total_players('DATE', $date, $selected_tags);
				$firs_and_second_deposit = $this->report_model->get_first_and_second_deposit('DATE', $date, $selected_tags);
				$betWinLossPayoutCol = $this->report_model->sumBetWinLossPayout('DATE', $date, $selected_tags);
				$data[] = array_merge(array('slug' => str_replace('-', '/', $date)), $betWinLossPayoutCol, $new_and_total_players, $firs_and_second_deposit,
					isset($transaction_summary_list[$date]) ? $transaction_summary_list[$date] : array(
						'common_date'           => $date,
						'total_deposit'         => 0,
						'total_withdraw'        => 0,
						'total_bonus'           => 0,
						'total_cashback'        => 0,
						'total_transaction_fee' => 0,
						'bank_cash_amount'      => 0,
						'total_bet'             => 0,
						'total_win'             => 0,
						'total_loss'            => 0,
						'payment'               => 0,
                        'payout'                => 0
					));
				if ($date == $month_end) {
					break;
				}
			}
		} else if ($year) {
			//months of this year
			for ($i = 1; $i <= 12; $i++) {
				$month = str_pad($i, 2, '0', 0);
				$year_month = $year . $month;
				$this->utils->debug_log($year_month);
				$new_and_total_players = $this->report_model->get_new_and_total_players('YEAR_MONTH', $year_month, $selected_tags);
				$firs_and_second_deposit = $this->report_model->get_first_and_second_deposit('YEAR_MONTH', $year_month, $selected_tags);
				$betWinLossPayoutCol = $this->report_model->sumBetWinLossPayout('YEAR_MONTH', $year . '-' . $month . '-01', $selected_tags);
				$data[] = array_merge(array('slug' => "{$year}/{$month}"), $betWinLossPayoutCol, $new_and_total_players, $firs_and_second_deposit,
					isset($transaction_summary_list[$year_month]) ? $transaction_summary_list[$year_month] : array(
						'common_date'           => $year_month,
						'total_deposit'         => 0,
						'total_withdraw'        => 0,
						'total_bonus'           => 0,
						'total_cashback'        => 0,
						'total_transaction_fee' => 0,
						'bank_cash_amount'      => 0,
						'total_bet'             => 0,
						'total_win'             => 0,
						'total_loss'            => 0,
						'payment'               => 0,
                        'payout'                => 0
					));
			}
		} else {
			//years
			foreach ($transaction_summary_list as $transaction_summary) {
				$new_and_total_players = $this->report_model->get_new_and_total_players('YEAR', $transaction_summary['common_date'], $selected_tags);
				$firs_and_second_deposit = $this->report_model->get_first_and_second_deposit('YEAR', $transaction_summary['common_date'], $selected_tags);

				$betWinLossPayoutCol = $this->report_model->sumBetWinLossPayout('YEAR', $transaction_summary['common_date'], $selected_tags);

				$data[] = array_merge($betWinLossPayoutCol, $transaction_summary, $new_and_total_players, $firs_and_second_deposit, array(
					'slug' => $transaction_summary['common_date'],
				));
			}

            $this->utils->debug_log('the data 2 -------->', $data);
		}
		if (empty($data)) {
			$output['data'] = [
				["total_bet" => 0, "total_win" => 0, "total_loss" => 0, "payout" => 0, "common_date" => date('Y'),
					"total_deposit" => "0", "total_withdraw" => "0", "total_bonus" => "0", "total_cashback" => "0",
					"total_transaction_fee" => "0", "bank_cash_amount" => "0", "total_players" => "0",
					"new_players" => "0", "first_deposit" => 0, "second_deposit" => 0, "slug" => date('Y')],
			];
		} else {
			$output['data'] = array_values($data);
		}
		$this->returnJsonResult($output);
	}

	/**
	 * detail: get all new members
	 *
	 * @param string $year
	 * @param string $month
	 * @param string $day
	 *
	 * @return json
	 */
	public function new_members($year, $month = null, $day = null) {
		$this->load->model('report_model');
		if (isset($day, $month, $year)) {
			$data = $this->report_model->get_new_players('DATE', "{$year}-{$month}-{$day}");
		} else if (isset($month, $year)) {
			$data = $this->report_model->get_new_players('YEAR_MONTH', $year . str_pad($month, 2, '0'));
		} else if (isset($year)) {
			$data = $this->report_model->get_new_players('YEAR', $year);
		}
		$output['data'] = $data;
		$this->returnJsonResult($output);
	}

	/**
	 * detail: get total members
	 *
	 * @param string $year
	 * @param string $month
	 * @param string $day
	 *
	 * @return json
	 */
	public function total_members($year = null, $month = null, $day = null) {
		$this->load->model('report_model');
		if (isset($day, $month, $year)) {
			$data = $this->report_model->get_total_players('DATE', "{$year}-{$month}-{$day}");
		} else if (isset($month, $year)) {
			$data = $this->report_model->get_total_players('YEAR_MONTH', $year . str_pad($month, 2, '0'));
		} else if (isset($year)) {
			$data = $this->report_model->get_total_players('YEAR', $year);
		}
		$output['data'] = $data;
		$this->returnJsonResult($output);

	}

	/**
	 * detail: get total members
	 *
	 * @param string $year
	 * @param string $month
	 * @param string $day
	 *
	 * @return json
	 */
	public function total_deposit_members($year = null, $month = null, $day = null) {
		$this->load->model('report_model');
		if (isset($day, $month, $year)) {
			$data = $this->report_model->get_total_deposit_players('DATE', "{$year}-{$month}-{$day}");
		} else if (isset($month, $year)) {
			$data = $this->report_model->get_total_deposit_players('YEAR_MONTH', $year . str_pad($month, 2, '0'));
		} else if (isset($year)) {
			$data = $this->report_model->get_total_deposit_players('YEAR', $year);
		}
		$this->utils->debug_log('total_deposit_members :', $this->utils->printLastSQL());
		$output['data'] = $data;
		$this->returnJsonResult($output);

	}

	/**
	 * detail: get first deposit
	 *
	 * @param string $year
	 * @param string $month
	 * @param string $day
	 *
	 * @return json
	 */
	public function first_deposit($year = null, $month = null, $day = null) {
		$this->load->model('report_model');
		if (isset($day, $month, $year)) {
			$data = $this->report_model->get_first_deposit('DATE', "{$year}-{$month}-{$day}");
		} else if (isset($month, $year)) {
			$data = $this->report_model->get_first_deposit('YEAR_MONTH', $year . str_pad($month, 2, '0'));
		} else if (isset($year)) {
			$data = $this->report_model->get_first_deposit('YEAR', $year);
		}
		$output['data'] = $data;
		$this->returnJsonResult($output);

	}

	/**
	 * detail: get second deposit
	 *
	 * @param string $year
	 * @param string $month
	 * @param stirng $day
	 *
	 * @return json
	 */
	public function second_deposit($year = null, $month = null, $day = null) {
		$this->load->model('report_model');
		if (isset($day, $month, $year)) {
			$data = $this->report_model->get_second_deposit('DATE', "{$year}-{$month}-{$day}");
		} else if (isset($month, $year)) {
			$data = $this->report_model->get_second_deposit('YEAR_MONTH', $year . str_pad($month, 2, '0'));
		} else if (isset($year)) {
			$data = $this->report_model->get_second_deposit('YEAR', $year);
		}
		$output['data'] = $data;
		$this->returnJsonResult($output);

	}

	/**
	 * detail: manage cashback request
	 *
	 * @return json
	 */
	public function cashback_request() {

		$this->load->model(array('cashback_request'));

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(
			array(
				'dt' => $i++,
				'select' => 'cashback_request.id',
				'alias' => 'cashback_request_id',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.player_id',
				'alias' => 'player_id',
				'visible' => false,
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.processed_by',
				'alias' => 'processed_by',
				'visible' => false,
			),
			array(
				'dt' => $i++,
				'select' => 'player.username',
				'alias' => 'playerUsername',
				'formatter' => function ($data, $row) {
					return '<a href="/player_management/userInformation/' . $row['player_id'] . '" target="_blank">' . $data . '</a>';
				},
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.request_datetime',
				'alias' => 'request_datetime',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.request_amount',
				'alias' => 'request_amount',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.status',
				'alias' => 'status',
				'formatter' => function ($data, $row) {
					switch ($data) {
					case Cashback_request::APPROVED:
						return '<span class="label label-success full-width"><strong>APPROVED</strong></span>';
					case Cashback_request::DECLINED:
						return '<span class="label label-danger full-width"><strong>DECLINED</strong></span>';
					case Cashback_request::PENDING:
						return '<span class="label label-primary full-width"><strong>PENDING</strong></span>';
					default:
						return '<span class="label label-warning full-width"><strong>UNKNOWN</strong></span>';
					}
				},
			),
			array(
				'dt' => $i++,
				'select' => 'adminusers.username',
				'alias' => 'adminUsername',
				'formatter' => function ($data, $row) {
					return '<a href="/user_management/viewUser/' . $row['processed_by'] . '" target="_blank">' . $data . '</a>';
				},
			),

			array(
				'dt' => $i++,
				'select' => 'cashback_request.processed_datetime',
				'alias' => 'processed_datetime',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.notes',
				'alias' => 'notes',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.created_at',
				'alias' => 'created_at',
			),
			array(
				'dt' => $i++,
				'select' => 'cashback_request.status',
				'alias' => 'status',
				'formatter' => function ($data, $row) {

					$ret = '';

					if (Cashback_request::PENDING == $data) {
						$ret = '<a href="#" class="btn btn-xs btn-success" onclick="approveCashback(' . $row['cashback_request_id'] . ')" >' . lang('lang.approve') . '</a>&nbsp;&nbsp;';
						$ret .= '<a href="#" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#cashbackRequestCancel" onclick="viewCashbackDeclineForm(' . $row['cashback_request_id'] . ')">' . lang('lang.decline') . '</a>';
					}

					return $ret;
				},
			),

		);

		$table = 'cashback_request';
		$joins = array(
			'player' => 'player.playerId = cashback_request.player_id',
			'adminusers' => 'adminusers.userId = cashback_request.processed_by',
		);

		if (isset($input['date_from'], $input['date_to'])) {
			$where[] = "cashback_request.request_datetime BETWEEN ? AND ?";
			$values[] = $input['date_from'];
			$values[] = $input['date_to'];
		}

		if (isset($input['username'])) {
			$where[] = "player.username = ?";
			$values[] = $input['username'];
		}

		if (isset($input['status'])) {
			$where[] = "cashback_request.status = ?";
			$values[] = $input['status'];
		}

		if (isset($input['request_amount_from'])) {
			$where[] = "cashback_request.request_amount >= ?";
			$values[] = $input['request_amount_from'];
		}

		if (isset($input['request_amount_to'])) {
			$where[] = "cashback_request.request_amount <= ?";
			$values[] = $input['request_amount_to'];
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	public function getCashbackRequestRecords() {

		$request = $this->input->post();
		$player_id = $this->authentication->getPlayerId();
		if(empty($player_id)) {
			return $this->returnText('');
		}
		$this->load->model(array('cashback_request'));
		$this->load->library('pagination');

		$result = $this->cashback_request->getCashbackRequestRecords($request, $player_id);

		$total = $result['recordsTotal'];
		$base_url = base_url() . 'api/game_history/';
		$config = $this->_getPaginationSetting($base_url, $total);
		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$page = 0;

		if ($this->uri->segment(3)) {
			$page = $this->uri->segment(3);
		}

		$request['length'] = $config['per_page'];
		$request['start'] = $page;

		$data['result'] = $result['data'];
		$data['total_result'] = $total;

		$template = $this->utils->getPlayerCenterTemplate();

		if ($this->utils->is_mobile()) {

			$template = $template . '/mobile';

		}

		$this->load->view($template . '/cashier/ajax_' . $this->input->post('template') . '_history', $data);
	}

	protected function _getPaginationSetting($base_url, $total) {
		//setting up the pagination
		$config['base_url'] = $base_url;
		$config['total_rows'] = $total;
		$config['prev_link'] = '&lt;';
		$config['next_link'] = '&gt;';
		$config['full_tag_open'] = '<ul>';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li><span class="page-numbers current">';
		$config['cur_tag_close'] = '</span></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['first_link'] = FALSE;
		$config['last_link'] = FALSE;
		$config['per_page'] = 10;

		return $config;
	}

	/**
	 * detail: check if agent username is exists
	 *
	 * @param string $username
	 * @return json
	 */
	public function exists_agent_username($username = null) {
		$username = $username ?: $this->input->post('username');
		$this->returnJsonResult((boolean) $this->is_exist($username, 'agency_agents.agent_name'));
	}
	/**
	 * detail: get wallet balance of a certain agent
	 *
	 * @param string $username
	 * @param string $walletType
	 *
	 * @return json
	 */
	public function get_agent_wallet_balance($username = null, $walletType = null) {
		$this->load->model(array('agency_model'));
		$username = $username ?: $this->input->post('username');
		$walletType = $walletType ?: $this->input->post('walletType');
		$agent_id = $this->agency_model->get_agent_id_by_agent_name($username);

		if (!$agent_id) {
			$result = array('balance' => 0);
			$this->returnJsonResult($result);
			return;
		}
		if ($walletType == 'main') {
			$balance = $this->agency_model->getMainWallet($agent_id);
		} else {
			$balance = $this->agency_model->getBalanceWallet($agent_id);
		}
		$data['balance'] = $balance ? $this->utils->formatCurrencyNoSym($balance) : 0.00;
		$this->returnJsonResult($data);
	}
	/**
	 * detail: check if affiliate username is exists
	 *
	 * @param string $username
	 * @return json
	 */
	public function exists_aff_username($username = null) {
		$username = $username ?: $this->input->post('username');
		$this->returnJsonResult((boolean) $this->is_exist($username, 'affiliates.username'));
	}

	/**
	 * detail: get wallet balance of a certain affiliate
	 *
	 * @param string $username
	 * @param string $walletType
	 *
	 * @return json
	 */
	public function get_aff_wallet_balance($username = null, $walletType = null) {
		$this->load->model(array('affiliatemodel'));
		$username = $username ?: $this->input->post('username');
		$walletType = $walletType ?: $this->input->post('walletType');
		$affId = $this->affiliatemodel->getAffiliateIdByUsername($username);

		if (!$affId) {
			$result = array('balance' => 0);
			$this->returnJsonResult($result);
			return;
		}
		if ($walletType == 'main') {
			$balance = $this->affiliatemodel->getMainWallet($affId);
		} else {
			$balance = $this->affiliatemodel->getBalanceWallet($affId);
		}
		$data['balance'] = $balance ? $this->utils->formatCurrencyNoSym($balance) : 0.00;
		$this->returnJsonResult($data);
	}

	/**
	 * detail: Lists all affiliate
	 *
	 * @return json
	 */
	public function aff_list() {
		$this->load->model(array('report_model'));
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$request = $this->input->post();

		$is_export = false;
		$allowed_affiliate_contact_info = $this->permissions->checkPermissions('affiliate_contact_info');
		$allowed_affiliate_tag = $this->permissions->checkPermissions('affiliate_tag');
		$allowed_delete_affiliate = $this->permissions->checkPermissions('delete_affiliate');
		$activate_deactivate_affiliate = $this->permissions->checkPermissions('activate_deactivate_affiliate');
		$permissions = array(
			'affiliate_contact_info' => $allowed_affiliate_contact_info,
			'affiliate_tag' => $allowed_affiliate_tag,
			'delete_affiliate' => $allowed_delete_affiliate,
			'activate_deactivate_affiliate' => $activate_deactivate_affiliate,
		);
		$result = $this->report_model->aff_list($request, $is_export, $permissions);

		$this->returnJsonResult($result);

	}

	/**
	 * Called initDataTableSummaryByXXX() of ajax in view.
	 *
	 */
	public function conversion_rate_report() {

		$this->load->model(array('report_model'));
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		$SummaryBy = strtolower($input['SummaryBy']);

		$result = $this->report_model->conversion_rate_report( $SummaryBy );
		$this->returnJsonResult($result);
	} // EOF conversion_rate_report

	/**
	 * detail: transfer request of a certain player
	 *
	 * @param int $playerId transfer_request player_id
	 * @return string json
	 */
	public function transfer_request($playerId = null) {

		$this->load->model(array('report_model'));
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$request = $this->input->post();

		$permissions = array(
			'make_up_transfer_record' => $this->permissions->checkPermissions('make_up_transfer_record'),
		);

		$is_export = false;
		$result = $this->report_model->transferRequest($playerId, $request, $permissions, $is_export);
		$this->returnJsonResult($result);

	}

	/**
	 * get the player transfer request records
	 *
	 * for frontend
	 *
	 * @author Elvis_Chen<elvis.chen@tripleonetech.net>
	 * @since 1.0.0 Elvis_Chen: Initial function
	 *
	 * @return void
	 */
	public function player_transfer_request() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$player_id = $this->authentication->getPlayerId();
		if(empty($player_id)) {
			return $this->returnJsonResult($this->data_tables->empty_data($request));
		}
		if (empty($player_id)) {
			$this->returnJsonResult($this->data_tables->empty_data($request));
			return;
		}

		$is_export = false;
		$datatable_result = $this->report_model->playerTransferRequest($player_id, $request, $is_export);

		$this->returnJsonResult($datatable_result);
	}

	/**
	 * detail: get balance history of a certain player
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function balance_history($player_id = null) {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$is_export = false;
		$result = $this->report_model->balance_history($player_id, $request, $is_export);

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get game description lists
	 *
	 * @return json
	 */
	public function gameDescriptionList($history = null) {
		$this->load->library('permissions');
		$this->load->model(array('report_model'));
		$this->permissions->setPermissions();

		$request = $this->input->post();

		$is_export = false;
		$result = [];
		if ($this->permissions->checkPermissions('game_description_history')) {
			if ($history)
				$result = $this->report_model->gameDescriptionListHistory($request, $is_export);
		}

		if ($this->permissions->checkPermissions('game_description')) {
			if (empty($history))
				$result = $this->report_model->gameDescriptionList($request, $is_export);
		}else{
			return $this->error_access();
		}

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get game description lists
	 *
	 * @return json
	 */
	public function gameProviderAuthList($history = null) {
		$this->load->library('permissions');
		$this->load->model(array('report_model'));
		$this->permissions->setPermissions();

		$request = $this->input->post();

		$is_export = false;
		$result = [];

		if ($this->permissions->checkPermissions('view_game_provider_auth_accounts')) {
			$result = $this->report_model->gameProviderAuthList($request, $is_export);
		}else{
			return $this->error_access();
		}

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get promo history of a certain player
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function getPlayerPromoHistoryWLimit($player_id = null) {

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(

			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promoName',
				'alias' => 'promoName',
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.dateApply',
				'alias' => 'dateApply',
			),
			array(
				'dt' => $i++,
				'select' => 'promorules.bonusAmount',
				'alias' => 'bonusAmount',
				'formatter' => 'currencyFormatter',
			),

			array(
				'dt' => $i++,
				'select' => 'playerpromo.transactionStatus',
				'alias' => 'transactionStatus',
				'formatter' => function ($d, $row) {

					if ($row['transactionStatus'] == 0) {
						return lang('cashier.131');
					} elseif ($row['transactionStatus'] == 1) {
						return lang('cashier.123');
					} elseif ($row['transactionStatus'] == 2) {
						return lang('cashier.98');
					} else {
						return lang('cashier.133');
					}
				},
			),
			array(
				'select' => 'playerpromo.cancelRequestStatus',
				'alias' => 'cancelRequestStatus',

			),
			array(
				'select' => 'playerpromo.declinedCancelReason',
				'alias' => 'declinedCancelReason',

			),
			array(
				'select' => 'playerpromo.declinedApplicationReason',
				'alias' => 'declinedApplicationReason',

			),
			array(
				'select' => 'playerpromo.declinedApplicationReason',
				'alias' => 'declinedApplicationReason',

			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.declinedApplicationReason',
				'alias' => 'remarks',
				'formatter' => function ($d, $row) {
					if ($row['cancelRequestStatus'] == 2 && $row['transactionStatus'] == 1) {
						return $row['declinedCancelReason'] == '' ? lang("cashier.135") : $row['declinedCancelReason'];
					} elseif ($row['cancelRequestStatus'] == 2 && $row['transactionStatus'] == 1) {
						return $row['declinedApplicationReason'] == '' ? lang("cashier.135") : $row['declinedApplicationReason'];
					} elseif (($row['transactionStatus'] == 0 || $row['transactionStatus'] == 1) || ($row['transactionStatus'] == 3 || $row['transactionStatus'] == 3)) {
						return '<i>' . lang("cashier.135") . '</i>';
					} elseif ($row['cancelRequestStatus'] == 0 && $row['transactionStatus'] == 2) {
						return $row['declinedApplicationReason'] == '' ? lang("cashier.135") : $row['declinedApplicationReason'];
					}

				},

			),

		);

		$table = 'playerpromo';
		$joins = array(

			'promocmssetting' => 'promocmssetting.promoCmsSettingId = playerpromo.promoCmsSettingId',
			'promorules' => 'promorules.promorulesId = playerpromo.promorulesId',
		);

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "playerpromo.dateApply BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}
		if ($player_id) {
			$where[] = "playerpromo.playerId = ? ";
			$values[] = $player_id;

		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);
	}

	/**
	 * detail: get adjustment history of a certain player
	 *
	 * @param int $playerId transactions to_id or form_type
	 */
	public function getPlayerAdjustmentHistoryWLimit($player_id = null) {

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(

			array(
				'dt' => $i++,
				'select' => 'transactions.created_at',
				'alias' => 'created_at',

			),
			array(
				'dt' => $i++,
				'select' => 'transactions.transaction_type',
				'alias' => 'transaction_type',
				'formatter' => function ($d, $row) {
					return lang('transaction.transaction.type.' . $row['transaction_type']);
				},

			),
			array(
				'dt' => $i++,
				'select' => 'transactions.amount',
				'alias' => 'amount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'select' => 'transactions.before_balance',
				'alias' => 'before_balance',
				'formatter' => 'currencyFormatter',

			),
			array(
				'dt' => $i++,
				'select' => 'transactions.after_balance',
				'alias' => 'after_balance',
				'formatter' => 'currencyFormatter',

			),
		);

		$table = 'transactions';
		$joins = array();

		if (isset($input['transaction_type'])) {
			if (is_array($input['transaction_type'])) {
				$where[] = "transactions.transaction_type IN (" . implode(',', array_fill(0, count($input['transaction_type']), '?')) . ")";
				$values = array_merge($values, $input['transaction_type']);
			} else {
				$where[] = "transactions.transaction_type = ?";
				$values[] = $input['transaction_type'];
			}
		}

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "transactions.created_at BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		if ($player_id) {
			$where[] = "(transactions.to_id = ? AND transactions.to_type = ?) or (transactions.from_id = ? AND transactions.from_type = ? )";
			$values[] = $player_id;
			$values[] = Transactions::PLAYER;
			$values[] = $player_id;
			$values[] = Transactions::PLAYER;
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	/**
	 * detail: get cashback history of a certain player
	 *
	 * @param int $player_id transactions to_id or form_type
	 * @return json
	 */
	public function getCashbackHistoryWLimit($player_id = null) {

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(

			array(
				'dt' => $i++,
				'select' => 'transactions.created_at',
				'alias' => 'receivedOn',

			),
			array(
				'dt' => $i++,
				'select' => 'transactions.amount',
				'alias' => 'amount',
				'formatter' => 'currencyFormatter',
			),

		);

		$table = 'transactions';
		$joins = array();

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "transactions.created_at BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		if ($player_id) {
			$where[] = "transactions.to_id = ? AND transactions.to_type = ? AND transactions.transaction_type = ?  AND transactions.status = ? ";
			$values[] = $player_id;
			$values[] = Transactions::PLAYER;
			$values[] = Transactions::AUTO_ADD_CASHBACK_TO_BALANCE;
			$values[] = Transactions::APPROVED;
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	public function cashbackHistory_json() {
		$this->load->model(array('transactions'));
		$player_id = $this->authentication->getPlayerId();

		$page_len = intval($this->input->get('page_len')) ?: 10;
		$page = intval($this->input->get('page')) ?: 0;

		$result =  $this->transactions->getLatestCashbackHistory($player_id, $page_len, $page);
		$ret = [ 'res' => $result ];

		return $this->returnJsonResult($ret);
	}

	/**
	 * detail: get all withdrawals of a certain player
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function getAllWithdrawalsWLimit($player_id = null) {

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(

			array(
				'dt' => $i++,
				'select' => 'walletaccount.dwDateTime',
				'alias' => 'dwDateTime',
			),

			array(
				'dt' => $i++,
				'select' => 'walletaccount.transactionCode',
				'alias' => 'transactionCode',
			),

			array(
				'select' => 'walletaccount.dwMethod',
				'alias' => 'dwMethod',
				'formatter' => function ($d, $row) {
					if ($row['dwMethod'] == 1) {
						return 'Localbank';
					} elseif ($row['dwMethod'] == 2 || $row['dwMethod'] == 3) {
						return 'Auto 3rd Party';
					} else {
						return 'Manual 3rd Party';
					}
				},
			),
			array(
				'dt' => $i++,
				'select' => 'walletaccount.dwStatus',
				'alias' => 'dwStatus',
				'formatter' => function ($d, $row) {
					switch ($row['dwStatus']) {

					case 'request':
						$status = lang('Pending');
						if ($row['is_checking']) {
							$status = lang('payment.checking');
						}
						return $status;
						break;

					case 'CS0':
					case 'CS1':
					case 'CS2':
					case 'CS3':
					case 'CS4':
					case 'CS5':
					case 'CS6':
					case 'payProc':
						return lang('pay.procssng');
						break;

					case 'approved':
						return lang('Approved');
						break;

					case 'declined':
						return lang('Declined');
						break;

					case 'paid':
						return lang('Paid');
						break;
					}
				},
			),
			array(
				'dt' => $i++,
				'select' => 'walletaccount.amount',
				'alias' => 'amount',
				'formatter' => 'currencyFormatter',

			),
			array(
				'select' => 'walletaccount.showNotesFlag',
				'alias' => 'showNotesFlag',
				'formatter' => function ($d, $row) {
					return $row['showNotesFlag'] == 'true' ? $row['notes'] : '<i class="help-block">' . lang('cashier.92') . '</i>';
				},
			),
			array(
				'select' => 'walletaccount.dwDateTime',
				'alias' => 'receivedOn',
			),
			array(
				'select' => 'walletaccount.transactionType',
				'alias' => 'transactionType',
			),
			array(
				'select' => 'walletaccount.is_checking',
				'alias' => 'is_checking',
			),
		);

		$table = 'playeraccount';
		$joins = array(
			'walletaccount' => 'walletaccount.playerAccountId = playeraccount.playerAccountId',
		);

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "walletaccount.dwDatetime BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		$where[] = 'walletaccount.transactionType = ? ';
		$values[] = 'withdrawal';

		$where[] = 'playeraccount.type = ?';
		$values[] = 'wallet';

		if ($player_id) {
			$where[] = "playeraccount.playerId = ? ";
			$values[] = $player_id;

		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	const SALE_ORDER_PAYMENT_KIND_DEPOSIT = 1;

	/**
	 * detail: get all deposits of a certain player
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function getAllDepositsWLimit($player_id = null) {

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(

			array(
				//'dt' => $i++,
				'select' => 'sale_orders.id',
				'alias' => 'id',

			),
			array(
				'dt' => $i++,
				'select' => 'sale_orders.created_at',
				'alias' => 'created_at',

			),
			array(
				'dt' => $i++,
				'select' => 'sale_orders.secure_id',
				'alias' => 'secure_id',

			),
			array(
				'dt' => $i++,
				'select' => 'sale_orders.payment_flag',
				'alias' => 'payment_flag',
				'formatter' => function ($d, $row) {
					switch ($row['payment_account_flag']) {
    					case MANUAL_ONLINE_PAYMENT:
    						$depositType = lang('pay.manual_online_payment');
    						break;
    					case AUTO_ONLINE_PAYMENT:
    						if ($row['system_id'] == MOBAO_PAYMENT_API) {
    							$depositType = lang($row['payment_type_name']);
    						} else {
    							$depositType = lang('pay.manual_online_payment');
    						}

    						break;
    					case LOCAL_BANK_OFFLINE:
    						$depositType = lang('pay.local_bank_offline');
    						break;
    					}

					return $depositType;
				},
			),
			array(
				'dt' => $i++,
				'select' => 'sale_orders.status',
				'alias' => 'status',
				'formatter' => function ($d, $row) {
					if ($row['status'] == '4') {
                        //webet request no to use Sett
						return lang('sale_orders.status.5');
					}
					return lang('sale_orders.status.' . $row['status']);
				},
			),

			array(
				'select' => 'system_id',
				'alias' => 'system_id',

			),
			array(
				'select' => 'sale_orders.payment_type_name',
				'alias' => 'payment_type_name',

			),

			array(
				'dt' => $i++,
				'select' => 'sale_orders.amount',
				'alias' => 'amount',
				'formatter' => 'currencyFormatter',
			),
			array(
				'select' => 'sale_orders.reason',
				'alias' => 'reason',

			),
			array(
				'select' => 'payment_account.flag',
				'alias' => 'payment_account_flag',

			),

		);

		$table = 'sale_orders';
		$joins = array(
			'payment_account' => 'payment_account.id = sale_orders.payment_account_id',
		);

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "sale_orders.created_at BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		if ($player_id) {
			$where[] = "sale_orders.player_id = ? AND sale_orders.payment_kind = ? ";
			$values[] = $player_id;
			$values[] = self::SALE_ORDER_PAYMENT_KIND_DEPOSIT;
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	/**
	 * detail: get all transfer history of a certain player
	 *
	 * @param int $player_id transactions from_id
	 * @return json
	 */
	public function getAllTransferHistoryByPlayerIdWLimit($player_id = null) {

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(

			array(
				'dt' => $i++,
				'select' => 'transactions.created_at',
				'alias' => 'requestDateTime',

			),
			array(
				'dt' => $i++,
				'select' => 'transactions.transaction_type',
				'alias' => 'transferFrom',
				'formatter' => function ($d, $row) {

					switch ($d) {
					//5
					case Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET:
						$wallet = lang('aff.action.mainwallet');
						break;
					//6
					case Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET:
						$wallet = $row['system_code'] . ' ' . lang('cashier.42');
						break;
					}

					return $wallet;
				},
			),
			# Baligtaran
			array(
				'dt' => $i++,
				'select' => 'transactions.transaction_type',
				'alias' => 'transferTo',
				'formatter' => function ($d, $row) {

					switch ($d) {
					//5
					case Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET:
						$wallet = $row['system_code'] . ' ' . lang('cashier.42');
						break;
					//6
					case Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET:
						$wallet = lang('aff.action.mainwallet');
						break;
					}

					return $wallet;
				},
			),

			array(
				'select' => 'external_system.system_code',
				'alias' => 'system_code',
			),

			array(
				'dt' => $i++,
				'select' => 'transactions.amount',
				'alias' => 'amount',
				'formatter' => 'currencyFormatter',
			),

		);

		$table = 'transactions';
		$joins = array(
			'external_system' => 'external_system.id = transactions.sub_wallet_id',

		);

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "transactions.created_at BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		if ($player_id) {
			$where[] = "transactions.from_type = ? AND transactions.from_id = ? AND transactions.transaction_type in (?,?) ";
			$values[] = Transactions::PLAYER;
			$values[] = $player_id;
			$values[] = Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET;
			$values[] = Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET;

		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	/**
	 * detail: get all transfer history of a certain player
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function getAllTransferHistoryByPlayerId($player_id = null) {

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(

			array(
				'select' => 'sale_orders.secure_id',
				'alias' => 'secure_id',
			),
			array(
				'select' => 'walletaccount.transactionCode',
				'alias' => 'transactionCode',
			),
			array(
				'select' => 'transactions.transaction_type',
				'alias' => 'transact_type',
			),
			array(
				'dt' => $i++,
				'select' => 'transactions.created_at',
				'alias' => 'requestDateTime',
			),
			array(
				'dt' => $i++,
				'select' => 'transactions.id',
				'alias' => 'transactionId',
				'formatter' => function ($d, $row) {
					if ($row['transact_type'] == Transactions::WITHDRAWAL) {
                        // withdrw
						return $row['transactionCode'];
					} else if ($row['transact_type'] == Transactions::DEPOSIT) {
                        //  deposit
						return $row['secure_id'];
					} else {
						return $d; // other transactions
					}

				},
			),
			array(
				'dt' => $i++,
				'select' => 'transactions.transaction_type',
				'alias' => 'transaction_type',
				'formatter' => function ($d, $row) {
					switch ($d) {
					//5
					case Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET:
						return lang('Main To Sub');
						break;
					case Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET:
						return lang('Sub To Main');
						break;
					case Transactions::DEPOSIT:
						return lang('Deposit');
						break;
					case Transactions::WITHDRAWAL:
						return lang('Withdraw');
						break;
					case Transactions::MANUAL_ADD_BALANCE:
						return lang('Manually Add Balance');
						break;
					case Transactions::MANUAL_SUBTRACT_BALANCE:
						return lang('Manually Minus Balance');
						break;
					case Transactions::AUTO_ADD_CASHBACK_TO_BALANCE:
						return lang('Cashback');
						break;
					case Transactions::ADD_BONUS:
					case Transactions::MEMBER_GROUP_DEPOSIT_BONUS:
					case Transactions::PLAYER_REFER_BONUS:
					case Transactions::RANDOM_BONUS:
						return lang('Bonus');
						break;
					}
				},
			),
			array(
				'dt' => $i++,
				'select' => 'transactions.sub_wallet_id',
				'alias' => 'sub_wallet_id',
				'formatter' => function ($d, $row) {
					$wallet = !empty($row['system_code']) ? $row['system_code'] . ' ' . lang('cashier.42') : lang('pay.mainwallt');
					return $wallet;
				},
			),
			array(
				'select' => 'external_system.system_code',
				'alias' => 'system_code',
			),
			array(
				'dt' => $i++,
				'select' => 'transactions.amount',
				'alias' => 'amount',
				'formatter' => 'currencyFormatter',
			),

		);

		$table = 'transactions';
		$joins = array(
			'external_system' => 'external_system.id = transactions.sub_wallet_id',
			'sale_orders' => "sale_orders.transaction_id = transactions.id",
			'walletaccount' => "walletaccount.transaction_id = transactions.id",

		);

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "transactions.created_at BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		if ($player_id) {
			$where[] = "transactions.to_type = ? AND transactions.to_id = ? AND transactions.transaction_type in (?,?,?,?,?,?,?) ";
			$values[] = Transactions::PLAYER;
			$values[] = $player_id;
			$values[] = Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET;
			$values[] = Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET;
			$values[] = Transactions::DEPOSIT;
			$values[] = Transactions::WITHDRAWAL;
			$values[] = Transactions::MANUAL_ADD_BALANCE;
			$values[] = Transactions::MANUAL_SUBTRACT_BALANCE;
			$values[] = Transactions::AUTO_ADD_CASHBACK_TO_BALANCE;

		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	/**
	 * detail: get bank details of a certain player
	 *
	 * @param int $player_id playerbankdetails playerId
	 * @param string $dwBank
	 * @param string $controllerName
	 *
	 * @return json
	 */
	public function getBankDetails($player_id = null, $dwBank, $controllerName) {

		$this->load->model(array('player'));
		$i = 0;
		$request = $this->input->post();

		$where = array();
		$values = array();

		$columns = array(
			array(
				'dt' => $i++,
				'select' => 'banktype.bankName',
				'alias' => 'bankName',
				'formatter' => function ($d, $row) {
					return lang($row['bankName']);
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerbankdetails.bankAccountFullName',
				'alias' => 'bankAccountFullName',
				'formatter' => function ($d, $row) {
					return ucwords($row['bankAccountFullName']);
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerbankdetails.bankAccountNumber',
				'alias' => 'bankAccountNumber',
			),
			array(
				'dt' => $i++,
				'select' => 'playerbankdetails.branch',
				'alias' => 'branch',
			),
			array(
				'dt' => $i++,
				'select' => 'playerbankdetails.bankAddress',
				'alias' => 'bankAddress',
				'formatter' => function ($d, $row) {
					return $row['bankAddress'] == '' ? '<i>No Record</i>' : $row['bankAddress'];
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerbankdetails.status',
				'alias' => 'status',
				'formatter' => function ($d, $row) {
					return $row['status'] == 0 ? '<span class="help-block" style="color:#46b8da;">Active</span>' : '<span class="help-block" style="color:#66cc66;">Inactive</span>';
				},
			),

			array(
				//'dt' => $i++,
				'select' => 'playerbankdetails.playerBankDetailsId',
				'alias' => 'playerBankDetailsId',
				'formatter' => function ($d, $row) {
					return $row['bankAddress'] == '' ? '<i>No Record</i>' : $row['bankAddress'];
				},
			),

			array(
				'dt' => $i++,
				'select' => 'playerbankdetails.isDefault',
				'alias' => 'isDefault',
				'formatter' => function ($d, $row) use ($controllerName, $dwBank, $player_id) {

					$playerHasBankDefault = $this->player->hasBankDefault($player_id, $dwBank);

					if ($dwBank == 0) {
						//Edit
						$addEditBank = '<a class="edit-bank-info"  href="' . site_url($controllerName . '/addEditBank') . '/' . 'deposit/' . $row['playerBankDetailsId'] . '">' . lang('cashier.99') . '</a>';
					} else {
						//Edit
						$addEditBank = '<a class="edit-bank-info"  href="' . site_url($controllerName . '/addEditWithdrawalBank') . '/' . 'withdrawal/' . $row['playerBankDetailsId'] . '">' . lang('cashier.99') . '</a>';
					}

					//Set Default
					$setDefaultLink = '<a class="bank-info"  href="' . site_url($controllerName . '/setDefaultBankDetails') . '/' . $row['playerBankDetailsId'] . '/1">' . lang('cashier.110') . '</a>';

					//Remove Default
					$removeDefaultLink = '<a class="bank-info"  href="' . site_url($controllerName . '/setDefaultBankDetails') . '/' . $row['playerBankDetailsId'] . '/0">' . lang('cashier.128') . '</a>';

					//Active baligtad naging zero
					$activeLink = '<a class="bank-info"  href="' . site_url($controllerName . '/changeBankStatus') . '/0/' . $row['playerBankDetailsId'] . '">' . lang('cashier.106') . '</a>';

					//Inactive
					$inactiveLink = '<a class="bank-info"  href="' . site_url($controllerName . '/changeBankStatus') . '/1/' . $row['playerBankDetailsId'] . '">' . lang('cashier.107') . '</a>';

					//Delete
					$deleteBankLink = '<a class="bank-info"  href="' . site_url($controllerName . '/deleteBankDetails') . '/' . $row['playerBankDetailsId'] . '">' . lang('cashier.108') . '</a>';

					if ($row['isDefault'] == 0 && $row['status'] == 0 && !$playerHasBankDefault) {
						return $setDefaultLink . ' | ' . $inactiveLink . ' | ' . $addEditBank . ' | ' . $deleteBankLink;

					}

					if ($row['isDefault'] == 1 && $playerHasBankDefault) {
						return $removeDefaultLink . ' | ' . $addEditBank;
					}

					if ($row['status'] == 1) {

						return $activeLink . ' | ' . $addEditBank . ' | ' . $deleteBankLink;
					}
					if ($row['status'] == 0) {
						return $inactiveLink . ' | ' . $addEditBank . '|' . $deleteBankLink;

					}

				},
			),
		);

		$table = 'playerbankdetails';
		$joins = array(
			'banktype' => ' banktype.bankTypeId = playerbankdetails.bankTypeId',
		);

		if ($player_id) {
			$where[] = "playerbankdetails.playerId = ? AND playerbankdetails.dwBank = ? AND playerbankdetails.isRemember = ?";
			$values[] = $player_id;
			$values[] = $dwBank;
			$values[] = '1';

		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	/**
	 *  detail: fetch data for player VIP level tree
	 *
	 *  @param string $vip_levels_str
	 *  @return JSON data for tree view
	 */
	function get_player_vip_level_tree($vip_levels_str = null) {
		$vip_levels = explode('_', $vip_levels_str);
		$playerLevel = $this->utils->getPlayerLvlTree(true);

		$tree_array = array();
		foreach ($playerLevel as $gpId => $playerLvlInfo) {
			$group_node = array(
				'id' => $gpId,
				'text' => $playerLvlInfo['groupName'],
				'icon' => 'fa fa-users',
			);
			foreach ($playerLvlInfo['playerLvlTree'] as $lvlInfo) {
				$level_id = $lvlInfo['playerLevelId'];
				if (!empty($vip_levels) && in_array($level_id, $vip_levels)) {
					$checked = true;
					$opened = true;
				} else {
					$checked = false;
					$opened = false;
				}

				$level_node = array(
					'id' => $gpId . '_' . $lvlInfo['playerLevelId'],
					'text' => $lvlInfo['playerLevelName'],
					'icon' => 'fa fa-user',
					'state' => ["checked" => $checked, "opened" => $opened],
				);
				$group_node['children'][] = $level_node;
			}
			$tree_array[] = $group_node;
		}
		$this->returnJsonResult($tree_array, false, '*', false);
	}

	/**
	 * detail: get active promo details of a certain player
	 *
	 * @param int $player_id playerpromo playerId
	 * @return json
	 */
	public function getPlayerActivePromoDetails($player_id) {

		if (empty($player_id)) {
			return null;
		}

		$this->load->model(array('promorules', 'player_promo'));
		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(

			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promoDetails',
				'alias' => 'promoDetails',
				'formatter' => function ($d, $row) {
					list($promoName, $promoType, $promoDetails) = $this->promorules->getPromoNameAndType(null, $row['transPromoTypeName'], $row['promoTypeName'], $row['promoName'], $row['promoDetails'], $row['vipLevelName']);
					$fullPromoDesc = $promoName;
					$promodetails = '<button type="button" class="btn btn-info btn-xs" >' . $fullPromoDesc . '</button>';

					return $promodetails;
				},
			),

			array(
				'dt' => $i++,
				'select' => 'playerpromo.dateApply',
				'alias' => 'dateApply',
			),

			array(
				'dt' => $i++,
				'select' => 'playerpromo.bonusAmount',
				'alias' => 'bonusAmount',
				'formatter' => 'currencyFormatter',

			),

			array(
				'dt' => $i++,
				'select' => 'promotype.promoTypeName',
				'alias' => 'promoTypeName',

			),

			array(
				'dt' => $i++,
				'select' => 'playerpromo.transactionStatus',
				'alias' => 'transactionStatus',
				'formatter' => function ($d, $row) {
					$status = lang('None');

					switch ($row['transactionStatus']) {

					case Player_promo::TRANS_STATUS_REQUEST:
						$status = '<span class="label label-info">' . lang('PENDING') . '</span>';
						break;
					case Player_promo::TRANS_STATUS_APPROVED:
						$status = '<span class="label label-success">' . lang('APPLIED') . '</span>';
						break;
					case Player_promo::TRANS_STATUS_DECLINED:
						$status = '<span class="label label-danger">' . lang('DECLINED') . '</span>';
						break;
					case Player_promo::TRANS_STATUS_EXPIRED:
						$status = '<span class="label label-warning">' . lang('EXPIRED') . '</span>';
						break;
					case Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS:
						$status = '<span class="label label-primary">' . lang('ACTIVE') . '</span>';
						break;
					case Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS:
						$status = '<span class="label label-primary">' . lang('LOCKED BONUS') . '</span>';
						break;
					case Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION:
						$status = '<span class="label label-primary">' . lang('Finished') . '</span>';
						break;

					}

					return $status;
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.withdrawConditionAmount',
				'alias' => 'withdrawConditionAmount',
				'formatter' => function ($d, $row) {
					return $d ?: lang('N/A');
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.playerId',
				'alias' => 'playerPromoPlayerId',
				'formatter' => function ($d, $row) {
					return lang('N/A');
				},
			),
			array(
				'select' => 'promocmssetting.promo_code',
				'alias' => 'promoCode',
			),
			array(
				'select' => 'promorules.promoName',
				'alias' => 'promoName',
			),
			array(
				'select' => 'playerpromo.playerpromoId',
				'alias' => 'playerpromoId',
			),
			array(
				'select' => 'vipsettingcashbackrule.vipLevelName',
				'alias' => 'vipLevelName',
			),
			array(
				'select' => 'promotype.promoTypeName',
				'alias' => 'transPromoTypeName',

			),
		);

		$table = 'playerpromo';
		$joins = array(
			'promorules' => 'promorules.promorulesId = playerpromo.promorulesId',
			'promotype' => 'promotype.promotypeId = promorules.promoCategory',
			'promocmssetting' => 'playerpromo.promoCmsSettingId = promocmssetting.promoCmsSettingId',
			'player' => 'player.playerId = playerpromo.playerId',
			'vipsettingcashbackrule' => 'vipsettingcashbackrule.vipsettingcashbackruleId = playerpromo.level_id',
		);

		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {

			$where[] = "playerpromo.dateApply BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}

		if ($player_id) {
			$where[] = "playerpromo.playerId = ? ";
			$values[] = $player_id;

		}
		$where[] = "playerpromo.transactionStatus != ? ";
		$values[] = Player_promo::TRANS_STATUS_DECLINED_FOREVER;

		$where[] = "promorules.promoName != ?";
		$values[] = Promorules::SYSTEM_MANUAL_PROMO_RULE_NAME;

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	/**
	 * detail: get eligible promo of a certain player
	 *
	 * @param int $player_id player id
	 * @return json
	 */
	public function getPlayerEligiblePromo($player_id) {
		$this->load->model(array('promorules', 'player_model', 'player_promo'));
		$this->load->library(array('language_function'));
		$language = $this->language_function->getCurrentLangForPromo();
		$player = $this->player_model->getPlayerById($player_id);

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(

			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promoName',
				'alias' => 'promoName',
			),

			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promoDetails',
				'alias' => 'promoDetails',
				'formatter' => function ($d, $row) {
					//  $promodetails = '<a tabindex="0" href="#" type="button" data-trigger="focus" role="button" class="btn btn-default btn-sm" data-toggle="popover" title="'.$row['promoName'].'"  >'.lang('tool.cms05').'</a>';
					$promodetails = '<button type="button" class="btn btn-info btn-xs" data-toggle="collapse" onclick="informIframeParent();" data-target="#promodetail' . $row['promoCmsSettingId'] . '">' . lang('tool.cms05') . '</button>';
					$promodetails .= '<div class="collapse" id="promodetail' . $row['promoCmsSettingId'] . '"><br><br>' . $row['promoDetails'] . '</div>';

					return $promodetails;
				},
			),

			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promo_code',
				'alias' => 'promocode',
				'formatter' => function ($d, $row) {
					return '<a class="btn btn-sm btn-primary promodetails"  href="' . site_url('player_center/show_promo') . '/' . $d . '"   >' . lang('cms.promocode') . ': ' . $d . '</a>';
				},
			),
			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promoId',
				'alias' => 'playerIsAllowed',
				'formatter' => function ($d, $row) use ($player) {
					$attributes = ' promocmsettingid= "' . $row['promoCmsSettingId'] . '" ';
					$disabled = '';
					$actionBtn = '<a href="javascript:void(0)" ' . $attributes . '  class="btn btn-primary btn-sm autopromo_accept" ' . $disabled . ' >' . lang('Accept') . '</a> ';
					$actionBtn .= '<a href="javascript:void(0)"  ' . $attributes . ' class="btn btn-danger btn-sm autopromo_decline" ' . $disabled . ' >' . lang('lang.decline') . '</a> ';
					return $actionBtn;
				},
			),
			array(
				'select' => 'promocmssetting.promoCmsSettingId',
				'alias' => 'promoCmsSettingId',

			),
			array(
				'select' => 'promocmssetting.promoDescription',
				'alias' => 'promoDescription',
			),

			array(
				'select' => 'promocmssetting.promoId',
				'alias' => 'promoId',
			),

			array(
				'select' => 'promorules.bonusReleaseToPlayer',
				'alias' => 'bonusReleaseToPlayer',
			),

			array(
				'select' => 'playerpromo.playerpromoId',
				'alias' => 'playerpromoId',
			),
		);

		$table = 'promocmssetting';
		$joins = array(
			'promorules' => 'promorules.promorulesId = promocmssetting.promoId',
			'playerpromo' => 'playerpromo.promoCmsSettingId = promocmssetting.promoCmsSettingId and playerpromo.transactionStatus =' . Player_promo::TRANS_STATUS_DECLINED_FOREVER,
			'promorulesallowedplayerlevel' => 'promorulesallowedplayerlevel.promoruleId=promorules.promorulesId and promorulesallowedplayerlevel.playerLevel = ' . $player->levelId,
			'promorulesallowedplayer' => 'promorulesallowedplayer.promoruleId=promorules.promorulesId and promorulesallowedplayer.playerId = ' . $player->playerId,
		);

		$affQry = '';
		if (!empty($player->affiliateId)) {
			$affQry = 'or promorulesallowedaffiliate.promoruleId is not null';
			$joins['promorulesallowedaffiliate'] = 'promorulesallowedaffiliate.promoruleId=promorules.promorulesId and promorulesallowedaffiliate.affiliateId=' . $player->affiliateId;
		}

		$where[] = "promocmssetting.language = ? ";
		$values[] = $language;
		$where[] = "promocmssetting.status = ? ";
		$values[] = 'active';
		if (!empty($player->affiliateId)) {
			//check allow
			$where[] = " (promorulesallowedplayerlevel.promoruleId is not null or promorulesallowedplayer.promoruleId is not null " . $affQry . ") ";
		}
		$where[] = "promorules.hide_date >= ? ";
		$values[] = $this->utils->getNowForMysql();
		$where[] = "promorules.bonusReleaseToPlayer = " . Promorules::BONUS_RELEASE_TO_PLAYER_AUTO;
		$where[] = "playerpromo.playerpromoId IS NULL";

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	/**
	 * detail: get available promo of a certain player
	 *
	 * @param int $player_id player id
	 * @return json
	 */
	public function getPlayerAvailPromo($player_id) {
		$this->load->model(array('promorules', 'player_model', 'player_promo'));
		$this->load->library(array('language_function'));
		$language = $this->language_function->getCurrentLangForPromo();
		$player = $this->player_model->getPlayerById($player_id);

		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(

			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promoName',
				'alias' => 'promoName',
			),

			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promoDetails',
				'alias' => 'promoDetails',
				'formatter' => function ($d, $row) {

					$promocode = !empty($row['promocode']) ? lang('cms.promocode') . ': ' . $row['promocode'] : "";

					//  $promodetails = '<a tabindex="0" href="#" type="button" data-trigger="focus" role="button" class="btn btn-default btn-sm" data-toggle="popover" title="'.$row['promoName'].'"  >'.lang('tool.cms05').'</a>';
					$promodetails = '<button type="button" class="btn btn-info btn-xs" data-toggle="collapse" onclick="informIframeParent();" data-target="#promodetail' . $row['promoCmsSettingId'] . '">' . lang('tool.cms05') . '</button>';
					$promodetails .= '<div class="collapse" id="promodetail' . $row['promoCmsSettingId'] . '"><br><br>' . $row['promoDetails'] . '</div> ' . $promocode;

					return $promodetails;
				},
			),

			array(
				// 'dt' => $i++,
				'select' => 'promocmssetting.promo_code',
				'alias' => 'promocode',
				// 'formatter' => function ($d, $row) {
				// 	return '<a class="btn btn-sm btn-primary promodetails"  href="'.site_url('player_center/show_promo').'/'.$d.'"   >'.lang('cms.promocode') . ': ' .$d.'</a>';
				// }
			),
			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promoId',
				'alias' => 'playerIsAllowed',
				'formatter' => function ($d, $row) use ($player) {

					$actionBtn = '<a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="requestPromo(' . $row['promoCmsSettingId'] . ')" >' . lang('Join') . '</a> ';
					return $actionBtn;

				},
			),
			array(
				//	'dt' => $i++,
				'select' => 'promocmssetting.promoCmsSettingId',
				'alias' => 'promoCmsSettingId',

			),
			array(
				//	'dt' => $i++,
				'select' => 'promocmssetting.promoDescription',
				'alias' => 'promoDescription',
			),

			array(
				//'dt' => $i++,
				'select' => 'promocmssetting.promoId',
				'alias' => 'promoId',
			),

			array(
				//'dt' => $i++,
				'select' => 'promorules.bonusReleaseToPlayer',
				'alias' => 'bonusReleaseToPlayer',
			),

			array(
				//'dt' => $i++,
				'select' => 'playerpromo.playerpromoId',
				'alias' => 'playerpromoId',
			),
		);

		$table = 'promocmssetting';
		$joins = array('promorules' => 'promorules.promorulesId = promocmssetting.promoId',
			'playerpromo' => 'playerpromo.promoCmsSettingId = promocmssetting.promoCmsSettingId and playerpromo.transactionStatus =' . Player_promo::TRANS_STATUS_DECLINED_FOREVER);

		$where[] = "promocmssetting.language = ? ";
		$values[] = $language;
		$where[] = "promocmssetting.status = ? ";
		$values[] = 'active';
		$where[] = "promorules.hide_date >= ? ";
		$values[] = $this->utils->getNowForMysql();
		// $where[] = "promorules.bonusReleaseToPlayer = " . Promorules::BONUS_RELEASE_TO_PLAYER_AUTO;
		$where[] = "playerpromo.playerpromoId IS NULL";

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$this->returnJsonResult($result);

	}

	/**
	 * detail: generate admin support live chats
	 *
	 * @return js files
	 */
	public function generateAdminSupportLiveChatJS() {
		$this->load->library(array('permissions', 'authentication'));
		$this->permissions->setPermissions();

		if ($this->permissions->checkPermissions('admin_support_live_chat')) {
			return $this->error_access();
		}

		$userId = $this->authentication->getUserId();
		$username = $this->authentication->getUsername();
		$linkurl = $this->utils->getSystemUrl('admin') . '/user_management/viewUser/' . $userId;

		require_once __DIR__ . '/../libraries/lib_livechat.php';

		$userInfo = array('username' => $username, 'linkurl' => $linkurl);
		$chat_options = $this->utils->getConfig('admin_support_live_chat');
		$chat_options['www_chat_options']['onlylink'] = $onlylink; //overwrite
		$js = Lib_livechat::getChatJs($chat_options, $userInfo);

		$this->returnJS($js);
	}

	public function transaction_request_notification() {

		$result = false;
		$this->load->model(array('notification_setting', 'player'));
		$this->load->library(array('permissions'));

	    $show_notification_data = $this->utils->getSBENotificationCount();

        $withdrawal_request = 0;
        if(isset($show_notification_data['notificatons']['withdrawal_request'])){
            $withdrawal_request = $show_notification_data['notificatons']['withdrawal_request'];
        }

        $messages = 0;
        if(isset($show_notification_data['notificatons']['messages'])){
            $messages = $show_notification_data['notificatons']['messages'];
        }

		$local_deposit = 0;
        if(isset($show_notification_data['notificatons']['deposit_list']['bank_deposit'])){
            $local_deposit = $show_notification_data['notificatons']['deposit_list']['bank_deposit'];
        }

		$thrdpartydeposit = 0;
        if(isset($show_notification_data['notificatons']['deposit_list']['thirdparty'])){
            $thrdpartydeposit = $show_notification_data['notificatons']['deposit_list']['thirdparty'];
        }

        $promo = 0;
        if(isset($show_notification_data['notificatons']['promo'])){
            $promo = $show_notification_data['notificatons']['promo'];
        }

        $thrdpartySettled = 0;
        if(isset($show_notification_data['notificatons']['deposit_list']['thirdparty_settled'])){
            $thrdpartySettled = $show_notification_data['notificatons']['deposit_list']['thirdparty_settled'];
        }

        $affiliate_withdraw_request = 0;
        if(isset($show_notification_data['notificatons']['affiliate_withdraw_request'])){
            $affiliate_withdraw_request = $show_notification_data['notificatons']['affiliate_withdraw_request'];
        }

        $new_player = 0;
        if(isset($show_notification_data['notificatons']['new_player'])){
            $new_player = $show_notification_data['notificatons']['new_player'];
        }

        $all_new_games = 0;
        if(isset($show_notification_data['notificatons']['new_games'])){
            $all_new_games = $show_notification_data['notificatons']['new_games'];
        }

        $agent_withdraw_request = 0;
        if(isset($show_notification_data['notificatons']['agent_withdraw_request'])){
            $agent_withdraw_request = $show_notification_data['notificatons']['agent_withdraw_request'];
        }

        $self_exclusion_request = 0;
        if(isset($show_notification_data['notificatons']['self_exclusion_request'])){
            $self_exclusion_request = $show_notification_data['notificatons']['self_exclusion_request'];
        }

        $new_player_attachment_count = 0;
        if(isset($show_notification_data['notificatons']['new_player_attachment'])){
            $new_player_attachment_count = $show_notification_data['notificatons']['new_player_attachment'];
        }

		$total_request = $show_notification_data['sum_notif'];

		$notification = array();
		$notifications = $this->notification_setting->getNotification();
		foreach ($notifications as $key => $value) {
			$notification[$value['notification_type']] = $value['file'];
		}

		$result = array(
			// 'deposit_request' => $deposit_request,
			'withdrawal_request' => $withdrawal_request,
			'messages' => $messages,
			'local_deposit' => $local_deposit,
			'thrdpartydeposit' => $thrdpartydeposit,
			'thrdpartySettled' => $thrdpartySettled,
			'promo' => $promo,
			'total_request' => $total_request,
			'affiliate_withdraw_request' => $affiliate_withdraw_request,
			'notification' => $notification,
			'new_player' => $new_player,
			'new_games' => $all_new_games,
            'agent_withdraw_request' => $agent_withdraw_request,
            'self_exclusion_request' => $self_exclusion_request,
            'new_player_attachment_count' => $new_player_attachment_count,
		);

		$this->returnJsonResult($result);
	}

	public function withdrawalCount($status, $today = '') {

		$today_date = date("Y-m-d");

		$setting = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();

		$allStatus = array(
			'request' => lang('pay.penreq'),
		);
		for ($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
			if ($setting[$i]['enabled']) {
				$allStatus["CS$i"] = lang($setting[$i]['name']);
			}
		}
		if ($setting['payProc']['enabled']) {
			$allStatus['payProc'] = lang('pay.processing');
		}
		$allStatus['paid'] = lang('lang.paid');
		$allStatus['declined'] = lang('pay.decreq');

		$statusCountResult = $this->payment_manager->getDWCountAllStatus('withdrawal');

		echo "<pre>";
		print_r($statusCountResult);
		exit;

		$statusCount = array();
		foreach ($statusCountResult as $row) {
			$statusCount[$row['dwStatus']] = intval($row['count']);
		}

		$statusCountTodayResult = $this->payment_manager->getDWCountAllStatus('withdrawal', $today_date, $today_date);

		$statusCountToday = array();
		foreach ($statusCountTodayResult as $row) {
			$statusCountToday[$row['dwStatus']] = intval($row['count']);
		}

		$result = array_merge($statusCount, $statusCountToday);

		$result = (isset($result[$status])) ? $result[$status] : 0;

		return $this->returnJsonResult(array('count' => $result));

	}

	public function depositCount() {

		$isSettingExist = $this->operatorglobalsettings->getSetting('deposit_count_list');
		$depositCountList = isset($isSettingExist->value) ? $isSettingExist->value : Payment_management::DEFAULT_DEPOSIT_COUNT_SETTING;

		if ($depositCountList == Payment_management::DEPOSIT_THIS_WEEK) {
			$depositStartDate = date("Y-m-d", strtotime('monday this week')) . ' 00:00:00';
			$depositEndDate = date("Y-m-d", strtotime('sunday this week')) . ' 23:59:59';
		} elseif ($depositCountList == Payment_management::DEPOSIT_THIS_MONTH) {
			$depositStartDate = date('Y-m-01 00:00:00', strtotime('this month'));
			$depositEndDate = date('Y-m-t 12:59:59', strtotime('this month'));
		} elseif ($depositCountList == Payment_management::DEPOSIT_THIS_YEAR) {
			$depositStartDate = date('Y-01-01 00:00:00', strtotime('this year'));
			$depositEndDate = date('Y-12-t 12:59:59', strtotime('this year'));
		} elseif ($depositCountList == Payment_management::DEPOSIT_TOTAL_ALL) {
			$depositStartDate = '';
			$depositEndDate = '';
		}

		$start_today = date("Y-m-d") . ' 00:00:00';
		$end_today = date("Y-m-d") . ' 23:59:59';

		$data = array(
			'deposit_request_cnt' => $this->utils->formatInt(
				$this->sale_order->countSaleOrders(Sale_order::PAYMENT_KIND_DEPOSIT, Sale_order::VIEW_STATUS_REQUEST, $depositStartDate, $depositEndDate)),
			'deposit_request_cnt_today' => $this->utils->formatInt(
				$this->sale_order->countSaleOrders(Sale_order::PAYMENT_KIND_DEPOSIT, Sale_order::VIEW_STATUS_REQUEST, $start_today, $end_today)),
			'deposit_approved_cnt' => $this->utils->formatInt(
				$this->sale_order->countSaleOrders(Sale_order::PAYMENT_KIND_DEPOSIT, Sale_order::VIEW_STATUS_APPROVED, $depositStartDate, $depositEndDate)),
			'deposit_approved_cnt_today' => $this->utils->formatInt(
				$this->sale_order->countSaleOrders(Sale_order::PAYMENT_KIND_DEPOSIT, Sale_order::VIEW_STATUS_APPROVED, $start_today, $end_today)),
			'deposit_declined_cnt' => $this->utils->formatInt(
				$this->sale_order->countSaleOrders(Sale_order::PAYMENT_KIND_DEPOSIT, Sale_order::VIEW_STATUS_DECLINED, $depositStartDate, $depositEndDate)),
			'deposit_declined_cnt_today' => $this->utils->formatInt(
				$this->sale_order->countSaleOrders(Sale_order::PAYMENT_KIND_DEPOSIT, Sale_order::VIEW_STATUS_DECLINED, $start_today, $end_today)),
		);

		echo "<pre>";
		print_r($data);
		exit;

	}

	/**
	 * detail: get cancelled withdrawal condition for a certain player
	 *
	 * @param int $player_id withdraw_conditions player_id
	 * @return json
	 */
	public function cancelled_withdrawal($player_id = '') {

		$this->load->model(array('withdraw_condition'));

		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'wc_id',
				'select' => 'wc_id',
				'formatter' => function ($d, $row) {
					return "<input type='checkbox' name='chkWC' id='chkWC$d' value='$d' class='chk-wc-item' />";
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'transaction_type',
				'select' => 'transaction_type',
			),
			array(
				'dt' => $i++,
				'alias' => 'promoName',
				'select' => 'promocmssetting.promoName',
			),
			array(
				'dt' => $i++,
				'alias' => 'promo_code',
				'select' => 'promocmssetting.promo_code',
			),
			array(
				'dt' => $i++,
				'alias' => 'deposit_amount',
				'select' => 'withdraw_conditions.deposit_amount',
				'formatter' => function ($d, $row) {
					return number_format($row['deposit_amount'], 2, '.', ',');
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'bonus_amount',
				'select' => 'withdraw_conditions.bonus_amount',
				'formatter' => function ($d, $row) {
					return number_format($row['bonus_amount'], 2, '.', ',');
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'started_at',
				'select' => 'withdraw_conditions.started_at',
			),
			array(
				'dt' => $i++,
				'alias' => 'condition_amount',
				'select' => 'withdraw_conditions.condition_amount',
				'formatter' => function ($d, $row) {
					return number_format($row['condition_amount'], 2, '.', ',');
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'notes',
				'select' => 'withdraw_conditions.note',
			),
			array(
				'dt' => $i++,
				'alias' => 'bet_amount',
				'select' => 'withdraw_conditions.bet_amount',
				'formatter' => function ($d, $row) {
					return number_format($row['bet_amount'], 2, '.', ',');
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'updated_at',
				'select' => 'withdraw_conditions.updated_at',
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'adminusers.username',
			),
			array(
				'dt' => $i++,
				'alias' => 'detail_status',
				'select' => 'withdraw_conditions.detail_status',
				'formatter' => function ($d, $row) {
					switch($d) {
						case Withdraw_condition::DETAIL_STATUS_FINISHED_BETTING_AMOUNT_WHEN_DEPOSIT:
							return lang('Finished betting amount when deposit');
							break;
                        case Withdraw_condition::DETAIL_STATUS_CANCELLED_MANUALLY:
							return lang('Cancelled Manually');
							break;
                        case Withdraw_condition::DETAIL_STATUS_CANCELLED_DUE_TO_SMALL_BALANCE:
							return lang('Cancelled due to small balance');
                        case Withdraw_condition::DETAIL_STATUS_FINISHED_BETTING_AMOUNT_WHEN_WITHDRAW:
							return lang('Finished betting amount when withdraw');
                        case Withdraw_condition::DETAIL_STATUS_FINISHED_BY_DELETING_PROMO_MANAGER:
                            return lang('Finished by deleting promo manager');
                        case Withdraw_condition::DETAIL_STATUS_CANCELED_BY_DELETING_PROMO_MANAGER:
                            return lang('Canceled by deleting promo manager');
						default :
							// try to catch old wc data because default was set to 1 which is active
							return lang('Finished betting amount when deposit');
					}
				},
			),
		);

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "withdraw_conditions.started_at >=";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "withdraw_conditions.started_at <=";
			$values[] = $input['dateRangeValueEnd'];
		}

		$data = $this->withdraw_condition->getPlayerCancelledWithdrawalCondition($player_id, $where, $values);

		$result = $this->data_tables->_prepareDataForLists($columns, $data);

		$this->returnJsonResult($result);

	}

    /**
     * detail: get cancelled transfer condition for a certain player
     *
     * @param int $player_id transfer_conditions player_id
     * @return json
     */
    public function cancelled_transfer_condition($player_id = '') {

        $this->load->model(array('transfer_condition'));

        $i = 0;
        $columns = array(
            array(
                'dt' => $i++,
                'alias' => 'tc_id',
                'select' => 'tc_id',
                'formatter' => function ($d, $row) {
                    return "<input type='checkbox' name='chkTC' id='chkTC$d' value='$d' class='chk-tc-item' />";
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'promoName',
                'select' => 'promorules.promoName',
            ),
            array(
                'dt' => $i++,
                'alias' => 'bonus_amount',
                'select' => 'playerpromo.bonusAmount',
                'formatter' => function ($d, $row) {
                    return number_format($row['bonus_amount'], 2, '.', ',');
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'started_at',
                'select' => 'transfer_condition.started_at',
            ),
            array(
                'dt' => $i++,
                'alias' => 'condition_amount',
                'select' => 'transfer_condition.condition_amount',
                'formatter' => function ($d, $row) {
                    return number_format($row['condition_amount'], 2, '.', ',');
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'currentBet',
                'select' => 'currentBet',
                'formatter' => function ($d, $row) {
                    return number_format($row['currentBet'], 2, '.', ',');
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'updated_at',
                'select' => 'transfer_condition.updated_at',
            ),
            array(
                'dt' => $i++,
                'alias' => 'username',
                'select' => 'adminusers.username',
            ),
            array(
                'dt' => $i++,
                'alias' => 'notes',
                'select' => 'transaction_notes.note',
            ),
            array(
                'dt' => $i++,
                'alias' => 'status',
                'select' => 'transfer_condition.status',
                'formatter' => function ($d, $row) {
                    return lang('Cancelled Manually');
                },
            ),
        );

        $request = $this->input->post();
        $input = $this->data_tables->extra_search($request);

        if (isset($input['dateRangeValueStart'])) {
            $where[] = "transfer_conditions.started_at >=";
            $values[] = $input['dateRangeValueStart'];
        }

        if (isset($input['dateRangeValueEnd'])) {
            $where[] = "transfer_conditions.started_at <=";
            $values[] = $input['dateRangeValueEnd'];
        }

        $data = $this->transfer_condition->getPlayerCancelledTransferCondition($player_id, $where, $values);

        if ( $data > 0){
            $result = $this->data_tables->_prepareDataForLists($columns, $data);
        } else {
            $result = $this->data_tables->empty_data($request);
        }

        $this->returnJsonResult($result);

    }

	/**
	 * detail: get Adjustment History for a certain player
	 *
	 * @param int $player_id adjustment_history_tab player_id
	 * @return json
	 */
	public function adjustment_history_tab($player_id = '') {

		$this->load->model(array('responsible_gaming'));
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'adjustedOn',
				'select' => 'balanceadjustmenthistory.adjustedOn',
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'p.username',
			),
			array(
				'dt' => $i++,
				'alias' => 'walletType',
				'select' => 'balanceadjustmenthistory.walletType',
				'formatter' => function ($d, $row) {
					if ($d == 0) {
						return 'Main';
					} else {
						if ($d == $row['esID']) {
							if ($row['esSysType'] == 1 AND $row['esStatus'] == 1) {
								return $row['esSysCode'];
							}
						} else {
							return "";
						}
					}
				},
		    ),
		    array(
				'dt' => $i++,
				'alias' => 'adjustmentType',
				'select' => 'balanceadjustmenthistory.adjustmentType',
				'formatter' => function ($d, $row) {
					return lang('transaction.transaction.type.' . $d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'amountChanged',
				'select' => 'balanceadjustmenthistory.amountChanged',
				'formatter' => function ($d, $row) {
					return number_format($d, 2);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'oldBalance',
				'select' => 'balanceadjustmenthistory.oldBalance',
				'formatter' => function ($d, $row) {
					return number_format($d, 2);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'newBalance',
				'select' => 'balanceadjustmenthistory.newBalance',
				'formatter' => function ($d, $row) {
					return number_format($d, 2);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'adjusted by',
				'select' => 'au.username',
			),
			array(
				'dt' => $i++,
				'alias' => 'reason',
				'select' => 'balanceadjustmenthistory.reason',
			),
		);

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "bah.adjustedOn >=";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "bah.adjustedOn <=";
			$values[] = $input['dateRangeValueEnd'];
		}

		$data = $this->payment->viewAdjustmentHistoryTab($player_id, $where, $values);
        // $data = $this->responsible_gaming->getResponsibleGData($player_id,$where,$values);

		if ( $data > 0){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}
		$this->returnJsonResult($result);
	}

	/**
	 * Retrieve Player's Adjustment History V2
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function adjustment_history_tab_v2($player_id = '') {

		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'created_at',
				'select' => 'tr.created_at',
			),
			array(
				'dt' => $i++,
				'alias' => 'to_username',
				'select' => 'tr.to_username',
			),
			array(
				'dt' => $i++,
				'alias' => 'system_code',
				'select' => 'es.system_code',
				'formatter' => function ($d, $row) {
					return $d ?: lang('player.uab07');
				},
		    ),
		    array(
				'dt' => $i++,
				'alias' => 'transaction_type',
				'select' => 'tr.transaction_type',
				'formatter' => function ($d, $row) {
					return lang('transaction.transaction.type.' . $d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'tr.amount',
				'formatter' => function ($d, $row) {
					return number_format($d, 2);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'before_balance',
				'select' => 'tr.before_balance',
				'formatter' => function ($d, $row) {
					return number_format($d, 2);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'after_balance',
				'select' => 'tr.after_balance',
				'formatter' => function ($d, $row) {
					return number_format($d, 2);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'au.username',
				'formatter' => function ($d, $row) {
					return !$d ? $row['from_username'] : $d;
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'note',
				'select' => 'tr.note',
			),
		);

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "tr.created_at >=";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "tr.created_at <=";
			$values[] = $input['dateRangeValueEnd'];
		}

		$data = $this->payment->viewAdjustmentHistoryTabV2($player_id, $where, $values);

		if ( $data > 0){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}
		$this->returnJsonResult($result);
	}


	function activePlayers() {

		$start = (isset($_GET['date_form'])) ? $_GET['date_form'] : '';
		$end = (isset($_GET['date_to'])) ? $_GET['date_to'] : '';
		$view_type = $_GET['view_type'];

		$this->load->library(array('data_tables'));
		$this->load->model(array('external_system', 'total_player_game_day'));
		$game_provider = $this->external_system->getAllGameApis();
        $i=0;
		$columns = array();
		$columns[] = array(
			'dt' => $i++,
			'select' => 'date',
			'alias' => 'date',
		);
		$columns[] = array(
			'dt' => $i++,
			'select' => 'total_players',
			'alias' => 'total_players',
			'formatter' => function ($d, $row) use ($view_type) {

				switch ($view_type) {
				case 'monthly':
					$start_date = date('Y-m-d', strtotime($row['date']));
					$end_date = date('Y-m-t', strtotime($row['date']));
					break;
				case 'weekly':
					$date = explode(" - ", $row['date']);
					$start = DateTime::createFromFormat('d/m/Y', $date[0]);
					$end = DateTime::createFromFormat('d/m/Y', $date[1]);
					$start_date = $start->format('Y-m-d');
					$end_date = $end->format('Y-m-d');
					break;
				default:
					$start_date = $row['date'];
					$end_date = $row['date'];
					break;
				}

				if ($d > 0) {
					return '<a target="_blank" href=" ' . site_url('report_management/viewTotalActivePlayers?date_start=' . $start_date . '&date_end=' . $end_date) . '">' . $d . '</a>';
				}

				return $d;

			},
		);

		foreach ($game_provider as $key => $value) {

			$columns[] = array(
				'dt' => $i++,
				'select' => $value['id'],
				'alias' => $value['id'],
				'formatter' => function ($d, $row) use ($view_type, $value) {
					switch ($view_type) {
					case 'monthly':
						$start_date = date('Y-m-d', strtotime($row['date']));
						$end_date = date('Y-m-t', strtotime($row['date']));
						break;
					case 'weekly':
						$date = explode(" - ", $row['date']);
						$start = DateTime::createFromFormat('d/m/Y', $date[0]);
						$end = DateTime::createFromFormat('d/m/Y', $date[1]);
						$start_date = $start->format('Y-m-d');
						$end_date = $end->format('Y-m-d');
						break;
					default:
						$start_date = $row['date'];
						$end_date = $row['date'];
						break;
					}

					$game_platform_id = $value['id'];
					if ($d > 0) {
						$d = '<a target="_blank" href=" ' . site_url('report_management/viewTotalActivePlayers?date_start=' . $start_date . '&date_end=' . $end_date . '&game_platform_id=' . $game_platform_id) . '">' . $d . '</a>';
					}
					return $d;
				},
			);
		}

        $_data = array();

        if($_GET['view_type'] == 'monthly'){
            $result = $this->total_player_game_day->getActivePlayerCountMonthly($start, $end, $_GET);
        }else{
            $result = $this->total_player_game_day->getActivePlayerCountPerPlayerId($start, $end, $_GET);
            foreach ($result as $key => $value) {
                $_data[$value['created_at']][$value['game_platform_id']] = $value['active_players'];
                $_data[$value['created_at']]['game_platform_id'] = $value['game_platform_id'];
            }
        }

		switch ($_GET['view_type']) {
		case 'weekly':
			$_data = $this->_weekly($start, $end, $_data);
			break;
		case 'monthly':
			$_data = $this->_monthly($start, $end, $result);
			break;
		default:
			$_data = $this->_daily($start, $end, $_data);
		}

		$list = $this->_compile_result($_data, $game_provider, $_GET['view_type']);

		$result = $this->data_tables->_prepareDataForLists($columns, $list);

		$this->returnJsonResult($result);

	}

	function _compile_result($_data, $game_provider, $view_type = null) {

		$result = array();

		$total_players = 0;
		$ctr = 0;
		foreach ($_data as $key => $value) {
			(isset($value['val'])) ? $total_players = $value['val'] : $total_players = 0;
			(isset($value['game_platform_id'])) ? $game_platform_id = $value['game_platform_id'] : $game_platform_id = 0;

			$_datas = array(
				'id' => $ctr,
				'date' => $key,
				'total_players' => $total_players,
				'game_platform_id' => $game_platform_id,
			);

			if (!empty($game_provider)) {
				foreach ($game_provider as $key => $val) {

					$total = 0;
					if (isset($value[$val['id']])) {
						$total = $value[$val['id']];
					}

					$active_players = !empty($total['active_players']) ? $total['active_players'] : 0;
                    $_datas[$val['id']] = ($view_type == 'weekly') ? $active_players : $total;

				}
			}

			$result[] = $_datas;

			$ctr++;

		}

		return $result;

	}

	function _daily($start, $end, $data = array()) {

		$start_time = strtotime($start);
		$end_time1 = strtotime($end);
		$end_time1 = ($end_time1 + 86400);

		$result = array();
		for ($i = $start_time; $i < $end_time1; $i += 86400) {

			$day = date('Y-m-d', $i);
			$result[$day] = array();

			foreach ($data as $key => $value) {
				if ($key != $day) {
					continue;
				}

				$list_of_active_players = $this->total_player_game_day->getActivePlayersByDate($key, $key);
                $result[$key]['val'] = count($list_of_active_players);

				foreach ($value as $idx => $val) {
					$result[$day][$idx] = $val;
				}

			}

		}
		return $result;

	}

	function _weekly($start, $end, $data = array()) {

		$start = new DateTime($start);
		$end = new DateTime($end);
		$end->modify('+1 day');
		$interval = new DateInterval('P1D');
		$dateRange = new DatePeriod($start, $interval, $end);

		$weekNumber = 1;
		$weeks = array();
		foreach ($dateRange as $date) {
			$weeks[$weekNumber][] = $date->format('Y-m-d');
			if ($date->format('w') == 0) {
				$weekNumber++;
			}
		}

		$ranges = array_map(function ($week) {
			return array(
				'start' => array_shift($week),
				'end' => array_pop($week),
			);
		}, $weeks);

		$result = array();

		foreach ($ranges as $key => $value) {

			$_idx_start = date('d/m/Y', strtotime($value['start']));
            if($value['end'] == null){
                $value['end']= $value['start'];
            }
            $_idx_end = date('d/m/Y', strtotime($value['end']));

            if (strtotime($value['start']) > strtotime($value['end'])) {
				continue;
			}

			$result[$_idx_start . ' - ' . $_idx_end] = array();

            $start_date = strtotime($value['start']);
			$end_date = strtotime($value['end']);

			if (!empty($data)) {

                foreach ($data as $idx => $val) {

                    $created_at = strtotime($idx);

                    if ($created_at >= $start_date && $created_at <= $end_date) {

                        $date_range = $_idx_start . ' - ' . $_idx_end;

                        // $result[$date_range]['val'] = $this->total_player_game_day->getActivePlayersCountByDate($value['start'], $value['end']);

                        $list_of_active_players = $this->total_player_game_day->getActivePlayersByDate($value['start'], $value['end']);
                        $result[$date_range]['val'] = count($list_of_active_players);

                        foreach ($val as $game_platform_id => $player_ids) {
                            if (isset($result[$date_range][$game_platform_id])) {
                                $existing_gameID = $result[$date_range][$game_platform_id];
                                $player_ids += $existing_gameID;
                                $result[$date_range][$game_platform_id] = $player_ids;
                                @$result[$date_range][$game_platform_id]['active_players'] = count($result[$date_range][$game_platform_id]) -1;
                                continue;
                            }

                            $result[$date_range][$game_platform_id] = $player_ids;
                        }

                    }

                }
            }
		}
		return $result;
	}

    function _monthly($start, $end, $data) {
        return $data;
    }

	public function getPromotions() {

		$this->load->library('pagination');
		$this->load->model(array('promorules', 'player_model'));

		$view = ($this->input->post('view')) ? 1 : '';

		$player_id = $this->authentication->getPlayerId();
		$player = $this->player_model->getPlayerById($player_id);

		if ($player->disabled_promotion == 1) {
			$promo_list = [];
		} else {
			$promo_list = $this->promorules->getAllPromo(100, 0);
		}

		$page = 0;

		if ($this->uri->segment(3)) {
			$page = $this->uri->segment(3);
		}

		//setting up the pagination
		$config['base_url'] = base_url() . 'api/getPromotions/';
		$config['total_rows'] = count($promo_list);
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['prev_link'] = '&lt;';
		$config['next_link'] = '&gt;';
		$config['full_tag_open'] = '<ul>';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li><span class="page-numbers current">';
		$config['cur_tag_close'] = '</span></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['per_page'] = 5;

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		if ($player->disabled_promotion == 1) {
			$promo_list = [];
		} else {

			if (!empty($view)) {
				$promo_list = $this->promorules->getAllPromo();
			} else {
				$promo_list = $this->promorules->getAllPromo($config['per_page'], $page);
			}

		}

		if (!empty($promo_list)) {

			$available_list = [];

			foreach ($promo_list as &$promo_item) {

				$promorulesId = $promo_item['promoId'];

                // add resend
                $promorule = $this->promorules->getPromorule($promorulesId);

				$hide = false;
				$playerIsAllowed = $this->promorules->isAllowedPlayerBy($promorulesId, $promorule, $player->levelId, $player->playerId, $player->affiliateId, $hide);

				if ($hide) {
					$this->utils->debug_log('ingore promotion', $promorulesId, 'player id', $player->playerId);
					continue;
				}

				if ($playerIsAllowed) {
					$promo_item['promorule'] = $promorule;
					$status['enable_resend'] = false; // ! $isVerifiedEmail && $this->promorules->isEmailPromo($promorule);
					$promo_item['status'] = $promorule['status'];
					$promo_item['disabled_pre_application'] = $promorule['disabled_pre_application'] == '1';
				} else {
					$promo_item['disabled_pre_application'] = true;
				}

				$promo_item['promoType'] = $this->promorules->getPromoCmsDetails($promo_item['promoCmsSettingId'])[0]['promoType'];
				$promo_item['disabled'] = !$playerIsAllowed;

				$available_list[] = $promo_item;
			}

			$promo_list = $available_list;
		}

		$data['promo_list'] = $promo_list;
		$data['view'] = $view;
		$data['promotion_rules'] = $this->utils->getConfig('promotion_rules');
		$data['enabled_request_without_check'] = $promotion_rules['enabled_request_without_check'];
		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_all_promotions', $data);
	}

	public function myPromotion() {

		$this->load->library('pagination');
		$this->load->model(array('player_promo', 'promorules'));
		$playerId = $this->authentication->getPlayerId();

		$record_count = $this->player_promo->getPlayerActivePromoDetails($playerId, null, null, 1);

		//setting up the pagination
		$config['base_url'] = base_url() . 'api/myPromotion/';
		$config['total_rows'] = $record_count;
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['prev_link'] = '&lt;';
		$config['next_link'] = '&gt;';
		$config['full_tag_open'] = '<ul>';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li><span class="page-numbers current">';
		$config['cur_tag_close'] = '</span></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['per_page'] = 10;

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$page = 1;

		if ($this->uri->segment(3)) {
			$page = $this->uri->segment(3);
		}

		$data['playerpromo'] = $this->player_promo->getPlayerActivePromoDetails($playerId, $config['per_page'], $page);
		$data['playerId'] = $playerId;
		$data['currentLang'] = $this->language_function->getCurrentLanguage();

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_my_promotion', $data);

	}

	public function getMessages() {

		$this->load->model(array('internal_message'));
		$this->load->library('pagination');

		$player_id = $this->authentication->getPlayerId();
		$record = $this->internal_message->getMessages($player_id, null, null);

		//setting up the pagination
		$config['base_url'] = base_url() . 'api/getMessages/';
		$config['total_rows'] = count($record);
		$config['prev_link'] = '&lt;';
		$config['next_link'] = '&gt;';
		$config['full_tag_open'] = '<ul>';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li><span class="page-numbers current">';
		$config['cur_tag_close'] = '</span></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['per_page'] = 10;
		$config['first_link'] = false;
		$config['last_link'] = false;

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$page = 0;

		if ($this->uri->segment(3)) {
			$page = $this->uri->segment(3);
		}

		$data['chat'] = $this->internal_message->getMessages($player_id, $config['per_page'], $page);

		$template = $this->utils->getPlayerCenterTemplate() . '/player/ajax_inbox';
		if ($this->input->post('drafts')) {
			$template = $this->utils->getPlayerCenterTemplate() . '/player/ajax_outbox';
		}

		$this->load->view($template, $data);

	}

	public function transactions() {

		$this->load->model(array('report_model', 'transactions'));
		$this->load->library('pagination');
		$this->load->library('user_agent');
		$mobile = $this->agent->is_mobile();

		$status = array();
		$status[] = Transactions::APPROVED;

		if ($this->utils->isEnabledFeature('show_declined_deposit')) {
			$status[] = Transactions::DECLINED;
		}

		if ($this->utils->isEnabledFeature('show_pending_deposit')) {
			$status[] = Transactions::PENDING;
		}

		$status_condition = implode(", ", $status);

		$where_status = 'transactions.status IN (' . $status_condition . ')';

		$request = $this->input->post();
		$player_id = $this->authentication->getPlayerId();
		if(empty($player_id)) {
			return $this->returnJsonResult($this->data_tables->empty_data($request));
		}

		if ($this->input->is_ajax_request()) {
			$result = $this->report_model->transaction_details($player_id, $request, null, 0, $where_status, $mobile);
			return $this->returnJsonResult($result);
		}

		$data = array();

		$page = 0;

		if ($this->uri->segment(3)) {
			$page = $this->uri->segment(3);
		}

		$request['length'] = $config['per_page'];
		$request['start'] = $page;

		$data['result'] = $this->report_model->transaction_details($player_id, $request, null, 1, $where_status, $mobile);

		$total = count($data['result']);
		$data['total_result'] = $total;

		//setting up the pagination
		$config['base_url'] = base_url() . 'api/transactions/';
		$config['total_rows'] = $total;
		$config['prev_link'] = '&lt;';
		$config['next_link'] = '&gt;';
		$config['full_tag_open'] = '<ul>';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li><span class="page-numbers current">';
		$config['cur_tag_close'] = '</span></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['first_link'] = FALSE;
		$config['last_link'] = FALSE;
		$config['per_page'] = 10;

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$template = $this->utils->getPlayerCenterTemplate();

		$this->load->view($template . '/cashier/ajax_' . $this->input->post('template') . '_history', $data);

	}

	function json_lang() {
		return $this->returnJsonResult($this->lang->language);
	}

	# Calls the first available telephone API with parameter configured by admin user
	public function call_player_tele($playerId) {
		$this->load->model(array('users', 'player_model'));
		$this->load->library(array('authentication'));

		# Check for configured cooldown (in seconds)
		$cooldown = $this->utils->getConfig('call_tele_cooldown') ?: 0;
		$lastCallTime = $this->session->userdata('call_tele_timestamp');
		$currentTime = time();
		if ($currentTime - $lastCallTime <= $cooldown) {
			$this->utils->error_log("Last call time [$lastCallTime], Current time [$currentTime], Cooldown [$cooldown] sec not reached.");
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Calling TeleSystem too frequent.'));
			return redirect('/');
		}
		$this->session->set_userdata('call_tele_timestamp', $currentTime);

		# Get current logged on adminuser's configured telemarketing ID
		$userId = $this->authentication->getUserId();
		$adminUserInfo = $this->users->getUserInfoById($userId);
		$callerId = $adminUserInfo['tele_id'];

		$this->utils->debug_log("Caller ID: ", $callerId);

		# load contact number from player with the given Id
		$playerInfo = $this->player_model->getPlayerInfoDetailById($playerId);
		$playerTelephone = $playerInfo['contactNumber'];

		$this->utils->debug_log("Player telephone: ", $playerTelephone);

		$error = null;

		$result = $this->utils->call_tele_api($playerTelephone, $error, $callerId);

		if(!isset($result['success']) || !isset($result['type'])) {
			$error = 'Call Telephone System Failed';
			$this->utils->error_log("============call_player_tele No TeleSystem API configured.", $error);
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang($error));
			return redirect('/'); # redirect to home page
		}

		if(($result['success']) && ($result['type'] == Abstract_telephone_api::REDIRECT_TYPE_GET_URL) ) {
			$url = $result['url'];
			$this->utils->debug_log("============call_player_tele Using url [$url] to make a call");

			//check if the request is from jquery ajax
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$this->utils->error_log("============is ajax");
				echo json_encode(array('redirect_url'=> $url));
			}
			else {
				$this->utils->error_log("============not ajax");
				redirect($url);
			}
		}
		else if($result['type'] == Abstract_telephone_api::REDIRECT_TYPE_POST_RESULT) {
			if($result['success']) {
				// $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $result['msg']);
				echo json_encode(array('success'=> true, 'msg'=> $result['msg']));
			}
			else {
				// $this->alertMessage(self::MESSAGE_TYPE_ERROR, $result['msg']);
				echo json_encode(array('success'=> false, 'msg'=> $result['msg']));
			}
		}
		else {
			$error = 'Call Telephone System Failed';
			$this->utils->error_log("============call_player_tele No TeleSystem API configured.", $error);
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang($error));
			return redirect('/'); # redirect to home page
		}
	}

	public function verifyPlayer() {

		try {

			$this->load->model('player_model');

			$playerId = $this->authentication->getPlayerId();

			$random_verification_code = random_string('numeric', 6);

			if (!$this->player_model->updateVerifyCode($playerId, $random_verification_code)) {
				throw new Exception(false);
			}

			$type = $this->input->post('type');

			$this->_sendEmail($random_verification_code, $this->input->post('email'));

			return $this->returnJsonResult(array('status' => 'success', 'message' => ''));

		} catch (Exception $e) {
			return $this->returnJsonResult(array('status' => 'error', 'message' => ''));
		}

	}

	public function verifyAccount() {

		try {

			$this->load->model('player_model');

			$playerId = $this->authentication->getPlayerId();

			$code = $this->input->post('code');

			$getPLayerCode = $this->player_model->getVerifyCode($playerId, $code);

			if (!$getPLayerCode) {
				throw new Exception(false);
			}

			$success = $this->player_model->verifyEmail($playerId);
			if($success){
				$username=$this->player_model->getUsernameById($playerId);
				$this->syncPlayerCurrentToMDBWithLock($playerId, $username, false);
			}

			return $this->returnJsonResult(array('status' => 'success', 'message' => ''));

		} catch (Exception $e) {
			return $this->returnJsonResult(array('status' => 'error', 'message' => lang('Verification Code Error')));
		}

	}

	function _sendEmail($code, $email) {

		$playerId = $this->authentication->getPlayerId();
		$player_info = $this->player_functions->getPlayerById($playerId);

		$realname = $player_info['lastName'] . ' ' . $player_info['firstName'];

		$subject = 'Verification Code';

		$body = '<html><body>';
		$body .= 'Hi ' . $realname;
		$body .= '<br><br>';
		$body .= '<label>Verification Code</label>: <span>' . $code . '</span>';
		$body .= '</body></html>';

		$token = $this->utils->sendMail($email, null, null, $subject, $body,
			Queue_result::CALLER_TYPE_PLAYER, $playerId);

		$this->utils->debug_log('email', $email, 'query token', $token);

	}

	public function cancelFreeRound() {

		try {

			$this->load->library('isoftbet_free_round');

			$fround_id = $this->input->post('fround_id');
			$reason = $this->input->post('reason');

			$freespin = $this->isoftbet_free_round->freerounds_cancel($fround_id, $reason);

			if (isset($freespin->error)) {
				throw new Exception($freespin->error_message);
			}

			$this->alertMessage(1, lang('sys.gd27'));

		} catch (Exception $e) {

			$this->alertMessage(2, $e->getMessage());

		}

		redirect('marketing_management/freeround');

	}

	public function freeRoundActivate($fround_id = '') {

		try {

			if (empty($fround_id)) {
				throw new Exception(lang('sys.gd30'));
			}

			$this->load->library('isoftbet_free_round');

			$freespin = $this->isoftbet_free_round->freerounds_activate($fround_id);

			if (isset($freespin->error)) {
				throw new Exception($freespin->error_message);
			}

			$this->alertMessage(1, lang('sys.gd27'));

		} catch (Exception $e) {

			$this->alertMessage(2, $e->getMessage());

		}

		return true;

	}

	public function removeFreeroundCoin($fround_id = '') {

		try {

			if (empty($fround_id)) {
				throw new Exception(lang('sys.gd30'));
			}

			$this->load->library('isoftbet_free_round');

			$params = array();

			$coins = array(
				'currency' => $this->input->post('currency'),
				'coin_value' => $this->input->post('coin_value'),
				'game_id' => $this->input->post('game_id'),
			);

			$freespin = $this->isoftbet_free_round->currencies_remove($fround_id, $coins);

			if (isset($freespin->error)) {
				throw new Exception($freespin->error_message);
			}

			$this->alertMessage(1, lang('sys.gd27'));

		} catch (Exception $e) {

			$this->alertMessage(2, $e->getMessage());

		}

		return true;

	}

	public function set_coin() {

		$this->load->model('game_description_model');

		$game_code = $this->input->post('games');

		if (!is_array($game_code)) {
			$game_code = explode(',', $game_code);
		}

		$this->utils->debug_log('Result ======= ', $game_code);

		$games = $game_ids = array();

		foreach ($game_code as $key => $value) {
			$games[] = $this->game_description_model->getRecord($value);
			$game_ids[] = substr($value, -4);
		}

		$start_date = $this->input->post('start_date');

		if ($this->input->post('start_in_five_min')) {
			$date = date('Y-m-d H:i:s');
			$currentDate = strtotime($date);
			$futureDate = $currentDate + (60 * 5);
			$start_date = date("Y-m-d H:i:s", $futureDate);
		}

		$data = array();
		$data['games'] = $games;
		$data['game_ids'] = $game_ids;
		$data['name'] = $this->input->post('name');
		$data['max_players'] = $this->input->post('max_players');
		$data['promo_code'] = $this->input->post('promo_code');
		$data['player_ids'] = implode(',', $this->input->post('player_ids'));
		$data['limit_per_player'] = $this->input->post('limit_per_player');
		$data['start_date'] = $start_date;
		$data['end_date'] = $this->input->post('end_date');
		$data['relative_duration'] = $this->input->post('relative_duration');

		$this->utils->debug_log('Result ======= ', json_encode($games));

		$this->load->view('marketing_management/freespin/ajax_coins', $data);

	}

	public function getPromoCmsItemDetails($promoCmsId) {
        if(!$this->authentication->isLoggedIn()){
            return $this->returnJsonResult(array('status' => 'failed', 'msg' => lang('Not Login')));
        }

        $playerId = $this->authentication->getPlayerId();

        $display_keys = [
            'promoCmsSettingId',
            'promoName',
            'promoDetails',
            'promoDescription',
            'promoThumbnail',
            'promo_code',
            'promoTypeName'
        ];

        $display_promorule_keys = [
            'trigger_wallets'
        ];

        $allpromo = $this->utils->getPlayerPromo('allpromo', $playerId, $promoCmsId);

        if(!isset($allpromo['promo_list'])){
            return $this->returnJsonResult(array('status' => 'failed', 'msg' => lang('lang.norec')));
        }

        $promo_list = array_pop($allpromo);

        $display_promo_data = [];

        foreach($promo_list as $promo_data){
            if($promo_data['promoCmsSettingId'] == $promoCmsId){
                $display_promo_data = array_intersect_key($promo_data, array_flip($display_keys));

                if(file_exists($this->utils->getPromoThumbnails() . $display_promo_data['promoThumbnail']) && !empty($display_promo_data['promoThumbnail'])){
                    $promoThumbnail = $this->utils->getPromoThumbnailRelativePath(FALSE) . $display_promo_data['promoThumbnail'];
                } else {
                    if(!empty($display_promo_data['promoThumbnail'])){
                        $promoThumbnail = $this->utils->imageUrl('promothumbnails/'.$display_promo_data['promoThumbnail']);
                    } else {
                        $promoThumbnail = $this->utils->imageUrl('promothumbnails/default_promo_cms_1.jpg');
                    }
                }

                $display_promo_data['promoThumbnail'] = $promoThumbnail;

                $display_promo_data['promorule'] = array_intersect_key($promo_data['promorule'], array_flip($display_promorule_keys));
                return $this->returnJsonResult(array('status' => 'success', 'promo_data' => $display_promo_data));
            }
        }

		return $this->returnJsonResult(array('status' => 'error', 'promo_data' => $display_promo_data));
	}

	public function getDepositDeclinedRequest() {

		$isSettingExist = $this->operatorglobalsettings->getSetting('deposit_count_list');
		$data['depositCountList'] = isset($isSettingExist->value) ? $isSettingExist->value : Payment_management::DEFAULT_DEPOSIT_COUNT_SETTING;

		if ($data['depositCountList'] == Payment_management::DEPOSIT_THIS_WEEK) {
			$depositStartDate = date("Y-m-d", strtotime('monday this week')) . ' 00:00:00';
			$depositEndDate = date("Y-m-d", strtotime('sunday this week')) . ' 23:59:59';
		} elseif ($data['depositCountList'] == Payment_management::DEPOSIT_THIS_MONTH) {
			$depositStartDate = date('Y-m-01 00:00:00', strtotime('this month'));
			$depositEndDate = date('Y-m-t 12:59:59', strtotime('this month'));
		} elseif ($data['depositCountList'] == Payment_management::DEPOSIT_THIS_YEAR) {
			$depositStartDate = date('Y-01-01 00:00:00', strtotime('this year'));
			$depositEndDate = date('Y-12-t 12:59:59', strtotime('this year'));
		} elseif ($data['depositCountList'] == Payment_management::DEPOSIT_TOTAL_ALL) {
			$depositStartDate = '';
			$depositEndDate = '';
		}

		$count = $this->utils->formatInt(
			$this->sale_order->countSaleOrders(Sale_order::PAYMENT_KIND_DEPOSIT, Sale_order::VIEW_STATUS_DECLINED, $depositStartDate, $depositEndDate));

		return $this->returnJsonResult(array('count' => $count));

	}

	public function getDepositDeclinedRequestToday() {

		$start_today = date("Y-m-d") . ' 00:00:00';
		$end_today = date("Y-m-d") . ' 23:59:59';

		$count = $this->utils->formatInt(
			$this->sale_order->countSaleOrders(Sale_order::PAYMENT_KIND_DEPOSIT, Sale_order::VIEW_STATUS_DECLINED, $start_today, $end_today));

		return $this->returnJsonResult(array('count' => $count));

	}

	public function add_coin() {

		$this->load->model('game_description_model');

		$game_code = $this->input->post('games');

		if (!is_array($game_code)) {
			$game_code = explode(',', $game_code);
		}

		$this->utils->debug_log('Result ======= ', $game_code);

		$games = $game_ids = array();

		foreach ($game_code as $key => $value) {
			$games[] = $this->game_description_model->getRecord($value);
			$game_ids[] = substr($value, -4);
		}

		$data = array();
		$data['games'] = $games;
		$data['game_ids'] = $game_ids;

		$this->utils->debug_log('Result ======= ', json_encode($games));

		$this->load->view('marketing_management/freespin/ajax_add_coin_form', $data);

	}

	public function lockedDepositList($playerId = null) {
		if (!$this->isLoggedAdminUser()) {
			return;
		}

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$is_locked = true;
		$result = $this->report_model->depositList($playerId, $request, $is_export, $is_locked);

		$this->returnJsonResult($result);
	}

	public function lockedWithdrawList($playerId = null, $enabledAction = 'true') {
		if (!$this->isLoggedAdminUser()) {
			return;
		}

		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;
		$is_locked = true;
		$result = $this->report_model->withdrawList($playerId, $enabledAction, $request, $is_export, $is_locked);

		$this->returnJsonResult($result);
	}

	public function game_history() {

		$this->load->model(array('report_model', 'transactions'));
		$this->load->library('pagination');

		$request = $this->input->post();

		if (isset($request) && $request['trans_type'] == 'game') {
			$request['by_game_flag'] = 1;
		}

		$player_id = $this->authentication->getPlayerId();
		$data = array();

		$result = $this->report_model->gamesHistory($request, $player_id, null, 1);

		$ctr = 0;
		foreach ($result as $key => $value) {
			if (!is_numeric($key)) {
				continue;
			}

			$ctr++;
		}

		$total = $ctr;
		$data['total_result'] = $total;

		//setting up the pagination
		$config['base_url'] = base_url() . 'api/game_history/';
		$config['total_rows'] = $total;
		$config['prev_link'] = '&lt;';
		$config['next_link'] = '&gt;';
		$config['full_tag_open'] = '<ul>';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li><span class="page-numbers current">';
		$config['cur_tag_close'] = '</span></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['first_link'] = FALSE;
		$config['last_link'] = FALSE;
		$config['per_page'] = 10;

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$page = 0;

		if ($this->uri->segment(3)) {
			$page = $this->uri->segment(3);
		}

		$request['length'] = $config['per_page'];
		$request['start'] = $page;

		$data['result'] = $this->report_model->gamesHistory($request, $player_id, null, 1);
		$data['total'] = $total;

		$template = $this->utils->getPlayerCenterTemplate();

		if ($this->utils->is_mobile()) {

			$template = $template . '/mobile';

		}

		$this->load->view($template . '/cashier/ajax_' . $this->input->post('template') . '_history', $data);

	}

	public function player_games_history($player_id = null) {
		$this->load->model(array('report_model'));

		$request = $this->input->post();
		$request['extra_search'][] = ['name' => 'by_game_flag', 'value' =>1];

		$player_id = $this->authentication->getPlayerId();
		if(!$this->authentication->isLoggedIn()){
			$result = $this->data_tables->empty_data($request);
			$this->returnJsonResult($result);
			return;
		}

		$is_export = false;

		$result = $this->report_model->gamesHistory($request, $player_id, $is_export);
		// $this->output->set_content_type('application/json')->set_output(json_encode($result));
		$this->returnJsonResult($result);
	}

	public function retrieveAllSubWalletBalanceToMainBallance($player_id = null) {
		$this->load->model('wallet_model');
        if(empty($player_id)){
            $player_id = $this->authentication->getPlayerId();

            if(empty($player_id)) {
                return $this->returnText('');
            }
        }
		$playerName = $this->player_model->getUsernameById($player_id);

		$data = array();
		$wallets = $this->wallet_model->getOrderBigWallet($player_id);

		$subwallets = $wallets['sub'];

        $countError         = 0;
        $hasSuccessTransfer = false;
        $game_count         = 0;
        $trans_count        = 0;
        $error_msg          = "";
		foreach ($subwallets as $key => $value) {

			$mainwallet = 0;
			$transfer_from = $value['id'];
			$amount = $value['totalBalanceAmount'];

			if ($amount != 0){
				$game_count++;
				$result = $this->utils->transferWallet($player_id, $playerName, $transfer_from, $mainwallet, $amount);
				if ($result['success']) {
					$trans_count++;
					$hasSuccessTransfer = true;
				} else {
					$error_msg = $error_msg . $value['game'] . "/";
					$countError++;
				}
			}
		}

		if($game_count == 0  && $trans_count == 0 ){
			$hasSuccessTransfer = true;
		}

		if (!$hasSuccessTransfer) {
			$message = lang('Transfer Failed');
		} else if ($hasSuccessTransfer && $countError == 0) {
			$message = lang('Transfer Success');
		}

		if($game_count != $trans_count ){
			$message = lang('Transfer success except other subwallet') . "(" . $error_msg . ")" ;
		}

		$status = $hasSuccessTransfer ? 'success' : 'error';

		$this->returnJsonResult(array('status' => $status, 'msg' => $message, 'failedTransferCount' => $countError));
	}

	function refreshMainBalance() {

		$playerId = $this->authentication->getPlayerId();
		$info = $this->player_model->getPlayerInfoDetailById($playerId);

		$this->returnJsonResult(array('rec' => $this->utils->formatCurrencyNoSym($info['totalBalanceAmount'])));

	}

	function getDepositsList() {

		$this->load->model(array('report_model', 'sale_order', 'payment_account'));
		$this->load->library('pagination');

		$playerId = $this->authentication->getPlayerId();

		$request = $this->input->post();

		$where = 'sale_orders.status IN (' . Sale_order::STATUS_SETTLED . ',' . Sale_order::STATUS_DECLINED . ',' . Sale_order::STATUS_BROWSER_CALLBACK . ',' . Sale_order::STATUS_FAILED . ')';

		$data = array();

		$result = $this->report_model->depositList($playerId, $request, null, null, 1, 1, $where);

		$total = count($result);
		$data['total_result'] = $total;

		//setting up the pagination
		$config['base_url'] = base_url() . 'api/getDepositsList/';
		$config['total_rows'] = $total;
		$config['prev_link'] = '&lt;';
		$config['next_link'] = '&gt;';
		$config['full_tag_open'] = '<ul>';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li><span class="page-numbers current">';
		$config['cur_tag_close'] = '</span></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['first_link'] = FALSE;
		$config['last_link'] = FALSE;
		$config['per_page'] = 10;

		$this->pagination->initialize($config);

		$data['create_links'] = $this->pagination->create_links();

		$page = 0;

		if ($this->uri->segment(3)) {
			$page = $this->uri->segment(3);
		}

		$request['length'] = $config['per_page'];
		$request['start'] = $page;

		$data['result'] = $this->report_model->depositList($playerId, $request, null, null, 1, 1, $where);
		$data['total'] = $total;

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/ajax_' . $this->input->post('template'), $data);

	}

	function getVIPSettingRulesUpgrade() {

		$this->load->model(array('game_logs', 'group_level', 'transactions'));

		$playerId = $this->authentication->getPlayerId();
		if (empty($playerId)) {
			return $this->returnText('');
		}

		$playerInfo = $this->player_model->getPlayerInfoDetailById($playerId);

		$vipsettingcashbackruleId = $this->group_level->getPlayerLevelId($playerId);

		$getPlayerLevelId = $this->group_level->getVipGroupLevelDetails($vipsettingcashbackruleId);
		$VIPGroupDetails = $this->group_level->getVIPGroupDetails($getPlayerLevelId['vipSettingId']);

		$playerCurrentLevel = $playerInfo['vipLevel']; //current player level
		$topLevelTarget = $VIPGroupDetails[0]['groupLevelCount'];

		$data = array();

		if ($playerCurrentLevel >= $topLevelTarget) {

			$data['playerCurrentLevel'] = $playerCurrentLevel;
			$data['topLevelTarget'] = $topLevelTarget;
			$data['on_top'] = true;
			$data['level_info'] = $this->group_level->getVIPTopLevel($VIPGroupDetails[0]['vipSettingId'], $VIPGroupDetails[0]['groupLevelCount']);

		} else {

			$vipGroupLevel = $this->group_level->getVipGroupLevelDetails($vipsettingcashbackruleId);
			$nextGroupLevel = $this->group_level->getNextLevel($vipsettingcashbackruleId);

			$upgrade_settings = $this->group_level->upgradeLevelSetting($vipGroupLevel['vip_upgrade_id']);
			$upgrade_settings = json_decode($upgrade_settings[0]->formula);
			$query_date = date('Y-m-d', time());

			$date_from = date('Y-m-01', strtotime($query_date));
			$date_to = date('Y-m-t', strtotime($query_date));

			$bet_amount = $this->game_logs->getPlayerTotalBetsWinsLossByDatetime($playerId, $date_from, $date_to);
			$bet_amount = (!empty($bet_amount[0])) ? $bet_amount[0] : 0;

			$totalDeposit = $this->transactions->getPlayerTotalDeposits($playerId, $date_from, $date_to);

			$data['player_deposit_amount'] = $totalDeposit;
			$data['player_bet_amount'] = $bet_amount;

			$depositAmount = $betAmount = 0;

			if (isset($upgrade_settings->deposit_amount)) {

				if ($totalDeposit >= $upgrade_settings->deposit_amount[1]) {
					$depositAmount = 1;
				} else {
					$depositAmount = $totalDeposit / $upgrade_settings->deposit_amount[1];
				}

				$depositAmount *= 50;

				$data['upgrade_setting_deposit_amount'] = $upgrade_settings->deposit_amount[1];
				$data['deposit_amount'] = $depositAmount;

			}

			if (isset($upgrade_settings->bet_amount)) {

				if ($bet_amount >= $upgrade_settings->bet_amount[1]) {
					$betAmount = 1;
				} else {
					$betAmount = $bet_amount / $upgrade_settings->bet_amount[1];
				}
				$betAmount *= 50;

				$data['upgrade_setting_bet_amount'] = $upgrade_settings->bet_amount[1];
				$data['bet_amount'] = $betAmount;

			}

			$date_remaining = date('t') - date('j');

			$data['date_remaining'] = $date_remaining;

			$data['from_level'] = $vipGroupLevel['vipLevelName'];
			$data['to_level'] = (!empty($nextGroupLevel)) ? $nextGroupLevel['vipLevelName'] : '';

		}

		$this->load->view($this->utils->getPlayerCenterTemplate() . '/player/ajax_vip_level', $data);

	}

	/**
	 * New Api::getPlayerVipGroupDetails(), with all calculation details moved to player_functions
	 * @see		player_functions::getPlayerVipGroupDetails()
	 * @return	json
	 */
	public function getPlayerVipGroupDetails() {
		$playerId = $this->authentication->getPlayerId();

		if (empty($playerId)) {
			return $this->returnText('');
		}

		$this->load->library('player_functions');

		$vip_res = $this->player_functions->getPlayerVipGroupDetails($playerId);

		return $this->returnJsonResult($vip_res);
	}

	// public function getPlayerVipGroupDetails0() {
	// 	$this->load->model(array('game_logs', 'group_level', 'transactions', 'player_model', 'total_player_game_day'));

 //        $overview_config = $this->utils->getConfig('overview');

 //        $player_today_total_betting_platforms = (isset($overview_config['today_total_betting_platforms']) && !empty($overview_config['today_total_betting_platforms'])) ? $overview_config['today_total_betting_platforms'] : null;

	// 	$playerId = $this->authentication->getPlayerId();

	// 	if (empty($playerId)) {
	// 		return $this->returnText('');
	// 	}

	// 	//get server protocol
	// 	$serverProtocol = $this->utils->getServerProtocol();

	// 	//player info data
	// 	$playerInfo = $this->player_model->getPlayerInfoDetailById($playerId);
	// 	if (empty($playerInfo['vipLevel'])) {
 //            // set to default level
 //            $defaultVipLevel = $this->group_level->getVIPTopLevel(1, 1);
 //            if (!empty($defaultVipLevel)) {
 //                $newPlayerLevel = $defaultVipLevel->vipsettingcashbackruleId;
 //                $this->utils->debug_log('======================== defaultVipLevel' . $newPlayerLevel);
 //                $this->group_level->startTrans();
 //                $this->group_level->adjustPlayerLevel($playerId, $newPlayerLevel);
 //                $data = array(
 //                    'playerId' => $playerId,
 //                    'changes' => 'Set player level to default level',
 //                    'createdOn' => date('Y-m-d H:i:s'),
 //                    'operator' => $this->authentication->getUsername(),
 //                );
 //                $this->player_model->addPlayerInfoUpdates($playerId, $data);

 //                $this->saveAction('Player Management', 'Adjust Player Level', "User " . $this->authentication->getUsername() . " has adjusted player '" . $playerId . "'");
 //                $this->group_level->endTrans();
 //            }
 //            $playerInfo = $this->player_model->getPlayerInfoDetailById($playerId);
 //        }


	// 	//get player current level details
	// 	$vipSettingId = $this->group_level->getPlayerLevelId($playerId);
	// 	$getPlayerCurrentLevelDetails = $this->group_level->getVipGroupLevelDetails($vipSettingId);

	// 	$vipGroupDetails = $this->group_level->getVIPGroupDetail($getPlayerCurrentLevelDetails['vipSettingId']);

	// 	//get how many levels in a vip group where player belong
	// 	$vipGroupLevels = $this->group_level->getVIPGroupLevels($vipGroupDetails['vipSettingId']);

	// 	$maxLevel = 1;
	// 	foreach ($vipGroupLevels as $detail) {
	// 		if ($detail['vipLevel'] >= $maxLevel) {
	// 			$maxLevel = $detail['vipLevel'];
	// 		}
	// 	}

	// 	//initialize data
	// 	$vipUpgradeDetailsDepositAmountRequirement = 0;
	// 	$vipUpgradeDetailsBetAmountRequirement = 0;
	// 	$getCurrentPlayerTotalBetAmt = 0;
	// 	$vipNextLevelPercentageDeposit = 0;
	// 	$vipNextLevelPercentageBet = 0;
	// 	$playerTotalBonus = 0;
	// 	$playerCurrentLvlBirthdayBonusAmt = 0;
	// 	$playerCurrentLvlDepositAmt = 0;
	// 	$nextLvlReqOperator = false;
	// 	$formula = null;
	// 	$playerUpgradeProgress = array();
	// 	$schedule = '';

	// 	//get upgrade details
	// 	$vipUpgradeDetails = $this->group_level->getVIPGroupUpgradeDetails($getPlayerCurrentLevelDetails['vip_upgrade_id']);
	// 	if ($vipUpgradeDetails) {
	// 		$accumulation = $vipUpgradeDetails['accumulation'];
	// 		$vipUpgradeDetails = json_decode($vipUpgradeDetails['formula'], true);
	// 		$formula = $this->group_level->displayPlayerFormulaForUpgrade($vipUpgradeDetails);
	// 		$playerUpgradeProgress = $this->group_level->getPlayerUpgradePercentage($playerId, $vipUpgradeDetails, $getPlayerCurrentLevelDetails['period_up_down_2']);


	// 		if(!empty($getPlayerCurrentLevelDetails['period_up_down_2'])) {
	// 			$schedule = json_decode($getPlayerCurrentLevelDetails['period_up_down_2'], true);
	// 			# $schedule = $this->group_level->getUpgradeSchedule($schedule)['sched'];
	// 			$tmp = $this->group_level->getUpgradeSchedule($schedule);
 //                if (isset($tmp['sched'])){
 //                    $schedule = $tmp['sched'];
 //                }
	// 		}

	// 		$arrayChildAmt = 1;
	// 		$nextLvlReq = $this->getNextLevelRequirementsForBetAndDepAmt($vipUpgradeDetails);
	// 		$vipUpgradeDetailsDepositAmountRequirement = isset($nextLvlReq['deposit_amount']) ? $nextLvlReq['deposit_amount'][$arrayChildAmt] : 0;
	// 		$vipUpgradeDetailsBetAmountRequirement = isset($nextLvlReq['bet_amount']) ? $nextLvlReq['bet_amount'][$arrayChildAmt] : 0;

	// 		//get player total deposit amount for its currrent level
	// 		list($playerCurrentLvlDepositAmt, $playerCurrentLvlBirthdayBonusAmt, $playerTotalBonus) = $this->getCurrentPlayerLvlBonusAmount($playerId, json_decode($getPlayerCurrentLevelDetails['period_up_down_2'], true), $accumulation);

	// 		//generate next level percentage by deposit, deposit condition must exists if not it will not show the percentage
	// 		if ($playerCurrentLvlDepositAmt && $vipUpgradeDetailsDepositAmountRequirement) {
	// 			$vipNextLevelPercentageDeposit = round(($playerCurrentLvlDepositAmt / $vipUpgradeDetailsDepositAmountRequirement) * 100);
	// 			$vipNextLevelPercentageDeposit = $vipNextLevelPercentageDeposit >= 100 ? 100 : $vipNextLevelPercentageDeposit;
	// 		}

	// 		//get current player total bet amt per vip level
	// 		$getCurrentPlayerTotalBetAmt = $this->getCurrentPlayerTotalBetAmt($playerId, $getPlayerCurrentLevelDetails['period_up_down_2'], $accumulation);

	// 		//generate next level percentage by bet, bet condition must exists if not it will not show the percentage
	// 		if ($getCurrentPlayerTotalBetAmt && $vipUpgradeDetailsBetAmountRequirement) {
	// 			$vipNextLevelPercentageBet = round(($getCurrentPlayerTotalBetAmt / $vipUpgradeDetailsBetAmountRequirement) * 100);
	// 			$vipNextLevelPercentageBet = $vipNextLevelPercentageBet >= 100 ? 100 : $vipNextLevelPercentageBet;
	// 		}

	// 		if (isset($nextLvlReq['operator'])) {
	// 			$nextLvlReqOperator = $nextLvlReq['operator'];
	// 		}
	// 	}

	// 	//get next vip group level
	// 	$nextVipGroupLvl = $this->getVipGroupNextLevel($playerInfo['vipsettingcashbackruleId'], $vipGroupLevels);

	// 	//get last login time
	// 	// $lastLoginTime = new DateTime($playerInfo['lastLoginTime']);
	// 	$lastLoginTimeObj = new DateTime($playerInfo['last_login_time']);
	// 	$lastLoginTime = $this->utils->formatDatetimeForDisplay($lastLoginTimeObj);
	// 	$lastLoginTimeZone = $this->utils->getDatetimeTimezone($lastLoginTimeObj);

	// 	//get total cashback
	// 	$playerTotalCashbackBalance = $this->getPlayerTotalCashbackBalance($playerId);

	// 	//get player available cashback
	// 	$playerAvailableCashback = $this->getCurrentPlayerAvailableBalance($playerId);

	// 	//get player points details
	// 	// list($playerAvailablePoints, $playerTotalPoints) = $this->getPlayerTotalPoints($playerId);

	// 	//get days left before birthday
	// 	$bdate = new DateTime($playerInfo['birthdate']);
	// 	$daysLeftBeforeBday = $this->getDaysLeftBeforeBirthday($bdate);

	// 	//get vip badge
	// 	$firstChild = 0;

	// 	//$vipBadge = $getPlayerCurrentLevelDetails['badge'] ?: "vip-icon.png";
	// 	if(file_exists($this->utils->getVipBadgePath().$getPlayerCurrentLevelDetails['badge'])){
	// 		$vipBadge = $this->utils->getVipBadgeUri().'/'.$getPlayerCurrentLevelDetails['badge'];
	// 	} else {
	// 		$vipBadge = base_url() . $this->utils->getPlayerCenterTemplate().'/img/icons/star.png';
	// 	}

	// 	if(!empty($schedule)) {
	// 		$schedule = lang('Level upgrade is set to').' '.lang($schedule);
	// 	}

	// 	$vipGroupInfo = array(
	// 		"current_vip_level" => array(
	// 			"maxLevel" => $maxLevel,
	// 			"vip_group_name" => lang($playerInfo['groupName']),
	// 			"vip_lvl_name" => lang($playerInfo['vipLevelName']),
	// 			"vip_group_lvl" => lang($playerInfo['groupName']) . " - " . lang($playerInfo['vipLevelName']), //. " " . $playerInfo['vipLevel'],
	// 			"vip_group_lvl_id" => $playerInfo['vipsettingcashbackruleId'],
	// 			"vip_group_lvl_name" => lang($playerInfo['vipLevelName']),
	// 			"vip_group_lvl_number" => $playerInfo['vipLevel'],
	// 			"vip_group_lvl_bday_bonus_amt" => $getPlayerCurrentLevelDetails['birthday_bonus_amount'] ?: 0,
	// 			"vip_group_lvl_badge" => $vipBadge,
	// 			"upgrade_deposit_amt_req" => $this->utils->formatCurrency($vipUpgradeDetailsDepositAmountRequirement, false, true, true),
	// 			"upgrade_bet_amt_req" => $this->utils->formatCurrency($vipUpgradeDetailsBetAmountRequirement, false, true, true),
	// 			"current_lvl_deposit_amt" => $this->utils->formatCurrency($playerCurrentLvlDepositAmt, false, true, true),
	// 			"current_lvl_bet_amt" => $this->utils->formatCurrency($getCurrentPlayerTotalBetAmt, false, true, true),
	// 			// "next_level_percentage" => $vipNextLevelPercentageDeposit,
	// 			"next_level_percentage_deposit" => $vipNextLevelPercentageDeposit,
	// 			"next_level_percentage_bet" => $vipNextLevelPercentageBet,
	// 			"next_level_percentage_operator" => $nextLvlReqOperator,
	// 			"birthday_bonus_expiration_datetime" => $getPlayerCurrentLevelDetails['birthday_bonus_expiration_datetime'],
	// 			"bonus_mode_birthday" => empty($getPlayerCurrentLevelDetails['bonus_mode_birthday']) ? null : $getPlayerCurrentLevelDetails['bonus_mode_birthday'],
	// 			"formula" => $formula,
	// 			"player_upgrade_progress" => $playerUpgradeProgress,
	// 			"schedule" => $schedule,
	// 		),
	// 		"next_vip_level" => array(
	// 			"vip_group_lvl_name" => lang($nextVipGroupLvl['vipLevelName']),
	// 			"vip_group_lvl_number" => $nextVipGroupLvl['vipLevel'],
	// 		),
	// 		"others" => array(
	// 			"player_total_bonus" => $playerTotalBonus,
	// 			"player_total_cashback_amount_received" => $this->utils->formatCurrencyNoSym(empty($playerTotalCashbackBalance) ? 0 : $playerTotalCashbackBalance),
	// 			"player_available_cashback_amount" => $playerAvailableCashback,
	// 			// "player_available_points" => $playerAvailablePoints,
	// 			// "player_total_points" => $playerTotalPoints,
	// 			"player_days_left_before_bday_bonus" => $daysLeftBeforeBday,
	// 			"player_last_login_time" => $lastLoginTime,
	// 			'player_last_login_timezone' => $lastLoginTimeZone,
	// 			"player_birthdate" => $playerInfo['birthdate'] ?: false,
	// 			"player_birthdate_exists" => $playerInfo['birthdate'] ? true : false,
	// 			"player_birthday_bonus_amount_received" => $playerCurrentLvlBirthdayBonusAmt,
	// 			"player_profile_pic" => $serverProtocol . "://" . $this->utils->getSystemHost('player') . "/" . $this->utils->getPlayerCenterTemplate() . $this->setProfilePicture(),
	// 			"player_profile_progress" => $this->getProfileProgress(),
 //                'player_today_total_betting_amount' => $this->utils->formatCurrency($this->game_logs->getPlayerCurrentBetByPlatform($playerId, date("Y-m-d 00:00:00"), null, null, $player_today_total_betting_platforms)),
	// 		),
	// 	);

	// 	return $this->returnJsonResult($vipGroupInfo);
	// }

	public function claimBirthdayBonus($bdayBonustAmt) {
		$this->load->model(array('wallet_model', 'transactions', 'group_level', 'withdraw_condition'));

		$playerId = $this->authentication->getPlayerId();
		$adminUserId = Transactions::ADMIN;

		$fromRange = new DateTime();
		$fromRange = $fromRange->format('Y') . "-01-01";

		$toRange = new DateTime();
		$toRange = $toRange->format('Y') . "-12-31";

		$bonusData = $this->transactions->getTotalDepositBonusAndBirthdayByPlayers($playerId, $fromRange, $toRange);
		$lastTransactionId = null;

		if (!$bonusData[Transactions::BIRTHDAY_BONUS]) {
			$beforeBalance = $this->wallet_model->getMainWalletBalance($playerId);

			$lastTransactionId = $this->transactions->createBonusTransaction($adminUserId, $playerId, $bdayBonustAmt, $beforeBalance,
				null, null, Transactions::MANUAL, null, Transactions::BIRTHDAY_BONUS,
				'birthday bonus amount is ' . $bdayBonustAmt);

			$vipSettingId = $this->group_level->getPlayerLevelId($playerId);
			$getPlayerCurrentLevelDetails = $this->group_level->getVipGroupLevelDetails($vipSettingId);
			$playerBirthdayBetAmtWithdrawCondition = $getPlayerCurrentLevelDetails['birthday_bonus_withdraw_condition'] * $getPlayerCurrentLevelDetails['birthday_bonus_amount'];

			$this->withdraw_condition->createWithdrawConditionForBirthdayBonus($lastTransactionId, $playerBirthdayBetAmtWithdrawCondition, $getPlayerCurrentLevelDetails['birthday_bonus_amount'], $getPlayerCurrentLevelDetails['birthday_bonus_withdraw_condition'], $playerId);

		}
		if ($lastTransactionId) {
			$result = array("lastTransactionId" => $lastTransactionId, "status" => "success");
		} else {
			$result = array("lastTransactionId" => null, "status" => "failed");
		}
		return $this->returnJsonResult($result);
	}

	private function getNextLevelRequirementsForBetAndDepAmt($vipUpgradeDetails) {
		$result = array();
		if (array_key_exists('bet_amount', $vipUpgradeDetails)) {
			$result['bet_amount'] = $vipUpgradeDetails['bet_amount'];
		}

		if (array_key_exists('deposit_amount', $vipUpgradeDetails)) {
			$result['deposit_amount'] = $vipUpgradeDetails['deposit_amount'];
		}

		if (array_key_exists('operator_2', $vipUpgradeDetails)) {
			$result['operator'] = $vipUpgradeDetails['operator_2'];
		}
		return $result;
	}

	private function getDaysLeftBeforeBirthday($bdate) {
		$currentDate = new DateTime();
		$currentYear = $currentDate->format("Y");
		$bday = new DateTime($currentYear . "-" . $bdate->format("m-d"));

		$daysDiff = $currentDate->diff($bday);
		$daysLeft = $daysDiff->format('%R%a');
		$aYear = 365;
		if ($daysLeft < 0) {
			return $aYear - ($daysLeft * -1);
		} else {
			return (int) $daysDiff->format('%a');
		}
	}

	private function getPlayerTotalPoints($playerId) {
		$this->load->model('point_transactions');
		$totalPoints = $this->point_transactions->getPlayerTotalPoints($playerId);
		$playerTotalDeductedPoints = 0;
		$playerTotalPoints = 0;
		if (!empty($totalPoints)) {
			$playerTotalPoints = array_sum(array_column($totalPoints, 'points'));
		}
        $pointInfo=$this->point_transactions->getPlayerTotalDeductedPoints($playerId);
        if(!empty($pointInfo)){
    		$playerTotalDeductedPoints = $pointInfo['points'];
        }
		$remainingPoints = $playerTotalPoints - $playerTotalDeductedPoints;
		return array($remainingPoints, $playerTotalPoints);
	}

	private function getPlayerTotalCashbackBalance($playerId) {
		$this->load->model('transactions');
		$balance = $this->transactions->sumCashback($playerId);
		return $balance;
	}

	private function getCurrentPlayerLvlBonusAmount($playerId, $groupLevelUpgradePeriodSetting, $accumulation) {
		if ($groupLevelUpgradePeriodSetting) {
			$this->load->model(array("group_level", "transactions", "player_model"));
			// $rangeType = json_decode($groupLevelUpgradePeriodSetting, true);
			// $result = $this->group_level->getUpgradeSchedule($rangeType);
			$fromRange = $this->group_level->getUpgradeSchedule($groupLevelUpgradePeriodSetting, true)['dateFrom'];
			$toRange = $this->group_level->getUpgradeSchedule($groupLevelUpgradePeriodSetting, true)['dateTo'];
			if ((int)$accumulation == 1) {
				$playerDetails = $this->player_model->getPlayerDetailsById($playerId);
				$now = new DateTime();
				$fromRange = $playerDetails->createdOn;
				$toRange = $now->format('Y-m-d H:i:s');
			}
			$result = $this->transactions->getTotalDepositBonusAndBirthdayByPlayers($playerId, $fromRange, $toRange);
			return array($result[Transactions::DEPOSIT], $result[Transactions::BIRTHDAY_BONUS], $result['totalBonus'], $result['totalBonus']);
		}

		return false;
	}

	private function getCurrentPlayerTotalBetAmt($playerId, $groupLevelUpgradePeriodSetting, $accumulation) {
		if ($groupLevelUpgradePeriodSetting) {
			$gameLogData = $this->group_level->getPlayerBetAmtForNextLvl($playerId, $groupLevelUpgradePeriodSetting, $accumulation);
			return $gameLogData;
		}

		return false;
	}

	private function getCurrentPlayerAvailableBalance($playerId) {
		$fromRange = new DateTime();
		$fromRange = $fromRange->format("Y-m-d") . " 00:00:00";
		$toRange = new DateTime();
		$toRange = $toRange->format("Y-m-d") . " 23:59:59";
		$this->load->model("transactions");
		return $this->transactions->getTotalDepositBonusAndBirthdayByPlayers($playerId, $fromRange, $toRange)['availableCashback'];
	}

	private function getVipGroupNextLevel($currentGroupLvlId, $groupLevel) {
		foreach ($groupLevel as $key) {
			if ($currentGroupLvlId + 1 == $key['vipsettingcashbackruleId']) {
				return $key ?: null;
			}
		}
	}

	/**
	 * detail: get cancelled withdrawal condition for a certain player
	 *
	 * @param int $player_id withdraw_conditions player_id
	 * @return json
	 */
	public function kyc_history($player_id = '') {

		$this->load->model(array('player_kyc'));

		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'ui',
				'select' => 'ui',
			),
			array(
				'dt' => $i++,
				'alias' => 'un',
				'select' => 'un',
			),
			array(
				'dt' => $i++,
				'alias' => 'ac',
				'select' => 'ac',
			),
			array(
				'dt' => $i++,
				'alias' => 'dt',
				'select' => 'dt',
			),
		);

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "kyc_player.created_at >=";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "kyc_player.created_at <=";
			$values[] = $input['dateRangeValueEnd'];
		}

		$data = $this->player_kyc->getPlayerKycHistory($player_id, $where, $values);
		if ( $data > 0){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}
		$this->returnJsonResult($result);

	}

	/**
	 * detail: get friend_referral_status a certain player
	 * for SBE  Player's Logs - Friend Referral Status
	 *
	 * @param int $player_id withdraw_conditions player_id
	 * @return json
	 */
	public function friend_referral_status($player_id = '') {
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'createdOn',
				'select' => 'player.createdOn',
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'player.username',
			),
			array(
				'dt' => $i++,
				'alias' => 'totalBettingAmount',
				'select' => 'totalBettingAmount',
				'formatter' => function ($d, $row) {
					if ($row['totalBettingAmount'] == null) {
						return '<i style="color:#676565;">--</i>';
					} else {
						return number_format($row['totalBettingAmount'], 2);
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'amount',
				'select' => 'transactions.amount',
				'formatter' => function($d, $row) {
					if ($row['amount'] == null) {
						return '<i style="color:#676565;">' . lang('lang.no') . ' ' . lang('cms.bonusRelease') . '</i>';
					} else {
						return number_format($row['amount'], 2);
					}
				},
			),
		);

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "player.createdOn >=";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "player.createdOn <=";
			$values[] = $input['dateRangeValueEnd'];
		}

		$data = $this->player_model->getPlayerReferralStatus($player_id, $where, $values);
		if ( $data > 0){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}
		$this->returnJsonResult($result);

	}

	/**
	 * Get communication history for a certain player
	 *
	 * Recommand DO NOT use the function,
	 * Cause memory exhausted
	 * "message":"Allowed memory size of 268435456 bytes exhausted (tried to allocate 32 bytes)",
	 * "file":"/home/vagrant/Code/og/admin/application/models/communication_preference_model.php",
	 * "line":255
	 *
	 * Ref. to OGP-13339 communication pref report shows http error 500
	 *
	 * @param int $player_id
	 * @param post $request
	 * @return json $result
	 * @author Cholo Miguel Antonio <cholo.php.ph@tripletech.net>
	 */
	public function communicationPreferenceHistory($player_id = '') {

		$this->load->model(array('communication_preference_model'));
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'requested_at',
				'select' => 'cp.requested_at',
			),
			array(
				'dt' => $i++,
				'alias' => 'changes',
				'select' => 'cp.changes',
				'formatter' => function ($d, $row) {
					$config_comm_pref = $this->utils->getConfig('communication_preferences');

					$d = json_decode($d);
					$preference = lang('lang.norecyet');

					foreach ($d as $key => $value) {
						if(isset($config_comm_pref[$key]))
							$preference = lang($config_comm_pref[$key]);
					}

					return $preference;

		        },
		    ),
		    array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'p.username',
			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'cp.status',
				'formatter' => function ($d, $row) {
					switch ($d) {
						case Communication_preference_model::STATUS_SET_AS_PREFERENCE:
						return $status = lang('Set as preference');
						break;
						case Communication_preference_model::STATUS_CANCELLED:
						return $status = lang('Cancelled');
						break;
						default:
						return $status = lang('Set as preference');
						break;
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'notes',
				'select' => 'cp.notes',
				'formatter' => function ($d, $row) {

					return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'requested_by',
				'select' => 'cp.requested_by',
				'formatter' => function ($d, $row) {
					if($row['platform_used'] == Communication_preference_model::PLATFORM_SBE)
					{
						$user = (array) $this->db->get_where('adminusers', array('userId' => $d))->row();
						if(!empty($user))
							return $user['username'];

						return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
					elseif($row['platform_used'] == Communication_preference_model::PLATFORM_PLAYER_CENTER)
					{
						return $row['username'];
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'platform_used',
				'select' => 'cp.platform_used',
				'formatter' => function ($d, $row) {
					switch ($d) {
						case Communication_preference_model::PLATFORM_SBE:
						return $status = lang('SBE');
						break;
						case Communication_preference_model::PLATFORM_PLAYER_CENTER:
						return $status = lang('Player Center');
						break;
						default:
						return $status = lang('Player Center');
						break;
					}
				},
			),
		);

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "cp.requested_at >=";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "cp.requested_at <=";
			$values[] = $input['dateRangeValueEnd'];
		}

        $data = $this->communication_preference_model->getCommunicationPreferenceHistory($player_id,$where,$values);

		if ( $data > 0){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}

		$this->returnJsonResult($result);
	}

	/**
	 * Return Income Access Signup Report
	 *
	 * @param void
	 * @return json
	 */
	public function incomeAccessSignupReports() {
		$i = 0;
		$columns = array();

		$signup_csv_headers = $this->utils->getConfig('ia_daily_signup_csv_headers');

		foreach ($signup_csv_headers as $key => $header) {
			$data = array(
				'dt' => $i++,
				'alias' => $header,
				'select' => $header,
			);

			array_push($columns, $data);
		}

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$from = isset($input['date_from']) ? $input['date_from'] : null;
		$to = isset($input['date_to']) ? $input['date_to'] : null;
		$username = isset($input['username']) ? $input['username'] : null;

		$this->load->model('player_model');
		$data = $this->player_model->getDailySignupWithBtag($from, $to, $username);

		if ( $data > 0){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}

		$result['header_data'] = $signup_csv_headers;

		$this->returnJsonResult($result);

	}

	/**
	 * Return Income Access Sales Report
	 *
	 * @param void
	 * @return json
	 */
	public function incomeAccessSalesReports() {
		$i = 0;
		$columns = array();

		$sales_csv_headers = $this->utils->getConfig('ia_daily_sales_csv_headers');

		foreach ($sales_csv_headers as $key => $header) {
			$data = array(
				'dt' => $i++,
				'alias' => $header,
				'select' => $header,
			);

			array_push($columns, $data);
		}

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		$from = isset($input['date_from']) ? $input['date_from'] : null;
		$to = isset($input['date_to']) ? $input['date_to'] : null;
		$username = isset($input['username']) ? $input['username'] : null;

		$this->load->model('player_model');
		$data = $this->player_model->getDailySalesWithBtag($from, $to, $username);

		if ( $data > 0){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}

		$result['header_data'] = $sales_csv_headers;

		$this->returnJsonResult($result);

	}

	/***/
	public function chat_history($player_id = ''){
		$this->load->model(array('cs'));
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'date',
				'select' => 'c.date',
			),
			array(
				'dt' => $i++,
				'alias' => 'subject',
				'select' => 'c.subject',
			),
			array(
				'dt' => $i++,
				'alias' => 'session',
				'select' => 'c.session',
			),
			array(
				'dt' => $i++,
				'alias' => 'sender',
				'select' => 'p.username',
			),
			array(
				'dt' => $i++,
				'alias' => 'recepient',
				'select' => 'au.username',
			),
			array(
				'dt' => $i++,
				'alias' => 'action',
				'select' => 'c.messsageId',
				'formatter' => function($d, $row){
					$link = '<a href="javascript:void(0);" data-toggle="tooltip" title="'.lang('tool.cs01').'" onclick="modal('."'".'/cs_management/viewChatHistoryDetails/'.$row["messageId"]."'".', '."'".lang('cs.messagedetail')."'".')" data-original-title="Show Message"><span class="glyphicon glyphicon-zoom-in"></span></a>';
					return $link;
				}
			),
		);

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "c.date >=";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "c.date <=";
			$values[] = $input['dateRangeValueEnd'];
		}

		$data = $this->cs->getPlayerMessageHistory($player_id, $where, $values);
		if (!empty($data)){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}
		$this->returnJsonResult($result);

	}

	/**
	 * get bank history for a certain player
	 * @param  string $player_id player id
	 * @return json $result
	 */
	public function bank_history($player_id = ''){
		$this->load->model(array('payment'));
		$i = 0;
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'createdOn',
				'select' => 'bt.createdOn',
			),
			array(
				'dt' => $i++,
				'alias' => 'changes',
				'select' => 'pbh.changes',
			),
			array(
				'alias' => 'bankName',
				'select' => 'bt.bankName',
			),
			array(
				'dt' => $i++,
				'alias' => 'dwBank',
				'select' => 'pbd.dwBank',
				'formatter' => function ($d, $row){
					return ($d == 0) ? lang('player.ub05') : lang('player.ub06') ." ".lang($row['bankName']);
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'operator',
				'select' => 'pbh.operator',
			),
		);

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "pbh.createdOn >=";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "pbh.createdOn <=";
			$values[] = $input['dateRangeValueEnd'];
		}

		$data = $this->payment->getBankHistoryByPlayer($player_id, $where, $values);
		if (!empty($data)){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}
		$this->returnJsonResult($result);
	}


	/**
	 * detail: get responsible gaming history for a certain player
	 *
	 * @param int $player_id
	 * @param string $where $columns
	 * @param datetime $values
	 * @param post $request
	 * @return json $result
	 */
	public function rg_history($player_id = '') {
        $currency = $this->utils->getCurrentCurrency();

		$this->load->model(array('responsible_gaming'));
		$i = 0;
		$columns = array(
            array(
                'dt' => $i++,
                'alias' => 'id',
                'select' => 'responsible_gaming.id',
            ),
            array(
                'dt' => $i++,
                'alias' => 'created_at',
                'select' => 'responsible_gaming.created_at',
            ),
            array(
                'dt' => $i++,
                'alias' => 'date_from',
                'select' => 'responsible_gaming.date_from',
            ),
			array(
				'dt' => $i++,
				'alias' => 'date_to',
				'select' => 'responsible_gaming.date_to',
			),
			array(
				'dt' => $i++,
				'alias' => 'type',
				'select' => 'responsible_gaming.type',
				'formatter' => function ($d, $row) use($currency) {
					switch($d){
						case Responsible_gaming::SELF_EXCLUSION_TEMPORARY:
		                case Responsible_gaming::SELF_EXCLUSION_PERMANENT:
                            if($row['type']=="2"){
                                return $rfArr[$d]=lang('Self Exclusion, Permanent');
                            }else{
                                $startDate = strtotime($row['date_from']);
                                $endDate = strtotime($row['date_to']);
                                $datediff = $endDate - $startDate;
                                $days = floor($datediff / (60 * 60 * 24));
                                return $rfArr[$d]=lang('Self Exclusion Remaining Days')." - ".$days.lang('day');
                            }
		                break;
                        case Responsible_gaming::COOLING_OFF:
                            $day =$row['period_cnt'];
                            return $rfArr[$d] = lang('Cooling Off')." - ".$day." ".lang('day');
                            break;
		                case Responsible_gaming::TIMER_REMINDERS:
                            $min =$row['period_cnt'];
                            return $rfArr[$d] = lang('Time Reminders')." - ".$min." ".lang('min');
                            break;
		                case Responsible_gaming::SESSION_LIMITS:
                            $min =$row['period_cnt'];
                            return $rfArr[$d] = lang('Session limits')." - ".$min." ".lang('min');
                            break;
                        case Responsible_gaming::DEPOSIT_LIMITS:
                            return $rfArr[$d] = lang('Deposit Limits')." - " . $currency['symbol'] . $row['amount']." ".$row['period_cnt'].lang('day');
                            break;
                        case Responsible_gaming::LOSS_LIMITS:
                            return $rfArr[$d] =lang('Loss limits')." - ". $row['amount'];
                            break;
                        case Responsible_gaming::WAGERING_LIMITS:
                            return $rfArr[$d] =lang('Wagering Limits')." - " . $currency['symbol'] . $row['amount']." ".$row['period_cnt'].lang('day');
                            break;
		            }
		        },
		    ),
		    array(
				'dt' => $i++,
				'alias' => 'user',
				'select' => 'p.username',
			),
            array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'responsible_gaming.status',
				'formatter' => function ($d) {
					switch ($d) {
						case Responsible_gaming::STATUS_REQUEST:
                            return $status = lang('REQUEST');
                            break;
						case Responsible_gaming::STATUS_APPROVED:
                            return $status = lang('rg.approved');
                            break;
						case Responsible_gaming::STATUS_DECLINED:
                            return $status = lang('DECLINED');
                            break;
                        case Responsible_gaming::STATUS_CANCELLED:
                            return $status = lang('rg.cancelled');
                            break;
						case Responsible_gaming::STATUS_EXPIRED:
                            return $status = lang('EXPIRED');
                            break;
						case Responsible_gaming::STATUS_PLAYER_DEACTIVATED:
                            return $status = lang('rg.deactivated');
                            break;
                        case Responsible_gaming::STATUS_COOLING_OFF:
                            return $status = lang('rg.coolingoff');
                            break;
					}
				},
			),
            array(
                'dt' => $i++,
                'alias' => 'updated at',
                'select' => 'responsible_gaming_history.created_at',
            ),
            array(
				'dt' => $i++,
				'alias' => 'remarks',
				'select' => 'responsible_gaming.remarks',
                'formatter' => function ($d) {
				    return lang($d);
                }
			),
			array(
				'dt' => $i++,
				'alias' => 'action player',
				'select' => 'a.username',
			),
		);

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		if (isset($input['dateRangeValueStart'])) {
			$where[] = "rg.created_at >=";
			$values[] = $input['dateRangeValueStart'];
		}

		if (isset($input['dateRangeValueEnd'])) {
			$where[] = "rg.created_at <=";
			$values[] = $input['dateRangeValueEnd'];
		}

        $data = $this->responsible_gaming->getResponsibleGData($player_id,$where,$values);

		if ( $data > 0){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}
		$this->returnJsonResult($result);
	}

	public function transferAllWallet() {
		$this->load->model(array('player_model'));
		$result = [
			'success' => false,
			'message' => lang('notify.61'),
		];

		$player_id = $this->authentication->getPlayerId();
		$player_name = $this->player_model->getUsernameById($player_id);


		$this->utils->debug_log('--- check custom error notif ---', $player_name);

		if (empty($player_id) || empty($player_name)) {
			$this->returnJsonResult($result);
			return;
		}

		# testing notification error message
		# applicable for test player only
		if($this->utils->isEnabledFeature('cashier_custom_error_message')){
			$api = $this->utils->loadExternalSystemLibObject($this->input->post('transfer_to'));
			if (!empty($api->test_notif_player) && !empty($api->test_notif_error_code)) {
				if($api->test_notif_player == $player_name) {
					$this->CI->load->model(['operatorglobalsettings']);
					if($this->utils->getCashierCustomErrorMessage('transfer_fund_notif',$api->test_notif_error_code)){
						$message = $this->utils->composeCustomErrMsg($api->test_notif_error_code);
						$result['success'] = false;
						$result['message'] = $message;
						$this->returnJsonResult($result);
						return;
					}
				}
			}
		}

		# The last 'true' in the param list ignores the enabled_single_wallet_switch
		# setting which is used to control auto transfer upon entering game
		$transfer_result = $this->_transferAllWallet($player_id, $player_name, $this->input->post('transfer_to'),
			null, null, null, false, true);
		if (FALSE === $transfer_result || (!isset($transfer_result['success']) || (FALSE === $transfer_result['success']))) {
			//$result['message'] = lang('notify.62');

			if($this->utils->isEnabledFeature("cashier_custom_error_message")){
				$result['message'] = $transfer_result['message'];
			}else{
				$result['message'] = lang('notify.62');
			}

		} else {
			$result['success'] = true;
			$result['message'] = lang('notify.63');
		}

		$this->returnJsonResult($result);
	}

	/**
	 * detail: check if the email is exist
	 *
	 * @param string $email
	 * @return json
	 */
	public function checkplayerEmailExist($email = null) {
        if(static::API_ACL_RESULT_SUCCESS !== $this->_check_api_acl(__FUNCTION__, 'default')){
            return $this->_show_last_check_acl_response('json');
        }

        $email = $email ?: $this->input->get_post('email');
		$this->returnJsonResult((boolean) $this->is_exist($email, 'player.email'));
	}

	/**
	 * detail: check if the email is exist
	 *
	 * @param string $email
	 * @return json
	 */
	public function checkPlayerContactNumberExist($contact_number = null) {
	    if(static::API_ACL_RESULT_SUCCESS !== $this->_check_api_acl(__FUNCTION__, 'default')){
	        return $this->_show_last_check_acl_response('json');
        }

		$contact_number = $contact_number ?: $this->input->post('contact_number');
		$this->returnJsonResult((boolean) $this->is_exist($contact_number, 'playerdetails.contactNumber'));
	}

    public function trialCashback($player_id, $begtime=null, $endtime=null){
        if (!$this->isLoggedAdminUser()) { return; }

        if( empty($begtime) ){ $begtime = " - 21 days"; }
        if( empty($endtime) ){ $endtime = "now"; }
        $time_beg = strtotime($begtime);
        $time_end = strtotime($endtime);

        $this->load->model(array('group_level','player','cashback_request','game_logs'));
        $this->load->library(array('player_manager'));

        $data = [];
        $data["player_id"] = $player_id;
        $data["begtime"] = $begtime;
        $data["endtime"] = $endtime;
        $data["time_interval"] = array(
                "begin" => date("c", $time_beg ),
                "end" => date("c", $time_end ),
                );

        {
            $player_level_id = $this->group_level->getPlayerLevelId($player_id);
            $can_user_cashback = $this->group_level->isAllowedCashback($player_level_id);
            $chk1 = ( $can_user_cashback == 'true' );

            $data["checkPermissionForCashback"] = array(
                    "player_level_id" => $player_level_id,
                    "can_user_cashback" => $can_user_cashback,
                    "return" => ( $can_user_cashback == 'true' ),
                    );
        }
        {
            $cashback_requests = $this->cashback_request->listCashbackRequest($player_id);
            $data["cashback_requests"] = $cashback_requests;

            $game_logs = $this->game_logs->listGameLog($player_id);
            $data["game_logs"] = $game_logs;
        }
        //$data["level"];
        $this->returnJsonResult($data);
    }

    public function DepositWalletTransaction() {
		$this->load->model(array('report_model', 'transactions'));

		$request = $this->input->post();
		$player_id = $this->authentication->getPlayerId();
		if(empty($player_id)) {
			return $this->returnJsonResult($this->data_tables->empty_data($request));
		}
		$result = $this->report_model->transactionsByWalletDeposit($player_id, $request);
		return $this->returnJsonResult($result);
	}

    public function getRebateTransaction() {
		$this->load->model(array('report_model', 'transactions'));
		$request = $this->input->post();
		$player_id = $this->authentication->getPlayerId();
		if(empty($player_id)) {
			return $this->returnJsonResult($this->data_tables->empty_data($request));
		}
		// $result = $this->report_model->transactionsByGetRebate($player_id, $request);

		// use table total_cashback_player_game_daily
		// display also referred player and cashback type
		$result = $this->report_model->playerCashbackHistory($player_id, $request);
		return $this->returnJsonResult($result);
	}

	/**
	 * for Player Center - Account History - Friend Referral Status
	 */
	public function getReferralFriend(){
		$player_id = $this->authentication->getPlayerId();
		$i = 0;
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
		$where = array();
		$values = array();

		$columns = array(
			array(
				'dt' => $i++,
				'select' => 'player.createdOn',
                'alias' => 'createdOn',
                'data' => 'createdOn',
			),
			array(
				'dt' => $i++,
				'select' => 'player.username',
				'alias' => 'username',
                'data' => 'username',
			),
			array(
				'dt' => $i++,
				'select' => 'SUM(total_player_game_day.betting_amount)',
				'alias' => 'betting_amount',
                'data' => 'betting_amount',
			),
			array(
                'dt' => $i++,
                'select' => 'SUM(transactions.amount)',
                'alias' => 'amount',
                'data' => 'amount',
			),
		);
		$table = 'player';
		$joins = array(
			'playerfriendreferral referred' => 'referred.invitedPlayerId = player.playerId',
			'total_player_game_day' => 'total_player_game_day.player_id = total_player_game_day.player_id = player.playerId',
			'transactions' => 'transactions.to_type = ' . Transactions::PLAYER . ' AND transactions.transaction_type = ' . Transactions::PLAYER_REFER_BONUS . ' AND transactions.status = ' . Transactions::APPROVED . ' AND transactions.id = referred.transactionId',
		);
		if (isset($input['dateRangeValueStart'], $input['dateRangeValueEnd'])) {
			$where[] = "player.createdOn BETWEEN ? AND ?";
			$values[] = $input['dateRangeValueStart'];
			$values[] = $input['dateRangeValueEnd'];
		}
		$where[] = "player.refereePlayerId = ? ";
		$values[] = $player_id;
		$group_by = array('player.username');

		$result = $this->data_tables->get_data($request, $this->data_tables->apply_request_columns($columns, $request), $table, $where, $values, $joins, $group_by);
		$this->returnJsonResult($result);

	}

    public function WithdrawWalletTransaction() {
		$this->load->model(array('report_model', 'transactions'));

		$request = $this->input->post();
		$player_id = $this->authentication->getPlayerId();
		if(empty($player_id)) {
			return $this->returnJsonResult($this->data_tables->empty_data($request));
		}
		$result = $this->report_model->transactionsByWalletWithdraw($player_id, $request);
		return $this->returnJsonResult($result);
    }

    public function TransferWalletTransaction() {
		$this->load->model(array('report_model', 'transactions'));

		$request = $this->input->post();
		$player_id = $this->authentication->getPlayerId();
		if(empty($player_id)) {
			return $this->returnJsonResult($this->data_tables->empty_data($request));
		}
		$result = $this->report_model->transactionsByWalletTransfer($player_id, $request);
		return $this->returnJsonResult($result);
    }

    /**
     * ajax endpoint for tab promotions on player center transaction history page
     * Code moved to Player_promo::getPlayerPromoHistoryWLimitById()
     *
     * @uses	Player_promo::getPlayerPromoHistoryWLimitById()
     * @see 	stable_center2/report/report.php
     * @return	JSON	JSON dataset for Datatable.js
     */
    public function getPlayerPromoHistoryWLimitById() {
    	$player_id = $this->authentication->getPlayerId();
    	$result = $this->player_promo->getPlayerPromoHistoryWLimitById($player_id, $this->input->post());

    	return $this->returnJsonResult($result);
    }

    public function crossSiteAPI(){
    	$api_url = $this->input->get('api_url');
    	$output = file_get_contents($api_url);

        $this->output->set_header('Access-Control-Allow-Origin:*');
        $this->output->set_content_type('application/json');
        $this->output->set_output($output);

    	return $this->returnJsonResult($output);
    }

    public function  tzchk(){

        echo "date.timezone:". ini_get("date.timezone")."\n";

        $input_str = "2017-08-01 00:00:00";

        $tzs = array(
                "",
                "UTC",
                "Etc/UTC",
                "Zulu",
                "Europe/Kiev",
                "GMT",
                "Asia/Taipei",
                "Asia/Hong_Kong",
                "Asia/Seoul",
                );

        foreach( $tzs as $tz ){
            if( !empty($tz) ){ ini_set("date.timezone",$tz); }

            if( empty($tz) ){ $tz = "(default)"; }

            $tsmp = strtotime($input_str);
            printf( "%20s strtotime('%s') => %s , mktime(): %s , time(): %s \n", $tz, $input_str, $tsmp, mktime(), time() );
        }
	}
	/*
	 * check payment type
	 * */

	public function chkPayType($externalSysId){


	    /*
	     * if count >2 ,paymnet is qrcode else not qrcode
	     * */
        $sys = $this->CI->external_system->getSystemName($externalSysId);

        $cons = substr_count($sys, '_');
        if($cons>2){
            $rs['qrcode'] = 1;
        }else{
            $rs['qrcode'] = 0;
        }


       return $this->returnJsonResult($rs);
    }

    /*
     *send message by admin
     *
     * @param string $playerId  id for player
	 * @param string $subject messages subject
	 * @param string $message messagedetails message
     * */

    public function sendMessageAdmin(){
        $rs['flg'] = 0;
        $request = $this->input->post();
        $playerId = $request['playerId'];
        $subject = $request['subject'];
        $message = $request['msg'];
        $this->load->library(array('authentication'));

        if($this->isLoggedAdminUser()) {
            $userId = $this->authentication->getUserId();
            $username = $this->authentication->getUsername();
            //clean data
            if(intval($playerId)<0){
                return $this->returnJsonResult($rs);
            }
            $subject = htmlentities($subject, ENT_QUOTES, 'UTF-8');
            $message = htmlentities($message, ENT_QUOTES, 'UTF-8');
            $id = $this->utils->adminSendMsg($userId,$playerId,$username,$subject,$message);
            if($id>0){
                $rs['flg']=1;
            }


        }else{

            $rs['msg']="not authentication";
        }

        return $this->returnJsonResult($rs);


    }
    public function getAllMsgTpl(){
        if($this->isLoggedAdminUser()) {
            $this->load->model(array('operatorglobalsettings',));
            $rs = $this->operatorglobalsettings->getAllMsgTpl();
            $rfArr = [];
            foreach ($rs as $data){
                $name = $data['name'];
                $optval = $data['template'];
                $note = $data['note'];
                $rfArr[]=array('name'=>$name,'value'=>$optval,'note'=>$note);

            }
            return $this->returnJsonResult($rfArr);
        }
    }

    public function setDefaultMsgTpl(){
        if($this->isLoggedAdminUser()) {
            $this->load->model(array('operatorglobalsettings'));
            $request = $this->input->post();
            $reset = $request['reset'];

            /*  build data row
             *
             *
             */
            $depositOk=lang('msgtpl.depositOk');
            $depositFail=lang('msgtpl.depositFail');
            $WithdrawalsOk =lang('msgtpl.WithdrawalsOk');
            $WithdrawalsFail=lang('msgtpl.WithdrawalsFail');

            $jsonAry['type']="text";
            $jsonAry['default_value']="";
            $jsonStr =json_encode($jsonAry);


            $rows=array(
                array(
                    'name' => 'Deposits Success',
                    'value' => 'msgTpl',
                    'note' => 'Deposits',
                    'template' => htmlentities($depositOk),
                    'description_json' => $jsonStr,
                ),
                array(
                    'name' => 'Deposits Failed',
                    'value' => 'msgTpl',
                    'note' => 'Deposits',
                    'template' => htmlentities($depositFail),
                    'description_json' => $jsonStr,
                ),
                array(
                    'name' => 'Withdrawals Success',
                    'value' => 'msgTpl',
                    'note' => 'Withdrawals',
                    'template' => htmlentities($WithdrawalsOk),
                    'description_json' => $jsonStr,
                ),
                array(
                    'name' => 'Withdrawals Failed',
                    'value' => 'msgTpl',
                    'note' => 'Withdrawals',
                    'template' => htmlentities($WithdrawalsFail),
                    'description_json' => $jsonStr,
                )
            );



            $rs = array();
            if($reset) {

                $rlt = $this->operatorglobalsettings->resestMsgTpl($rows);
                if ($rlt) {
                    $rs['flg'] = 1;
                } else {
                    $rs['msg'] = "not authentication";
                }
            }

        }else{
            $rs['flg']=0;
            $rs['msg']="not authentication";
        }

        return $this->returnJsonResult($rs);

    }


    public function setPaymentInPopWindow(){
        if($this->isLoggedAdminUser()) {
            $request = $this->input->post();
            $this->load->model(array('external_system'));
            $rs['flg'] =$this->external_system->triggerPopWindow($request);


        }else{
            $rs['flg']=0;
            $rs['msg']="not authentication";
        }

        return $this->returnJsonResult($rs);
    }


    public function responsiblegaming(){
        $request = $this->input->post();
        $type = $request['type'];
        $this->load->model(array('responsible_gaming'));
        $defaultRs['status']=0;
        $uid= $request['uid'];
        switch($type){
            case "selfExclusion":
                $selftype = $request['selftype'];


                switch ($selftype){
                    case "Temporary":
                        $month = intval($request['month']);
                        if($month==0){
                            return $this->returnJsonResult($defaultRs);
                        }
                        $rs =  $this->responsible_gaming->saveSelfExclusion($uid, $month);
                        break;
                    case "Permanent":
                        $rs =  $this->responsible_gaming->saveSelfExclusion($uid);
                        break;
                    default:
                        return $this->returnJsonResult($defaultRs);
                        break;
                }
                break;
            case "coolingOff":
                $day = intval($request['day']);
                if($day==0){
                    return $this->returnJsonResult($defaultRs);
                }
                $rs =  $this->responsible_gaming->saveCoolOff($uid,$day);
                break;

                break;
            case "timeReminder":

                break;
            case "sessionLimits":
                break;
            case "lostLimits":
                break;
            case "depositLimits":
                break;
            default:
                return $this->returnJsonResult($defaultRs);
                break;


        }
        return $this->returnJsonResult($rs);

    }

	/**
	 * Query player's deposit today! by token
	 * @param	string	$token	the token generated on login, stored in table common_token.
	 * @return	JSON	{ status: string, msg: string, result:  }, where:
	 *                status	any of [ 'success', 'error' ]
	 *                msg		error message if status == 'error'
	 *                result	result (player's total deposit today as )
	 */
	public function getPlayerDepositTodayByToken($token = null) {
		$ret = [ 'status' => 'error' , 'msg' => 'incomplete', 'result' => 0 ];

		try {
			$this->load->model(['common_token', 'transactions']);
			$player_id = $this->common_token->getPlayerIdByToken($token);
			if (empty($player_id)) {
				throw new Exception('Invalid token', 1);
			}

			$ldelim = $this->utils->getTodayForMysql();
			$rdelim = "{$ldelim} 23:59:59";
			$player_deposit_today = $this->transactions->getPlayerTotalDeposits($player_id, $ldelim, $rdelim);

			$ret = [ 'status' => 'success', 'msg' => '', 'result' => floatval($player_deposit_today) ];
		}
		catch (Exception $ex) {
			$ret = [ 'status' => 'error' , 'msg' => $ex->getMessage(), 'result' => 0 ];
		}
		finally {
			// Force float format, if applicable
			$ret['result'] = floatval($ret['result']);
			$this->returnJsonResult($ret);
		}
	}



	/*
	 * detail: get Withdrawal Declined Category
	 *
	 * @return json
	 */
	public function getCommonCategory() {

		if (!$this->isLoggedAdminUser()) {
			return;
		}

		$this->load->model('common_category');

		$request = $this->input->post();

		$result = $this->common_category->getWithdrawalDeclinedCategory($request);

		// $this->output->set_content_type('application/json')->set_output(json_encode($result));
		$this->returnJsonResult($result);
	}

	/*
	 * detial: getPromoApplyable
	 * @return true/false
	 */
	public function getPlayerPromoApplyable() {
		$input = $this->input->post();
		$deposit_amount = $input['deposit_amount'];
		$playerId = $this->authentication->getPlayerId();
		$this->load->model(['promorules', 'player_model']);
		$avail_promocms_list = $this->promorules->getAvailPromoOnDeposit($playerId);
		if(empty($avail_promocms_list)){
            return $this->returnJsonResult([]);
        }

		$this->utils->debug_log('--- getPlayerPromoApplyable --- getAvailPromoOnDeposit', $avail_promocms_list);
		$applyable_promocms_list = [];
		foreach ($avail_promocms_list as $promoCmsSettingId => $promo_data) {
			$data = [];
			$render = true;
			$promorule=$this->promorules->getPromoruleByPromoCms($promoCmsSettingId);
			$preapplication=false;
			$playerPromoId=null;
			$triggerEvent='deposit_amount_input';
			$dry_run=true;
			$mock=$this->utils->getConfig('promotion_mock');
			$notnull_mock=[];
			foreach ($mock as $key => &$value) {
				$post_value=$this->input->post($key);
				//maybe value will be 0
				if($post_value!==FALSE && $post_value!==null && $value!==''){
					$value=$post_value;
				}
				if($value!==FALSE && $value!==null && $value!==''){
					$notnull_mock[$key]=$value;
				}
			}
			$extra_info=['debug_log'=>'', 'mock'=>$notnull_mock, 'depositAmount' => $deposit_amount];
			$success=false;
			$message=null;
			$errorMessageLang=null;
			$playerBonusAmount=null;
			if(!empty($playerId)){
				list($success, $message)=$this->promorules->checkOnlyPromotionBeforeDeposit($playerId, $promorule, $promoCmsSettingId, $preapplication, $playerPromoId, $extra_info, $dry_run);
				$message=lang($message);
				$this->utils->debug_log('--- getPlayerPromoApplyable --- result', $success, $message, $extra_info);
			}
			if ($success) {
				$applyable_promocms_list[] = ['key'=> $promoCmsSettingId];
			}
		}
		$this->returnJsonResult($applyable_promocms_list);
	}

	/**
	 * detail: check if the username is exist no criteria needed
	 *
	 * @param string $username
	 * @return json
	 */
	public function playerUsernameExistNoCriteria($username = null) {
		$username = $username ?: $this->input->post('username');
		$result =false;

		if(!empty($username)){

			$result = (boolean) $this->is_exist($username, 'player.username');

		}
		$this->returnJsonResult($result);
	}

	//refference from twinbet branch
	//03-08-2018 | OGP-5331
	//Super Report | super_report
	public function super_report_receiver($table) {
		$input = $this->input->post();

		$this->load->model('super_report');

		$data = $input['data'];

		try {
			if ( ! empty($data)) {
				$this->super_report->superReportReceiverInsertBatch($table, $data);
			}
			$response['success'] = TRUE;
		} catch (Exception $e) {
			$response['success'] = FALSE;
			$response['message'] = $e->getMessage();
		}

		return $this->returnJsonResult($response);
	}

	public function declinePlayerFriendReferral(){
		$this->load->model(['player_friend_referral','player_model','promorules','friend_referral_settings','player_promo']);
		$referralId = $this->input->post('id');
		$invited_player = $this->input->post('invited_player');
		$inviter = $this->input->post('inviter');
		$this->CI->player_friend_referral->updatePlayerFriendReferral($referralId,array("status" => player_friend_referral::STATUS_CANCELLED));
		$this->CI->player_model->updatePlayer($invited_player,array("refereePlayerId" => NULL));

		$promorulesId = $this->CI->promorules->getSystemManualPromoRuleId(); // manual promo rules
		$promoCmsSettingId = $this->CI->promorules->getSystemManualPromoCMSId(); // manual cms setting
		$friend_referral_settings = $this->CI->friend_referral_settings->getFriendReferralSettings();
		if($friend_referral_settings['promo_id'] != 0) { // if bind a cms on friend referral
			$promoCmsSettingId = $friend_referral_settings['promo_id'];
			$promorulesId = $this->CI->promorules->getPromorulesIdByPromoCmsId($friend_referral_settings['promo_id']);
		}
		$success = true;
		$promo = $this->player_promo->getPlayerPromoIdByPlayerIdAndPromoCmsSettingId($inviter,$promoCmsSettingId);
		$playerPromoId = $promo->playerpromoId;

		//declined promo request if exist
        $playerPromoStatus = $this->player_promo->getPlayerPromoStatusById($playerPromoId);
        $allow_decline_player_promo = in_array($playerPromoStatus, [PLAYER_PROMO::TRANS_STATUS_REQUEST,
                                                                    Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS,
                                                                    Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS]);
		if(!empty($playerPromoId) && $allow_decline_player_promo){
			$this->CI->player_model->startTrans();

			$adminId = $this->authentication->getUserId();
			$reason = "Decline from friend refferal by  User " . $this->authentication->getUsername();
			$playerPromo=$this->player_promo->getPlayerPromo($playerPromoId);
			$promorule=$this->promorules->getPromoRuleRow($playerPromo->promorulesId);
			$promoCmsSettingId=$playerPromo->promoCmsSettingId;
			$playerId=$playerPromo->playerId;
			$bonusAmount=null;
			$depositAmount = null;
			$withdrawConditionAmount=null;
			$betTimes=null;

			$dataadasda = $this->promorules->declinePromo($playerId, $promorule, $promoCmsSettingId, $adminId,
				$bonusAmount, $depositAmount, $withdrawConditionAmount, $betTimes,
				$reason, $playerPromoId);


			$this->saveAction('Player Management', 'Decline Player Promo Application', "User " . $this->authentication->getUsername() . " has successfully declined player promo application request.");

			$success = $this->CI->player_model->endTransWithSucc();
		}
		return $success;
	}

	/**
	 * detail: check if the username is exist no criteria needed
	 *
	 * @param string $username
	 * @return json
	 */
	public function search_multiple_id($id) {

		$success = false;
		$url = '';
		$is_username = true;
		$prefix = substr($id, 0,1);
		$suffix = substr($id,1,12);
		$len = strlen($id);
		if($len == 13 && is_numeric($suffix)){
			switch ($prefix) {
				case 'T':
					//transfer
					$url=site_url('/payment_management/transfer_request?secure_id='.$id);
					$success=true;
					$is_username=false;
					break;

				case 'D':
					//deposit
					$url=site_url('/payment_management/deposit_list?secure_id='.$id);
					$success=true;
					$is_username=false;
					break;

				case 'W':
					//withdraw
					$url=site_url('/payment_management/viewWithdrawalRequestList?withdraw_code='.$id);
					$success=true;
					$is_username=false;
					break;

				default:
					$is_username=true;
					break;
			}
		}

		if($is_username){
			$this->load->model(['player_model']);
			$username=$id;
			$playerId=$this->player_model->getPlayerIdByUsername($username);
			$success=!empty($playerId);
			if($success){
				$url=site_url('/player_management/userInformation/'.$playerId);
			}
		}

		$result=['success'=>$success, 'url'=>$url];

		$this->returnJsonResult($result);
	}

	public function sync_table_receiver_by_date($table, $id_field, $date_field, $unique_id_field='null') {
		//$table = $table.'1';
		$this->utils->debug_log('sync_table_receiver_by_date params',$table, $id_field, $date_field, $unique_id_field);
		$input = $this->input->post();
		$data = $input['data'];
		$response = null;
		$response['message'] = null;


		if (!empty($data)) {
			$for_verification_ids = [];
			$date_sync_from = null;
			$date_sync_to = null;

			foreach ($data as $value) {
				array_push($for_verification_ids, $value[$id_field]);
				$date_sync_from = isset($value['table_date_sync_from']) ? $value['table_date_sync_from'] : null;
				$date_sync_to = isset($value['table_date_sync_to']) ? $value['table_date_sync_to'] : null;
				//remove added column to insert table successfully
				unset($value['table_date_sync_from']);
				unset($value['table_date_sync_to']);
				try {
					$unique_id_field = ($unique_id_field == 'null') ? null : $unique_id_field ;
					//prevent duplicate error
                    if(!empty($unique_id_field)){
                    	//prevent duplicate error
                    	$this->checkAndDeleteWouldbeDuplicateUniqueId($table,$value,$unique_id_field);
                    }
					$sql='SELECT * FROM `'.$table.'` WHERE `'.$id_field.'` = "'.$value[$id_field].'" ';
					$query1 = $this->db->query($sql);
					$rows_count = $query1->num_rows();

					if($rows_count > 0){
						$this->db->where($id_field, $value[$id_field]);
						$query2 = $this->db->update($table, $value);
					}else{
						$this->db->insert($table, $value);
					}
				} catch (Exception $e) {
					$response['message'] .= $e->getMessage().' ';
				}
			}
			try { // @todo NEED TEST THE FUNCTION.
				$sql2= <<<EOF
				SELECT * FROM `$table` WHERE `$date_field`>="$date_sync_from" and `$date_field`<='$date_sync_to'
EOF;
				$query2 = $this->db->query( trim($sql2) );
				$result = $query2->result_array();

				foreach ($result as $row) {
					$id = $row[$id_field];
					if(!in_array($id,$for_verification_ids)){
						$sql3 =<<<EOF
						DELETE FROM `$table` WHERE `$id_field`=$id
EOF;
						$this->db->query( trim($sql3) );
					}
				}
			} catch (Exception $e) {
				$response['message'] .= $e->getMessage().' ';
				$this->utils->debug_log('error response', 'table', $table, $response);
			}
		}
		$response['success'] = TRUE;
		return $this->returnJsonResult($response);
	}


	public function sync_table_receiver_by_id($table,$id_field,$unique_id_field='null') {
		//$table = $table.'1';
		$this->utils->debug_log('sync_table_receiver_by_id params',$table,$id_field,$unique_id_field);
		$input = $this->input->post();
		$data = $input['data'];
		$response = null;
		$response['message'] = null;
		$unique_id_field = ($unique_id_field == 'null') ? null : $unique_id_field ;
		if (!empty($data)) {
			$for_verification_ids = [];
			$id_sync_from = null;
			$id_sync_to = null;
			$min_id = null;
			$max_id = null;

			foreach ($data as $value) {
				array_push($for_verification_ids, $value[$id_field]);
				//for id within range
				$id_sync_from = isset($value['table_id_sync_from']) ? $value['table_id_sync_from'] : null;
				$id_sync_to = isset($value['table_id_sync_to']) ? $value['table_id_sync_to'] : null;
				//for the whole table
				$min_id = isset($value['table_min_id_']) ?  $value['table_min_id_'] : null;
				$max_id= isset($value['table_max_id_']) ? $value['table_max_id_'] : null;
				//Remove added column to insert table successfully
				unset($value['table_id_sync_from']);
				unset($value['table_id_sync_to']);
				unset($value['table_min_id_']);
				unset($value['table_max_id_']);
				//Insert and Update
				try {
                    if(!empty($unique_id_field)){
                    	//prevent duplicate error
                    	$this->checkAndDeleteWouldbeDuplicateUniqueId($table,$value,$unique_id_field);
                    }
					$sql='SELECT * FROM `'.$table.'` WHERE `'.$id_field.'` = "'.$value[$id_field].'" ';

					$query1 = $this->db->query($sql);
					//$rows_count = $query1->num_rows();
					$rows_count = count($query1->result_array());
					if($rows_count > 0){
						$this->db->where($id_field, $value[$id_field]);
						$query2 = $this->db->update($table, $value);
					}else{
						$this->db->insert($table, $value);
					}

				} catch (Exception $e) {
					$response['message'] .= $e->getMessage().' ';
				}
			}
			$this->utils->debug_log('verificationsIDs',$for_verification_ids );
		    //For inside range deletion
			$min_verify_id = min($for_verification_ids);
			$max_verify_id = max($for_verification_ids);
			try {
				$this->utils->debug_log('id_sync_from',$id_sync_from,'id_sync_to',$id_sync_from);
				if(!empty($id_sync_from) || !empty($id_sync_to)){
					$sql2='SELECT * FROM `'.$table.'` WHERE `'.$id_field."`>='".$id_sync_from."' and ".$id_field."<='".$id_sync_to."'";
					$query2 = $this->db->query($sql2);
					$result = $query2->result_array();
					$sql6='DELETE FROM `'.$table.'` WHERE   `'.$id_field.'` NOT IN ('.implode(",", $for_verification_ids).')   and `'.$id_field."`>'".$min_verify_id."' and ".$id_field."<'".$max_verify_id."'" ;
					$this->db->query($sql6);
				}else{
					$sql4='DELETE FROM `'.$table.'` WHERE `'.$id_field."`<'".$min_id."'";
					$this->db->query($sql4);
					$sql5='DELETE FROM `'.$table.'` WHERE `'.$id_field."`>'".$max_id."'";
					$this->db->query($sql5);
					$this->utils->debug_log('outside range deletion', 'max_id', $max_id, 'min_id', $min_id, 'sql4', $sql4, 'sql5', $sql5);
					$sql7='DELETE FROM `'.$table.'` WHERE   `'.$id_field.'` NOT IN ('.implode(",", $for_verification_ids).')   and `'.$id_field."`>'".$min_verify_id."' and ".$id_field."<'".$max_verify_id."'" ;
					$this->db->query($sql7);
					$this->utils->debug_log('inside range deletion', 'sql7', $sql7);
				}

			} catch (Exception $e) {
				$response['message'] .= $e->getMessage().' ';
				$this->utils->debug_log('error response', 'table', $table, $response);
			}
		}
		$response['success'] = TRUE;
		return $this->returnJsonResult($response);
	}


	private function checkAndDeleteWouldbeDuplicateUniqueId($table,$value,$unique_id_field){
		//check idx field if duplicate ,sometimes origin tables has changed -ex. total_player_game_hour
		$sql='SELECT * FROM `'.$table.'` WHERE `'.$unique_id_field.'` = "'.$value[$unique_id_field].'" ' ;
		$this->utils->debug_log('checkAndDeleteDuplicateUniqueId', $sql);
		$query = $this->db->query($sql);
		$rows_count= $query->num_rows();
		if($rows_count > 0){
			$sql ='DELETE FROM `'.$table.'` WHERE `'.$unique_id_field.'` = "'.$value[$unique_id_field].'" ' ;
			$this->db->query($sql);
		}
		return;
	}


	public function playertaggedlist() {
		$this->load->model(array('report_model'));
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$request = $this->input->post();
		$permissions = $this->getContactPermissions();
		$is_export = false;
		$result = $this->report_model->playertaggedlist($request, $permissions, $is_export);
		$this->returnJsonResult($result);

	}

	public function playerFavoriteGames(){
        $this->load->model(['player_preference']);

	    if(!$this->authentication->isLoggedIn()){
	        $result = [
	            'status' => FALSE,
                'message' => lang('Not Login')
            ];
	        return $this->returnJsonResult($result);
        }

        $player_id = $this->authentication->getPlayerId();

        $display_keys = [
            'id',
            'game_platform_id',
            'game_type_id',
            'game_code',
            'game_name',
            'external_game_id',
            'sub_game_provider',
            'dlc_enabled',
            'flash_enabled',
            'offline_enabled',
            'mobile_enabled',
            'flag_new_game',
            'image_url',
            'url'
        ];

        $game_list = $this->utils->getActiveGameList();

        $player_myfavorites = $this->player_preference->getPlayerMyFavorites($player_id);

	    foreach($game_list as &$game){
            $game = array_intersect_key($game, array_flip($display_keys));

            if(isset($player_myfavorites[$game['id']])){
                $game['favorite'] = $player_myfavorites[$game['id']];
            }else{
                $game['favorite'] = 0;
            }
        }

        $result = [
            'status' => TRUE,
            'message' => NULL,
            'game_list' => $game_list
        ];
        return $this->returnJsonResult($result);
    }

    public function playerSaveFavoriteGames(){
	    $this->load->model(['player_preference', 'game_description_model']);

        if(!$this->authentication->isLoggedIn()){
            $return = [
                'status' => FALSE,
                'message' => lang('Not Login')
            ];
            return $this->returnJsonResult($return);
        }

        $player_id = $this->authentication->getPlayerId();

        $myfavorite_games = $this->input->post('game_ids');

        $game_list = $this->game_description_model->getGameDescriptionByIdList($myfavorite_games);

        $result = $this->player_preference->updatePlayerMyFavorites($player_id, $game_list);

        $return = [
            'status' => TRUE,
            'message' => NULL
        ];

        if($result){

        }else{
            $return['status'] = FALSE;
        }

        return $this->returnJsonResult($return);
    }

    public function player_deposit_notify(){
        $this->load->model(['sale_order']);

        if(!$this->authentication->isLoggedIn()){
            $return = [
                'status' => FALSE,
                'message' => lang('Not Login')
            ];
            return $this->returnJsonResult($return);
        }

        $player_id = $this->authentication->getPlayerId();

        $sale_order_id = $this->input->post('sale_order_id');

        $result = $this->sale_order->setPlayerNotify($player_id, $sale_order_id, 1);

        $return = [
            'status' => TRUE,
            'message' => NULL
        ];

        if($result){

        }else{
            $return['status'] = FALSE;
        }

        return $this->returnJsonResult($return);
    }

    /**
	 * detail: get Kingrich api response logs
	 *
	 * @param string transaction_batch_id
	 * @param datetime create_date
	 * @return json
	 */
	public function kingrich_api_response_logs($player_id = null, $not_datatable = '') {
		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;

		$result = $this->report_model->kingrichApiResponseLogs($request, $player_id, $is_export, $not_datatable);

		$this->returnJsonResult($result);
	}

	public function ole777_wager_sync_list() {
		$this->load->model(['ole_reward_model']);

		$request = $this->input->post();
		$result = $this->ole_reward_model->get_local_sync_list($request);
		$this->returnJsonResult($result);
	}

	/**
	 * detail: get Kingrich summary report
	 *
	 * @param string transaction_batch_id
	 * @param datetime create_date
	 * @return json
	 */
	public function kingrich_summary_report($player_id = null, $not_datatable = '') {
		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;

		$result = $this->report_model->kingrichSummaryReport($request, $player_id, $is_export, $not_datatable);

		$this->returnJsonResult($result);
	}


    /**
     * detail: getExternalApiResponseByOrder
     * @param  int $orderId     [sale_orders id]
     * @return json
     */
    public function getExternalApiResponseByOrderId($orderId){
        $saleOrder = $this->CI->sale_order->getSaleOrderById($orderId);
        $api = $this->utils->loadExternalSystemLibObject($saleOrder->system_id);
        $result = $api->getExternalApiResponseByOrder($saleOrder);
        return $this->returnJsonResult($result);
    }

    /**
     * detail: getExternalApiResponseByOrder
     * @param  int $orderId     [sale_orders id]
     * @return json
     */
    public function setExternalApiValueByOrderId($orderId){
        $external_data = $this->input->post("external_data");

        $saleOrder = $this->CI->sale_order->getSaleOrderById($orderId);
        $api = $this->utils->loadExternalSystemLibObject($saleOrder->system_id);
        $result = $api->setExternalApiValueByOrder($saleOrder, $external_data);
        return $this->returnJsonResult($result);
    }

    public function submit_request_form(){
        $this->load->library(['form_validation', 'player_message_library']);
        if(!$this->utils->isEnabledFeature('enable_player_message_request_form')){
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('Bad Request'));
        }

        $submit_request_form_call_times = (int)$this->session->userdata('submit_request_form_call_times');
        $submit_request_form_call_times++;

        $this->session->set_userdata('submit_request_form_call_times', $submit_request_form_call_times);

        if($submit_request_form_call_times > (int)$this->CI->utils->getConfig('message_request_form_default_allow_submit_times')){
            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('Bad Request'));
        }

        $request_form_settings = $this->CI->player_message_library->getRequestFormSettings();

        $this->form_validation->set_message('required', lang('formvalidation.required'));

        $player_id = NULL;
        $player_name = NULL;
        $real_name = NULL;
        $username = NULL;
        $contact_number = NULL;
        $email = NULL;

        if($this->authentication->isLoggedIn()){
            $player_id = $this->authentication->getPlayerId();
            $player_name = $this->authentication->getUsername();
            if(!$request_form_settings['enable_for_player']){
                return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('Not Login'));
            }
        }else{
            if(!$request_form_settings['enable_for_guest']){
                return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, lang('Not Login'));
            }
        }

        if($request_form_settings['real_name_enable']){
            $real_name_validator_rule = 'trim|xss_clean';
            $real_name_validator_rule .= ($request_form_settings['real_name_required']) ? 'required' : '';

            $this->form_validation->set_rules('real_name', lang('First Name'), $real_name_validator_rule);
            $real_name = $this->input->post('real_name', TRUE);
        }

        if($request_form_settings['username_enable']){
            $username_validator_rule = 'trim|xss_clean';
            $username_validator_rule .= ($request_form_settings['username_required']) ? 'required' : '';

            $this->form_validation->set_rules('username', lang('Username'), $username_validator_rule);
            $username = $this->input->post('username', TRUE);
        }

        if($request_form_settings['contact_method_enable']){
            $contact_method_name = ($request_form_settings['contact_method'] == 'email') ? lang('Email Address') : lang('Contact Number');
            $contact_method_validator_rule = 'trim|xss_clean';
            $contact_method_validator_rule .= ($request_form_settings['contact_method_required']) ? 'required' : '';

            $this->form_validation->set_rules('contact_method', $contact_method_name, $contact_method_validator_rule);
            if($request_form_settings['contact_method'] == 'email'){
                $email = $this->input->post('contact_method', TRUE);
            }else{
                $contact_number = $this->input->post('contact_method', TRUE);
            }
        }

        if ($this->form_validation->run() == false) {
            $message = $this->form_validation->error_string();

            return $this->returnCommon(BaseController::MESSAGE_TYPE_ERROR, $message);
        }

        $result = $this->player_message_library->addRequestForm($player_id, $player_name,  $real_name, $username, $contact_number, $email);

        if ($result['status']) {
            $message = lang('mess.18');
            $status = self::MESSAGE_TYPE_SUCCESS;
        } else {
            $message = $result['message'];
            $status = self::MESSAGE_TYPE_ERROR;
        }

        return $this->returnCommon($status, $message, NULL, $this->utils->getPlayerMessageUrl());
    }

    public function setCurrentLanguage($language) {
        $this->language_function->setCurrentLanguage($language);

        return $this->returnCommon(BaseController::MESSAGE_TYPE_SUCCESS, NULL);
    }

    /**
	 * Return Player Attachment Files Report
	 *
	 * @param void
	 * @return json
	 */
	public function playerAttachmentFileList(){

        $this->load->library(array('permissions'));
        $this->load->helper('player_helper');
        $this->permissions->setPermissions();

		if (!$this->permissions->checkPermissions('attached_file_list')) return false;

		$this->load->model(['kyc_status_model','report_model']);

		$is_export = false;
		$request = $this->input->post();
		$result = $this->report_model->playerAttachmentFileList($request, $is_export);

		$this->returnJsonResult($result);

	}

	/**
	 * detail: get Kingrich Data Send Scheduler
	 *
	 * @param string transaction_batch_id
	 * @param datetime create_date
	 * @return json
	 */
	public function kingrich_scheduler_report() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;

		$result = $this->report_model->kingrichSchedulerReport($request, $is_export);

		$this->returnJsonResult($result);
	}

	/**
	 * detail: get Kingrich Data Send Scheduler Summary Logs
	 *
	 * @param string transaction_batch_id
	 * @param datetime create_date
	 * @return json
	 */
	public function kingrich_scheduler_summary_logs() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;

		$result = $this->report_model->kingrichSchedulerSummarLogs($request, $is_export);

		$this->returnJsonResult($result);
	}
	/**
	* detail: get KYC C6 Acuris by player report
	 *
	 * @param datetime player_id
	 * @return json
	 */
	public function kyc_c6_acuris_by_player_report() {
		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;

		$result = $this->report_model->kycC6AcurisByPlayerReport($request, $is_export);

		$this->returnJsonResult($result);
	}

	/**

	 * detail: get risk score history logs for a certain player
	 *
	 * @param int $player_id
	 * @return json
	 */
	public function risk_score_history($player_id = '') {
		$this->load->model(array('report_model'));

		$request = $this->input->post();

		$is_export = false;

		$result = $this->report_model->risk_score_history_logs($request, $player_id, $is_export);

		$this->returnJsonResult($result);
	}


	public function gameApiUpdateHistory($history = null) {
		$this->load->library('permissions');
		$this->load->model(array('report_model'));
		$this->permissions->setPermissions();

		$request = $this->input->post();

		$is_export = false;
		$result = [];


		if ($this->permissions->checkPermissions('game_description')) {
			if (empty($history))
				$result = $this->report_model->gameApiUpdateHistory($request, $is_export);
		}else{
			return $this->error_access();
		}

		$this->returnJsonResult($result);
	}

}
/////END OF FILE//////////////
