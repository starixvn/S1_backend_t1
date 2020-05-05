<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_total_cashback_player_game_daily_201610291732 extends CI_Migration {

	private $tableName = "total_cashback_player_game_daily";

	public function up() {

		//add new permission
		$this->load->model(array('roles'));

		// $this->roles->startTrans();
		// //last function is Export Report Transactions
		// //59 is marketing management
		// $this->roles->initFunction('manually_calculate_cashback', 'Manually Calculate Cashback', 181, 59, true);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }

		$fields = array(
			'max_bonus' => array(
				'type' => 'DOUBLE',
				'null' => TRUE,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->load->model(array('roles'));

		// $this->roles->startTrans();
		// $this->roles->deleteFunction(181);
		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }

		$this->dbforge->drop_column($this->tableName, 'max_bonus');

	}
}
