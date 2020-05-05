<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_columns_to_bbin_game_logs_201602102159 extends CI_Migration {

	private $tableName = 'bbin_game_logs';

	public function up() {
		$this->load->model(array('bbin_game_logs'));

		$this->dbforge->modify_column($this->tableName, array(
			'wagers_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'payoff' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'commisionable' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		));

		$this->dbforge->add_column($this->tableName, array(
			'origin' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'flag' => array(
				'type' => 'INT',
				'null' => false,
				'default' => Bbin_game_logs::FLAG_FINISHED,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'origin');
		$this->dbforge->drop_column($this->tableName, 'flag');
	}
}

///END OF FILE//////////