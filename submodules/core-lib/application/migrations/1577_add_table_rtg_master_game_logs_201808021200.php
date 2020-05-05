<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_rtg_master_game_logs_201808021200 extends CI_Migration {

	private $tableName = 'rtg_master_game_logs';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'agentid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'agentname' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'casinoplayerid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'casinoid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'playername' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gamedate' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'gamestartdate' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
			'gamenumber' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gamename' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'bet' => array(
                'type' => 'double',
                'null' => true,
			),
			'win' => array(
                'type' => 'double',
                'null' => true,
			),
			'jpbet' => array(
                'type' => 'double',
                'null' => true,
			),
			'jpwin' => array(
                'type' => 'double',
                'null' => true,
			),
			'currency' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'roundid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'balancestart' => array(
                'type' => 'double',
                'null' => true,
			),
			'balanceend' => array(
                'type' => 'double',
                'null' => true,
			),
			'platform' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'externalgameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'sidebet' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'jackpotdetails' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'md5_sum' => array(
                'type' => 'VARCHAR',
				'constraint' => '32',
                'null' => true,
            ),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
		);


		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('casinoplayerid');
		$this->dbforge->add_key('gameId');
		$this->dbforge->add_key('external_uniqueid');
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
