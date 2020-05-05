<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_vivo_gaming_game_logs_20190912 extends CI_Migration {

	private $tableName = 'vivo_gaming_idr1_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
            ),
            'accounting_transaction_id' => array(
                'type' => 'INT',
                'constraint' => '50',
                'null' => false,
            ),
            'player_login_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
            ),
            'transaction_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'transaction_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'null' => true,
            ),
            'transaction_type_id' => array(
                'type' => 'INT',
                'constraint' => '25',
                'null' => true,
            ),
            'balance_before' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'debit_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'credit_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'balance_after' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'table_round_id' => array(
                'type' => 'INT',
                'constraint' => '25',
                'null' => true,
            ),
            'table_id' => array(
                'type' => 'INT',
                'constraint' => '25',
                'null' => true,
            ),
            'card_provider_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'card_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'game_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'game_id' => array(
                'type' => 'INT',
                'constraint' => '25',
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
			$this->dbforge->add_key('player_login_name');
			$this->dbforge->create_table($this->tableName);

			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->tableName, 'idx_vivogaming_player_login_name', 'player_login_name');
	        $this->player_model->addIndex($this->tableName, 'idx_vivogaming_transaction_date', 'transaction_date');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_vivogaming_table_round_id', 'table_round_id');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_vivogaming_external_uniqueid', 'external_uniqueid');
		}
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
