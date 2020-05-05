<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_AGMG_game_logs_2019050661311 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'BIGINT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'billno' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'playername' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'agentcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'gamecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'netamount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'bettime' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'gametype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'betamount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'validbetamount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'flag' => array(
				'type' => 'INT',
				'null' => true,
			),
			'playtype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			),
			'tablecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'loginip' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'recalcutime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),

			'platformtype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'remark' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'round' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'slottype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'result' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'mainbillno' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'beforecredit' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'datatype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
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
			'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('agmg_game_logs');

		# Add Index
		$this->load->model('player_model');
		$this->player_model->addIndex('agmg_game_logs', 'idx_bettime', 'bettime');
		$this->player_model->addIndex('agmg_game_logs', 'idx_external_uniqueid', 'external_uniqueid',true);

	}

	public function down() {
		$this->dbforge->drop_table('agmg_game_logs');
	}
}
