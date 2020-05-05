<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_sagaming_gamelogs_201705191530 extends CI_Migration {

	private $tableName = 'sagaming_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'PlayerId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'BetTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'PayoutTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Username' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'HostID' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'GameID' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Round' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Set' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'BetID' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'BetAmount' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'ResultAmount' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Balance' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'GameType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'BetType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'BetSource' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'State' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'Detail' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			)
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
