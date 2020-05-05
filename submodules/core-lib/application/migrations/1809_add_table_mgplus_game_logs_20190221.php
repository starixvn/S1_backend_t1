<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_mgplus_game_logs_20190221 extends CI_Migration {

	private $tableName = 'mgplus_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'betuid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'createddateutc' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'gamestarttimeutc' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'gameendtimeutc' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'productid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'productplayerid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'playerid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'betamount' => array(
                'type' => 'double',
                'null' => true,
			),
			'payoutamount' => array(
                'type' => 'double',
                'null' => true,
			),
			'betstatus' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'pca' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'metadata' => array(
				'type' => 'TEXT',
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
		$this->dbforge->add_key('playerid');
		$this->dbforge->create_table($this->tableName);
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_mgplus_betuid', 'betuid',true);
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
