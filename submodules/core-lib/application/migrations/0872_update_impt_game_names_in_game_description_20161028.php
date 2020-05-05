<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// require_once dirname(__FILE__) . '/../models/game_description/game_description_impt.php';

class Migration_update_impt_game_names_in_game_description_20161028 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	// use game_description_impt;

	public function __construct() {
		// parent::__construct();

		// // $this->config->set_item('app_debug_log', APPPATH . 'logs/sync.log');

		// $this->config->set_item('print_log_to_console', true);

		// $default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		// set_time_limit($default_sync_game_logs_max_time_second);

	}

	public function up() {
		// $funcName='sync_game_description_impt';

		// $this->db->trans_start();

		// $cnt=$this->$funcName();

		// $this->db->trans_complete();

		// $this->utils->debug_log('update game '.$funcName, $cnt);

	}

	public function down() {
		//nothing
	}

}