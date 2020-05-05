<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_fg_seamless_gamelogs_20191120 extends CI_Migration {

	private $origTableName = 'fg_seamless_gamelogs';
	private $transTableName = 'fg_seamless_gamelogs_per_transaction';

	public function up() {
		$field1 = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'req_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'timestamp' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'token' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'account_ext_ref' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'tx_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'application_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'item_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'external_game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'round_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'txs' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'wager_count' => array(
				'type' => 'INT',
                'constraint' => '11',
				'null' => true,
			),
			'wager_sum' => array(
				'type' => 'double',
                'null' => true,
			),
			'payout_count' => array(
				'type' => 'INT',
                'constraint' => '11',
				'null' => true,
			),
			'payout_sum' => array(
				'type' => 'double',
                'null' => true,
			),
			'refund_count' => array(
				'type' => 'INT',
                'constraint' => '11',
				'null' => true,
			),
			'refund_sum' => array(
				'type' => 'double',
                'null' => true,
			),
			'status' => array(
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
			'ext_tx_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'before_balance' => array(
				'type' => 'double',
                'null' => true,
            ),
            'after_balance' => array(
				'type' => 'double',
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

		$field2 = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'req_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'timestamp' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'token' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'account_ext_ref' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'category' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'tx_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'refund_tx_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'pool_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'application_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'item_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'external_game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'round_id' => array(
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
			'ext_tx_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'before_balance' => array(
				'type' => 'double',
                'null' => true,
            ),
            'after_balance' => array(
				'type' => 'double',
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


		if(!$this->db->table_exists($this->origTableName)){
			$this->dbforge->add_field($field1);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->origTableName);
			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->origTableName, 'idx_req_id', 'req_id');
	        $this->player_model->addIndex($this->origTableName, 'idx_account_ext_ref', 'account_ext_ref');
	        $this->player_model->addIndex($this->origTableName, 'idx_external_game_id', 'external_game_id');
	        $this->player_model->addIndex($this->origTableName, 'idx_round_id', 'round_id');
	        $this->player_model->addIndex($this->origTableName, 'idx_md5_sum', 'md5_sum');
	        $this->player_model->addUniqueIndex($this->origTableName, 'idx_external_uniqueid', 'external_uniqueid');
	        $this->player_model->addUniqueIndex($this->origTableName, 'idx_tx_id', 'tx_id');
	        $this->player_model->addUniqueIndex($this->origTableName, 'idx_ext_tx_id', 'ext_tx_id');
	    }

	    if(!$this->db->table_exists($this->transTableName)){
			$this->dbforge->add_field($field2);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->transTableName);
			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->transTableName, 'idx_req_id', 'req_id');
	        $this->player_model->addIndex($this->transTableName, 'idx_account_ext_ref', 'account_ext_ref');
	        $this->player_model->addIndex($this->transTableName, 'idx_external_game_id', 'external_game_id');
	        $this->player_model->addIndex($this->transTableName, 'idx_md5_sum', 'md5_sum');
	        $this->player_model->addUniqueIndex($this->transTableName, 'idx_external_uniqueid', 'external_uniqueid');
	        $this->player_model->addUniqueIndex($this->transTableName, 'idx_tx_id', 'tx_id');
	        $this->player_model->addUniqueIndex($this->transTableName, 'idx_ext_tx_id', 'ext_tx_id');
	    }
	}

	public function down() {
		$this->dbforge->drop_table($this->origTableName);
		$this->dbforge->drop_table($this->transTableName);
	}
}
