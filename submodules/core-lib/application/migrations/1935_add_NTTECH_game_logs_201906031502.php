<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_NTTECH_game_logs_201906031502 extends CI_Migration {

	public function up() {


		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'BIGINT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'dealerdomain' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'tableid' => array(
				'type' => 'INT',
				'null' => false,
			),
			'gameshoe' => array(
				'type' => 'INT',
				'null' => false,
			),
			'gameround' => array(
				'type' => 'INT',
				'null' => false,
			),
			'userid' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'extension1' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'gametype' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'roundtime' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'lossamount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'txnamount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'validbet' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'txid' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'betamount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'updatetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'bettime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'category' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'roundId' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'roundstarttime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'result' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'winloss' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'odds' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),

			# SBE additional info
			'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
			'last_update_time' => array(
                'type' => 'DATETIME',
            ),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'creationtime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'extra' => array(
                'type' => 'TEXT', # show $_POST and $_GET data
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
		));

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('nttech_game_logs');

		# Add Index
		$this->load->model('player_model');
		$this->player_model->addIndex('nttech_game_logs', 'idx_bettime', 'bettime');
		$this->player_model->addIndex('nttech_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
		$this->player_model->addIndex('nttech_game_logs', 'idx_uniqueid', 'uniqueid', true);
	}

	public function down() {
		$this->dbforge->drop_table('nttech_game_logs');
	}
}
