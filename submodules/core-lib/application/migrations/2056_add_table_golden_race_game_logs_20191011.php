<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_golden_race_game_logs_20191011 extends CI_Migration {

	private $tableName = 'golden_race_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'game_id' => array(
				'type' => 'SMALLINT',
				'null' => true,
			),
			'game' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'session_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '150',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'round' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'result_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'real_bet_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'after_balance' => array(
				'type' => 'double',
                'null' => true,
			),
			'before_balance' => array(
				'type' => 'double',
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

		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->tableName);
			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->tableName, 'idx_session_id', 'session_id');
	        $this->player_model->addIndex($this->tableName, 'idx_game_id', 'game_id');
	        $this->player_model->addIndex($this->tableName, 'idx_username', 'username');
	        $this->player_model->addIndex($this->tableName, 'idx_start_at', 'start_at');
	        $this->player_model->addIndex($this->tableName, 'idx_end_at', 'end_at');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
