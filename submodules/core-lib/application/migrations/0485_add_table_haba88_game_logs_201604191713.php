<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_haba88_game_logs_201604191713 extends CI_Migration {

	private $tableName = 'haba88_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => '10',
				'auto_increment' => TRUE,
				'null' => false
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false
			),
			'brandgameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false
			),
			'gamename' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false
			),
			'gamekeyname' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'gameinstanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'friendlygameinstanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'stake' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'payout' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'jackpotwin' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'jackpotcontribution' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'dtstart' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'dtcompleted' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'gamestatename' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'gamestateid' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => true
			),
			'gametypeid' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => true
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