<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_isb_vnd5_game_logs_20190808 extends CI_Migration {

	private $tableName = 'isb_vnd5_game_logs';

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
		
		if(!$this->db->table_exists($this->tableName)){
			
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);

            # Add Index
	        $this->load->model('player_model');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_isb_vnd5_game_logs_external_uniqueid', 'external_uniqueid');
	        $this->player_model->addIndex($this->tableName, 'idx_isb_vnd5_md5_sum', 'md5_sum');
	        $this->player_model->addIndex($this->tableName, 'idx_isb_vnd5_result_time', 'result_time');
	        $this->player_model->addIndex($this->tableName, 'idx_isb_vnd5_playerid', 'playerid');
        }

	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
	}
}