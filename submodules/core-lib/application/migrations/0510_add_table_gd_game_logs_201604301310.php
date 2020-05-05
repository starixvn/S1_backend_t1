<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_gd_game_logs_201604301310 extends CI_Migration {

	private $tableName = 'gd_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'no' => array(
				'type' => 'INT',
				'null' => true,
			),
			'user_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'balance_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'product_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_interface' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),

			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),

			'win_loss' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_result' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'start_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'end_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bet_arrays' => array(
				 'type' => 'VARCHAR',
				 'constraint' => '1000',
                 'null' => TRUE,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}