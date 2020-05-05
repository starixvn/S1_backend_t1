<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_livegaming_game_logs_20190305 extends CI_Migration {

	private $tableName = 'livegaming_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'livegaming_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'get_money' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'total_bet_money' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'draw_money' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'is_test' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'create_time' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_detail' => array(
				'type' => 'TEXT',
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
		$this->dbforge->add_key('game_username');
		$this->dbforge->create_table($this->tableName);
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_livegaming_id', 'livegaming_id',true);
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
