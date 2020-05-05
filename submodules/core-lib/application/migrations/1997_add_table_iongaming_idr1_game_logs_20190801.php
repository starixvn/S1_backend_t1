<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_iongaming_idr1_game_logs_20190801 extends CI_Migration {

	private $tableName = 'iongaming_idr1_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'refNo' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'accountId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'playerWinloss' => array(
                'type' => 'double',
                'null' => true,
			),
			'stake' => array(
                'type' => 'double',
                'null' => true,
			),
			'orderTime' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'lastCashBalance' => array(
                'type' => 'double',
                'null' => true,
			),
			'gameType' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameStartTime' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'settleTime' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'gameId' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
			'tableName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'groupBetOptions' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'betOptions' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'seqNo' => array(
                'type' => 'INT',
                'constraint' => '3',
				'null' => true,
			),
			'turnoverStake' => array(
				'type' => 'double',
				'null' => true,
			),
			'ticketStatus' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'isCommission' => array(
				'type' => 'INT',
                'constraint' => '3',
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
		$this->dbforge->add_key('accountId');
		$this->dbforge->create_table($this->tableName);
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_iongaming_accountId', 'accountId');
        $this->player_model->addIndex($this->tableName, 'idx_iongaming_gameStartTime', 'gameStartTime');
        $this->player_model->addIndex($this->tableName, 'idx_iongaming_settleTime', 'settleTime');
        $this->player_model->addUniqueIndex($this->tableName, 'idx_iongaming_refNo', 'refNo');
        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
