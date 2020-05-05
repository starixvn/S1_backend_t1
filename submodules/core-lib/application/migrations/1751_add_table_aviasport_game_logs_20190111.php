<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_aviasport_game_logs_20190111 extends CI_Migration {

	private $tableName = 'aviasport_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'order_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'cate_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'category' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'league_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'league' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'match_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'match' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'content' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_amount' => array(
                'type' => 'double',
                'null' => true,
			),
			'bet_money' => array(
                'type' => 'double',
                'null' => true,
			),
			'money' => array(
                'type' => 'double',
                'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'create_at' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'update_at' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'start_at' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'end_at' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'result_at' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'reward_at' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'odds' => array(
                'type' => 'double',
                'null' => true,
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),

			# SBE additional info
            'response_result_id' => array(
                'type' => 'INT',
                'constraint' => '11',
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
		$this->dbforge->add_key('username');
		$this->dbforge->create_table($this->tableName);
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_aviasport_order_id', 'order_id',true);
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
