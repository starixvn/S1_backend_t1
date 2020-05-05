<?php

class Data_tables {

	private $db = null;
	private $ci=null;

	function __construct($config = array()) {
		$this->ci = &get_instance();
		if (isset($config['DB'])) {
			$this->db = $config['DB'];
		} else {
			$this->db = $this->ci->db;
		}
		$this->utils=$this->ci->utils;

	}
	/*
		data = array(
			'display' 	=> display data,
			'sort' 		=> data used for ordering,
			'filter' 	=> data used for searching,
			'type' 		=> type of detection data,
			date, num, num-fmt,html-num,html-num-fmt,html,string
		)
	*/

	public $is_export = false;
	private $export_token;
	private $process_id;
	private $is_remote_export;

    /**
     * @var array is_export, only_sql, callback_row, use_SQL_CALC_FOUND_ROWS, countOnlyField
     */
	public $options= [];

	private $request;
	private $columns;
	private $table;
	private $form_search;
	private $values;
	private $joins;
	private $group_by;
	private $having;
	private $distinct;
	private $external_order;
    private $innerJoins;
//	private $not_datatable;
//	private $use_SQL_CALC_FOUND_ROWS=true;
//	private $countOnlyField;

	public $last_query; // for trace sql or used again.
	/**
	 * Store data in options attr. and other attributes.
	 *
	 * @param array $request
	 * @param array $columns The array the column from the rows data object, ref. to https://datatables.net/reference/option/columns.data .
	 * @param string $table The main table for query DB.
	 * @param array $form_search Someone set $where for more readable, use AND to connect each condition(elemant of array).
	 * @param array $values For $form_search, with $this->db->compile_binds() in $this->filter().
	 * @param array $joins The tables for element of array, use "left join" connection.<br/>
	 * If need inner join, pls use param, $innerJoins.<br/>
	 * The format example,
	 * - $joins[tableName1]='OnCondition1'
	 * - $joins[tableName2]='OnCondition2'
	 *
	 * @param array $group_by The field name for element of array.
	 * @param array $having The conditional sentence for element of array. Ref. to https://codeigniter.org.tw/user_guide/database/active_record.html , keyword:"$this->db->having();".
	 * The format example for  "HAVING title = 'My Title', id < 45" in query,
	 * - $having['title = ']='My Title'
	 * - $having['id <']='45'
	 * - $having['MIN(createdAt) = `createdAt`'] = '' // The field eq. field.
	 * @param boolean $distinct The "DISTINCT" in SELECT of query.
	 * @param array $external_order The field for external into order.
	 * @param string $not_datatable
	 * @param string $countOnlyField
	 * @param array $innerJoins  The tables for element of array, use "inner join".
	 * @param array $useIndex
	 */
	public function set_data( $request // #1
							, $columns // #2
							, $table // #3
							, $form_search = array() // #4
							, $values = array() // #5
							, $joins = array() // #6
							, $group_by = array() // #7
							, $having = array() // #8
							, $distinct = true // #9
							, $external_order=[] // #10
							, $not_datatable = '' // #11
							, $countOnlyField='' // #12
							, $innerJoins=[] // #13
							, $useIndex=[] // #14
	) {
		$this->request = $request;
		$this->columns = $columns;
		$this->table = $table;
		$this->form_search = $form_search;
		$this->values = $values;
		$this->joins = $joins;
		$this->group_by = $group_by;
		$this->having = $having;
		$this->distinct = $distinct;
		$this->external_order = $external_order;
        $this->innerJoins=$innerJoins;
        $this->useIndex=$useIndex;
//		$this->not_datatable = $not_datatable;
//		$this->use_SQL_CALC_FOUND_ROWS=empty($countOnlyField);
//		$this->countOnlyField=$countOnlyField;

		if(isset($this->options['is_export'])){
			$this->export_token = isset($request['extra_search']['export_token']) ? $request['extra_search']['export_token'] : null;
			$this->is_remote_export = isset($request['extra_search']['is_remote_export']) ? true : false;
			$this->process_id = getmypid();
		}

		//merge options
        $this->options['use_SQL_CALC_FOUND_ROWS']=empty($countOnlyField);
        $this->options['countOnlyField']=$countOnlyField;
        if(!isset($this->options['is_export'])){
            $this->options['is_export']=$this->is_export;
        }
        $this->options['not_datatable']=$not_datatable;

        //search options
        $check_items=['only_sql', 'csv_filename', 'excel_filename'];

        foreach ($check_items as $check_item) {

            if(!isset($this->options[$check_item])){
                $this->options[$check_item]=null;
            }

        }

        if(!isset($this->options['is_readonly'])){
            $this->options['is_readonly']=$this->ci->utils->getConfig('enable_readonly_db');
        }


	}// EOF set_data()

	/**
	 * return datatable empty data format
	 *
	 * @author Elvis_Chen<elvis.chen@tripleonetech.net>
	 * @since 1.0.0 Elvis_Chen: Initial function
	 *
	 * @param array $request
	 * @return void
	 */
	public function empty_data($request = null){
		$request = (NULL === $request) ? $this->input->post() : $request;

		$result = [
			'draw' => ((isset($request['draw'])) ? ++$request['draw'] : 1),
			'recordsFiltered' => 0,
			'recordsTotal' => 0,
			'data' => [],
			'header_data' => [],
		];

		return $result;
	}

    public function get_data($request, $columns, $table, $form_search = array(), $values = array(), $joins = array(), $group_by = array(), $having = array(),
	    	$distinct = true, $external_order=[], $not_datatable = '', $countOnlyField='', $innerJoins=[], $useIndex=[]) {

		$this->set_data($request, $columns, $table, $form_search, $values, $joins, $group_by, $having, $distinct, $external_order, $not_datatable, $countOnlyField, $innerJoins, $useIndex);

		foreach ($this->columns as &$column) {
			if (!isset($column['alias'])) {
				$column['alias'] = $column['select'];
			}
		}

		if ($this->distinct) {
			$this->db->distinct();
		}

		$this->select($this->columns);
		$this->db->from($this->table);
		$this->join($this->joins);
		$this->filter(	$this->request
						, $this->columns
						, array_filter($this->form_search // filter empty string element.
							, array($this, 'array_filter4form_search')  // $this->array_filter4form_search
							, ARRAY_FILTER_USE_BOTH )
						, $this->values
					);
        $this->db->_protect_identifiers = FALSE;
		$this->group_by($this->group_by);
        $this->db->_protect_identifiers = TRUE;
		$this->having($this->having);
		$this->order($this->request, $this->columns);
		$this->limit($this->request);

		if( ! empty( $this->options['not_datatable'] ) )
		{
			//please use external_order
			//marked by spencer.kuo 2017.05.02
			//$this->db->order_by("created_at","desc");
			//add by spencer.kuo 2017.05.02
			//only for game_logs
			if ($table == 'game_logs') {
				$this->db->order_by("end_at", "desc");
			} else if($table=='transactions'){
				$this->db->order_by("created_at","desc");
			}
		}

		//always create sql
		 $sql=$this->db->_compile_select();
        //$this->utils->debug_log('the sql ------>', $sql);

		if ($this->ci->config->item('debug_data_table_sql')) {
			$this->last_query = $sql;
			$this->ci->utils->debug_log($sql);
		}

        $this->db->_reset_select();
        if($this->options['only_sql']){
            //only return sql
            $rltData= array(
	            "draw" => (isset($this->request['draw'])) ? $this->request['draw']: 0,
                "recordsFiltered" => null,
                "recordsTotal" => null,
                "data" => $sql,
                "header_data" => $this->get_columns($this->columns),
            );

            return $rltData;
        }

        //run sql to fetch rows

//		$data_query = $this->db->get();

        $csv_filename=$this->options['csv_filename'];
		$is_export_csv=$this->options['is_export'] && !empty($csv_filename);

//		if($this->options['is_export'] && !empty($this->options['csv_filepath'])){

            //run sql and create csv file
//            $this->ci->utils->create_csv($sql, $filename);
            //and return filename
//            return $filename;
//        }

        $fp =null;
        if($is_export_csv){
        	$csv_filepath = realpath(dirname(__FILE__) . '/../../public/reports').'/' . $csv_filename . '.csv';
            //open csv file
        	$fp = fopen($csv_filepath, 'w');
        	if ($fp) {
        		$BOM = "\xEF\xBB\xBF";
                fwrite($fp, $BOM); // NEW LINE
            } else {
                //create report failed
            	$this->ci->utils->error_log('create csv file failed', $csv_filepath);
            	return $this->empty_data($request);
            }
        }

        $data=[];

        $conn=null;
        $charset=null;

		$_multiple_db=Multiple_db::getSingletonInstance();
		$conn=$_multiple_db->rawConnectDB($this->options['is_readonly']);
        // if($this->options['is_readonly']){
        //     $conn=mysqli_connect($this->ci->utils->getConfig('db.readonly.hostname'),
        //         $this->ci->utils->getConfig('db.readonly.username'),
        //         $this->ci->utils->getConfig('db.readonly.password'),
        //         $this->ci->utils->getConfig('db.readonly.database'),
        //         $this->ci->utils->getConfig('db.readonly.port'));
        //     $charset=$this->ci->utils->getConfig('db.readonly.char_set');
        // }else{
        //     $conn=mysqli_connect($this->ci->utils->getConfig('db.default.hostname'),
        //         $this->ci->utils->getConfig('db.default.username'),
        //         $this->ci->utils->getConfig('db.default.password'),
        //         $this->ci->utils->getConfig('db.default.database'),
        //         $this->ci->utils->getConfig('db.default.port'));
        //     $charset=$this->ci->utils->getConfig('db.default.char_set');
        // }

        $totalRecords=0;

        if($conn){
            try {
            	$sql_time=time();
            	//update charset code first
            	// mysqli_set_charset($conn, $charset);

           		// $this->ci->utils->debug_log('try get sql', $sql);

                //get sql then run, large mode
                if ($is_export_csv) {
                	$this->ci->load->model(['queue_result']);
                    $qry = mysqli_query($conn, $sql, MYSQLI_USE_RESULT);
                    //write header

                    $header_data=$this->get_columns($this->columns);
					fputcsv($fp, $header_data, ',', '"');

                } else {
                    $qry = mysqli_query($conn, $sql, MYSQLI_STORE_RESULT);
                }
                $sql_time=time()-$sql_time;

                if(!isset($this->ci->report_sql)){
                	$this->ci->report_sql=[];
                }
                $this->ci->report_sql[''.$sql_time]=$sql;

                if ($qry) {
                	$count_loop=1;
                	$processId = null;
                	$state=null;
                	if ($is_export_csv) {
                		$state = array('processId'=>$this->process_id);
                		$this->ci->queue_result->updateResultRunning($this->export_token, [], $state);
                		$this->ci->utils->debug_log('dt export_token', $this->export_token);
                	}
                	if ($this->ci->config->item('dt_use_fetch_all_on_csv_export')) {
                		$rows = mysqli_fetch_all($qry,MYSQLI_ASSOC);
                		$totalCount =  count($rows);
                		$percentage_steps = [];
                        for ($i=.01; $i <= 10 ; $i +=.01) {
                        	array_push($percentage_steps, ceil($i/10 * $totalCount));
                        }
                        if(!empty($rows)){
                        	foreach ($rows as $row) {
                        		if ($is_export_csv) {
                        			$start_process_one_row_time_micro = microtime(true);
                        			$processed_row = $this->process_row($row);
                        			$end_process_one_row_time_micro = microtime(true);
                        			$time_elapsed_secs = $end_process_one_row_time_micro - $start_process_one_row_time_micro;
                        			$this->ci->utils->info_log('fetch all cost time process per row in  microtime', $time_elapsed_secs. " second at loopcount: ", $count_loop, "total rows: ",$totalCount);
                        			fputcsv($fp, $processed_row , ',', '"');
                        			$this->ci->utils->debug_log('export token', $this->export_token, $totalCount);

                        			if(!empty($this->export_token)){
                                        if(in_array($count_loop, $percentage_steps)){
                                            $rlt=['success'=>false, 'is_export'=>true, 'processMsg'=> lang('Writing').'...', 'written' => $count_loop, 'total_count' => $totalCount, 'progress' => ceil($count_loop/$totalCount * 100)];
                                            $this->ci->queue_result->updateResultRunning($this->export_token, $rlt, $state);
                                        }
                                        if(($count_loop) == $totalCount){
                                            $rlt=['success'=>true, 'is_export'=>true, 'filename'=>$csv_filename.'.csv','processMsg'=> lang('Done'),'written' => $count_loop, 'total_count' => $totalCount, 'progress' => 100];
                                            $this->ci->queue_result->updateResult($this->export_token, $rlt);
                                        }

                                    }
                        		}else{
                        			$data[] = $this->process_row($row);
                        		}
                                unset($row);
                        		$count_loop++;
                        	}
                        }else{//empty rows
                        	$rlt=['success'=>true, 'is_export'=>true, 'filename'=>$csv_filename.'.csv','written' => 0, 'total_count' => $totalCount, 'progress' => 100];
                        	$this->ci->queue_result->updateResult($this->export_token, $rlt);
                        }

                	}else{
                		//use previous way
                		while ($row = mysqli_fetch_array($qry, MYSQLI_ASSOC)) {
//                        $this->ci->utils->debug_log('row', $row);
                           //write csv or add it to array
                			if ($is_export_csv) {
                				$start_process_one_row_time_micro = microtime(true);
                				$processed_row=$this->process_row($row);
                				$end_process_one_row_time_micro = microtime(true);
                				$time_elapsed_secs = $end_process_one_row_time_micro - $start_process_one_row_time_micro;
                				$this->ci->utils->debug_log('fetch array cost time process per row in  microtime', $time_elapsed_secs. " second at loopcount: ", $count_loop);
                				fputcsv($fp, $processed_row , ',', '"');
                			} else {
                				$data[] = $this->process_row($row);
                			}

                			unset($row);
                			$count_loop++;
                		}
                	} // EOF if ($this->ci->config->item('dt_use_fetch_all_on_csv_export'))

                	mysqli_free_result($qry);

                	if($this->options['use_SQL_CALC_FOUND_ROWS'] ) {
                		$qry=mysqli_query($conn, 'SELECT FOUND_ROWS() recordsFiltered', MYSQLI_STORE_RESULT);
                		if($qry){
                			$row = mysqli_fetch_array($qry, MYSQLI_ASSOC);
							$totalRecords = $row['recordsFiltered'];
                			mysqli_free_result($qry);
                		}
                	}

                } else {

                    $this->ci->utils->error_log('query db error, ' . mysqli_errno($conn) . ':' . mysqli_error($conn), $sql);
                    return $this->empty_data($request);

                }

            }finally{
                mysqli_close($conn);
            }

        }else{
            //error
            $this->ci->utils->error_log('connect db error, '.mysqli_connect_errno().':'.mysqli_connect_error(), $sql);
            return $this->empty_data($request);
        }

        if($is_export_csv) {

            fclose($fp);
            //close and return
            return $csv_filename;
        }

//        $data = $data_query->result_array();

		if( ! empty( $this->options['not_datatable'] ) ) return $data;

		if($this->options['use_SQL_CALC_FOUND_ROWS'] ){
//			$total_query = $this->db->query('SELECT FOUND_ROWS() recordsFiltered');
//			$total = $total_query->row_array();
//			$totalRecords=$total['recordsFiltered'];
		}else{
			//run count
			$this->db->select('count( '.($this->distinct ? ' DISTINCT ' : '').$this->options['countOnlyField'].' ) as cnt', false);
			$this->db->from($this->table);
			$this->join($this->joins);
			$this->filter($this->request
							, $this->columns
							, array_filter($this->form_search // filter empty string element.
								, array($this, 'array_filter4form_search')  // $this->array_filter4form_search
								, ARRAY_FILTER_USE_BOTH )
							, $this->values
						);
			$data_query = $this->db->get();
			if($data_query){
				$row = $data_query->row_array();
				$totalRecords=$row['cnt'];
			}
		}

		// $this->ci->utils->debug_log('totalRecords:'. $totalRecords);

		$draw = isset($this->request['draw']) ? $this->request['draw'] : $totalRecords;

		return array(
			"draw" => $draw,
			"recordsFiltered" => $totalRecords,
			"recordsTotal" => $totalRecords,
			"data" => $data,
			"header_data" => $this->get_columns($this->columns),
		);

	}

	public function get_column($column) {
		$this->select($this->columns);
		$this->db->from($this->table);
		$this->join($this->joins);
		$this->filter($this->request
						, $this->columns
						, array_filter($this->form_search // filter empty string element.
							, array($this, 'array_filter4form_search')  // $this->array_filter4form_search
							, ARRAY_FILTER_USE_BOTH )
						, $this->values);
		$this->group_by($this->group_by);
		$this->having($this->having);
		$query = $this->db->get();
		$rows = $query->result_array();
		return array_column($rows, $column);
	}

	public function get_columns($columns) {
		$col_data = array();
		foreach ($columns as $column) {
			if (isset($column['dt']) && $column['dt'] !== null) {
				if (isset($column['name'])) {
					$col_data[] = @$column['name'];
				} else {
					$col_data[] = '';
				}
			}
		}

		return $col_data;
	}

	public function apply_request_columns($columns, $request){
	    foreach($columns as $key => $column){
	        if(!isset($column['dt']) || !isset($column['alias'])){
	            continue;
            }

            $is_display = FALSE;
            foreach($request['columns'] as $request_column){
                if(!isset($request_column['name'])){
                    continue;
                }

                if($column['alias'] === $request_column['name']){
                    $is_display = TRUE;
                }
            }

            if(!$is_display){
                unset($columns[$key]);
            }
        }
        $dt = 0;
        $columns = array_values($columns);
        foreach($columns as &$column){
            if(isset($column['dt'])){
                $column['dt'] = $dt++;
            }
        }

	    return $columns;
    }

	/**
	 * Ignore where for summary by filed, $select
	 */
	public function summary($request, $table, $joins, $select, $group_by, $columns, $form_search = array(), $values = array()) {
		$this->db->select($select, false);
		$this->db->from($table);
		$this->join($joins);
		$this->filter($request, $columns, $form_search, $values);
		$this->group_by($group_by);
		$query = $this->db->get();

		if ($this->ci->config->item('debug_data_table_sql')) {
			$this->last_query = $this->db->last_query();
			$this->ci->utils->debug_log($this->db->last_query());
		}

		if(!empty($query)){
			return $query->result_array();
		}

		return [];
	}

	public function get_simple_sum($request, $table, $joins, $columns, $sum_column, $form_search = array(), $values = array()) {
		$this->db->select_sum($sum_column);
		$this->db->from($table);
		$this->join($joins);
		$this->filter($request, $columns, $form_search, $values);
		return reset($this->db->get()->row_array());
	}

	# HELPER #############################################################################################################################################
	public function extra_search($request = array()) {
		$input = array();
		if (isset($request['extra_search'])) {
			$extra_search_list = $request['extra_search'];
			foreach ($extra_search_list as $extra_search) {
				if (isset($extra_search['name'], $extra_search['value'])) {
					$key = trim($extra_search['name']);
					$key = rtrim($extra_search['name'],'[]');
					$value = trim($extra_search['value']);
					if ($value !== '') {
						if (!isset($input[$key])) {
							$input[$key] = $value;
						} else {
							if (!is_array($input[$key])) {
								$input[$key] = (array) $input[$key];
							}
							$input[$key][] = $value;
						}
					}
				}
			}
		}
		return $input;
	}

	# CORE FUNCTIONS #############################################################################################################################################
	private function select($columns) {
		$i = 0;
		foreach ($columns as $column) {
			if (isset($column['alias'], $column['select'])) {
				$select = $this->options['use_SQL_CALC_FOUND_ROWS'] && $i++ == 0? 'SQL_CALC_FOUND_ROWS ' : '';
				$select .= $column['select'] . ' ' . $column['alias'];

				$this->db->select($select, false);
			}
		}
	}

    private function join($joins) {
        $joins = array_unique($joins);
        foreach ($joins as $key => $value) {
            $joinMode='left';
            if(!empty($this->innerJoins)){
                if(in_array($key, $this->innerJoins)){
                    $joinMode='innder';
                }
            }
            // $this->ci->utils->debug_log('join', $key, $joinMode, $value, $this->innerJoins);
            $this->db->join($key, $value, $joinMode);
        }
    }

	private function filter($request, $server_columns, $form_search, $values) {
		if ($search_string_1 = $this->_flatten($form_search)) {
			$search_string_1 = $this->db->compile_binds($search_string_1, $values);
			$this->db->where($search_string_1);
		}

		if (isset($request['search']) && isset($request['search']['value']) && $request['search']['value'] != '') {

			$data_list = $this->_pluck($server_columns, 'dt');
			$search_value = $this->db->escape_like_str($request['search']['value']);
			$search_list = array();

			foreach ($request['columns'] as $request_column) {
				if ($request_column['searchable'] == 'true') {
					$data = $request_column['data'];
					$index = array_search($data, $data_list);
					$column = $server_columns[$index];
					$column_name = $column['select'];
					$search_list[] = "{$column_name} LIKE '%{$search_value}%'";
				}
			}

			$search_string_2 = sprintf('(%s)', $this->_flatten($search_list, ' OR '));
			$this->db->where($search_string_2);
		}
	}

	private function group_by($group_by) {
		if ($group_by) {
			if (is_array($group_by)) {
				foreach ($group_by as $value) {
					$this->db->group_by($value);
				}
			} else {
				$this->db->group_by($group_by);
			}
		}
	}

	private function having($having) {
		if ($having) {
			if (is_array($having)) {
				foreach ($having as $key => $value) {
					$this->db->having($key, $value);
				}
			}
		}
	}
	private function limit($request) {
		if (isset($request['start']) && $request['length'] != -1) {
			$this->db->limit($request['length'], $request['start']);
		}
	}

	private function order($request, $server_columns) {
		if (isset($request['order']) && count($request['order'])) {

			$client_columns = $request['columns'];
			$order_list = $request['order'];
			if(!empty($this->external_order)){
				$order_list= array_merge($this->external_order, $order_list);
			}
			$data_list = $this->_pluck($server_columns, 'dt');

			foreach ($order_list as $order) {

				$client_index = $order['column'];
				$client_column = $client_columns[$client_index];

				if ($client_column['orderable'] == 'true') {
					$data = $client_column['data'];
					$server_index = array_search($data, $data_list);
					$server_column = $server_columns[$server_index];
					$this->db->order_by($server_column['alias'], ($order['dir'] === 'asc' ? 'ASC' : 'DESC'));
				}

			}
		}
	}

    public function process_row($row)
    {
        $item = [];
        foreach ($this->columns as $column) {

            if (isset($column['dt']) && $column['dt'] !== null) {
                $column_index = $column['dt'];

                $column_value = null;

                if (isset($column['alias'], $column['select'])) {
                    $column_name = $column['alias'];
                    $column_value = isset($row[$column_name]) ? $row[$column_name] : null;
                }

                if (isset($column['formatter'])) {

                    $formatter = $column['formatter'];

                    if (is_callable($formatter)) {
                        $item[$column_index] = $formatter($column_value, $row);
                    } else {
                        $item[$column_index] = $this->$formatter($column_value, $row);
                    }

                } else {
                    $item[$column_index] = $column_value;
                }

                if(isset($column['data'])){
                    $item[$column['data']] = $item[$column_index];
                }
            }
        }
        return $item;
    }

	# UTILITIES #############################################################################################################################################
	private function _pluck($list, $key) {
		$ret = array();
		foreach ($list as $item) {
			$ret[] = isset($item[$key]) ? $item[$key] : null;
		}
		return $ret;
	}

	private function _flatten($a, $join = ' AND ') {
		if (!$a) {
			return '';
		} else if (is_array($a)) {
			return implode($join, $a);
		} else {
			return $a;
		}
	}

	/**
	 * Clear more lankSpace after defined heredoc content.
	 *
	 * Convert multiple consecutive whitespace to one whitespace.
	 *
	 * @param string $heredocStr The content of defined heredoc.
	 * @return string $heredocStr The content after processing.
	 */
	public function clearBlankSpaceAfterHeredoc($heredocStr){
		$heredocStr = preg_replace('/\t+/', ' ', $heredocStr);
		$heredocStr = trim( $heredocStr);
		return $heredocStr;
	}// EOF clearBlankSpaceAfterHeredoc

	# FORMATTERS #############################################################################################################################################
	public function defaultFormatter($d) {
		if (isset($this->options['is_export']) && $this->options['is_export']) {
			return trim(trim($d), ',') ?: lang('lang.norecyet');
		} else {
			return trim(trim($d), ',') ?: '<i>' . lang('lang.norecyet') . '</i>';
		}
	}

	public function percentageFormatter($d) {
		return $this->ci->utils->formatCurrencyNoSym($d * 100) . '%';
	}

	public function currencyFormatter($d) {
		if ($this->options['is_export']) {
			return $this->ci->utils->formatCurrencyNoSym($d);
		} else {
			//return $d == 0 ? '<span class="text-muted">' . $this->ci->utils->formatCurrencyNoSym($d) . '</span>' : '<strong>' . $this->ci->utils->formatCurrencyNoSym($d) . '</strong>';
			return $d == 0 ?  $this->ci->utils->formatCurrencyNoSym($d)  :  $this->ci->utils->formatCurrencyNoSym($d) ;
		}
	}

	public function languageFormatter($d) {
		return $this->defaultFormatter(lang($d) ?: $d);
	}

	public function dateTimeFormatter($d) {
		if ($this->options['is_export']) {
			return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
		} else {
			return (!$d || strtotime($d) < 0) ? '<i>' . lang('lang.norecyet') . '</i>' : date('Y-m-d H:i:s', strtotime($d));
		}
	}

	public function dateFormatter($d) {
		if ($this->options['is_export']) {
			return (!$d || strtotime($d) < 0) ? lang('N/A') : date('Y-m-d', strtotime($d));
		} else {
			return (!$d || strtotime($d) < 0) ? '<i>' . lang('N/A') . '</i>' : date('Y-m-d', strtotime($d));
		}
	}

	/**
	 * for $this->form_search filter empty element
	 *
	 * @param string $v The conditions of WHERE , the following example,
	 * <code>
	 * $this->form_search[] = "transactions.to_type = ?";
	 * $this->form_search[] = "(CASE transactions.to_type WHEN ? THEN toUser.userId WHEN ? THEN toPlayer.playerId WHEN ? THEN toAffiliate.affiliateId ELSE NULL END) = ? ";
	 * $this->form_search[] = "toPlayer.deleted_at IS NULL";
	 * </code>
	 * @param integer $k The index of array
	 * @return boolean IF true keep it , else filter.
	 */
	public function array_filter4form_search($v, $k) {
		$isFilter = false;
		$willFilterValues = array();
		$willFilterValues[] = 'null';
		$willFilterValues[] = '';
		// $willFilterValues = 0; // NOT SURE, someone use "AND 0" for empty output.
		$v = (string)$v; /// convert to string type for null
		$v = trim($v);
		$v = strtolower($v);
		if ( in_array( $v, $willFilterValues, true ) ) {
			$isFilter = true;
		}

		return !$isFilter;
	}// EOF array_filter4form_search

	/**
	 * detail: function to prepare the correct array format for the datatables
	 *
	 * @param array $columns
	 * @param array $data
	 *
	 * @return array
	 */
	public function _prepareDataForLists( $columns, $data ){

		$result = array(
			"draw" => '',
			"recordsFiltered" => count($data),
			"recordsTotal" => count($data),
			"data" => $this->out($columns, $data),
			"header_data" => '',
		);

		return $result;

	}

    private function out($columns, $rows) {
        $data = array();
//		$this->CI = &get_instance();
        // $this->CI->utils->debug_log($columns, $rows);
        foreach ($rows as $row) {
//            $data[] =$this->process_row($row);
            $item = array();
            foreach ($columns as $column) {

                if (isset($column['dt']) && $column['dt'] !== null) {
                    $column_index = $column['dt'];

                    $column_value = null;

                    if (isset($column['alias'], $column['select'])) {
                        $column_name = $column['alias'];
                        $column_value = isset($row[$column_name]) ? $row[$column_name] : null;
                    }

                    if (isset($column['formatter'])) {

                        $formatter = $column['formatter'];

                        if (is_callable($formatter)) {
                            $item[$column_index] = $formatter($column_value, $row);
                        } else {
                            $item[$column_index] = $this->$formatter($column_value, $row);
                        }

                    } else {
                        $item[$column_index] = $column_value;
                    }
                }
            }
            $data[] = $item;
        }
        return $data;
    }

}