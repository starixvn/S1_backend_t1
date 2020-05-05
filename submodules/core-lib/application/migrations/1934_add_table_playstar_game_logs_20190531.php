<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_playstar_game_logs_20190531 extends CI_Migration {

	private $tableName = 'playstar_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'member_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_round_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'sub_game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameround_end_time' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'game_round_denomination' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
			),
			'win_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'result_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'win_bonus_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'bonus_data' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'win_gamble_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'win_jackpot_amount' => array(
                'type' => 'double',
                'null' => true,
			),
			'result_data' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'extra_data' => array(
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

        if(!$this->db->table_exists($this->tableName)){
    		$this->dbforge->add_field($fields);
    		$this->dbforge->add_key('id', TRUE);
    		$this->dbforge->create_table($this->tableName);
        }
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_playstar_game_round_id', 'game_round_id',true);
        $this->player_model->addIndex($this->tableName, 'idx_playstar_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex($this->tableName, 'idx_playstar_memberid', 'member_id');
        $this->player_model->addIndex($this->tableName, 'idx_playstar_gameround_end_time', 'gameround_end_time');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
