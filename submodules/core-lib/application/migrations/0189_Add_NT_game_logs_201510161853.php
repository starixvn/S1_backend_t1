<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_NT_game_logs_201510161853 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'external_uniqueid' => array(
				'type' => 'INT',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'log_info_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'time' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'after_balance' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'betting_amount' => array(
				'type' => 'INT',
				'null' => true,
			),
			'lines' => array(
				'type' => 'INT',
				'null' => true,
			),
			'multiplier' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'win_amount' => array(
				'type' => 'INT',
				'null' => true,
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'symbol' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('nt_game_logs');
	}

	public function down() {
		$this->dbforge->drop_table('nt_game_logs');
	}
}
