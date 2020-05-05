<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_withdraw_conditions_201607251637 extends CI_Migration {

	private $tableName = 'withdraw_conditions';

	public function up() {
		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// $this->roles->initFunction('enabled_move_all_to_real', 'Enabled Move All Wallet To Real', 163, 59, true);

		// $succ = $this->roles->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migrate failed');
		// }

        //0 = main wallet
		$fields = array(
			'wallet_type' => array(
				'type' => 'INT',
				'null' => true,
                'default' => 0,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'wallet_type');

		// $this->load->model(array('roles'));
		// $this->roles->deleteFunction(163);
	}
}
