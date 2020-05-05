<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ibc24tech_thb1_game_logs_20190904 extends CI_Migration {

	private $tableName = 'ibc24tech_idr1_game_logs';
	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'ballid' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => true,
			),
			'balltime' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'curpl' => array(
				'type' => 'double',
                'null' => true,
			),
			'isbk' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => true,
			),
			'game_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'iscancel' => array(
				'type' => 'VARCHAR',
				'constraint' => '2',
				'null' => true,
			),
			'isjs' => array(
				'type' => 'VARCHAR',
				'constraint' => '2',
				'null' => true,
			),
			'win' => array(
				'type' => 'double',
                'null' => true,
			),
			'lose' => array(
				'type' => 'double',
                'null' => true,
			),
			'result_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'moneyrate' => array(
				'type' => 'double',
                'null' => true,
			),
			'orderid' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'result' => array(
                'type' => 'VARCHAR',
				'constraint' => '80',
				'null' => true,
			),
			'sportid' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'truewin' => array(
				'type' => 'double',
                'null' => true,
			),
			'tzip' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'tzmoney' => array(
				'type' => 'double',
                'null' => true,
			),
			'tztype' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '30',
				'null' => true,
			),
			'updatetime' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '60',
				'null' => true,
			),
			'content' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'vendorid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'validamount' => array(
				'type' => 'double',
                'null' => true,
			),
			'abc' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'oddstype' => array(
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
	        $this->player_model->addIndex($this->tableName, 'idx_ibc24t_thb1_username', 'username');
	        $this->player_model->addIndex($this->tableName, 'idx_ibc24t_thb1_balltime', 'balltime');
	        $this->player_model->addIndex($this->tableName, 'idx_ibc24t_thb1_updatetime', 'updatetime');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_ibc24t_thb1_orderid', 'orderid');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_ibc24t_thb1_external_uniqueid', 'external_uniqueid');
	    }
	}

	public function down() {
		if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
		}
	}
}
