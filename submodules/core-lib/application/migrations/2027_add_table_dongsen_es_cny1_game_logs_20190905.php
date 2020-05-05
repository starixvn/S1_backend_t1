<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_dongsen_es_cny1_game_logs_20190905 extends CI_Migration {

	private $tableName = 'dongsen_es_cny1_game_logs';
	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'userName' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => true,
			),
			'userId' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => true
			),
			'projectId' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
                'null' => true,
			),
			'betAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'realAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'prize' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'profit' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'betTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
                'null' => true,
			),
			'settleTime' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
                'null' => true,
			),
			'gameId' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
                'null' => true,
			),
			'gameName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'oddType' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
                'null' => true,
			),
			'oddBet' => array(
                'type' => 'DOUBLE',
				'null' => true,
			),
			'oddFinally' => array(
                'type' => 'DOUBLE',
				'null' => true,
			),
			'content' => array(
				'type' => 'VARCHAR',
				'constraint' => '40',
				'null' => true,
			),
			'leagueName' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
                'null' => true,
			),
			'matchName' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'matchFinishTime' => array(
				'type' => 'DATETIME',
                'null' => true,
			),
			'type' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'acceptBetterOdds' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'isLive' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true
			),
			'finished' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
				'null' => true,
			),
			'isTest' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'stat' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'parlayData' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
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
	        $this->player_model->addIndex($this->tableName, 'idx_es_cny1_userName', 'userName');
	        $this->player_model->addIndex($this->tableName, 'idx_es_cny1_matchFinishTime', 'matchFinishTime');
	        $this->player_model->addIndex($this->tableName, 'idx_es_cny1_betTime', 'betTime');
	        $this->player_model->addIndex($this->tableName, 'idx_es_cny1_settleTime', 'settleTime');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_es_cny1_projectId', 'projectId');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_es_cny1_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
