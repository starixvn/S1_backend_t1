<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_cq9_game_logs_201806061115 extends CI_Migration {

	private $tableName = 'cq9_game_logs';

	public function up() {

		if($this->db->table_exists('cq9_game_logs')){
			return;
		}

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'gamehall' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gametype' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gameplat' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gamecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'account' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'round' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'balance' => array(
				'type' => 'double',
				'null' => true,
			),
			'win' => array(
				'type' => 'double',
				'null' => true,
			),
			'bet' => array(
				'type' => 'double',
				'null' => true,
			),
			'jackpot' => array(
				'type' => 'double',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'endroundtime' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'createtime' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'bettime' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'detail' => array(
                'type' => 'TEXT',
                'null' => true,
			),
			'gamerole' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
                'null' => true,
			),
			'bankertype' => array(
                'type' => 'VARCHAR',
				'constraint' => '50',
                'null' => true,
			),
			'rake' => array(
				'type' => 'double',
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
		$this->dbforge->add_key('gamecode');
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
