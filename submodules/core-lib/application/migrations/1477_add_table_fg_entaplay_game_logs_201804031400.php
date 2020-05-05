<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_fg_entaplay_game_logs_201804031400 extends CI_Migration {

	private $tableName = 'fg_entaplay_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'trans_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'party_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'user_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_info_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_tran_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'tran_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'date_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'rollback_tran_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'rollback_tran_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'platform_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'platform_tran_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'win_flag' => array(
				'type' => 'INT',
				'default' => 0,
			),
            'extra' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('game_tran_id');
		$this->dbforge->add_key('external_uniqueid');

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}