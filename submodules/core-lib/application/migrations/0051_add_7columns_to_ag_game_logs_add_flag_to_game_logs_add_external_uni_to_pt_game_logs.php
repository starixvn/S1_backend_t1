<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_7columns_to_ag_game_logs_add_flag_to_game_logs_add_external_uni_to_pt_game_logs extends CI_Migration {

	public function up() {
		$ag_game_logs_fields = array(
			'transfertype' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'transferamount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'previousAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'currentamount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'creationtime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => 64,
				'null' => false,
			),
		);
		$this->dbforge->add_column('ag_game_logs', $ag_game_logs_fields);
		$game_logs_fields = array(
			'flag' => array(
				'type' => 'INT',
				'unsigned' => true,
				'null' => false,
			),
		);
		$this->dbforge->add_column('game_logs', $game_logs_fields);
		$pt_game_logs_fields = array(
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,
				'null' => false,
			),
		);
		$this->dbforge->add_column('pt_game_logs', $pt_game_logs_fields);
	}

	public function down() {
		$this->dbforge->drop_column('ag_game_logs', $this->up->ag_game_logs_fields);
		$this->dbforge->drop_column('game_logs', $this->up->game_logs_fields);
		$this->dbforge->drop_column('pt_game_logs', $this->up->pt_game_logs_fields);
	}
}