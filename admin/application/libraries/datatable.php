<?php

class Datatable {

	
	public $ci 				= array();
	public $select 			= array();
	public $select_index 	= 0;
	public $from 			= '';
	public $join 			= array();
	public $where 			= array();
	public $group 	 		= array();
	public $having 			= array();
	public $limit 			= array();
	public $order 			= array();
	public $request 		= array();

	function __construct() {
		$this->ci = &get_instance();
		$this->request = $this->ci->input->post();
		##########################################################################################
		// if (isset($request['search']) && $request['search']['value'] != '') {

		// 	$data_list = $this->_pluck($server_columns, 'dt');
		// 	$search_value = $this->ci->db->escape_like_str($request['search']['value']);
		// 	$search_list = array();

		// 	foreach ($request['columns'] as $request_column) {
		// 		if ($request_column['searchable'] == 'true') {
		// 			$data = $request_column['data'];
		// 			$index = array_search($data, $data_list);
		// 			$column = $server_columns[$index];
		// 			$column_name = $column['select'];
		// 			$search_list[] = "{$column_name} LIKE '%{$search_value}%'";
		// 		}
		// 	}

		// 	$search_string_2 = sprintf('(%s)', $this->_flatten($search_list, ' OR '));
		// 	$this->ci->db->where($search_string_2);
		// }
		##########################################################################################
	}

	public function distinct() {

	}

	public function select($select, $visible = true, $alias = NULL) {
		$select = trim($select);
		if ($select !== '') {
			$this->select[] = array(
				'index'  => ($visible ? $this->select_index++ : NULL),
				'select' => $select,
				'alias'  => $alias,
			);
		}
	}

	public function from($table) {
		$this->from = $table;
	}

	public function join($table, $condition, $type = 'LEFT') {
		$this->join[] = array(
			'table' => $table,
			'condition' => $condition,
			'type' => $type,
		);
	}

	public function where($key, $value, $string = TRUE) {
		$this->where[] = array(
			'key' => $key,
			'value' => $string ? "`{$value}`" : $value,
		);
	}

	public function or_where($key, $value) {
		
	}

	public function or_where_in($key, $value) {
		
	}

	public function or_where_not_in($key, $value) {
		
	}

	public function where_in($key, $value) {
		
	}

	public function where_not_in($key, $value) {
		
	}

	public function like($key, $value) {
		
	}

	public function or_like($key, $value) {
		
	}

	public function not_like($key, $value) {
		
	}

	public function or_not_like($key, $value) {
		
	}

	public function group_by($key, $value) {
		
	}

	public function having($key, $value, $string = true) {
		$this->having[] = array(
			'key' => $key,
			'value' => $string ? "`{$value}`" : $value,
		);
	}

	public function order_by($key, $value) {
		
	}

	public function limit($key, $value) {
		
	}

	public function query() {
		$query = $this->ci->db->query(sprintf("SELECT DISTINCT SQL_CALC_FOUND_ROWS %s FROM %s %s %s %s %s %s %s", 
			$this->select_str(), 
			$this->from, 
			$this->join_str(), 
			$this->where_str(), 
			$this->group_str(), 
			$this->having_str(),
			$this->limit_str(),
			$this->order_str()
		));
		// data: []
		// draw: "1"
		// header_data: ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""]
		// 0: ""
		// 1: ""
		// 2: ""
		// 3: ""
		// 4: ""
		// 5: ""
		// 6: ""
		// 7: ""
		// 8: ""
		// 9: ""
		// 10: "" 
		// 11: ""
		// 12: ""
		// 13: ""
		// 14: ""
		// 15: ""
		// 16: ""
		// 17: ""
		// 18: ""
		// 19: ""
		// 20: ""
		// 21: ""
		// 22: ""
		// recordsFiltered: "0"
		// recordsTotal: "0"
		return $query->result();

		// return printf("SELECT DISTINCT SQL_CALC_FOUND_ROWS %s FROM %s %s WHERE %s GROUP BY %s HAVING %s ORDER BY %s %s LIMIT %s, %s");
		// $this->ci->db->distinct();
		// $this->select($columns);
		// $this->ci->db->from($table);
		// $this->join($joins);
		// $this->filter($request, $columns, $form_search, $values);
		// $this->group_by($group_by);
		// $this->having($having);
		// $this->limit($request);
		// $this->order($request, $columns);
	}

	private function select_str() {
		$select_str = '';
		foreach ($this->select as $i => $select) {
			if ($i) {
				$select_str .= ', ';
			} 

			$select_str .= $select['select'];

			if ($select['alias']) {
				$select_str .= $select['alias'];
			}
		}
		return $select_str;
	}

	private function join_str() {
		$join_str = '';
		foreach ($this->join as $value) {
			$join_str .= sprintf(' %s JOIN %s ON %s ', $value['type'], $value['table'], $value['condition']);
		}
		return $join_str;
	}

	private function where_str() {
		$where_str = '';
		foreach ($this->where as $i => $value) {

			if ($i) {
				$where_str = ' AND ';
			}

			$operator = '';
			if (!preg_match("/(\s|<|>|!|=|is null|is not null)/i", trim($value['key']))) {
				$operator = '=';
			} 

			$where_str .= sprintf(' %s %s %s ', $value['key'], $operator, $value['value']);
		}
		return $where_str ? " WHERE {$where_str}" : null;
	}

	private function group_str() {
		$group_str = '';
		foreach ($this->group as $value) {
			if ($i) {
				$group_str .= ', ';
			} 

			$group_str .= $value;
		}
		return $group_str ? " GROUP BY {$group_str}" : null;
	}

	private function having_str() {
		$having_str = '';
		foreach ($this->having as $i => $value) {

			if ($i) {
				$having_str = ' AND ';
			}

			$operator = '';
			if (!preg_match("/(\s|<|>|!|=|is null|is not null)/i", trim($value['key']))) {
				$operator = '=';
			} 

			$having_str .= sprintf(' %s %s %s ', $value['key'], $operator, $value['value']);
		}
		return $having_str ? " HAVING {$having_str}" : null;
	}

	private function limit_str() {
		if (isset($this->request['start']) && $this->request['length'] != -1) {
			sprintf(' LIMIT %s, %s', $this->request['length'], $this->request['start']);
		} return '';
	}

	private function order_str() {
		if (isset($this->request['order']) && count($this->request['order'])) {

			$client_columns = $this->request['columns'];
			$order_list = $this->request['order'];

			// $data_list = $this->_pluck($server_columns, 'dt');

			// foreach ($order_list as $order) {

			// 	$client_index = $order['column'];
			// 	$client_column = $client_columns[$client_index];

			// 	if ($client_column['orderable'] == 'true') {
			// 		$data = $client_column['data'];
			// 		$server_index = array_search($data, $data_list);
			// 		$server_column = $server_columns[$server_index];
			// 		$this->ci->db->order_by($server_column['alias'], ($order['dir'] === 'asc' ? 'ASC' : 'DESC'));
			// 	}

			// }
		} else return '';
	}

	// private function out($columns, $rows) {
	// 	$data = array();
	// 	foreach ($rows as $row) {
	// 		$item = array();
	// 		foreach ($columns as $column) {

	// 			if (isset($column['dt']) && $column['dt'] !== null) {

	// 				$column_index = $column['dt'];

	// 				if (isset($column['formatter'])) {

	// 					$formatter = $column['formatter'];
	// 					$column_value = null;

	// 					if (isset($column['alias'], $column['select'])) {
	// 						$column_name = $column['alias'];
	// 						$column_value = $row[$column_name];
	// 					}

	// 					if (is_callable($formatter)) {
	// 						$item[$column_index] = $formatter($column_value, $row);
	// 					} else {
	// 						$item[$column_index] = $this->$formatter($column_value, $row);
	// 					}

	// 				} else {
	// 					$item[$column_index] = $column_value;
	// 				}
	// 			}
	// 		}
	// 		$data[] = $item;
	// 	}
	// 	return $data;
	// }

	# UTILITIES #############################################################################################################################################
	private function _pluck($list, $key) {
		$ret = array();
		foreach ($list as $item) {
			$ret[] = isset($item[$key]) ? $item[$key] : null;
		}
		return $ret;
	}

}