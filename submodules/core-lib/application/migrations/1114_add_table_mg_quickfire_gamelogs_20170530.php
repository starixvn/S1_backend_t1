<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_mg_quickfire_gamelogs_20170530 extends CI_Migration {

	private $tableName = 'mg_quickfire_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'system' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'timestamp' => array(
				'type' => 'TIMESTAMP',
				'null' => false,
			),
			'token' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'seq' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'playtype' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'gameid' => array(
				'type' => 'INT',
				'null' => false,
			),
			'gamereference' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'actionid' => array(
				'type' => 'INT',
				'null' => false,
			),
			'actiondesc' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'start' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'finish' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'offline' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'freegame' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'freegameofferinstanceid' => array(
				'type' => 'INT',
				'null' => true,
			),
			'freegamenumgamesplayed' => array(
				'type' => 'INT',
				'null' => true,
			),
			'freegamenumgamesremaining' => array(
				'type' => 'INT',
				'null' => true,
			),
			'clienttypeid' => array(
				'type' => 'INT',
				'null' => true,
			),
			'extinfo' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'game_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'gameshortcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
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
