<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ebetimpt_game_logs_201707311353 extends CI_Migration {

	private $tableName = 'ebetimpt_game_logs';

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
			'UserName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'betId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'thirdParty' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'tag' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'playerName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'fullName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'vipLevel' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'country' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameCode1' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameType' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameDate' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'startAmount' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'win' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'endAmount' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
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
