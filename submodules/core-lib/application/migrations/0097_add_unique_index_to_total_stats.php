<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_index_to_total_stats extends CI_Migration {

	public function up() {

		//add field
		$fields = array(
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
		);
		$this->dbforge->add_column('total_player_game_hour', $fields);
		$this->dbforge->add_column('total_player_game_day', $fields);
		$this->dbforge->add_column('total_player_game_month', $fields);
		$this->dbforge->add_column('total_player_game_year', $fields);
		$this->dbforge->add_column('total_operator_game_hour', $fields);
		$this->dbforge->add_column('total_operator_game_day', $fields);
		$this->dbforge->add_column('total_operator_game_month', $fields);
		$this->dbforge->add_column('total_operator_game_year', $fields);

		$this->db->query('create unique index idx_unique_total_player_game_hour_uniqueid on total_player_game_hour(uniqueid)');
		$this->db->query('create unique index idx_unique_total_player_game_day_uniqueid on total_player_game_day(uniqueid)');
		$this->db->query('create unique index idx_unique_total_player_game_month_uniqueid on total_player_game_month(uniqueid)');
		$this->db->query('create unique index idx_unique_total_player_game_year_uniqueid on total_player_game_year(uniqueid)');
		$this->db->query('create unique index idx_unique_total_operator_game_hour_uniqueid on total_operator_game_hour(uniqueid)');
		$this->db->query('create unique index idx_unique_total_operator_game_day_uniqueid on total_operator_game_day(uniqueid)');
		$this->db->query('create unique index idx_unique_total_operator_game_month_uniqueid on total_operator_game_month(uniqueid)');
		$this->db->query('create unique index idx_unique_total_operator_game_year_uniqueid on total_operator_game_year(uniqueid)');
	}

	public function down() {
		$this->db->query('drop index idx_unique_total_player_game_hour_uniqueid on total_player_game_hour');
		$this->db->query('drop index idx_unique_total_player_game_day_uniqueid on total_player_game_day');
		$this->db->query('drop index idx_unique_total_player_game_month_uniqueid on total_player_game_month');
		$this->db->query('drop index idx_unique_total_player_game_year_uniqueid on total_player_game_year');
		$this->db->query('drop index idx_unique_total_operator_game_hour_uniqueid on total_operator_game_hour');
		$this->db->query('drop index idx_unique_total_operator_game_day_uniqueid on total_operator_game_day');
		$this->db->query('drop index idx_unique_total_operator_game_month_uniqueid on total_operator_game_month');
		$this->db->query('drop index idx_unique_total_operator_game_year_uniqueid on total_operator_game_year');

		$this->dbforge->drop_column('total_player_game_hour', 'uniqueid');
		$this->dbforge->drop_column('total_player_game_day', 'uniqueid');
		$this->dbforge->drop_column('total_player_game_month', 'uniqueid');
		$this->dbforge->drop_column('total_player_game_year', 'uniqueid');
		$this->dbforge->drop_column('total_operator_game_hour', 'uniqueid');
		$this->dbforge->drop_column('total_operator_game_day', 'uniqueid');
		$this->dbforge->drop_column('total_operator_game_month', 'uniqueid');
		$this->dbforge->drop_column('total_operator_game_year', 'uniqueid');

	}
}

///END OF FILE//////////