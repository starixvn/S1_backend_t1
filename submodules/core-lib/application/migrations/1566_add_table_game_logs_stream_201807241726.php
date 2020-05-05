<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_game_logs_stream_201807241726 extends CI_Migration {

	private $tableName = 'game_logs_stream';

	public function up() {

		if(!$this->db->table_exists($this->tableName)){
			//remove note field
			$fields = array(
				'id' => array(
					'type' => 'BIGINT',
					'null' => false,
					'auto_increment' => TRUE,
				),
				'game_platform_id' => array(
					'type' => 'INT',
					'null' => false,
				),
				'game_type_id' => array(
					'type' => 'INT',
					'null' => false,
				),
				'game_description_id' => array(
					'type' => 'INT',
					'null' => false,
				),
				'game' => array(
					'type' => 'VARCHAR',
					'constraint' => '200',
					'null' => true,
				),
				'game_type' => array(
					'type' => 'VARCHAR',
					'constraint' => '200',
					'null' => true,
				),
				'game_code' => array(
					'type' => 'VARCHAR',
					'constraint' => '200',
					'null' => true,
				),
				'room' => array(
					'type' => 'VARCHAR',
					'constraint' => '64',
					'null' => true,
				),
				'table' => array(
					'type' => 'VARCHAR',
					'constraint' => '64',
					'null' => true,
				),
				'player_id' => array(
					'type' => 'INT',
					'null' => false,
				),
				'player_username' => array(
					'type' => 'VARCHAR',
					'constraint' => '200',
					'null' => true,
				),
				'result_amount' => array(
					'type' => 'double',
					'null' => false,
				),
				'bet_amount' => array(
					'type' => 'double',
					'null' => false,
				),
				'rent' => array(
					'type' => 'double',
					'null' => true,
				),
				'after_balance' => array(
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
				'response_result_id' => array(
					'type' => 'INT',
					'null' => true,
				),
	            'external_log_id' => array(
	                'type' => 'INT',
	                'null' => true,
	            ),
				'external_uniqueid' => array(
					'type' => 'VARCHAR',
					'constraint' => '64',
					'null' => true,
				),
	            'flag' => array(
	                'type' => 'INT',
	                'null' => true,
	            ),
	            'has_both_side' => array(
	                'type' => 'INT',
	                'null' => true,
	            ),
	            'updated_at' => array(
	                'type' => 'DATETIME',
	                'null' => true,
	            ),
				'trans_amount' => array(
					'type' => 'double',
					'null' => true,
				),
	            'trans_type' => array(
	                'type' => 'INT',
	                'null' => true,
	            ),
				'win_amount' => array(
					'type' => 'double',
					'null' => true,
				),
				'loss_amount' => array(
					'type' => 'double',
					'null' => true,
				),
				'odds' => array(
					'type' => 'double',
					'null' => true,
				),
				'bet_for_cashback' => array(
					'type' => 'double',
					'null' => true,
				),
				'real_betting_amount' => array(
					'type' => 'double',
					'null' => true,
				),
				'match_details' => array(
					'type' => 'VARCHAR',
					'constraint' => '200',
					'null' => true,
				),
				'match_type' => array(
					'type' => 'VARCHAR',
					'constraint' => '100',
					'null' => true,
				),
				'bet_info' => array(
					'type' => 'VARCHAR',
					'constraint' => '100',
					'null' => true,
				),
				'handicap' => array(
					'type' => 'double',
					'null' => true,
				),
				'bet_type' => array(
					'type' => 'VARCHAR',
					'constraint' => '100',
					'null' => true,
				),
	            'running_platform' => array(
	                'type' => 'INT',
	                'null' => true,
	            ),
				'bet_details' => array(
					'type' => 'TEXT',
					'null' => true,
				),
				'ip_address' => array(
					'type' => 'VARCHAR',
					'constraint' => '100',
					'null' => true,
				),
				'sync_index' => array(
					'type' => 'BIGINT',
					'null' => true,
				),
				'odds_type' => array(
					'type' => 'INT',
					'null' => true,
				),
	            'bet_at' => array(
	                'type' => 'DATETIME',
	                'null' => true,
	            ),
				'md5_sum' => array(
					'type' => 'VARCHAR',
					'constraint' => '32',
					'null' => true,
				),

			);


			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->add_key('external_uniqueid');
			$this->dbforge->add_key('bet_at');
			$this->dbforge->add_key('updated_at');
			$this->dbforge->create_table($this->tableName);
		}

		$this->load->model(['player_model']);
		if(!$this->db->field_exists('bet_at', 'game_logs')){
			$addFields=[
				'bet_at' => [
	                'type' => 'DATETIME',
	                'null' => true
	            ],
			];
	        $this->dbforge->add_column('game_logs', $addFields);
	        $this->player_model->addIndex('game_logs', 'idx_bet_at', 'bet_at');
		}
		if(!$this->db->field_exists('md5_sum', 'game_logs')){
			$addFields=[
				'md5_sum' => [
					'type' => 'VARCHAR',
					'constraint' => '32',
					'null' => true,
				],
			];
	        $this->dbforge->add_column('game_logs', $addFields);
	        $this->player_model->addIndex('game_logs', 'idx_md5_sum', 'md5_sum');
		}
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
		//don't drop bet_at, md5_sum
	}
}
