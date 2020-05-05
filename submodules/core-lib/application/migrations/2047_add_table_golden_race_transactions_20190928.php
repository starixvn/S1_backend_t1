<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_golden_race_transactions_20190928 extends CI_Migration {

	private $tableName = 'golden_race_transactions';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'action' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'sessionId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameId' => array(
				'type' => 'SMALLINT',
				'null' => false,
			),
			'playerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'group' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'gameCycle' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameCycleClosed' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'transactionId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'transactionAmount' => array(
				'type' => 'double',
                'null' => true,
			),
			'transactionCategory' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'transactionType' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'timestamp' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'requestId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'siteId' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'fingerprint' => array(
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

		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->tableName);
			# Add Index
	        $this->load->model('player_model');
	        $this->player_model->addIndex($this->tableName, 'idx_sessionId', 'sessionId');
	        $this->player_model->addIndex($this->tableName, 'idx_gameId', 'gameId');
	        $this->player_model->addIndex($this->tableName, 'idx_playerId', 'playerId');
	        $this->player_model->addIndex($this->tableName, 'idx_transactionId', 'transactionId');
	        $this->player_model->addIndex($this->tableName, 'idx_timestamp', 'timestamp');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if(!$this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
