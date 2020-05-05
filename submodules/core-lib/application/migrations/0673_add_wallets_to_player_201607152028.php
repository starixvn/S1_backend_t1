<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_wallets_to_player_201607152028 extends CI_Migration {

	public function up() {

		$fields = array(
			'big_wallet' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'total_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'main_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);

		$this->dbforge->add_column('player', $fields);

		$fields = array(
			'big_wallet' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('balance_history', $fields);

		// $this->load->model(['wallet_model']);
		// $this->wallet_model->batchImportToBigWallet();

	}

	public function down() {
		$this->dbforge->drop_column('player', 'big_wallet');
		$this->dbforge->drop_column('player', 'total_balance');
		$this->dbforge->drop_column('player', 'main_balance');

		$this->dbforge->drop_column('balance_history', 'big_wallet');
	}
}