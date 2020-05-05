<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_lotus_game_logs_20181025 extends CI_Migration {

	private $tableName = 'lotus_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'uid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'user_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'channel_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'start_balance' => array(
                'type' => 'double',
                'null' => true,
			),
			'end_balance' => array(
                'type' => 'double',
                'null' => true,
			),
			'result_amount' => array(
                'type' => 'double',
                'null' => true,
			),
			'total_betting' => array(
                'type' => 'double',
                'null' => true,
			),
			'betting_log' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'result_log' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'result_date' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'round' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('game_code');
		$this->dbforge->add_key('external_uniqueid');
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
