<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_adjustment_history_reports_to_functions_201608220621 extends CI_Migration {

	// const PARENT_ID = 40;

	# This function defines whether one can manage the orders with wait_API status

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('report_adjustment_history', 'Adjustment History', 166, self::PARENT_ID, true);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(165);
	}

}