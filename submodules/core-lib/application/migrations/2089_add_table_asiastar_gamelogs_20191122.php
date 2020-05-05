<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_asiastar_gamelogs_20191122 extends CI_Migration {

	private $tableName = 'asiastar_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'playerid' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => false,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'billno' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'productid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'agcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gmcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'billtime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'reckontime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'playtype' => array(
				'type' => 'INT',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'tablecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '25',
				'null' => true,
			),
			'cur_ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'account' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'cus_account' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'valid_account' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'basepoint' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'flag' => array(
				'type' => 'TINYINT',
				'constraint' => '2',
				'null' => true,
			),
			'devicetype' => array(
				'type' => 'INT',
				'null' => true,
			),
			'remark' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
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
	        $this->player_model->addIndex($this->tableName, 'idx_as_billtime', 'billtime');
	        $this->player_model->addIndex($this->tableName, 'idx_as_reckontime', 'reckontime');
	        $this->player_model->addUniqueIndex($this->tableName, 'idx_as_external_uniqueid', 'external_uniqueid');

	    }
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
