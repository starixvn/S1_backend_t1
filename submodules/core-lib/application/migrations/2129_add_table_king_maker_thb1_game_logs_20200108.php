<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_king_maker_thb1_game_logs_20200108 extends CI_Migration {

	private $tableName = 'king_maker_thb1_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'gametype' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'comm' => array(
				'type' => 'double',
                'null' => true,
			),
			'txtime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'bizdate' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'winamt' => array(
				'type' => 'double',
                'null' => true,
			),
			'gameinfo' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'betamt' => array(
				'type' => 'double',
                'null' => true,
			),
			'updatetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'jackpotwinamt' => array(
				'type' => 'double',
                'null' => true,
			),
			'turnover' => array(
				'type' => 'double',
                'null' => true,
			),
			'userid' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'bettype' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'txstatus' => array(
				'type' => 'INT',
				'constraint' => '2',
				'null' => true,
			),
			'jackpotbetamt' => array(
				'type' => 'double',
                'null' => true,
			),
			'createtime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'platformtxid' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'realbetamt' => array(
				'type' => 'double',
                'null' => true,
			),
			'gamecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'transid' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'realwinamt' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'roundid' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'result_amount' => array(
				'type' => 'double',
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
	        $this->player_model->addIndex($this->tableName, 'idx_userid', 'userid');
	        $this->player_model->addIndex($this->tableName, 'idx_gamecode', 'gamecode');
	        $this->player_model->addIndex($this->tableName, 'idx_txtime', 'txtime');
	        $this->player_model->addIndex($this->tableName, 'idx_createtime', 'createtime');
	        $this->player_model->addIndex($this->tableName, 'idx_updatetime', 'updatetime');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_platformtxid', 'platformtxid');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
