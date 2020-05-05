<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_dongsen_lottery_game_logs_20190907 extends CI_Migration {

	private $tableName = 'dongsen_lottery_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'agentCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => true,
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
			'gameOId' => array(
				'type' => 'INT',
				'constraint' => '11',
                'null' => true,
			),
			'gameName' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'projectId' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
                'null' => true,
			),
			'taskId' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
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
			'stat' => array(
				'type' => 'INT',
				'constraint' => '11',
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
			'betType' => array(
				'type' => 'INT',
				'constraint' => '11',
                'null' => true,
			),
			'code' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
                'null' => true,
			),
			'issue' => array(
				'type' => 'VARCHAR',
				'constraint' => '11',
                'null' => true,
			),
			'prizeCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
                'null' => true,
			),
			'methodId' => array(
				'type' => 'INT',
				'constraint' => '16',
                'null' => true,
			),
			'methodName' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
                'null' => true,
			),
			'multiple' => array(
				'type' => 'INT',
				'constraint' => '11',
                'null' => true,
			),
			'mode' => array(
				'type' => 'INT',
				'constraint' => '11',
                'null' => true,
			),
			'isFast' => array(
				'type' => 'INT',
				'constraint' => '11',
                'null' => true,
			),
			'dypointDec' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
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
	        $this->player_model->addIndex($this->tableName, 'idx_userName', 'userName');
	        $this->player_model->addIndex($this->tableName, 'idx_betTime', 'betTime');
	        $this->player_model->addIndex($this->tableName, 'idx_settleTime', 'settleTime');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_projectId', 'projectId');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
