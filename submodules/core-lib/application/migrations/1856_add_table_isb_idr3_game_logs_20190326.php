<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_isb_idr3_game_logs_20190326 extends CI_Migration {

	private $tableName = 'isb_idr3_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'playerid' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => true,
			),
			'operator' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => true,
			),
			'sessionid' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'roundid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => true,
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => true,
			),
			'transactionid' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => true,
			),
			'time' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'jpc' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'jpw' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'jpw_jpc' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'result_time' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'result_balance' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);

		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_isb_idr3_game_logs_external_uniqueid', 'external_uniqueid',true);
        $this->player_model->addIndex($this->tableName, 'idx_md5_sum', 'md5_sum');
        $this->player_model->addIndex($this->tableName, 'transactionid_UNIQUE', 'transactionid');
        $this->player_model->addIndex($this->tableName, 'uniqueid_UNIQUE', 'uniqueid');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
///END OF FILE//////////