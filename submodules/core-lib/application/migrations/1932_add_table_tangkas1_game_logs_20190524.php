<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_tangkas1_game_logs_20190524 extends CI_Migration {

	private $tableName = 'tangkas1_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'idhistory' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'invoice_number' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'table_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'table_type_text' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'table_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'table_multiply' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'table_coin' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'amount' => array(
                'type' => 'double',
                'null' => true,
			),
			'result_end' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'step' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'balance_start' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'balance_end' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'credit_start' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'credit_end' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'start_time' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'end_time' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'duration' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'cards' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'wl' => array(
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
        $this->player_model->addIndex($this->tableName, 'idx_tangkas1_invoice_number', 'invoice_number',true);
        $this->player_model->addIndex($this->tableName, 'idx_tangkas1_username', 'username');
        $this->player_model->addIndex($this->tableName, 'idx_tangkas1_start_time', 'start_time');
        $this->player_model->addIndex($this->tableName, 'idx_tangkas1_end_time', 'end_time');
        $this->player_model->addIndex($this->tableName, 'idx_tangkas1_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
