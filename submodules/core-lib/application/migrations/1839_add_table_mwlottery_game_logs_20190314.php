<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_mwlottery_game_logs_20190314 extends CI_Migration {

	private $tableName = 'mwlottery_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'bettime' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'cancelstatus' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'confirmamount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'content' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'counts' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'issueno' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'lotterycode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'lotterynumber' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'method' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'nums' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'odds' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'orderid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'terminaltype' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'unit' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'useraccount' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'winamount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'wincount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'winnumber' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'username' => array(
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


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('username');
		$this->dbforge->create_table($this->tableName);
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_orderid', 'orderid',true);
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
