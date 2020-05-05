<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_haba88_idr1_game_logs_20190212 extends CI_Migration {
	private $tableName = 'haba88_idr1_game_logs';
	public function up() {
		$this->dbforge->drop_table($this->tableName);

		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'auto_increment' => TRUE,
				'null' => false
			),
			'playerid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false
			),
			'brandgameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false
			),
			'gamekeyname' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'gametypeid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'dtstarted' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'dtcompleted' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'friendlygameinstanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'gameinstanceid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'stake' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'payout' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'jackpotwin' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'jackpotcontribution' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'currencycode' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
			'channeltypeid' => array(
				'type' => 'INT',
				'constraint' => '10',
				'null' => true
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'brandid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'createdat DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updatedat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
			'balanceafter' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'bonustoreal' => array(
				'type' => 'DOUBLE',
				'null' => true
			),
			'bonustorealcoupon' => array(
				'type' => 'VARCHAR',
				'constraint' => '150',
				'null' => true
			),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
		# Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_haba88_idr1_game_logs_external_uniqueid', 'external_uniqueid',true);
	}

	public function down() {
	}
}