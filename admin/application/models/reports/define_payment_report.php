<?php
/**
 *
    'dt' => index of column,
    'alias' => sql alias,
    'name' => readable name,
    'select' => sql select string,
    'select_sum' => sql for total,
    'fixed' => , default is false
    'sortable' => , default is true
    'formatter' => formatter function or standard formatter
    'minWidth' => ,
    'key_for_count' => (for count select), default is false

 */
trait define_payment_report{

    public function queryPaymentReport($conditions, $type, $db=null){

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
        $columns=$this->columnsForPaymentReport($conditions);
        $rows=null;
        //condition
        if($type==self::QUERY_REPORT_TYPE_ONE_PAGE){
            //select sql
            $this->buildSelectOnDBFromColumns($columns, $db);
            //main table
            $db->from('payment_report_daily');
            //join
            $this->buildJoinForPaymentReport($conditions, $db);
            //group by
            $this->buildGroupByForPaymentReport($conditions, $db);
            //order by
            $this->buildOrderByForPaymentReport($conditions, $columns, $db);
            //limit
            $this->buildCommonLimitOnDB($conditions, $db);
            //where
            $this->buildWhereForPaymentReport($conditions, $db);
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
            $db->from('payment_report_daily');
            //join
            $this->buildJoinForPaymentReport($conditions, $db);
            //group by
            $this->buildGroupByForPaymentReport($conditions, $db);
            //where
            $this->buildWhereForPaymentReport($conditions, $db);
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
            $db->from('payment_report_daily');
            //join
            $this->buildJoinForPaymentReport($conditions, $db);
            //where
            $this->buildWhereForPaymentReport($conditions, $db);
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
    public function columnsForPaymentReport($conditions){

        $searchBy=$conditions['searchBy'];
        $date_from=$searchBy['dateFrom'];
        $date_to=$searchBy['dateTo'];
        $group_by=$conditions['groupBy'];

        $this->load->model(['player_model']);
        $i=0;

        $columns = [
            [
                'alias'=>'id',
                'select'=>'id',
                'key_for_count'=>true,
            ],
            [
                'select' => 'player_realname'
            ],
            [
                'dt' => $i++,
                'alias' => 'payment_date',
                'name' => lang('Date'),
                'select' => 'payment_date',
                'minWidth'=>90,
                'formatter'=>'dateFormatter',
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
                'alias' => 'payment_account_name',
                'name' => lang('Account Name'),
                'select' => 'payment_account_name',
                'minWidth'=>90,
            ],
            [
                'dt' => $i++,
                'alias' => 'bank_type_name',
                'name' => lang('Bank Name'),
                'select' => 'bank_type_name',
                'minWidth'=>90,
            ],
            [
                'dt' => $i++,
                'alias' => 'first_category_flag',
                'name' => lang('Category'),
                'select' => 'first_category_flag',
                'minWidth'=>90,
            ],
            [
                'dt' => $i++,
                'alias' => 'second_category_flag',
                'name' => lang('Second Category'),
                'select' => 'second_category_flag',
                'minWidth'=>90,
            ],
            [
                'dt' => $i++,
                'alias' => 'amount',
                'name' => lang('Amount'),
                'select' => 'amount',
                'select_sum' => 'sum(amount)',
                'minWidth'=>90,
                'formatter'=>'currencyFormatter',
            ],
            [
                'dt' => $i++,
                'alias' => 'external_system_code',
                'name' => lang('API'),
                'select' => 'external_system_code',
                'minWidth'=>90,
                'formatter'=>'languageFormatter',
            ],

        ];

        return $columns;

    }

    public function buildJoinForPaymentReport($conditions, $db){
        //nothing
    }
    public function buildGroupByForPaymentReport($conditions, $db){
        $searchBy=$conditions['searchBy'];

        // $group_by=$conditions['groupBy'];
        // $groupList=[];
        // if (!empty($group_by)) {
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
        // }else{
        //     $groupList[] = 'player_id';
        // }
        // $this->buildGroupByOnDB($groupList, $db);
    }
    public function buildOrderByForPaymentReport($conditions, $columns, $db){
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
            $orderList[]=['field'=>'payment_date', 'direction'=>'desc'];
            $orderList[]=['field'=>'id', 'direction'=>'desc'];
        }
        $this->buildOrderByOnDB($orderList, $db);
    }
    public function buildWhereForPaymentReport($conditions, $db){
        $searchBy=$conditions['searchBy'];
        $from=$searchBy['dateFrom'];
        $to=$searchBy['dateTo'];

        $whereList=[];
        $whereList[]=['key'=>'payment_date >=', 'value'=>$from];
        $whereList[]=['key'=>'payment_date <=', 'value'=>$to];

        $this->buildSimpleWhereOnDB($whereList, $db);

    }

}
