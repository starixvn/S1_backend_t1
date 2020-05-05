<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_mtech_bbin_game_logs_201811120500 extends CI_Migration {

	private $tableName = 'mtech_bbin_game_logs';

	public function up() {
		$fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'game_kind' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'wagers_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'wagers_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'game_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'exchange_rate' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'result' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'payoff' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'commissionable' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'origin' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'uptime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'order_date' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'payout_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'account_date' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'serial_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'modified_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'modified_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),            
            'round_no' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'wager_detail' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'card' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'result_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'is_paid' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),

            # SBE data
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => false
            ),

            'response_result_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

		if (!$this->db->table_exists($this->tableName)) {
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table($this->tableName);

	        $this->load->model('original_game_logs_model');
	        $this->original_game_logs_model->addIndex($this->tableName, 'idx_username', 'username');
	        $this->original_game_logs_model->addIndex($this->tableName, 'idx_wagers_id', 'wagers_id');
	        $this->original_game_logs_model->addIndex($this->tableName, 'idx_wagers_date', 'wagers_date');
	        $this->original_game_logs_model->addIndex($this->tableName, 'idx_payout_time', 'payout_time');
	        $this->original_game_logs_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid', true);

		}

       
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}