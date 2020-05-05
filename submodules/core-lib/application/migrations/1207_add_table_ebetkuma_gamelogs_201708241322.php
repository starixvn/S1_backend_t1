<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ebetkuma_gamelogs_201708241322 extends CI_Migration {

	private $tableName = 'ebetkuma_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'billNo' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gameId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betValue' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'netAmount' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'settleTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'agentsCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'account' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'ticketStatus' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'thirdParty' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'tag' => array(
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
