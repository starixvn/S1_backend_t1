<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_isin4d_game_logs_20190730 extends CI_Migration {

	private $tableName = 'isin4d_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'ticket_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'acct_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'bet_date' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'draw_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'bet_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '3',
				'null' => true,
			),
			'cancelled' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'msg' => array(
				'type' => 'VARCHAR',
				'constraint' => '512',
				'null' => true,
			),
			'msg_from' => array(
				'type' => 'VARCHAR',
				'constraint' => '512',
				'null' => true,
			),
			'bet_ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'odds' => array(
                'type' => 'double',
                'null' => true,
			),
			'bet_amount' => array(
                'type' => 'double',
                'null' => true,
			),
			'success_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'pay_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'wl_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'win' => array(
				'type' => 'double',
                'null' => true,
			),
			'result_amount' => array(
				'type' => 'double',
                'null' => true,
			),
			'process_date' => array(
				'type' => 'DATETIME',
				'null' => true
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
		$this->dbforge->add_key('acct_id');
		$this->dbforge->create_table($this->tableName);
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_isin4d_acct_id', 'acct_id');
        $this->player_model->addIndex($this->tableName, 'idx_isin4d_bet_date', 'bet_date');
        $this->player_model->addIndex($this->tableName, 'idx_isin4d_process_date', 'process_date');
        $this->player_model->addUniqueIndex($this->tableName, 'idx_isin4d_ticket_id', 'ticket_id');
        $this->player_model->addUniqueIndex($this->tableName, 'idx_isin4d_external_uniqueid', 'external_uniqueid');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
