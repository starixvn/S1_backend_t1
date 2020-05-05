<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_slot_factory_game_logs_20191205 extends CI_Migration {

	private $tableName = 'slot_factory_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'account_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '36',
				'null' => true,
			),
			'round_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true
			),
			'transaction_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
                'null' => true,
			),
			'game_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'spin_date' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => true,
			),
			'lines' => array(
				'type' => 'INT',
				'constraint' => '16',
				'null' => true,
			),
			'lineBet' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
                'null' => true,
			),
			'totalbet' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
                'null' => true,
			),
			'cashWon' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
                'null' => true,
			),
			'gambleGames' => array(
				'type' => 'TINYINT',
				'null' => true,
			),
			'freeGames' => array(
				'type' => 'TINYINT',
                'null' => true,
			),
			'freeGamesPlayed' => array(
                'type' => 'INT',
                'constraint' => '16',
				'null' => true,
			),
			'freeGamesRemaining' => array(
                'type' => 'INT',
                'constraint' => '16',
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
	        $this->player_model->addIndex($this->tableName, 'idx_account_id', 'account_id');
	        $this->player_model->addIndex($this->tableName, 'idx_round_id', 'round_id');
	        $this->player_model->addIndex($this->tableName, 'idx_spin_date', 'spin_date');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_transaction_id', 'transaction_id');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
