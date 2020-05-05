<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_hub88_gamelogs_20200129 extends CI_Migration {

	private $tableName = 'hub88_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'user' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'hub88_updated_at' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'transaction_uuid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'round' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'reference_transaction_uuid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'kind' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'inserted_at' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'hub88_id' => array(
				'type' => 'BIGINT',
				'null' => true,
			),
			'game_id' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'amount' => array(
				'type' => 'DOUBLE',
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
	        $this->player_model->addIndex($this->tableName, 'idx_transaction_uuid', 'transaction_uuid');
	        $this->player_model->addIndex($this->tableName, 'idx_reference_transaction_uuid', 'reference_transaction_uuid');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_as_external_uniqueid', 'external_uniqueid');

	    }
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
