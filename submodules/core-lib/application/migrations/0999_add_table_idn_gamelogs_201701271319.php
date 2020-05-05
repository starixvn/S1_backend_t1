<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_idn_gamelogs_201701271319 extends CI_Migration {

	private $tableName = 'idn_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'playerId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'transaction_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'tableno' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'userid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'idndate' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'idntable' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'periode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'room' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'curr_bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'hand' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'card' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'prize' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'curr' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'curr_player' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'curr_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'agent_comission' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'agent_bill' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'uniqueid' => array(
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
