<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_more_reports_to_functions_201607102215 extends CI_Migration {

	// const PARENT_ID = 40;

	# This function defines whether one can manage the orders with wait_API status

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('report_transactions', 'Transaction List', 155, self::PARENT_ID, true);
		// $this->roles->initFunction('report_transfer_request', 'Transfer List', 156, self::PARENT_ID, true);
		// $this->roles->initFunction('report_gamelogs', 'Game Log List', 157, self::PARENT_ID, true);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }
	}

	public function down() {
		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(155);
		// $this->roles->deleteFunction(156);
		// $this->roles->deleteFunction(157);
	}

}