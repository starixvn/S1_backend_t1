<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_xlcod_game_logs_201604210932 extends CI_Migration {

	private $tableName = 'xlcod_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'user_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_content' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'odds' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'result_amount' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'timestamp' => array(
				'type' => 'DATETIME',
   				'null' => true,
			),
			'settle_flag' => array(
				'type' => 'VARCHAR',
				'constraint' => '2',
				'null' => true,
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