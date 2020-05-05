<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_fishinggame_gamelogs_201609121316 extends CI_Migration {

	private $tableName = 'fishinggame_game_logs';

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

			'Username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'cuuency' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'accountno' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'autoid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
			),
			'creditdelat' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'gameId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true
			),
			'bet' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'detail_autoid'=>array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'bettimeStr' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'profit' => array(
				'type' => 'DOUBLE',
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