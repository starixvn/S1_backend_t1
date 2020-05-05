<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_kycard_game_logs_201805311544 extends CI_Migration {

	private $tableName = 'kycard_game_logs';

	public function up() {

		if($this->db->table_exists('kycard_game_logs')){
			return;
		}

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'accounts' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'serverid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'kindid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'tableid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'chairid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'usercount' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'cellscore' => array(
				'type' => 'double',
				'null' => true,
			),
			'allbet' => array(
				'type' => 'double',
				'null' => true,
			),
			'profit' => array(
				'type' => 'double',
				'null' => true,
			),
			'revenue' => array(
				'type' => 'double',
				'null' => true,
			),
			'gamestarttime' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'gameendtime' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'cardvalue' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
                'null' => true,
			),
			'channelid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
                'null' => true,
			),
			'linecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
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
		$this->dbforge->add_key('external_uniqueid');
		$this->dbforge->add_key('gameid');
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
