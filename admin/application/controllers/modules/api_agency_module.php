<?php
/**
 *   filename:   api_agency_module.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-06-12
 *   @brief:     APIs for agency sub system
 */

trait api_agency_module {
    // agency_player_reports {{{2
	public function agency_player_reports() {
    	if(!$this->isAdminOrAgency('agency_player_report')){
    		return;
    	}

		$this->load->model(array('report_model'));

		$request = $this->input->post();
        $this->utils->debug_log('agency_player_reports', $request);
		$viewPlayerInfoPerm = true;
		$is_export = false;


        $result = null;
        if ($this->utils->isEnabledFeature('enable_agency_player_report_generator')) {
            $result = $this->report_model->get_agency_player_reports_hourly($request, $viewPlayerInfoPerm, $is_export);
        } else {
            $result = $this->report_model->get_agency_player_reports($request, $viewPlayerInfoPerm, $is_export);
        }
        // $this->utils->debug_log('agency_player_reports, result', $result);

		// $this->output->set_content_type('application/json')->set_output(json_encode($result));
		$this->returnJsonResult($result);
	}
    // agency_player_reports }}}2
    // agency_agent_reports {{{2
	public function agency_agent_reports() {
    	$logged_agent_id=null;
    	if(!$this->isLoggedAgency($logged_agent_id)){
    		return;
    	}

		$this->load->model(array('report_model'));

		$request = $this->input->post();
        $this->utils->debug_log('agency_agent_reports', $request);
		$viewAgentInfoPerm = true;
		$is_export = false;
		$result = $this->report_model->get_agency_agent_reports($request, $viewAgentInfoPerm, $is_export);
        // $this->utils->debug_log('agency_agent_reports, result', $result);

		// $this->output->set_content_type('application/json')->set_output(json_encode($result));
		$this->returnJsonResult($result);
	}
    // agency_agent_reports }}}2
    // agency_game_reports {{{2
	public function agency_game_reports($player_id = null) {
    	if(!$this->isAdminOrAgency('agency_game_report')){
    		return;
    	}

		$this->load->model(array('report_model'));
		$request = $this->input->post();
        $this->utils->debug_log('GAME_REPORTS REQUEST', $request);
		$is_export = false;
		$result = $this->report_model->get_agency_game_reports($request, $player_id, $is_export);

        // $this->utils->debug_log('GAME_REPORTS RESULT', $result);

		// $this->output->set_content_type('application/json')->set_output(json_encode($result));
		$this->returnJsonResult($result);
	} // agency_game_reports }}}2
    // get_agent_hierarchical_tree {{{2
    /**
     *  fetch hierarchical tree data for a given agent
     *
     *  @param  in agent_id
     *  @return JSON data for tree view
     */
    public function get_agent_hierarchical_tree($agent_id) {
    	if(!$this->isAdminOrAgency('view_agent')){
    		return;
    	}

        $this->utils->debug_log('in agent tree', $agent_id);
        $this->load->model(array('agency_model'));
        $result = $this->agency_model->get_agent_hierarchical_tree($agent_id);

        // $this->utils->debug_log('agent tree', $result);
        $this->returnJsonResult($result, false, '*', false);
    } // get_agent_hierarchical_tree  }}}2
    // structure_list {{{2
    /**
     *  fetch data from agency_structures table
     *
     *  @param
     *  @return output in JSON
     */
    public function structure_list() {
    	// $logged_agent_id=null;
    	// if(!$this->isLoggedAgency($logged_agent_id)){
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();
        if (!$this->permissions->checkPermissions('structure_list')) {
    		return;
    	}

        $this->load->model(array('agency_model'));
        $request = $this->input->post();
        //$this->utils->debug_log($request);
        $result = $this->agency_model->get_structure_list($request);

        //$this->utils->debug_log($result);

        $this->returnJsonResult($result);
    } // structure_list  }}}2
    // sgent_list {{{2
    /**
     *  fetch data from agency_agents table
     *
     *  @param
     *  @return output in JSON
     */
    public function agent_list() {
    	//it's for admin
    	if(!$this->isAdminOrAgency('agent_list')){
    		return;
    	}

        if($this->utils->isAgencySubProject()){
            if($this->isAgencyReadonlySubaccountLogged()){
                return show_error('No permission', 403);
            }
        }

        $this->load->model(array('agency_model'));
        $request = $this->input->post();
        //$this->utils->debug_log('agent_list request', $request);
        $result = $this->agency_model->get_agent_list($request);

        //$this->utils->debug_log($result);

        $this->returnJsonResult($result);
    } // sgent_list  }}}2

    // credit_transactions {{{2
    /**
     *  fetch data from agency_transactions table
     *
     *  @param
     *  @return output in JSON
     */
    public function credit_transactions() {
    	if(!$this->isAdminOrAgency('credit_transactions')){
    		return;
    	}

        $this->load->model(array('agency_model'));
        $request = $this->input->post();
        //$this->utils->debug_log($request);
        $result = $this->agency_model->get_transactions($request);

        //$this->utils->debug_log($result);

        $this->returnJsonResult($result);
    } // credit_transactions  }}}2

    // agency_settlement {{{2
    /**
     *  fetch settlement data
     *
     *  @param
     *  @return output in JSON
     */
    public function agency_settlement() {
    	if(!$this->isAdminOrAgency('settlement')){
    		return;
    	}

        $this->load->model(array('agency_model'));
        $request = $this->input->post();
        //$this->utils->debug_log('logs request', $request);
        $result = $this->agency_model->get_settlement($request);

        //$this->utils->debug_log($result);

        $this->returnJsonResult($result);
    } // agency_settlement  }}}2
    // agency_logs {{{2
    /**
     *  fetch data from agency_logs table
     *
     *  @param
     *  @return output in JSON
     */
    public function agency_logs() {
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();
        if (!$this->permissions->checkPermissions('agency_logs')) {
    		return;
    	}
    	// $logged_agent_id=null;
    	// if(!$this->isLoggedAgency($logged_agent_id)){
    	// 	return;
    	// }

        $this->load->model(array('agency_model'));
        $request = $this->input->post();
        //$this->utils->debug_log('logs request', $request);
        $result = $this->agency_model->get_logs($request);

        //$this->utils->debug_log($result);

        $this->returnJsonResult($result);
    } // agency_logs  }}}2

    // players_list_under_agent {{{2
    /**
     *  get players under a given agent
     *
     *  @param
     *  @return output in JSON
     */
    public function players_list_under_agent() {
    	$logged_agent_id=null;
    	if(!$this->isLoggedAgency($logged_agent_id)){
    		return;
    	}

        if($this->isAgencyReadonlySubaccountLogged()){
            return show_error('No permission', 403);
        }

        $this->load->model(array('player_model'));
        $request = $this->input->post();
        $readonlyLogged=$this->isAgencyReadonlySubaccountLogged();
        // $this->utils->debug_log('logs request', $request);
        $result = $this->player_model->get_players_under_agent($request, $readonlyLogged);

        // $this->utils->debug_log($result);

        $this->returnJsonResult($result);
    } // players_list_under_agent  }}}2

    // agency_game_history {{{2
	public function agency_game_history($player_id = null) {
    	$logged_agent_id=null;
    	if(!$this->isLoggedAgency($logged_agent_id)){
    		return;
    	}

		$this->load->model(array('game_logs','agency_model'));
		# START DEFINE COLUMNS #####################################################################

		$show_bet_detail_on_game_logs=$this->utils->isEnabledFeature('show_bet_detail_on_game_logs');

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
				'alias' => 'external_uniqueid',
				'select' => 'game_logs.external_uniqueid',
			),
			array(
				'dt' => $i++,
				'alias' => 'end_at',
				'select' => 'game_logs.end_at',
				'formatter' => 'dateTimeFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'player_username',
				'select' => 'player.username',
				'formatter' => function ($d, $row) {
					return sprintf('<a href="/agency/player_information/%s">%s</a>', $row['playerId'], $d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'agent_username',
				'select' => 'agency_agents.agent_name',
                'formatter' => 'defaultFormatter',
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
						return lang('N/A');
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
						return lang('N/A');
					}
					return $this->data_tables->languageFormatter($d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'real_bet_amount',
				'select' => 'game_logs.trans_amount',
				'formatter' => function ($d, $row) {
					//only for game type
					if($row['flag']==Game_logs::FLAG_TRANSACTION){
						return lang('N/A');
					}else{
						if($d==0){
							$d=$row['bet_amount'];
						}
						return $this->utils->formatCurrencyNoSym($d);
					}
				},
				// 'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'bet_amount',
				'select' => 'game_logs.bet_amount',
				'formatter' => function ($d, $row) {
					if($row['flag']==Game_logs::FLAG_TRANSACTION){
						return lang('N/A');
					}else{
						return $this->utils->formatCurrencyNoSym($d);
					}
				},
				// 'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'result_amount',
				'select' => 'game_logs.result_amount',
				'formatter' => function ($d, $row) {
					if($row['flag']==Game_logs::FLAG_TRANSACTION){
						return lang('N/A');
					}else{
						return $this->utils->formatCurrencyNoSym($d);
					}
				},
				// 'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'bet_plus_result_amount',
				'select' => 'game_logs.bet_amount + game_logs.result_amount',
				'formatter' => function ($d, $row) {
					if($row['flag']==Game_logs::FLAG_TRANSACTION){
						return lang('N/A');
					}else{
						return $this->utils->formatCurrencyNoSym($d);
					}
				},
				// 'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'win_amount',
				'select' => 'game_logs.win_amount',
				'formatter' => function ($d, $row) {
					if($row['flag']==Game_logs::FLAG_TRANSACTION){
						return lang('N/A');
					}else{
						return $this->utils->formatCurrencyNoSym($d);
					}
				},
				// 'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'loss_amount',
				'select' => 'game_logs.loss_amount',
				'formatter' => function ($d, $row) {
					if($row['flag']==Game_logs::FLAG_TRANSACTION){
						return lang('N/A');
					}else{
						return $this->utils->formatCurrencyNoSym($d);
					}
				},
				// 'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'after_balance',
				'select' => 'game_logs.after_balance',
				'formatter' => 'currencyFormatter',
			),
			/*array(
				'dt' => $i++,
				'alias' => 'trans_amount',
				'select' => 'game_logs.trans_amount',
				'formatter' => function ($d, $row) {
					if($row['flag']!=Game_logs::FLAG_TRANSACTION){
						return lang('N/A');
					}else{
						return $this->utils->formatCurrencyNoSym($d);
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'roundno',
				'select' => 'game_logs.table',
				'formatter' => function($d, $row) use($show_bet_detail_on_game_logs){
					$str='<p>'.$d.'</p>';
					if($show_bet_detail_on_game_logs){
						$str=$str.'<ul class="list-inline">'.
						'<li><a href="javascript:void(0)" onclick="betDetail(\''.$row['external_uniqueid'].'\')">'.lang('Bet Detail').'</a></li>'.
						'<li><a href="javascript:void(0)" onclick="betResult(\''.$row['external_uniqueid'].'\')">'.lang('Bet Result').'</a></li>'.
						'</ul>';
					}
					return $str;

				},
			),*/
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
                        $data = json_decode($d, true);
                        $betDetailLink = "";
                        $platform_id = (int)$row['game_platform_id'];
                        if (!empty($data) && $show_bet_detail_on_game_logs && is_array($data)) {
                            foreach ($data as $key => $value) {
                                if (!empty($value)) {
                                    if (!empty($betDetailLink))
                                        $betDetailLink .= ", ";
                                    if(is_array($value)){
                                        $value=formatDebugMessage($value);
                                    }else{
                                        $value=lang($value);
                                    }
                                    if ($platform_id == (SBOBET_API || ULTRAPLAY_API || PINNACLE_API || GAMEPLAY_SBTECH_API) ){
                                        if($key == 'sports_bet'){
                                            $key = 'Sports Bet';
                                            $res =  json_decode($value, true);
                                            $this->utils->debug_log('the data =======>', $res);
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
                                    }
                                    $betDetailLink .= lang($key) . " : " .$value;
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
                            $betDetailLink = $d;
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
                'name' => lang('player.ut10'),
                'formatter' => 'defaultFormatter',
            ),
            array(
                'dt' => $i++,
                'alias' => 'bet_type',
                'select' => 'game_logs.bet_type',
                'name' => 'Bet Type',
                'formatter' => function ($d, $row) {
                    $unique_id = $row['external_uniqueid'];
                    // if( (strpos(strtolower($d), 'single') != 'single' && !empty($d))){
                    //     $bets = json_decode($row['betDetails'], true);
                    //     $count = count($bets['sports_bet']);
                    //     $h = ($count > 1) ? ($count * 33) + 155 : 188;
                    //     $link = "'/echoinfo/bet_details/$unique_id','Bet Type','width=800,height=$h'";
                    //     return sprintf('<a href="javascript:void(window.open('.$link.'))">%s</a>',$d);
                   // }else{
                        return $d;
                  //  }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'match_type',
                'select' => 'game_logs.match_type',
                'name' => 'Match Type',
                'formatter' => function ($d){
                    return ($d == '0' || empty($d)) ? 'N/A' : 'Live';
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'match_details',
                'select' => 'game_logs.match_details',
                'name' => 'Match Details',
                'formatter' => 'defaultFormatter',
            ),
            array(
                'dt' => $i++,
                'alias' => 'handicap',
                'select' => 'game_logs.handicap',
                'name' => 'Handicap',
                'formatter' => 'defaultFormatter',
            ),
            array(
                'dt' => $i++,
                'alias' => 'odds',
                'select' => 'game_logs.odds',
                'name' => 'Odds',
                'formatter' => 'defaultFormatter',
            ),
            array(
                'alias' => 'game_platform_id',
                'select' => 'game_logs.game_platform_id',
            )


			// array(
			// 	'dt' => $i++,
			// 	'alias' => 'external_uniqueid',
			// 	'select' => 'game_logs.external_uniqueid',
			// 	'formatter' => function($d, $row) {
			// 		return '<ul class="list-inline">'.
			// 		'<li><a href="javascript:void(0)" onclick="betDetail(\''.$d.'\')">'.lang('Bet Detail').'</a></li>'.
			// 		'<li><a href="javascript:void(0)" onclick="betResult(\''.$d.'\')">'.lang('Bet Result').'</a></li>'.
			// 		'</ul>';
			// 	},
			// ),
		);
		# END DEFINE COLUMNS #######################################################################

		$table = 'game_logs use index(idx_end_at)';
		$joins = array(
			'player' => 'player.playerId = game_logs.player_id',
			'agency_agents' => 'agency_agents.agent_id = player.agent_id',
			'game_description' => 'game_description.id = game_logs.game_description_id',
			'game_type' => 'game_type.id = game_description.game_type_id',
			'external_system' => 'game_logs.game_platform_id = external_system.id',
		);

        $left_joins_to_use_on_summary = array();


		# START PROCESS SEARCH FORM ################################################################
		$where = array();
		$values = array();
		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
        // $this->utils->debug_log('AGENCY_GAME_HISTORY request111', $request);

        // -- Validate date range if config was set
        if($this->utils->getConfig('agency_game_logs_report_date_range_restriction'))
        {
            // -- If date range is empty || does not exist from the request, immediately return empty result.
            if(!isset($input['by_date_from']) || !isset($input['by_date_to']) ||  trim($input['by_date_from']) == '' || trim($input['by_date_to']) == '')
            {

                $result = $this->data_tables->empty_data($request);
                $result['header_data'] = $this->data_tables->get_columns($columns);
                $result['sub_summary'] = array(array("total_amount"=>"0.00","total_bet"=>"0.00","total_bet_result"=>"0.00","total_loss"=>"0.00","total_real_bet"=>"0.00","total_result"=>"0.00","total_win"=>"0.00"));
                $result['summary'] = array(array("total_amount"=>"0.00","total_bet"=>"0.00","total_bet_result"=>"0.00","total_loss"=>"0.00","total_real_bet"=>"0.00","total_result"=>"0.00","total_win"=>"0.00"));

                return $this->returnJsonResult($result);
            }


            // -- Check date range if within the provided restriction

            $date_diff = date_diff(date_create($input['by_date_to']), date_create($input['by_date_from']));

            $restriction = $this->utils->getConfig('agency_game_logs_report_date_range_restriction') - 1;

            if($date_diff->format('%a') >  $restriction){

                $result = $this->data_tables->empty_data($request);
                $result['header_data'] = $this->data_tables->get_columns($columns);
                $result['sub_summary'] = array(array("total_amount"=>"0.00","total_bet"=>"0.00","total_bet_result"=>"0.00","total_loss"=>"0.00","total_real_bet"=>"0.00","total_result"=>"0.00","total_win"=>"0.00"));
                $result['summary'] = array(array("total_amount"=>"0.00","total_bet"=>"0.00","total_bet_result"=>"0.00","total_loss"=>"0.00","total_real_bet"=>"0.00","total_result"=>"0.00","total_win"=>"0.00"));

                return $this->returnJsonResult($result);
            }
        }


		$where[] = "player.playerId IS NOT NULL";

		// if (isset($input['group_level']) && isset($input['only_allowed_game'])) {
		// 	$joins['vipsetting_cashback_game'] = 'vipsetting_cashback_game.game_description_id = game_logs.game_description_id';
		// }

        if (isset($input['agent_id']) && $input['agent_id'] != '') {
        	$downlines = $this->agency_model->get_all_downline_arr($input['agent_id']);
            $where[] = "player.agent_id IN (" . implode(',', $downlines) . ")";
            array_push($left_joins_to_use_on_summary, 'player');
        }
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
            array_push($left_joins_to_use_on_summary, 'player');
		}

		if (isset($input['by_username'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['by_username'] . '%';
            array_push($left_joins_to_use_on_summary, 'player');
		}

		if (isset($input['by_game_code'])) {
			$where[] = "game_description.game_code = ?";
			$values[] = $input['by_game_code'];
            array_push($left_joins_to_use_on_summary, 'game_description');
		}

		if (isset($input['game_description_id'])) {
			$where[] = "game_description.id = ?";
			$values[] = $input['game_description_id'];
            array_push($left_joins_to_use_on_summary, 'game_description');
		}

		if (isset($input['by_group_level'])) {
			$where[] = "player.levelId  = ?";
			$values[] = $input['by_group_level'];
            array_push($left_joins_to_use_on_summary, 'player');
		}

		$all_game_types= isset($input['all_game_types']) ? ($input['all_game_types']=='true' || $input['all_game_types']=='on') : false ;
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

            array_push($left_joins_to_use_on_summary, 'game_type');

		} else if (isset($input['game_type_id_null'])) {
			$where[] = "game_type.id IS NULL";
            array_push($left_joins_to_use_on_summary, 'game_type');

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

		if (isset($input['by_round_number'])) {
			$where[] = "game_logs.table = ?";
			$values[] = $input['by_round_number'];
		}

		# END PROCESS SEARCH FORM ##################################################################
        $group_by = [];
        $having = [];
        $distinct = false;

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, $distinct);
        // $this->utils->debug_log('AGENCY_GAME_HISTORY request', $request, $where, $values, $joins);
        // $this->utils->debug_log('AGENCY_GAME_HISTORY result', $result);

        // -- remove unecessary joins from total / summary queries
        foreach ($joins as $join_key => &$join_value) {
            if(!in_array($join_key, $left_joins_to_use_on_summary))
                unset($joins[$join_key]);
        }

        $summary = $this->data_tables->summary($request, $table, $joins,
            'SUM( case when flag='.Game_logs::FLAG_GAME.' && trans_amount>0 then trans_amount when flag='.Game_logs::FLAG_TRANSACTION.' then 0 else bet_amount end ) total_real_bet, SUM(bet_amount) total_bet, SUM(result_amount) total_result, SUM(bet_amount + result_amount) total_bet_result, SUM(win_amount) total_win, SUM(loss_amount) total_loss, SUM(trans_amount) total_amount',
            null, $columns, $where, $values);
        // $sub_summary = $this->data_tables->summary($request, $table, $joins,
        //     'external_system.system_code, SUM(bet_amount) total_bet, SUM(result_amount) total_result, SUM(win_amount) total_win, SUM(loss_amount) total_loss',
        //     'external_system.system_code', $columns, $where, $values);



		$result['summary'][0]['total_real_bet'] 		= $this->utils->formatCurrencyNoSym($summary[0]['total_real_bet']);
		$result['summary'][0]['total_bet'] 				= $this->utils->formatCurrencyNoSym($summary[0]['total_bet']);
		$result['summary'][0]['total_result'] 			= $this->utils->formatCurrencyNoSym($summary[0]['total_result']);
		$result['summary'][0]['total_bet_result'] 		= $this->utils->formatCurrencyNoSym($summary[0]['total_bet_result']);
		$result['summary'][0]['total_win'] 				= $this->utils->formatCurrencyNoSym($summary[0]['total_win']);
		$result['summary'][0]['total_loss'] 			= $this->utils->formatCurrencyNoSym($summary[0]['total_loss']);
		$result['summary'][0]['total_amount'] 			= $this->utils->formatCurrencyNoSym($summary[0]['total_amount']);

		$result['sub_summary'][0]['total_real_bet'] 	= $this->utils->formatCurrencyNoSym(array_sum(array_map(function($row) { return str_replace(',', '', $row[6]);}, $result['data'])));
		$result['sub_summary'][0]['total_bet'] 			= $this->utils->formatCurrencyNoSym(array_sum(array_map(function($row) { return str_replace(',', '', $row[7]);}, $result['data'])));
		$result['sub_summary'][0]['total_result'] 		= $this->utils->formatCurrencyNoSym(array_sum(array_map(function($row) { return str_replace(',', '', $row[8]);}, $result['data'])));
		$result['sub_summary'][0]['total_bet_result'] 	= $this->utils->formatCurrencyNoSym(array_sum(array_map(function($row) { return str_replace(',', '', $row[9]);}, $result['data'])));
		$result['sub_summary'][0]['total_win'] 			= $this->utils->formatCurrencyNoSym(array_sum(array_map(function($row) { return str_replace(',', '', $row[10]);}, $result['data'])));
		$result['sub_summary'][0]['total_loss'] 		= $this->utils->formatCurrencyNoSym(array_sum(array_map(function($row) { return str_replace(',', '', $row[11]);}, $result['data'])));
		$result['sub_summary'][0]['total_amount'] 		= $this->utils->formatCurrencyNoSym(array_sum(array_map(function($row) { return str_replace(',', '', $row[13]);}, $result['data'])));

		$this->returnJsonResult($result);
	}
    // agency_game_history }}}2
    // player_rolling_comm_info {{{2
    /**
     *  fetch data from agency_player_rolling_comm table
     *
     *  @param
     *  @return output in JSON
     */
    // public function player_rolling_comm_info() {
    // 	$logged_agent_id=null;
    // 	if(!$this->isLoggedAgency($logged_agent_id)){
    // 		return;
    // 	}

    //     $this->load->model(array('agency_model'));
    //     $request = $this->input->post();
    //     //$this->utils->debug_log('logs request', $request);
    //     $result = $this->agency_model->get_player_rolling_comm_info($request);

    //     //$this->utils->debug_log($result);

    //     $this->returnJsonResult($result);
    // } // player_rolling_comm_info  }}}2
    // game_rolling_comm_info {{{2
	public function game_rolling_comm_info() {
    	$logged_agent_id=null;
    	if(!$this->isLoggedAgency($logged_agent_id)){
    		return;
    	}

        $this->utils->debug_log('GAME_ROLLING_COMM_INFO start');
		$this->load->model(array('game_logs', 'agency_model'));

		$table = 'game_logs';
		$joins = array(
			'player' => 'player.playerId = game_logs.player_id',
			'game_description' => 'game_description.id = game_logs.game_description_id',
			'game_type' => 'game_type.id = game_description.game_type_id',
			'external_system' => 'game_logs.game_platform_id = external_system.id',
            'agency_game_rolling_comm' =>
            'agency_game_rolling_comm.agent_id = player.agent_id AND agency_game_rolling_comm.game_description_id = game_logs.game_description_id',
        );

		# START PROCESS SEARCH FORM ################################################################
		$where = array();
		$values = array();

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);
        $this->utils->debug_log('GAME_ROLLING_COMM_INFO input', $input);

        $settlement_id = $input['settlement_id'];
        $player_id = $input['player_id'];

        $settlement = $this->agency_model->get_settlement_by_id($settlement_id);

        $where[] = "game_logs.flag = ".Game_logs::FLAG_GAME;
        $where[] = "game_logs.end_at BETWEEN ? AND ?";
        $values[] = $settlement['settlement_date_from'];
        $values[] = $settlement['settlement_date_to'];

        $where[] = "player.playerId = ?";
        $values[] = $player_id;

        $this->utils->debug_log('GAME_ROLLING_COMM_INFO where values', $where, $values);
        # START DEFINE COLUMNS #####################################################################
		$i = 0;
		$columns = array(
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
				'alias' => 'game_platform_percentage',
				'select' => 'agency_game_rolling_comm.game_platform_percentage',
            ),
			array(
				'alias' => 'game_type_percentage',
				'select' => 'agency_game_rolling_comm.game_type_percentage',
            ),
			array(
				'dt' => $i++,
				'alias' => 'player_username',
				'select' => 'player.username',
				'formatter' => function ($d, $row) {
					return sprintf('<a href="/agency/player_information/%s">%s</a>', $row['playerId'], $d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'end_at',
				'select' => 'game_logs.end_at',
				'formatter' => 'dateTimeFormatter',
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
					return $this->data_tables->languageFormatter($d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'game_name',
				'select' => 'game_description.game_name',
				'formatter' => function ($d, $row) {
					return $this->data_tables->languageFormatter($d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'roundno',
				'select' => 'game_logs.table',
			),
			array(
				'dt' => $i++,
				'alias' => 'bet_amount',
				//'select' => 'game_logs.bet_amount',
				'select' => 'SUM(game_logs.bet_amount)',
				'formatter' => function ($d, $row) {
					return $this->utils->formatCurrencyNoSym($d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'result_amount',
				//'select' => 'game_logs.result_amount',
				'select' => 'SUM(game_logs.result_amount)',
				'formatter' => function ($d, $row) {
					return $this->utils->formatCurrencyNoSym($d);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'bet_plus_result_amount',
				//'select' => 'game_logs.bet_amount + game_logs.result_amount',
				'select' => 'game_logs.player_id',
				//'formatter' => 'currencyFormatter',
				'formatter' => function ($d, $row) {
                    $sum = $row['bet_amount'] + $row['result_amount'];
					return $this->utils->formatCurrencyNoSym($sum);
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'game_rolling_comm',
				'select' => 'agency_game_rolling_comm.game_desc_percentage',
				'formatter' => function ($d, $row) {
                    $rolling_comm = 0.0;
                    if(!empty($d) && $d > 0) {
                        $rolling_comm = $d;
                    } else if (!empty($row['game_type_percentage']) && $row['game_type_percentage'] > 0) {
                        $rolling_comm = $row['game_type_percentage'];
                    } else if (!empty($row['game_platform_percentage']) && $row['game_platform_percentage'] > 0) {
                        $rolling_comm = $row['game_platform_percentage'];
                    }
					return $rolling_comm;
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'rolling_comm_amt',
				'select' => 'player.rolling_comm',
				'formatter' => function ($d, $row) {
                    // TODO: which one has higher priority, player_rolling_comm or game_rolling_comm?
                    if ($row['game_rolling_comm'] > 0) {
                        $rolling_comm = $row['game_rolling_comm'];
                    } else {
                        $rolling_comm = $d;
                    }
                    $rolling_comm_amt = $row['bet_amount'] * $rolling_comm / 100.0;
					return $rolling_comm_amt;
				},
			),
		);
		# END DEFINE COLUMNS #######################################################################

		# END PROCESS SEARCH FORM ##################################################################

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
        //$this->utils->debug_log('AGENCY_GAME_HISTORY request', $request, $where, $values, $joins);
        //$this->utils->debug_log('AGENCY_GAME_HISTORY result', $result);

		$this->returnJsonResult($result);
	}
    // game_rolling_comm_info }}}2
    // agent_tier_comm_pattern_list {{{2
    /**
     *  fetch data from agency_structures table
     *
     *  @param
     *  @return output in JSON
     */
    public function agent_tier_comm_pattern_list() {
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();
        //if (!$this->permissions->checkPermissions('tier_comm_pattern_list')) {
        if (!$this->permissions->checkPermissions('view_agent')) {
    		return;
    	}

        $this->load->model(array('agency_model'));
        $request = $this->input->post();
        //$this->utils->debug_log($request);
        $result = $this->agency_model->get_tier_comm_pattern_list($request);

        //$this->utils->debug_log($result);

        $this->returnJsonResult($result);
    } // agent_tier_comm_pattern_list  }}}2

    public function bet_limit_template_list($agent_id = null){
    	$logged_agent_id=null;
    	if(!$this->isLoggedAgency($logged_agent_id)){
    		return;
    	}

		$this->load->model(['game_description_model','agency_model']);

		$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

		# START DEFINE COLUMNS #####################################################################
		$i = 0;
		$columns = array(
			array(
				'alias' => 'agent_id',
				'select' => 'bet_limit_template_list.agent_id',
			),
			array(
				'alias' => 'default_template',
				'select' => 'IF(agent_id = '.$input['agent_id'].', default_template, 0)',
			),
			array(
				'dt' => $i++,
				'alias' => 'id',
				'select' => 'bet_limit_template_list.id',
				'formatter' => function ($d, $row) use ($input) {
					return $row['agent_id'] == $input['agent_id'] ? ('<a class="btn btn-xs btn-primary" href="/agency/edit_bet_limit_template/'.$d.'">'.lang('Edit').'</a>') : '';
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'updated_at',
				'select' => 'bet_limit_template_list.updated_at',
				'formatter' => 'dateTimeFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'template_name',
				'select' => 'bet_limit_template_list.template_name',
				'formatter' => function ($d, $row) {
					return $d . ($row['default_template'] == 1 ? (' (' . lang('Default') . ')') : '');
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'note',
				'select' => 'bet_limit_template_list.note',
				// 'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'bet_limit_template_list.status',
				'formatter' => function ($d, $row) {
					$txt=lang('Active');

					if($d==Game_description_model::STATUS_DISABLED){
						$txt=lang('Inactive');
					}
					return $txt;
				},
			),
		);
		# END DEFINE COLUMNS #######################################################################

		$table = 'bet_limit_template_list';
		$joins = array(
			// 'player' => 'player.playerId = game_logs.player_id',
			// 'game_description' => 'game_description.id = game_logs.game_description_id',
			// 'game_type' => 'game_type.id = game_description.game_type_id',
			// 'external_system' => 'game_logs.game_platform_id = external_system.id',
		);

		# START PROCESS SEARCH FORM ################################################################
		$where = array();
		$values = array();

        if (isset($input['agent_id']) && !empty($input['agent_id'])) {
        	$upline = $this->agency_model->get_upline($agent_id, false);
        	if(!empty($upline)){
	            $where[] = "(bet_limit_template_list.agent_id = ? OR (bet_limit_template_list.agent_id IN(".implode(',', $upline).") AND bet_limit_template_list.public_to_downline = 1))";
				$values[] = $agent_id;
        	}
        }

        if (isset($input['by_game_platform_id'])) {
			$where[] = "bet_limit_template_list.game_platform_id = ?";
			$values[] = $input['by_game_platform_id'];
		}

		if (isset($input['date_from'], $input['date_to'])) {
			$where[] = "bet_limit_template_list.updated_at >= ?";
			$values[] = $input['date_from'];
			$where[] = "bet_limit_template_list.updated_at <= ?";
			$values[] = $input['date_to'];
		}

		if (isset($input['template_name'])) {
			$where[] = "bet_limit_template_list.template_name LIKE ?";
			$values[] = '%' . $input['template_name'] . '%';
		}

		# END PROCESS SEARCH FORM ##################################################################

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		$this->returnJsonResult($result);
    }

    public function player_rolling_comm(){
    	$logged_agent_id=null;
    	if(!$this->isLoggedAgency($logged_agent_id)){
    		return;
    	}

        $this->load->model(array('player_model','report_model'));
        $request = $this->input->post();
        $is_export=false;
        // $this->utils->debug_log('logs request', $request);
        $result = $this->report_model->player_rolling_comm($request, $logged_agent_id, $is_export);

        $this->returnJsonResult($result);

    }

    private function isAdminOrAgency($permission = null){

    	if(!$this->isLoggedAgency()){
            if($permission == null)
                return false;
    		//not agency
			$this->load->library(array('permissions'));
			$this->permissions->setPermissions();
			return $this->permissions->checkPermissions($permission);
    	}

    	return true;
    }

    public function agency_settlement_wl($mode = 'only_agent') {
        if(!$this->isAdminOrAgency('settlement_wl')){
            return;
        }

        $readonlyLogged=$this->isAgencyReadonlySubaccountLogged();

        $this->load->model(array('agency_model'));
        $request = $this->input->post();
        $is_export=false;
        //$this->utils->debug_log('logs request', $request);
        $result = $this->agency_model->getWlSettlement($request, $mode, $is_export, $readonlyLogged);

        //$this->utils->debug_log($result);

        $this->returnJsonResult($result);
    }

    public function agency_settlement_detail_wl() {
        if(!$this->isAdminOrAgency('settlement_wl')){
            return;
        }

        $this->load->model(array('agency_model'));
        $request = $this->input->post();
        $result = $this->agency_model->getWlSettlementDetail($request);
        $this->returnJsonResult($result);
    }

}
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of api_agency_module.php
