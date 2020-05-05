<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ae_slots_game_logs_20190808 extends CI_Migration {

	private $tableName = 'ae_slots_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'account_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '25',
				'null' => true
			),
			'game_id' => array(
				'type' => 'INT',
				'constraint' => '100',
				'null' => true
			),
			'round_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'free' => array(
				'type' => 'TINYINT',
				'null' => true
			),
			'bet_amt' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'payout_amt' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'completed_at' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'rebate_amt' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'jp_pc_con_amt' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'jp_jc_con_amt' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'jp_win_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'jp_pc_win_amt' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'jp_jc_win_amt' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
			'jp_win_lv' => array(
				'type' => 'INT',
				'constraint' => '50',
				'null' => true
			),
			'jp_direct_pay' => array(
				'type' => 'TINYINT',
				'null' => true
			),
			'prize_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '25',
				'null' => true
			),
			'prize_amt' => array(
				'type' => 'VARCHAR',
				'constraint' => '25',
				'null' => true
			),
			'site_id' => array(
				'type' => 'INT',
				'constraint' => '50',
				'null' => true
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
			$this->dbforge->add_key('account_name');
			$this->dbforge->create_table($this->tableName);

			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->tableName, 'idx_qqkl_account_name', 'account_name');
	        $this->player_model->addIndex($this->tableName, 'idx_qqkl_completed_at', 'completed_at');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_qqkl_round_id', 'round_id');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_qqkl_external_uniqueid', 'external_uniqueid');
		}
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
