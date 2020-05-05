<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_genesis_seamless_thb1_transactions_20200102 extends CI_Migration {

	private $tableName = 'genesis_seamless_thb1_transactions';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'txId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'debitTxId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'partnerTxId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'playerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameUsername' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'created' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'amount' => array(
				'type' => 'double',
				'null' => true,
			),
			'progressiveWin' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'roundId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'completed' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'roundType' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bonusRefId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'jpContrib' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'action' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'token' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'before_balance' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'after_balance' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
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
	        $this->player_model->addIndex($this->tableName, 'idx_roundId', 'roundId');
	        $this->player_model->addIndex($this->tableName, 'idx_playerId', 'playerId');
	        $this->player_model->addIndex($this->tableName, 'idx_txId', 'txId');
	        $this->player_model->addIndex($this->tableName, 'idx_created', 'created');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
