<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_cs_sports_game_logs_20190628 extends CI_Migration {

	public function up() {
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'BIGINT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'userid' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'platformname' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'bet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'payout' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'result' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bettime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'settletime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'synctime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'gamesn' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'roundno' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'gamename' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'rule' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'played' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
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
		$this->dbforge->create_table('cs_sports_game_logs');

		# Add Index
		$this->load->model('player_model');
		$this->player_model->addIndex('cs_sports_game_logs', 'idx_bettime', 'bettime');
		$this->player_model->addIndex('cs_sports_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);
		$this->player_model->addIndex('cs_sports_game_logs', 'idx_uniqueid', 'uniqueid', true);
	}

	public function down() {
		$this->dbforge->drop_table('cs_sports_game_logs');
	}
}