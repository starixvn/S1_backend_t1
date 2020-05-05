<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_gd_game_logs_201605111650 extends CI_Migration {

	private $tableName = 'gd_game_logs';

	public function up() {

		$this->db->trans_start();
		$this->dbforge->add_column($this->tableName, [
			'time'=> array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'jackpot_win' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'gd_game_type'=> array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
		]);

	    $this->db->trans_complete();
	
	}

	public function down() {
		$this->db->trans_start();
		$this->dbforge->drop_column($this->tableName, 'time');
		$this->dbforge->drop_column($this->tableName, 'jackpot_win');
		$this->dbforge->drop_column($this->tableName, 'status');
		$this->dbforge->drop_column($this->tableName, 'gd_game_type');
		$this->db->trans_complete();
	
		
	}
}