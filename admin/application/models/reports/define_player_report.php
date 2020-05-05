<?php
/**
 *
    'dt' => index of column,
    'alias' => sql alias,
    'name' => readable name,
    'select' => sql select string,
    'fixed' => , default is false
    'sortable' => , default is true
    'formatter' => formatter function or standard formatter
    'minWidth' => ,
    'key_for_count' => (for count select), default is false

 */
trait define_player_report{

    //===player report========================================
    public function queryPlayerReport($conditions, $type, $db=null){

        if(empty($db)){
            $db=$this->db;
        }
        $debug_report_mode=$this->utils->getConfig('debug_report_mode');
        $header=null;
        $useAssocRows=false;
        if(isset($conditions['options']['useAssocRows'])){
            $useAssocRows=$conditions['options']['useAssocRows'];
        }
        //define columns, formatter
        $columns=$this->columnsForPlayerReport($conditions);
        $rows=null;
        //condition
        if($type==self::QUERY_REPORT_TYPE_ONE_PAGE){
            //select sql
            $this->buildSelectOnDBFromColumns($columns, $db);
            //main table
            $db->from('player_report_hourly');
            //join
            $this->buildJoinForPlayerReport($conditions, $db);
            //group by
            $this->buildGroupByForPlayerReport($conditions, $db);
            //order by
            $this->buildOrderByForPlayerReport($conditions, $columns, $db);
            //limit
            $this->buildCommonLimitOnDB($conditions, $db);
            //where
            $this->buildWhereForPlayerReport($conditions, $db);
            //header
            $header=$this->buildHeaderByColumns($columns);
            // $colMap=$this->convertColumnsToMap($columns);

            // $this->utils->debug_log('colMap', $colMap);

            $sql=null;
            //run
            $rows=$this->runRawSelectOnMYSQLReturnNumberArray($db, function(&$row, $headerOfDB)
                    use($columns, $useAssocRows){
                // $this->utils->debug_log('row', $row, $fields);
                // $assocRow=array_combine($fields, $row);
                //try format
                $success=$this->formatColumns($row, $columns, $headerOfDB);
                if($success && $useAssocRows){
                    $this->utils->debug_log('row', $row, $headerOfDB);
                    //convert it to assoc array
                    $row=array_combine($headerOfDB, $row);
                }
                return $success;
                // unset($assocRow);
            }, false, false, $sql);

            if(!$debug_report_mode){
                $sql=null;
            }

            //get settings
            $settings=$this->getSuperReportSettings(self::SUPER_REPORT_TYPE_PLAYER);

            return ['header'=>$header, 'rows'=>$rows, 'settings'=>$settings, 'sql'=>$sql];

        }else if($type==self::QUERY_REPORT_TYPE_COUNT){
            $colForCount=$this->buildCountOnDBFromColumns($columns, $db);
            $db->from('player_report_hourly');
            //join
            $this->buildJoinForPlayerReport($conditions, $db);
            //group by
            $this->buildGroupByForPlayerReport($conditions, $db);
            //where
            $this->buildWhereForPlayerReport($conditions, $db);
            //run
            $cnt=$this->runOneRowOneField($colForCount['alias'], $db);
            // $this->utils->printLastSQL($db);
            $sql=null;
            if($debug_report_mode){
                $sql=$db->last_query();
                $this->utils->debug_log('count sql', $sql);
            }

            return ['count'=>intval($cnt), 'sql'=>$sql];
        }else if($type==self::QUERY_REPORT_TYPE_TOTAL){
            // $sumColumns=$this->convertToSumColumns($columns);
            $this->buildTotalOnDBFromColumns($columns, $db ,$existsSumField);
            if(!$existsSumField){
                //error
                $this->utils->error_log('didnot find any select_sum column');
                return ['total'=>null, 'sql'=>null];
            }
            $db->from('player_report_hourly');
            //join
            $this->buildJoinForPlayerReport($conditions, $db);
            //where
            $this->buildWhereForPlayerReport($conditions, $db);
            //run
            $sql=null;
            $rows=$this->runRawSelectOnMYSQLReturnNumberArray($db, function(&$row, $headerOfDB)
                    use($columns, $useAssocRows){
                // $this->utils->debug_log('row', $row, $fields);
                // $assocRow=array_combine($fields, $row);
                //try format
                $keepEmpty=true;
                $success=$this->formatColumns($row, $columns, $headerOfDB, $keepEmpty);
                if($success && $useAssocRows){
                    $this->utils->debug_log('row', $row, $headerOfDB);
                    //convert it to assoc array
                    $row=array_combine($headerOfDB, $row);
                }
                return $success;
                // unset($assocRow);
            }, false, false, $sql);

            $totalRow=$rows[0];

            if(!$debug_report_mode){
                $sql=null;
            }

            return ['total'=>$totalRow, 'sql'=>$sql];
        }else if($type==self::QUERY_REPORT_TYPE_SUMMARY){

            return ['summary'=>null, 'sql'=>null];
        }

        return null;
    }

    /**
     * player report
     * @param  string $date_from
     * @param  string $date_to
     * @param  string $group_by
     * @return array
     */
    public function columnsForPlayerReport($conditions){

        $searchBy=$conditions['searchBy'];
        $date_from=$searchBy['dateFrom'];
        $date_to=$searchBy['dateTo'];
        $group_by=$conditions['groupBy'];

        $this->load->model(['player_model', 'risk_score_model', 'player_kyc']);
        $i=0;

        $show_username = true;
        $show_realname = true;
        $show_tag = true;
        $show_risk_level = true;
        $show_kyc_level = true;
        $show_player_level = true;
        $show_affiliate = true;
        $show_agent= true;
        $show_register_date = true;
        $show_deposit_bonus=true;
        $show_cashback_bonus = true;
        $show_referral_bonus = true;
        $show_manual_bonus = true;
        $show_subtract_bonus = true;
        $show_total_bonus = true;
        $show_first_deposit = true;
        $show_first_deposit_date = true;
        $show_total_deposit = true;
        $show_total_deposit_times = true;
        $show_total_withdrawal = true;
        $show_total_dw = true;
        $show_total_bets = true;
        $show_total_win = true;
        $show_total_loss = true;
        $show_total_payout = true;
        $show_payout_rate = true;
        $show_total_revenue = true;

        if (empty($group_by)) {
            //default group by
            $group_by='player_id';
        }

        switch ($group_by) {
            case 'player_id':
                // $group_by[] = 'player_id';
                break;

            case 'playerlevel':
                // $group_by[] = 'level_id';
                $show_username = false;
                $show_realname = false;
                $show_tag = false;
                $show_risk_level = false;
                $show_kyc_level =false;
                $show_player_level = true;
                $show_affiliate = false;
                $show_agent= false;
                $show_register_date = false;

                break;
            case 'affiliate_id':
                // $group_by[] = 'affiliate_id';
                $show_username = false;
                $show_realname = false;
                $show_tag = false;
                $show_risk_level = false;
                $show_kyc_level =false;
                $show_player_level = false;
                $show_affiliate = true;
                $show_agent= false;
                $show_register_date = false;
                break;

            case 'agent_id':
                // $group_by[] = 'agent_id';
                $show_username = false;
                $show_realname = false;
                $show_tag = false;
                $show_risk_level = false;
                $show_kyc_level =false;
                $show_player_level = false;
                $show_affiliate = false;
                $show_agent= true;
                $show_register_date = false;
                break;

            default:
                break;
        }

        $columns = [
            [
                'alias'=>'id',
                'select'=>'id',
                'key_for_count'=>true,
            ],
            [
                'select' => 'affiliate_id'
            ],
            [
                'select' => 'agent_id'
            ],
            [
                'select' => 'level_name'
            ],
            [
                'select' => 'level_id'
            ],
            [
                'dt' => $i++,
                'alias' => 'username',
                'name' => lang('report.pr01'),
                'select' => 'player_username',
                'minWidth'=>90,
                'visible' => $show_username,
                'formatter' => function ($d, $row) use ($date_from, $date_to) {
                    $date_qry = '';
                    if (!empty($date_from) && !empty($date_to)) {
                        $date = new DateTime($date_from);
                        $date_qry = '&date_from=' . $date->format('Y-m-d') . '&hour_from=' . $date->format('H');

                        $date = new DateTime($date_to);
                        $date_qry .= '&date_to=' . $date->format('Y-m-d') . '&hour_to=' . $date->format('H');
                    }
                    // return $d;
                    return ['value'=>$d, 'url'=>"/report_management/viewGamesReport?username={$d}{$date_qry}"];// "<a href='/report_management/viewGamesReport?username={$d}{$date_qry}'>{$d}</a>";
                },
            ],
            [
                'dt' => $i++,
                'alias' => 'tagName',
                'select' => 'player_id',
                'minWidth'=>90,
                'name' => lang("player.41"),
                'visible' => $show_tag,
                'formatter' => function ($d) {
                    $tag_list = $this->player_model->getPlayerTags($d);
                    if(FALSE === $tag_list || !is_array($tag_list)){
                        return null;
                    }

                    $text_list = [];
                    foreach($tag_list as $tag_entry){
                        $text_list[] = $tag_entry['tagName'];
                    }

                    return implode(',', $text_list);
                },
            ],
            [
                'dt' => $i++,
                'name' => lang('report.pr03'),
                'alias' => 'member_level',
                'select' => 'group_name',
                'minWidth'=>90,
                'visible' => $show_player_level,
                'formatter' => function ($d, $row) {
                    return lang($d)." - ".lang($row['level_name']);
                },
            ],
            [
                'dt' => $i++,
                'alias' => 'affiliates_name',
                'name' => lang('Affiliate'),
                'select' => 'affiliate_username',
                'minWidth'=>90,
                'visible' => $show_affiliate,
                'formatter' => function($d,$row){
                    return ['value'=>$d, 'url'=>'/affiliate_management/userInformation/' . $row['affiliate_id']];
                },
            ],
            [
                'dt' => $i++,
                'alias' => 'agent',
                'select' => 'agent_username',
                'minWidth'=>90,
                'name' => lang("Agent"),
                'visible' => $show_agent,
                'formatter' => function ($d, $row) {
                    return ['value'=>$d, 'url'=>'/agency_management/agent_information/'.$row['agent_id']];
                },
            ],
            [
                'dt' => $i++,
                'alias' => 'createdOn',
                'name' => lang('report.pr10'),
                'select' => 'registered_date',
                'minWidth'=>90,
                'visible' => $show_register_date,
            ],
            [
                'dt' => $i++,
                'alias' => 'first_deposit_datetime',
                'select' => 'first_deposit_datetime',
                'minWidth'=>90,
                'name' => lang('aff.ap06'),
                'visible' => $show_first_deposit_date,
            ],
            [
                'dt' => $i++,
                'alias' => 'cashback',
                'name' => lang('report.sum15'),
                'select' => 'SUM(total_cashback)',
                'select_sum' => 'SUM(total_cashback)',
                'minWidth'=>90,
                'visible' => $show_cashback_bonus,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'deposit_bonus',
                'name' => lang('report.pr15'),
                'select' => 'SUM(deposit_bonus)',
                'select_sum' => 'SUM(deposit_bonus)',
                'minWidth'=>90,
                'visible' => $show_deposit_bonus,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'referral_bonus',
                'name' => lang('report.pr17'),
                'select' => 'SUM(referral_bonus)',
                'select_sum' => 'SUM(referral_bonus)',
                'minWidth'=>90,
                'visible' => $show_referral_bonus,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'manual_bonus',
                'name' => lang('transaction.manual_bonus'),
                'select' => 'SUM(manual_bonus)',
                'select_sum' => 'SUM(manual_bonus)',
                'minWidth'=>90,
                'visible' => $show_manual_bonus,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'subtract_bonus',
                'name' => lang('transaction.transaction.type.10'),
                'select' => 'SUM(subtract_bonus)',
                'select_sum' => 'SUM(subtract_bonus)',
                'minWidth'=>90,
                'visible' => $show_subtract_bonus,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_bonus',
                'name' => lang('report.pr18'),
                'select' => 'SUM(total_bonus)',
                'select_sum' => 'SUM(total_bonus)',
                'minWidth'=>90,
                'visible' => $show_total_bonus,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'first_deposit',
                'select' => 'first_deposit_amount',
                'select_sum' => 'SUM(first_deposit_amount)',
                'minWidth'=>90,
                'name' => lang('player.75'),
                'visible' => $show_first_deposit,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_deposit',
                'name' => lang('report.pr21'),
                'select' => 'SUM(total_deposit)',
                'select_sum' => 'SUM(total_deposit)',
                'minWidth'=>90,
                'visible' => $show_total_deposit,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_deposit_times',
                'name' => lang('yuanbao.deposit.times'),
                'select' => 'SUM(deposit_times)',
                'select_sum' => 'SUM(deposit_times)',
                'visible' => $show_total_deposit_times,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_deposit_and_bonus',
                'name' => lang('DNB'),
                'select' => 'SUM(total_deposit+total_bonus)',
                'select_sum' => 'SUM(total_deposit+total_bonus)',
                'minWidth'=>90,
                'visible' => $show_total_deposit_times,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_bonus_over_deposit',
                'name' => lang('BOD%'),
                'select' => 'SUM(total_bonus)/SUM(total_deposit)',
                'minWidth'=>90,
                'visible' => $show_total_deposit_times,
                'formatter' => 'percentageFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_withdrawal',
                'name' => lang('report.pr22'),
                'select' => 'SUM(total_withdrawal)',
                'select_sum' => 'SUM(total_withdrawal)',
                'minWidth'=>90,
                'visible' => $show_total_withdrawal,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_dw',
                'name' => lang('Net Deposit'),
                'select' =>'SUM(total_gross)',
                'select_sum' => 'SUM(total_gross)',
                'minWidth'=>90,
                'visible' => $show_total_dw,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_withdrawal_over_deposit',
                'name' => lang('WOD%'),
                'select' =>'SUM(total_withdrawal)/SUM(total_deposit)',
                'minWidth'=>90,
                'visible' => $show_total_dw,
                'formatter' => 'percentageFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_bets',
                'name' => lang('cms.totalbets'),
                'select' => 'SUM(total_bet)',
                'select_sum' => 'SUM(total_bet)',
                'minWidth'=>90,
                'visible' => $show_total_bets,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'turn_around_time',
                'name' => lang('TAT'),
                'select' => 'SUM(total_bet)/ (SUM(total_deposit)+SUM(total_bonus))',
                'minWidth'=>90,
                'visible' => $show_total_bets,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_win',
                'name' => lang('Win'),
                'select' => 'SUM(total_win)',
                'select_sum' => 'SUM(total_win)',
                'minWidth'=>90,
                'visible' => $show_total_win,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_loss',
                'name' => lang('Loss'),
                'select' => 'SUM(total_loss)',
                'select_sum' => 'SUM(total_loss)',
                'minWidth'=>90,
                'visible' => $show_total_loss,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_payout',
                'name' => lang('Payout'),
                'select' => 'SUM(payout)',
                'select_sum' => 'SUM(payout)',
                'minWidth'=>90,
                'visible' => $show_total_payout,
                'formatter' => 'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'payout_rate',
                'name' => lang('sys.payoutrate'),
                'select' => '(SUM(payout)/SUM(total_bet))',
                'minWidth'=>90,
                'visible' => $show_payout_rate,
                'formatter' => 'percentageFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_revenue',
                'name' => lang('Game Revenue'),
                'select' => 'SUM(total_loss)-SUM(total_win)',
                'minWidth'=>90,
                'visible' => $show_total_revenue,
                'formatter' => 'currencyFormatter',
            ]
        ];

        return $columns;

    }

    public function buildJoinForPlayerReport($conditions, $db){
        //nothing
    }

    public function buildGroupByForPlayerReport($conditions, $db){
        $searchBy=$conditions['searchBy'];

        $group_by=$conditions['groupBy'];
        $groupList=[];
        if (!empty($group_by)) {
            switch ($group_by) {
                case 'player_id':
                    $groupList[] = 'player_id';
                    break;
                case 'playerlevel':
                    $groupList[] = 'level_id';
                    break;
                case 'affiliate_id':
                    $groupList[] = 'affiliate_id';
                    break;
                case 'agent_id':
                    $groupList[] = 'agent_id';
                    break;
                default:
                    break;
            }
        }else{
            $groupList[] = 'player_id';
        }
        $this->buildGroupByOnDB($groupList, $db);
    }
    public function buildOrderByForPlayerReport($conditions, $columns, $db){
        $searchBy=$conditions['searchBy'];

        $orderList=[];
        if(isset($conditions['orderBy'])){
            $orderBy=$conditions['orderBy'];
            $orderList=$this->generateOrderByDefine($orderBy, $columns);
            if(!empty($orderList)){
                //append id
                $orderList[]=['field'=>'id', 'direction'=>$orderBy['direction']];
            }
        }
        //if nothing
        if(empty($orderList)){
            $orderList[]=['field'=>'date_hour', 'direction'=>'desc'];
            $orderList[]=['field'=>'id', 'direction'=>'desc'];
        }
        $this->buildOrderByOnDB($orderList, $db);
    }
    public function buildWhereForPlayerReport($conditions, $db){
        $searchBy=$conditions['searchBy'];
        $from=$this->utils->formatDateHourForMysql(new DateTime($searchBy['dateFrom'].' '.Utils::FIRST_TIME));
        $to=$this->utils->formatDateHourForMysql(new DateTime($searchBy['dateTo'].' '.Utils::LAST_TIME));

        $whereList=[];
        $whereList[]=['key'=>'date_hour >=', 'value'=>$from];
        $whereList[]=['key'=>'date_hour <=', 'value'=>$to];

        $this->buildSimpleWhereOnDB($whereList, $db);

    }
    //===player report========================================

}
