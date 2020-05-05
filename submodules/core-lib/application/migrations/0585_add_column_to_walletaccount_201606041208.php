<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_walletaccount_201606041208 extends CI_Migration {

	private $tableName = 'walletaccount';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'playerId' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

		$this->load->model(array('player_model'));
		$this->player_model->updateWalletAccountPlayerId();
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'playerId');
	}
}