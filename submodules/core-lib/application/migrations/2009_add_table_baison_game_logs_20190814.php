<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_baison_game_logs_20190814 extends CI_Migration {

	private $tableName = 'baison_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'user_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'room_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'table_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'seat_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'user_count' => array(
				'type' => 'SMALLINT',
				'null' => true,
			),
			'round_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'card_value' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'init_balance' => array(
				'type' => 'double',
                'null' => true,
			),
			'all_bet' => array(
				'type' => 'double',
                'null' => true,
			),
			'avail_bet' => array(
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
			'balance' => array(
				'type' => 'double',
                'null' => true,
			),
			'start_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'end_time' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'channel_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'sub_channel_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'room_type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'jackpot' => array(
				'type' => 'double',
                'null' => true,
			),
			'holdem_buy_insurance' => array(
				'type' => 'double',
                'null' => true,
			),
			'holdem_buy_card' => array(
				'type' => 'double',
                'null' => true,
			),
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			# SBE additional info
			'game_externalid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
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
	        $this->player_model->addIndex($this->tableName, 'idx_start_time', 'start_time');
	        $this->player_model->addIndex($this->tableName, 'idx_end_time', 'end_time');
	        $this->player_model->addIndex($this->tableName, 'idx_game_externalid', 'game_externalid');
	        $this->player_model->addIndex($this->tableName, 'idx_user_id', 'user_id');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
