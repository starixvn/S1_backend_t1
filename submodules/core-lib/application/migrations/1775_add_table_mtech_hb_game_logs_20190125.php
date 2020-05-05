<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_mtech_hb_game_logs_20190125 extends CI_Migration {

	private $tableName = 'mtech_hb_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'playerid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'brandid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'brandgameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gamekeyname' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gametypeid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'dtstarted' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'dtcompleted' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'friendlygameinstanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameinstanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'stake' => array(
                'type' => 'double',
                'null' => true,
			),
			'payout' => array(
                'type' => 'double',
                'null' => true,
			),
			'jackpotwin' => array(
                'type' => 'double',
                'null' => true,
			),
			'jackpotcontribution' => array(
                'type' => 'double',
                'null' => true,
			),
			'currencycode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'channeltypeid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'balanceafter' => array(
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
		$this->dbforge->add_key('gamekeyname');
		$this->dbforge->create_table($this->tableName);
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_mtechog_gameinstanceid', 'gameinstanceid',true);
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
