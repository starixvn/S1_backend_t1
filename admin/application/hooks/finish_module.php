<?php

class Finish_module {

	private $CI;

	public function __construct() {
		$this->CI = &get_instance();
		// $this->CI->load->library(['authentication']);
	}

	public function index() {
		if($this->CI->utils->getConfig('close_db_on_each_request')){
			//close db
			if(isset($this->CI->db) && !is_string($this->CI->db)){
				$rlt=$this->CI->db->close();
				$this->CI->utils->debug_log('close db on admin', $rlt);
			}
			//close mdb
		}

		$this->CI->utils->closeAllConn();

		$this->CI->utils->printSQLToJson();

	}

}