<?php
/**
 *   filename:   agency_settlement_module.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2017-11-16
 *   @brief:     module for agency settlement
 */
trait agency_settlement_module {
    // create_settlement_wl{{{2
    /**
     *  create settlement by win/loss using tier commission settings for a given agent
     *
     *  @param  INT agent_id
     *  @return void
     */
    public function create_settlement_wl($agent_id = null) {
        $this->load->model(array('player', 'game_logs'));
        if (!empty($agent_id)) {
            $this->create_settlement_wl_for_agent($agent_id);
        } else {
            $active_agents = $this->agency_model->get_active_agents();
            foreach ($active_agents as $agent) {
                $this->create_settlement_wl_for_agent($agent['agent_id']);
            }
        }
    } // create_settlement_wl}}}2
    // create_settlement_wl_for_agent {{{2
    /**
     *  create settlement data for a given agent
     *
     *  @param  INT agent_id
     *  @return void
     */
    private function create_settlement_wl_for_agent($agent_id) {
        $players = $this->player->get_players_by_agent_ids($agent_id);
        if (empty($players)) {
            $this->utils->debug_log('AGENT_ID: ' . $agent_id, 'SKIPPING: NO PLAYERS');
            return;
        }
        $players_id = array_column($players, 'playerId');

        $game_types = $this->agency_model->get_agent_game_types($agent_id);
        if (empty($game_types)) {
            $this->utils->debug_log('AGENT_ID: ' . $agent_id, 'SKIPPING: NO GAME TYPES');
            return;
        }
        $agent_game_info = $this->get_agent_game_info($agent_id, $players_id, NULL, NULL, NULL, NULL);
        if ($agent_game_info['total_bets'] == 0) {
            $this->utils->debug_log('AGENT_ID: ' . $agent_id, 'SKIPPING: NO BETS');
            return;
        }
        $agent_details = $this->agency_model->get_agent_by_id($agent_id);
        foreach ($players as $player) {
            // NOTE: don't change the original array
            $agent = $agent_details;
            $this->create_daily_player_settlement_wl($player, $agent, $game_types);
        }
        $today = date("Y-m-d H:i:s");
        if (empty($agent_details['settlement_period'])) {
            $agent_details['settlement_period'] = 'Weekly';
        }
        if (empty($agent_details['settlement_start_day'])) {
            $agent_details['settlement_start_day'] = 'Monday';
        }
        $date_start_day = date("Y-m-d 00:00:00", strtotime("-1 {$agent_details['settlement_start_day']}"));
        $date_start_day_next = date("Y-m-d 00:00:00", strtotime("+1 {$agent_details['settlement_start_day']}"));
        if ($date_start_day_next == date("Y-m-d 00:00:00")) {
            $date_start_day = date("Y-m-d 00:00:00", strtotime("+1 {$agent_details['settlement_start_day']}"));
            $date_start_day_next = date("Y-m-d 00:00:00", strtotime("+2 {$agent_details['settlement_start_day']}"));
        }

        $this->utils->debug_log('date_start_day', $date_start_day);
        $this->utils->debug_log('date_start_day_next', $date_start_day_next);
        $this->utils->debug_log('ALL_PLAYER_IDS', $players_id);

        $period_array = $this->create_settlement_period_array($agent_details);
        $this->utils->debug_log($agent_details['agent_name'], $agent_id, 'PERIOD_ARRAY', $period_array);

        $period_name = $agent_details['settlement_period'];
        $periods = $period_array[$period_name];
        // $period_name is 'Daily', 'Weekly', and so on
        // $periods is an array containing all possible settlement period
        foreach ($periods as $period) {
            $data = array();
            $date_from  = $period['date_from'];
            $date_to    = $period['date_to'];
            if ($period_name == 'Weekly') {
                if ($date_from >= $date_start_day && $date_to < $date_start_day_next) {
                    $status = 'current';
                } else {
                    $status = 'unsettled';
                }
            } else {
                if ($today >= $date_from && $today <= $date_to) {
                    $status = 'current';
                } else {
                    $status = 'unsettled';
                }
            }
            //handle player settlement
            foreach ($players as $player) {
                $this->create_player_settlement_wl($player, $period, $status);
            }

            //handle agent settlement
            $this->create_agent_settlement_wl($agent_details, $players_id, $period, $status);
        }
    } // create_settlement_wl_for_agent  }}}2
    // create_agent_settlement_wl {{{2
    /**
     *  create settlement record for the given agent
     *
     *  @param  array $agent  summary info fetched from agency_daily_player_settlement table
     *  @param  array $period settlement period
     *  @param  string $status  status of the settlement record
     *  @return void
     */
    private function create_agent_settlement_wl($agent, $players_id, $period, $status) {
        $date_from = $period['date_from'];
        $date_to = $period['date_to'];
        $agent_id                = $agent['agent_id'];
        $settlement_agent = $this->agency_model->checkDuplicatedSettlement('agent', $agent_id, $date_from, $date_to);

        $this->utils->debug_log('create_agent_settlement_wl SETTLEMENT_AGENT: ' , $settlement_agent);
        if(!empty($settlement_agent) && ($settlement_agent->status == 'settled' || $settlement_agent->status == 'closed')) {
            return;
        }

        $with_game = false;
        $agent_rows = $this->agency_model->getSettlementRawData($agent_id, $date_from, $date_to, 'agent', $with_game);
        
        if (!isset($agent_rows) || empty($agent_rows)) {
            return;
        }
        // there is only one row here
        $agent_row = $agent_rows[0];
        if(empty($agent_row['bets']) || $agent_row['bets'] == 0){
            return;
        }

        // get total player rolling amount from table agency_settlement_wl
        $comm_rows = $this->agency_model->getSettlementAgentTotalPlayerRolling($agent_id, $date_from, $date_to);
        $player_commission = (isset($comm_rows) && !empty($comm_rows))? $comm_rows[0]['player_commission'] : 0.0; 

        $data = array(
            'winning_bets'      => $agent_row['winning_bets'],
            'real_bets'         => $agent_row['real_bets'],
            'bets'              => $agent_row['bets'],
            'tie_bets'          => $agent_row['tie_bets'],
            'result_amount'     => $agent_row['result_amount'],
            'lost_bets'         => $agent_row['lost_bets'],
            'bets_except_tie'   => $agent_row['bets_except_tie'],
            'player_commission' => $player_commission,
            'wins'              => $agent_row['wins'],
            'net_gaming'        => $agent_row['net_gaming'],
            //'earnings'          => $agent_row['earnings'],
            'updated_on'        => $this->utils->getNowForMysql(),
            'agent_id'          => $agent_row['agent_id'],
        );
        list($rev_share_amt, $agent_commission) = $this->get_agent_commission($agent, $date_from, $date_to);
        $data['rev_share_amt'] = $rev_share_amt;
        $data['agent_commission']     = $agent_commission;

        $this->get_agent_fees($data, $agent, $players_id, $period);

        $data['status']                 = $status;
        $data['settlement_date_to']     = $date_to;

        if(empty($settlement_agent)){
            $data['type']                   = 'agent';
            $data['user_id']                = $agent_id;
            $data['settlement_date_from']   = $date_from;
            $data['created_on']             = $this->utils->getNowForMysql();
            $this->utils->debug_log($agent_row['agent_id'], 'create_settlement_by_win_loss->period', $period);
            $this->utils->debug_log($agent_row['agent_id'], 'create_settlement_by_win_loss->insert agent', $data);
            $this->agency_model->insertWlSettlement($data);
        } else { //if($settlement_agent->status == 'current'){
            $this->utils->debug_log('create_settlement_by_win_loss->period', $period);
            $this->utils->debug_log('create_settlement_by_win_loss->update agent', $data);
            $this->agency_model->updateWLSettlement($settlement_agent->id, $data);
        }
    } // create_agent_settlement_wl  }}}2
    // create_player_settlement_wl {{{2
    /**
     *  created player win/loss settlement records based on daily player settlement data
     *
     *  @param  array $player
     *  @param  Date Array $period  settlement period
     *  @param  string $status  status of the settlement record
     *  @return void
     */
    private function create_player_settlement_wl($player, $period, $status) {
        $date_from = $period['date_from'];
        $date_to = $period['date_to'];
        $player_id = $player['playerId'];
        $settlement_player = $this->agency_model->checkDuplicatedSettlement('player', $player_id, $date_from, $date_to);
        $this->utils->debug_log('create_player_settlement_wl SETTLEMENT_PLAYER: ' , $settlement_player);
        if (!empty($settlement_player) && $settlement_player->status == 'settled'){ 
            //|| $settlement_player->status == 'unsettled')) {
            // because there is delay on game logs we should check records in 'unsettled' status 
            // we can also set a limit on time span for settlement periods checking
            return;
        }
        $with_game = false;
        $player_rows = $this->agency_model->getSettlementRawData($player_id, $date_from, $date_to, 'player', $with_game);
        $this->utils->debug_log('create_player_settlement_wl PLAYER_ROWS000: ' , $player_rows);
        if (!isset($player_rows) || empty($player_rows)) {
            $this->utils->debug_log('create_player_settlement_wl PLAYER_ROWS111: ' , $player_rows);
            return;
        }
        // there is only one row here
        $player_row = $player_rows[0];
        if(empty($player_row['bets']) || $player_row['bets'] == 0){
            return;
        }

        $player_commission = $this->get_player_commission($player, $date_from, $date_to);
        // only for new settlement record or record in 'current' status
        $data = array(
            'real_bets'         => $player_row['real_bets'],
            'bets'              => $player_row['bets'],
            'tie_bets'          => $player_row['tie_bets'],
            'result_amount'     => $player_row['result_amount'],
            'lost_bets'         => $player_row['lost_bets'],
            'bets_except_tie'   => $player_row['bets_except_tie'],
            'player_commission' => $player_commission,
            'wins'              => $player_row['wins'],
            'net_gaming'        => $player_row['net_gaming'],
            'updated_on'        => $this->utils->getNowForMysql(),
            'agent_id'          => $player_row['agent_id'],
        );
        $data['status']                 = $status;
        $data['settlement_date_to']     = $date_to;

        if (empty($settlement_player)) {
            $data['type']                   = 'player';
            $data['user_id']                = $player_row['player_id'];
            $data['settlement_date_from']   = $date_from;
            $data['created_on']             = $this->utils->getNowForMysql();
            $this->utils->debug_log('create_settlement_by_win_loss->insert player', $data);
            $this->agency_model->insertWlSettlement($data);
        } else {
            $this->utils->debug_log('create_settlement_by_win_loss->update player', $data);
            $this->agency_model->updateWLSettlement($settlement_player->id, $data);
        }
    } // create_player_settlement_wl  }}}2
    // create_daily_player_settlement_wl {{{2
    /**
     *  create daily win/loss settlement data using tier commission settings for a given player
     *
     *  @param  array $player  
     *  @param  array $game_types for the parent agent
     *  @return void
     */
    private function create_daily_player_settlement_wl($player, $agent, $game_types) {
        $agent_id = $agent['agent_id'];
        $player_id = $player['playerId'];
        $agent['settlement_period'] = 'Daily';
        $period_array = $this->create_settlement_period_array($agent);
        $periods = $period_array['Daily'];
        $this->utils->debug_log('create_daily_player_settlement_wl PERIODS: ' , $periods);
        foreach($periods as $period) {
            $date_from  = $period['date_from'];
            $date_to    = $period['date_to'];

            $period_game_info = $this->get_agent_game_info($agent_id, [$player_id], NULL, NULL, $date_from, $date_to);
            if ($period_game_info['total_bets'] == 0) {
                $this->utils->debug_log('AGENT_ID: ' . $agent_id, 'PERIOD: ' . "{$date_from} - {$date_to}", 'SKIPPING: NO BETS');
                continue;
            }
            foreach ($game_types as $game_type) {
                $game_platform_id = $game_type['game_platform_id'];
                $game_type_id = $game_type['game_type_id'];
                $game_info = $this->get_agent_game_info($agent_id, [$player_id], $game_platform_id, $game_type_id, $date_from, $date_to);
                if (!isset($game_info) || empty($game_info) 
                    || !isset($game_info['total_bets']) 
                    || empty($game_info['total_bets']) || $game_info['total_bets'] == 0) {
                    continue;
                }
                $total_real_bets       = isset($game_info['total_real_bets']) ? $game_info['total_real_bets'] : 0;
                $total_bets            = isset($game_info['total_bets']) ? $game_info['total_bets'] : 0;
                $winning_bets          = isset($game_info['gain_sum']) ? $game_info['gain_sum'] : 0;
                $total_lost_bets       = isset($game_info['lost_bets']) ? $game_info['lost_bets'] : 0;
                $total_tie_bets        = isset($game_info['tie_bets']) ? $game_info['tie_bets'] : 0;
                $result_amount         = isset($game_info['gain_loss_sum']) ? $game_info['gain_loss_sum'] : 0;
                $total_bets_except_tie = $total_bets - $total_tie_bets;
                $net_gaming         = 0 - $result_amount;

                $data = array(
                    'player_id'            => $player_id,
                    'agent_id'             => $agent_id,
                    'game_platform_id'     => $game_platform_id,
                    'game_type_id'         => $game_type_id,
                    'settlement_date'      => $date_from,
                    'winning_bets'         => $winning_bets,
                    'real_bets'            => $total_real_bets,
                    'bets'                 => $total_bets,
                    'tie_bets'             => $total_tie_bets,
                    'result_amount'        => $result_amount,
                    'lost_bets'            => $total_lost_bets,
                    'bets_except_tie'      => $total_bets_except_tie,
                    'wins'                 => $result_amount,
                    'net_gaming'           => $net_gaming,
                    'updated_on'           => date('Y-m-d H:i:s'),
                );
                $this->utils->debug_log('agency_daily', $data);

                $this->db->where('player_id', $player_id);
                $this->db->where('agent_id', $agent_id);
                $this->db->where('game_platform_id', $game_platform_id);
                $this->db->where('game_type_id', $game_type_id);
                $this->db->where('settlement_date', $date_from);
                $this->db->update('agency_daily_player_settlement', $data);
                if ( ! $this->db->affected_rows()) {
                    $data['created_on'] = date('Y-m-d H:i:s');
                    try{
                        $this->db->insert('agency_daily_player_settlement', $data);
                    }catch (Exception $e){
                        $this->utils->debug_log('insert Exception', $e);
                    }
                }
            }
        }
    } // create_daily_player_settlement_wl  }}}2
    // create_settlement_period_array {{{2
    /**
     *  create all possible settlement datetime range according to settlement_period and created time
     *
     *  @param  array agent_details
     *  @return array all possible datetime range
     */
    private function create_settlement_period_array($agent_details) {

        $bgn_time = $this->config->item('agency_settlement_time');
        $end_time = date('H:i:s', strtotime($bgn_time) - 1);

        $interval = array(
            'Daily' => '+1 day',
            'Weekly' => '+1 week',
            'Monthly' => '+1 month',
            'Quarterly' => '+3 month',
            'Manual' => '+1 year',
        );
        $start_day = array(
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
        );

        $ranges = array();

        $period_name=$agent_details['settlement_period'];
        // $periods = explode(',', $agent_details['settlement_period']);
        $created_on = $agent_details['created_on'];
        $created_time = date("H:i:s", strtotime($created_on));

        $bgn_day = date("Y-m-d", strtotime($created_on));
        if($created_time < $bgn_time) {
            $bgn_day = date("Y-m-d", strtotime("$bgn_day -1 day"));
        }
        $now = date("Y-m-d H:i:s");
        // foreach ($periods as $period_name) {
            $inter = $interval[$period_name];
            $bgn = $bgn_day . ' ' . $bgn_time;
            if ($period_name == 'Weekly') {
                $weekday = date("w", strtotime($bgn));
                $settle_day = $start_day[$agent_details['settlement_start_day']];
                if ($settle_day <= $weekday) {
                    $settle_day += 7;
                }
                $next_day = $settle_day - $weekday;

                $end = date("Y-m-d H:i:s", strtotime("$bgn +$next_day day"));
            } else {
                $end = date("Y-m-d H:i:s", strtotime("$bgn $inter"));
            }
            if ($bgn_time == '00:00:00') {
                $end_day = date("Y-m-d", strtotime("$end -1 day"));
            } else {
                $end_day = date("Y-m-d", strtotime($end));
            }
            $this->utils->debug_log("END END_DAY", $end, $end_day);
            $end = $end_day . ' ' . $end_time;

            $ranges[$period_name] = array();

            $break_it = false;
            //for ($i = 0; $i < 16; $i++) {
            for ($i = 0; true; $i++) {
                $date_from = $bgn;
                if ($created_on > $date_from) {
                    $date_from = $created_on;
                }
                $date_to = $end;
                if ($date_to > $now) {
                    $break_it = true;
                    $date_to = $now;
                }
                $ranges[$period_name][] = array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                );
                if ($date_to >= $now || $break_it) {
                    break;
                }
                //$bgn = date("Y-m-d H:i:s", strtotime("$bgn $inter"));
                $end_day = date("Y-m-d", strtotime("$end"));
                $bgn = $end_day . ' ' . $bgn_time;
                if ($bgn_time == '00:00:00') {
                    $bgn = date("Y-m-d", strtotime("$bgn +1 day"));
                }
                $end = date("Y-m-d H:i:s", strtotime("$end $inter"));
            }
        // }
        return $ranges;
    } // create_settlement_period_array  }}}2
    // get_player_commission {{{2
    /**
     *  calculate rolling amount using tier rolling comm settings for a given player
     *
     *  @param  array $player player information in players table
     *  @return DOUBLE player rolling comm amount
     */
    private function get_player_commission($player, $date_from, $date_to) {
        $player_id = $player['playerId'];
        $p = $this->agency_model->get_player_game_types($player_id);
        $player_game_types = array_column($p, NULL, 'game_type_id');
        $this->utils->debug_log('get_player_commission PLAYER_GAME_TYPES', $p, $player_game_types);

        $player_rolling = 0.0;

        $game_rows = $this->agency_model->getSettlementRawData($player_id, $date_from, $date_to, 'player');
        if (!isset($game_rows) || empty($game_rows)) {
            $this->utils->debug_log('get_player_commission GAME_ROWS', $game_rows);
            return 0;
        }
        foreach($game_rows as $game_row) {
            if(empty($game_row['bet']) || $game_row['bet'] == 0) {
                continue;
            }
            $game_type_id = $game_row['game_type_id'];

            $game_rolling = 0.0;
            if ($this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
                $game_rolling = $this->get_player_game_rolling_tier_comm($game_row, $player_game_types, $game_type_id);
            } else {
                $game_rolling = $this->get_player_game_rolling_direct($game_row, $player_game_types, $game_type_id);
            }
            $player_rolling += $game_rolling;
        }

        return $player_rolling;
    } // get_player_commission  }}}2
    // get_player_game_rolling_direct {{{2
    /**
     *  calculate player game rolling via tier comm patterns
     *
     *  @param  array $game_row  row from agency_daily_player_settlement table
     *  @param  array $player_game_types  game types for this player
     *  @param  INT $game_type_id
     *  @return DOUBLE game rolling
     */
    private function get_player_game_rolling_direct($game_row, $player_game_types, $game_type_id) {
        $rolling_comm_basis = $player_game_types[$game_type_id]['rolling_comm_basis'];
        switch ($rolling_comm_basis) {
        case 'winning_bets':
            $basis = $game_row['winning_bets'];
        case 'total_bets':
            $basis = $game_row['bets'];
            break;
        case 'total_lost_bets':
            $basis = $game_row['lost_bets'];
            break;
        case 'total_bets_except_tie_bets':
            $basis = $game_row['bets_except_tie'];
            break;
        }

        $rolling_comm = $player_game_types[$game_type_id]['rolling_comm'];

        $game_rolling = $basis * $rolling_comm/100.0;

        return $game_rolling;
    } // get_player_game_rolling_direct  }}}2
    // get_player_game_rolling_tier_comm {{{2
    /**
     *  calculate player game rolling via tier comm patterns
     *
     *  @param  array $game_row  row from agency_daily_player_settlement table
     *  @param  array $player_game_types  game types for this player
     *  @param  INT $game_type_id
     *  @return DOUBLE game rolling
     */
    private function get_player_game_rolling_tier_comm($game_row, $player_game_types, $game_type_id) {
        $game_rolling = 0.0;

        $pattern_id = $player_game_types[$game_type_id]['pattern_id'];
        if (empty($pattern_id) || $pattern_id == 0){
            $tier_count = 1;
        } else {
            $pattern = $this->agency_model->get_tier_comm_pattern($pattern_id);
            $tier_count = $pattern['tier_count'];
        }
        //$rolling_comm_basis = $pattern['rolling_comm_basis'];
        $rolling_comm_basis = $player_game_types[$game_type_id]['rolling_comm_basis'];
        switch ($rolling_comm_basis) {
        case 'winning_bets':
            $basis = $game_row['winning_bets'];
        case 'total_bets':
            $basis = $game_row['bets'];
            break;
        case 'total_lost_bets':
            $basis = $game_row['lost_bets'];
            break;
        case 'total_bets_except_tie_bets':
            $basis = $game_row['bets_except_tie'];
            break;
        }

        if ($tier_count == 1) {
            //$rolling_comm = $pattern['rolling_comm'];
            $rolling_comm = $player_game_types[$game_type_id]['rolling_comm'];
            $game_rolling += $basis * $rolling_comm/100.0;
        } else {
            $t = $this->agency_model->get_tier_comm_pattern_tiers_by_pattern_id($pattern_id);
            $tiers = array_column($t, NULL, 'tier_index');
            if ($pattern['cal_method'] == self::HIGHEST_ATTAINED) {
                foreach($tiers as $tier) {
                    $rolling_comm = $tier['rolling_comm'];
                    if ($basis <= $tier['upper_bound']) {
                        break;
                    }
                }
                $game_rolling += $basis * $rolling_comm/100.0;
            } 
            /* This calculation method is NOT supported at present 
            else {
                // the calculation is based on the tier independent rolling_comm 
                $old_upper = 0;
                foreach($tiers as $i => $tier) {
                    $upper = $tier['upper_bound'];
                    if ($i < $tier_count - 1 && $basis > $upper) {
                        $game_rolling += ($upper - $old_upper) * $tier['rolling_comm'] / 100.0;
                        $old_upper = $upper;
                    } else {
                        $game_rolling += ($basis - $old_upper) * $tier['rolling_comm'] / 100.0;
                        break;
                    }
                }
            } */
        }
        return $game_rolling;
    } // get_player_game_rolling_tier_comm  }}}2

    // get_agent_commission {{{2
    /**
     *  calculate rolling amount using tier rolling comm settings for a given agent
     *
     *  @param  array $agent agent information in agency_agents table
     *  @return array agent rev share amount and rolling comm amount
     */
    private function get_agent_commission($agent, $date_from, $date_to) {
        $agent_id = $agent['agent_id'];
        $p = $this->agency_model->get_agent_game_types($agent_id);
        $agent_game_types = array_column($p, NULL, 'game_type_id');

        $rev_share_amt = 0.0;
        $agent_rolling = 0.0;

        $game_rows = $this->agency_model->getSettlementRawData($agent_id, $date_from, $date_to, 'agent');
        foreach($game_rows as $game_row) {
            $game_type_id = $game_row['game_type_id'];

            $game_rev_share = 0.0;
            $game_rolling   = 0.0;
            if ($this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
                list($game_rev_share, $game_rolling) = $this->get_agent_game_rolling_tier_comm($game_row, $agent_game_types, $game_type_id);
            } else {
                list($game_rev_share, $game_rolling) = $this->get_agent_game_rolling_direct($game_row, $agent_game_types, $game_type_id);
            }
            $rev_share_amt += $game_rev_share;
            $agent_rolling += $game_rolling;
        }

        return [$rev_share_amt, $agent_rolling];
    } // get_agent_commission  }}}2
    // get_agent_game_rolling_direct {{{2
    /**
     * calculate agent game rev share amount and rolling comm amount via tier comm patterns
     *
     *  @param  array $game_row  get from agency_daily_player_settlement table
     *  @param  array $agent_game_types  game types for this agent
     *  @param  INT $game_type_id
     *  @return array $game_rev_share $game_rolling
     */
    private function get_agent_game_rolling_direct($game_row, $agent_game_types, $game_type_id) {
        $rev_share = $agent_game_types[$game_type_id]['rev_share'];
        $net_gaming = $game_row['net_gaming'];
        $game_rev_share = $net_gaming * $rev_share / 100.0;

        $rolling_comm_basis = $agent_game_types[$game_type_id]['rolling_comm_basis'];
        switch ($rolling_comm_basis) {
        case 'winning_bets':
            $basis = $game_row['winning_bets'];
        case 'total_bets':
            $basis = $game_row['bets'];
            break;
        case 'total_lost_bets':
            $basis = $game_row['lost_bets'];
            break;
        case 'total_bets_except_tie_bets':
            $basis = $game_row['bets_except_tie'];
            break;
        }
        $rolling_comm = $agent_game_types[$game_type_id]['rolling_comm'];
        $game_rolling = $basis * $rolling_comm/100.0;

        return [$game_rev_share, $game_rolling];
    } // get_agent_game_rolling_direct  }}}2
    // get_agent_game_rolling_tier_comm {{{2
    /**
     * calculate agent game rev share amount and rolling comm amount via tier comm patterns
     *
     *  @param  array $game_row  get from agency_daily_player_settlement table
     *  @param  array $agent_game_types  game types for this agent
     *  @param  INT $game_type_id
     *  @return array $game_rev_share $game_rolling
     */
    private function get_agent_game_rolling_tier_comm($game_row, $agent_game_types, $game_type_id) {
        $game_rev_share = 0.0;
        $game_rolling = 0.0;

        $pattern_id = $agent_game_types[$game_type_id]['pattern_id'];
        if (empty($pattern_id) || $pattern_id == 0){
            $tier_count = 1;
        } else {
            $pattern = $this->agency_model->get_tier_comm_pattern($pattern_id);
            $tier_count = $pattern['tier_count'];
        }
        //$rolling_comm_basis = $pattern['rolling_comm_basis'];
        $rolling_comm_basis = $agent_game_types[$game_type_id]['rolling_comm_basis'];
        switch ($rolling_comm_basis) {
        case 'winning_bets':
            $basis = $game_row['winning_bets'];
        case 'total_bets':
            $basis = $game_row['bets'];
            break;
        case 'total_lost_bets':
            $basis = $game_row['lost_bets'];
            break;
        case 'total_bets_except_tie_bets':
            $basis = $game_row['bets_except_tie'];
            break;
        }

        $net_gaming = $game_row['net_gaming'];
        if ($tier_count == 1) {
            //$rolling_comm = $pattern['rolling_comm'];
            $rolling_comm = $agent_game_types[$game_type_id]['rolling_comm'];
            $game_rolling += $basis * $rolling_comm/100.0;
            //$rev_share = $pattern['rev_share'];
            $rev_share = $agent_game_types[$game_type_id]['rev_share'];
            $game_rev_share += $net_gaming * $rev_share / 100.0;
        } else {
            $t = $this->agency_model->get_tier_comm_pattern_tiers_by_pattern_id($pattern_id);
            $tiers = array_column($t, NULL, 'tier_index');
            if ($pattern['cal_method'] == self::HIGHEST_ATTAINED) {
                foreach($tiers as $tier) {
                    $rolling_comm = $tier['rolling_comm'];
                    $rev_share = $tier['rev_share'];
                    if ($basis <= $tier['upper_bound']) {
                        break;
                    }
                }
                $game_rolling += $basis * $rolling_comm/100.0;
                $game_rev_share += $net_gaming * $rev_share / 100.0;
            }
            /* This method is NOT supported at present 
            else {
                // the calculation is based on the tier independent rolling_comm 
                $old_upper = 0;
                foreach($tiers as $i => $tier) {
                    $upper = $tier['upper_bound'];
                    if ($i < $tier_count - 1 && $basis > $upper) {
                        $game_rolling += ($upper - $old_upper) * $tier['rolling_comm'] / 100.0;
                        $game_rev_share += ($upper - $old_upper) * $tier['rev_share'] / 100.0;
                        $old_upper = $upper;
                    } else {
                        $game_rolling += ($basis - $old_upper) * $tier['rolling_comm'] / 100.0;
                        $game_rev_share += ($basis - $old_upper) * $tier['rev_share'] / 100.0;
                        break;
                    }
                }
            } */
        }
        return [$game_rev_share, $game_rolling];
    } // get_agent_game_rolling_tier_comm  }}}2
// get_agent_fees {{{2
/**
 *  calculate fees for the given agent
 *
 *  @param  array $data OUT
 *  @param  array $agent_row settlement raw data for the agent
 *  @return void
 */
private function get_agent_fees(&$data, $agent, $player_ids, $period) {
    $earnings = $data['rev_share_amt'];
    $admin = 0;
    if ($agent['admin_fee'] && $earnings > 0) {
        $admin = ($agent['admin_fee'] / 100) * $earnings;
    }
    $fee_info = $this->get_agent_fee_info($agent, $player_ids, $period['date_from'], $period['date_to']);

    $data['bonuses']           = $fee_info['bonuses'];
    $data['rebates']           = $fee_info['rebates'];
    $data['transactions']      = $fee_info['transactions'];
    $data['admin']             = $admin;
} // get_agent_fees  }}}2
// get_player_game_info {{{2
    /**
     *  get all relative game info for given player
     *
     *  @param  array all downline player ids
     *  @param  array period [date_from, date_to]
     *  @return array all relative game data
     */
    public function get_player_game_info($player_id, $period) {
        $date_from = $period['date_from'];
        $date_to = $period['date_to'];

        $this->load->model(array('game_logs'));

        $game_info = array();
        $bets = $this->game_logs->get_player_bet_info($player_id, $date_from, $date_to);
        $this->utils->debug_log('game info', $bets);

        if(!empty($bets)) {
            foreach ($bets as $rec) {
                foreach ($rec as $key => $val) {
                    if(empty($val)) {
                        $game_info[$key] = 0;
                    } else {
                        $game_info[$key] = $val;
                    }
                }
            }
        }

        return $game_info;
    } // get_player_game_info  }}}2
    // get_agent_game_info {{{2
    /**
     *  get all relative game info for given agent
     *
     *  @param  array all downline player ids
     *  @param  array period [date_from, date_to]
     *  @return array all relative game data
     */
    private function get_agent_game_info($agent_id, $player_ids, $game_platform_id, $game_type_id, $date_from, $date_to) {
        $bets = $this->game_logs->get_agent_bet_info($agent_id, $player_ids, $game_platform_id, $game_type_id, $date_from, $date_to);
        $this->utils->debug_log('agent_id', $agent_id, 'game_platform_id', $game_platform_id, 'game_type_id', $game_type_id, 'date_from', $date_from, 'date_to', $date_to, 'game_info', $bets);
        return $bets;
    } // get_agent_game_info  }}}2
    // get_agent_fee_info {{{2
    /**
     *  get all relative game info for given agent
     *
     *  @param  array all downline player ids
     *  @param  array period [date_from, date_to]
     *  @return array all relative game data
     */
    private function get_agent_fee_info($agent_details, $player_ids, $date_from, $date_to) {
        $this->load->model(array('transactions','player_model'));

        $fee_info = array('bonuses' => 0, 'rebates' => 0, 'transactions' => 0);

        # BONUS FEE
        if ($agent_details['bonus_fee']) {
            $bonuses = $this->player_model->getPlayersTotalBonus($player_ids, $date_from, $date_to);
            $fee_info['bonuses'] = ($agent_details['bonus_fee'] / 100) * $bonuses;
            if ($bonuses != 0) {
                $this->utils->debug_log('agent_id', $agent_details['agent_id'], 'bonuses', $bonuses, 'bonuses_rate', $agent_details['bonus_fee'], 'bonus_fee', $fee_info['bonuses']);
            }
        }

        # CASHBACK FEE
        if ($agent_details['cashback_fee']) {
            $rebates = $this->player_model->getPlayersTotalCashback($player_ids, $date_from, $date_to);
            $fee_info['rebates'] = ($agent_details['cashback_fee'] / 100) * $rebates;
            if ($rebates != 0) {
                $this->utils->debug_log('agent_id', $agent_details['agent_id'], 'rebates', $rebates, 'rebates_rate', $agent_details['cashback_fee'], 'cashback_fee', $fee_info['rebates']);
            }
        }

        # TRANSACTION FEE
        if ($agent_details['transaction_fee']) {
            $transactions = $this->transactions->sumTransactionFee($player_ids, $date_from, $date_to);
            $fee_info['transactions'] = ($agent_details['transaction_fee'] / 100) * $transactions;
            if ($transactions != 0) {
                $this->utils->debug_log('agent_id', $agent_details['agent_id'], 'transactions', $transactions, 'transactions_rate', $agent_details['transaction_fee'], 'transaction_fee', $fee_info['transactions']);
            }
        }

        return $fee_info;
    } // get_agent_fee_info  }}}2
}
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agency_settlement_module.php
