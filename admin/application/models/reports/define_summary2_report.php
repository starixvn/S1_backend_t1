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
trait define_summary2_report{

    //===summary2 report==============================================================================
    /**
     *
     * @param  array $conditions format:
[
    'groupBy'=>(field), (from search field)
    'orderBy'=>['orderAlias'=>, 'direction'=>(desc/asc)], (from ui table)
    'searchBy'=>['key'=>'value']
    'limitBy'=>['sizePerPage'=>,'currentPage'=>(1 is first)]
    'options'=>['useAssocRows'=>(bool)]
]
     *
     * @param  string $type
     * @param  object $db
     * @return result
     */
    public function querySummary2Report(array $conditions, $type, $db=null){
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
        $columns=$this->columnsForSummary2Report($conditions);
        $rows=null;
        //condition
        if($type==self::QUERY_REPORT_TYPE_ONE_PAGE){
            //select sql
            $this->buildSelectOnDBFromColumns($columns, $db);
            //main table
            $db->from('summary2_report_daily');
            //join
            $this->buildJoinForSummary2Report($conditions, $db);
            //group by
            $this->buildGroupByForSummary2Report($conditions, $db);
            //order by
            $this->buildOrderByForSummary2Report($conditions, $columns, $db);
            //limit
            $this->buildCommonLimitOnDB($conditions, $db);
            //where
            $this->buildWhereForSummary2Report($conditions, $db);
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
            $settings=$this->getSuperReportSettings(self::SUPER_REPORT_TYPE_SUMMARY2);

            return ['header'=>$header, 'rows'=>$rows, 'settings'=>$settings, 'sql'=>$sql];

        }else if($type==self::QUERY_REPORT_TYPE_COUNT){
            $colForCount=$this->buildCountOnDBFromColumns($columns, $db);
            $db->from('summary2_report_daily');
            //join
            $this->buildJoinForSummary2Report($conditions, $db);
            //group by
            $this->buildGroupByForSummary2Report($conditions, $db);
            //where
            $this->buildWhereForSummary2Report($conditions, $db);
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
            $db->from('summary2_report_daily');
            //join
            $this->buildJoinForSummary2Report($conditions, $db);
            //where
            $this->buildWhereForSummary2Report($conditions, $db);
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
        }else if($type==self::QUERY_REPORT_TYPE_EXPORT){
            return $this->commonRemoteExportCSV($conditions, self::SUPER_REPORT_TYPE_SUMMARY2,
                'multiple_db_model', 'exportSummary2ReportOnCommand', $db);
        }

        return null;
    }

    /**
     * export csv file , will be called by command
     * @param  ExportEvent $event
     * @param  object      $db
     * @return array
     */
    public function exportSummary2ReportOnCommand(ExportEvent $event, $db=null){
        if(empty($db)){
            $db=$this->db;
        }
        $conditions=$event->getConditions();
        $debug_report_mode=$this->utils->getConfig('debug_report_mode');
        // $header=null;
        //define columns, formatter
        $columns=$this->columnsForSummary2Report($conditions);
        $headerNameList=$this->buildNameListFromColumns($columns);
        $rows=null;
        //condition
        //select sql
        $this->buildSelectOnDBFromColumns($columns, $db);
        //main table
        $db->from('summary2_report_daily');
        //join
        $this->buildJoinForSummary2Report($conditions, $db);
        //group by
        $this->buildGroupByForSummary2Report($conditions, $db);
        //where
        $this->buildWhereForSummary2Report($conditions, $db);
        //no order and limit on sql
        //header
        // $header=$this->buildHeaderByColumns($columns);
        // $colMap=$this->convertColumnsToMap($columns);

        // $this->utils->debug_log('colMap', $colMap);

        $csv_filename=$this->utils->create_csv_filename($event->getReportName());
        $csv_filepath=$this->utils->getRemoteReportPath().'/' . $csv_filename . '.csv';
        $csv_download_link=$this->utils->getRemoteReportDownloadPath().'/' . $csv_filename . '.csv';

        $sql=null;
        $exporting=true;
        $cacheOnMysql=true;
        $readonly=true;
        $token=$event->getToken();
        //run
        $success=$this->runRawSelectAndExportToCSV($db, $columns, $headerNameList, $csv_filepath,
            function($columns, &$row, $headerOfDB) use($exporting){
            // $this->utils->debug_log('row', $row, $fields);
            // $assocRow=array_combine($fields, $row);
            //try format
            $success=$this->formatColumns($row, $columns, $headerOfDB, $exporting);
            return $success;
            // unset($assocRow);
        }, $readonly, $cacheOnMysql, $sql, $token);

        if(!$debug_report_mode){
            $sql=null;
        }

        return ['success'=>$success, 'csv_filepath'=>$csv_filepath, 'csv_download_link'=>$csv_download_link, 'sql'=>$sql];
    }

    public function columnsForSummary2Report($conditions){

        $searchBy=$conditions['searchBy'];
        $monthOnly=isset($searchBy['monthOnly']) ? $searchBy['monthOnly'] : false;

        $i=0;
        $columns = [
            [
                'alias'=>'id',
                'select'=>'id',
                'key_for_count'=>true,
            ],
            [
                'dt' => $i++,
                'alias' => 'summary_date',
                'name' => lang('Date'),
                'fixed' => true,
                'sortable' => true,
                'select' => $monthOnly ? 'DATE_FORMAT(summary_date, "%Y%m")' : 'summary_date',
                'formatter' => 'dateFormatter',
                'minWidth'=>100,
            ],
            [
                'dt' => $i++,
                'alias' => 'currency',
                'name' => lang('Currency'),
                'fixed' => false,
                'sortable' => true,
                'select' => 'currency_key',
                'minWidth'=>70,
            ],
            [
                'dt' => $i++,
                'alias' => 'total_bet',
                'name' => lang('Betting'),
                'select' => $monthOnly ? 'sum(total_bet)' : 'total_bet',
                'formatter' => 'currencyFormatter',
                'minWidth'=>90,
                'select_sum'=>'sum(total_bet)',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_win',
                'name' => lang('Win'),
                'select' => $monthOnly ? 'sum(total_win)' : 'total_win',
                'formatter' => 'currencyFormatter',
                'minWidth'=>90,
                'select_sum'=>'sum(total_win)',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_loss',
                'name' => lang('Loss'),
                'select' => $monthOnly ? 'sum(total_loss)' : 'total_loss',
                'formatter' => 'currencyFormatter',
                'minWidth'=>90,
                'select_sum'=>'sum(total_loss)',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_payout',
                'name' => lang('Payout'),
                'select' => $monthOnly ? 'sum(total_payout)' : 'total_payout',
                'formatter' => 'currencyFormatter',
                'minWidth'=>96,
                'select_sum'=>'sum(total_payout)',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_deposit',
                'name' => lang('Deposit'),
                'select' => $monthOnly ? 'sum(total_deposit)' : 'total_deposit',
                'formatter' => 'currencyFormatter',
                'minWidth'=>100,
                'select_sum'=>'sum(total_deposit)',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_withdrawal',
                'name' => lang('Withdrawal'),
                'select' => $monthOnly ? 'sum(total_withdrawal)' : 'total_withdrawal',
                'formatter' => 'currencyFormatter',
                'minWidth'=>120,
                'select_sum'=>'sum(total_withdrawal)',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_bonus',
                'name' => lang('Bonus'),
                'select' => $monthOnly ? 'sum(total_bonus)' : 'total_bonus',
                'formatter' => 'currencyFormatter',
                'minWidth'=>90,
                'select_sum'=>'sum(total_bonus)',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_cashback',
                'name' => lang('Cashback'),
                'select' => $monthOnly ? 'sum(total_cashback)' : 'total_cashback',
                'formatter' => 'currencyFormatter',
                'minWidth'=>120,
                'select_sum'=>'sum(total_cashback)',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_fee',
                'name' => lang('Fee'),
                'select' => $monthOnly ? 'sum(total_fee)' : 'total_fee',
                'formatter' => 'currencyFormatter',
                'minWidth'=>90,
                'select_sum'=>'sum(total_fee)',
            ],
            [
                'dt' => $i++,
                'alias' => 'total_bank_cash_amount',
                'name' => lang('Bank Cash'),
                'select' => $monthOnly ? 'sum(total_bank_cash_amount)' : 'total_bank_cash_amount',
                'formatter' => 'currencyFormatter',
                'minWidth'=>110,
                'select_sum'=>'sum(total_bank_cash_amount)',
            ],
            [
                'dt' => $i++,
                'alias' => 'count_all_players',
                'name' => lang('All Players'),
                'select' => $monthOnly ? 'sum(count_all_players)' : 'count_all_players',
                'formatter' => 'currencyFormatter',
                'minWidth'=>120,
                'select_sum'=>'sum(count_all_players)',
            ],
            [
                'dt' => $i++,
                'alias' => 'count_new_player',
                'name' => lang('New Player'),
                'select' => $monthOnly ? 'sum(count_new_player)' : 'count_new_player',
                'formatter' => 'currencyFormatter',
                'minWidth'=>120,
                'select_sum'=>'sum(count_new_player)',
            ],
            [
                'dt' => $i++,
                'alias' => 'count_first_deposit',
                'name' => lang('First Deposit'),
                'select' => $monthOnly ? 'sum(count_first_deposit)' : 'count_first_deposit',
                'formatter' => 'currencyFormatter',
                'minWidth'=>130,
                'select_sum'=>'sum(count_first_deposit)',
            ],
            [
                'dt' => $i++,
                'alias' => 'count_second_deposit',
                'name' => lang('Second Deposit'),
                'select' => $monthOnly ? 'sum(count_second_deposit)' : 'count_second_deposit',
                'formatter' => 'currencyFormatter',
                'minWidth'=>140,
                'select_sum'=>'sum(count_second_deposit)',
            ],
        ];

        return $columns;
    }
    public function buildJoinForSummary2Report($conditions, $db){
        //nothing
        // $join=[];
        // $this->buildJoinOnDB($join, $db);
    }
    public function buildGroupByForSummary2Report($conditions, $db){
        $searchBy=$conditions['searchBy'];
        $monthOnly=isset($searchBy['monthOnly']) ? $searchBy['monthOnly'] : false;

        $groupList=[];
        if($monthOnly){
            $groupList[]='DATE_FORMAT(summary_date, "%Y%m")';
        }
        $this->buildGroupByOnDB($groupList, $db);
    }
    public function buildOrderByForSummary2Report($conditions, $columns, $db){
        $searchBy=$conditions['searchBy'];
        $monthOnly=isset($searchBy['monthOnly']) ? $searchBy['monthOnly'] : false;

        $orderList=[];
        if(isset($conditions['orderBy'])){
            $orderBy=$conditions['orderBy'];
            $orderList=$this->generateOrderByDefine($orderBy, $columns);
            if(!empty($orderList)){
                $orderList[]=['field'=>'id', 'direction'=>$orderBy['direction']];
            }
        }
        //if nothing
        if(empty($orderList)){
            if($monthOnly){
                $orderList[]=['field'=>'DATE_FORMAT(summary_date, "%Y%m")', 'direction'=>'desc'];
            }else{
                $orderList[]=['field'=>'summary_date', 'direction'=>'desc'];
            }
            $orderList[]=['field'=>'id', 'direction'=>'desc'];
        }
        $this->buildOrderByOnDB($orderList, $db);
    }
    public function buildWhereForSummary2Report($conditions, $db){
        $searchBy=$conditions['searchBy'];

        $whereList=[];
        $whereList[]=['key'=>'summary_date >=', 'value'=>$searchBy['dateFrom']];
        $whereList[]=['key'=>'summary_date <=', 'value'=>$searchBy['dateTo']];

        $this->buildSimpleWhereOnDB($whereList, $db);

    }
    //===summary2 report==============================================================================

}
