<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_cols_to_impt_game_logs_201604261632 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('impt_game_logs', array(
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'gameshortcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

		$this->db->query('create unique index idx_uniqueid on impt_game_logs(uniqueid)');
		$this->db->query('create unique index idx_external_uniqueid on impt_game_logs(external_uniqueid)');
		$this->db->query('create index idx_gameshortcode on impt_game_logs(gameshortcode)');
		$this->db->query('create index idx_player_name on impt_game_logs(PlayerName)');
		$this->db->query('create index idx_game_date on impt_game_logs(GameDate)');

	}

	public function down() {
		$this->db->query('drop index idx_uniqueid on impt_game_logs');
		$this->db->query('drop index idx_external_uniqueid on impt_game_logs');
		$this->db->query('drop index idx_gameshortcode on impt_game_logs');
		$this->db->query('drop index idx_player_name on impt_game_logs');
		$this->db->query('drop index idx_game_date on impt_game_logs');

		$this->dbforge->drop_column('impt_game_logs', 'uniqueid');
		$this->dbforge->drop_column('impt_game_logs', 'gameshortcode');
		$this->dbforge->drop_column('impt_game_logs', 'external_uniqueid');
		$this->dbforge->drop_column('impt_game_logs', 'response_result_id');
	}
}
