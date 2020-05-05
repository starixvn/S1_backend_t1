<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_n2live_game_logs_20190718 extends CI_Migration {

	private $tableName = 'n2live_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'startdate' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'enddate' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'code' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => true,
			),
			'login' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'payout_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'valid_turnover' => array(
				'type' => 'double',
                'null' => true,
			),
			'handle' => array(
                'type' => 'double',
                'null' => true,
			),
			'hold' => array(
                'type' => 'double',
                'null' => true,
			),
			'schemeid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'channel' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'betdetail' => array(
				'type' => 'text',
                'null' => true,
			),

			# SBE additional info
			'game_externalid' => array(
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


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_startdate', 'startdate');
        $this->player_model->addIndex($this->tableName, 'idx_enddate', 'enddate');
        $this->player_model->addIndex($this->tableName, 'idx_game_externalid', 'game_externalid');
        $this->player_model->addIndex($this->tableName, 'idx_login', 'login');
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
