<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_goldenf_pgsoft_game_logs_201808141551 extends CI_Migration {

	private $tableName = 'goldenf_pgsoft_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'trans_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_amount' => array(
                'type' => 'double',
                'null' => true,
			),
			'win_amount' => array(
                'type' => 'double',
                'null' => true,
			),
			'pgsoft_created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'traceId' => array(
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
			),
            'md5_sum' => array(
                'type' => 'VARCHAR',
				'constraint' => '32',
                'null' => true,
            ),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('player_name');
		$this->dbforge->add_key('game_code');
		$this->dbforge->add_key('pgsoft_created_at');
		$this->dbforge->add_key('external_uniqueid');
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
