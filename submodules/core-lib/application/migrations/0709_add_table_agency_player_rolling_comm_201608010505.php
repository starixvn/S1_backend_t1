<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_agency_player_rolling_comm_201608010505 extends CI_Migration {

	private $tableName = 'agency_player_rolling_comm';

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'settlement_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'total_bets' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'rolling_comm_amt' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'payment_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => true,
				'default' => '',
			),
		));
		$this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);
	}

	public function down() {
        $this->dbforge->drop_table($this->tableName);
	}
}
