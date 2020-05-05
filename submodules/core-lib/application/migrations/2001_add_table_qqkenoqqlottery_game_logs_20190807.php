<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_qqkenoqqlottery_game_logs_20190807 extends CI_Migration {

	private $tableName = 'qqkenoqqlottery_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'ticket_detail_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'ticket_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'acct_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet_time' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'market' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'draw_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '128',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'bet_choice' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'result_time' => array(
                'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'winloss' => array(
                'type' => 'double',
                'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '3',
				'null' => true,
			),
			'bet_unit' => array(
				'type' => 'double',
                'null' => true,
			),
			'bet_count' => array(
				'type' => 'INT',
                'constraint' => '11',
                'null' => true,
			),
			'odds' => array(
				'type' => 'double',
                'null' => true,
			),
			'bet_ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'cancelled' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'channel' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'brand_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
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
			$this->dbforge->add_key('acct_id');
			$this->dbforge->create_table($this->tableName);

			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->tableName, 'idx_qqkl_acct_id', 'acct_id');
	        $this->player_model->addIndex($this->tableName, 'idx_qqkl_bet_time', 'bet_time');
	        $this->player_model->addIndex($this->tableName, 'idx_qqkl_result_time', 'result_time');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_qqkl_ticket_detail_id', 'ticket_detail_id');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_qqkl_external_uniqueid', 'external_uniqueid');
		}
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
