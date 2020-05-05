<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_hogaming_idr1_game_logs_20190902 extends CI_Migration {

	private $tableName = 'hogaming_idr1_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'bet_start_date' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'bet_end_date' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'account_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'table_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => true,
			),
			'table_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '24',
				'null' => true,
			),
			'bet_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'payout' => array(
				'type' => 'double',
                'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'game_type' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_spot' => array(
                'type' => 'VARCHAR',
				'constraint' => '80',
				'null' => true,
			),
			'bet_no' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'bet_mode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'brand_name' => array(
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

		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->tableName);
			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->tableName, 'idx_hogm_idr1_account_id', 'account_id');
	        $this->player_model->addIndex($this->tableName, 'idx_hogm_idr1_bet_start_date', 'bet_start_date');
	        $this->player_model->addIndex($this->tableName, 'idx_hogm_idr1_bet_end_date', 'bet_end_date');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_hogm_idr1_bet_id', 'bet_id');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_hogm_idr1_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
