<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_pt_krw_game_logs_201711091620 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'playername' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'gamename' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'gameshortcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'gamecode' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'bet' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'win' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'gamedate' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'sessionid' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'gametype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'windowcode' => array(
				'type' => 'INT',
				'null' => true,
			),
			'balance' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'progressivebet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'progressivewin' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'currentbet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'livenetwork' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gameid' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'info' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,
				'null' => false,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('pt_krw_game_logs');
		$this->db->query('create unique index idx_pt_krw_game_logs_uniqueid on pt_krw_game_logs(uniqueid)');
		$this->db->query('create index idx_pt_krw_game_logs_external_uniqueid on pt_krw_game_logs(external_uniqueid)');
		$this->db->query('create index idx_playername on pt_krw_game_logs(playername)');
		$this->db->query('create index idx_gamedate on pt_krw_game_logs(gamedate)');
		$this->db->query('create index idx_gameshortcode on pt_krw_game_logs(gameshortcode)');
	}

	public function down() {
		$this->dbforge->drop_table('pt_krw_game_logs');
	}
}
