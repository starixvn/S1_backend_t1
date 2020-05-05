<?php
/**
 *
    'dt' => index of column,
    'alias' => sql alias,
    'name' => readable name,
    'select' => sql select string,
    'select_sum' => only for total,
    'fixed' => , default is false
    'sortable' => , default is true
    'formatter' => formatter function or standard formatter
    'minWidth' => ,
    'key_for_count' => (for count select), default is false

 */
trait define_game_report{

    public function queryGameReport($conditions, $type, $db=null){

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
        $columns=$this->columnsForGameReport($conditions);
        $rows=null;
        //condition
        if($type==self::QUERY_REPORT_TYPE_ONE_PAGE){
            //select sql
            $this->buildSelectOnDBFromColumns($columns, $db);
            //main table
            $db->from('game_report_hourly');
            //join
            $this->buildJoinForGameReport($conditions, $db);
            //group by
            $this->buildGroupByForGameReport($conditions, $db);
            //order by
            $this->buildOrderByForGameReport($conditions, $columns, $db);
            //limit
            $this->buildCommonLimitOnDB($conditions, $db);
            //where
            $this->buildWhereForGameReport($conditions, $db);
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
            $db->from('game_report_hourly');
            //join
            $this->buildJoinForGameReport($conditions, $db);
            //group by
            $this->buildGroupByForGameReport($conditions, $db);
            //where
            $this->buildWhereForGameReport($conditions, $db);
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
            $db->from('game_report_hourly');
            //join
            $this->buildJoinForGameReport($conditions, $db);
            //where
            $this->buildWhereForGameReport($conditions, $db);
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
    public function columnsForGameReport($conditions){

        $searchBy=$conditions['searchBy'];
        $date_from=$searchBy['dateFrom'];
        $date_to=$searchBy['dateTo'];
        $group_by=$conditions['groupBy'];

        $this->load->model(['player_model']);
        $i=0;

        $columns = [
            [
                'alias'=>'id',
                'select'=>'game_report_hourly.id',
                'key_for_count'=>true,
            ],
            [
                'select' => 'player_realname'
            ],
            [
                'dt' => $i++,
                'alias' => 'player_group_and_level',
                'name' => lang('VIP'),
                'select' => 'player_group_and_level',
                'minWidth'=>90,
            ],
            [
                'dt' => $i++,
                'alias' => 'player_username',
                'name' => lang('Player'),
                'select' => 'player_username',
                'minWidth'=>90,
            ],
            [
                'dt' => $i++,
                'alias' => 'game_platform',
                'name' => lang('Game Platform'),
                'select' => 'game_report_hourly.game_platform_code',
                'minWidth'=>90,
                'formatter'=>'languageFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'game_type',
                'name' => lang('Game Type'),
                'select' => 'game_type.game_type_lang',
                'minWidth'=>90,
                'formatter'=>'languageFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'game_description',
                'name' => lang('Game Description'),
                'select' => 'game_description.game_name',
                'minWidth'=>90,
                'formatter'=>'languageFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'currency_key',
                'name' => lang('Currency'),
                'select' => 'currency_key',
                'minWidth'=>90,
            ],
            [
                'dt' => $i++,
                'alias' => 'betting_amount',
                'name' => lang('Betting'),
                'select' => 'sum(betting_amount)',
                'select_sum' => 'sum(betting_amount)',
                'minWidth'=>90,
                'formatter'=>'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'real_betting_amount',
                'name' => lang('Real Betting'),
                'select' => 'sum(real_betting_amount)',
                'select_sum' => 'sum(real_betting_amount)',
                'minWidth'=>90,
                'formatter'=>'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'result_amount',
                'name' => lang('Result'),
                'select' => 'sum(result_amount)',
                'select_sum' => 'sum(result_amount)',
                'minWidth'=>90,
                'formatter'=>'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'win_amount',
                'name' => lang('Win'),
                'select' => 'sum(win_amount)',
                'select_sum' => 'sum(win_amount)',
                'minWidth'=>90,
                'formatter'=>'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'loss_amount',
                'name' => lang('Loss'),
                'select' => 'sum(loss_amount)',
                'select_sum' => 'sum(loss_amount)',
                'minWidth'=>90,
                'formatter'=>'currencyFormatter',
            ],


        ];

        return $columns;

    }

    public function buildJoinForGameReport($conditions, $db){
        $joins[] = ['table'=>'game_description', 'mode'=>'left',
        'join_condition'=>'game_description.external_game_id = game_report_hourly.external_game_id and game_description.game_platform_id=game_report_hourly.game_platform_id'];
        $joins[] = ['table'=>'game_type', 'mode'=>'left',
        'join_condition'=>'game_type.game_type_code = game_report_hourly.game_type_code and game_type.game_platform_id=game_report_hourly.game_platform_id'];

        $this->buildJoinOnDB($joins, $db);
    }
    public function buildGroupByForGameReport($conditions, $db){
        $searchBy=$conditions['searchBy'];

        $group_by=$conditions['groupBy'];
        $groupList=[];
        if (!empty($group_by)) {
        //     switch ($group_by) {
        //         case 'player_id':
        //             $groupList[] = 'player_id';
        //             break;
        //         case 'playerlevel':
        //             $groupList[] = 'level_id';
        //             break;
        //         case 'affiliate_id':
        //             $groupList[] = 'affiliate_id';
        //             break;
        //         case 'agent_id':
        //             $groupList[] = 'agent_id';
        //             break;
        //         default:
        //             break;
        //     }
        }else{
            $groupList[] = 'game_report_hourly.game_platform_id';
        }
        $this->buildGroupByOnDB($groupList, $db);
    }
    public function buildOrderByForGameReport($conditions, $columns, $db){
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
    public function buildWhereForGameReport($conditions, $db){
        $searchBy=$conditions['searchBy'];
        $from=$this->utils->formatDateHourForMysql(new DateTime($searchBy['dateFrom'].' '.Utils::FIRST_TIME));
        $to=$this->utils->formatDateHourForMysql(new DateTime($searchBy['dateTo'].' '.Utils::LAST_TIME));

        $whereList=[];
        $whereList[]=['key'=>'date_hour >=', 'value'=>$from];
        $whereList[]=['key'=>'date_hour <=', 'value'=>$to];

        $this->buildSimpleWhereOnDB($whereList, $db);

    }

}
