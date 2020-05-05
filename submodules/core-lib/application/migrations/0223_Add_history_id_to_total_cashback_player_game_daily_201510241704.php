<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 *
 */
class Migration_Add_history_id_to_total_cashback_player_game_daily_201510241704 extends CI_Migration {

	private $tableName = 'total_cashback_player_game_daily';

	public function up() {

		$fields = array(
			'history_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

		$this->db->query('create index idx_total_date on total_cashback_player_game_daily(total_date)');
		$this->db->query('create index idx_player_id on total_cashback_player_game_daily(player_id)');
		$this->db->query('create index idx_game_description_id on total_cashback_player_game_daily(game_description_id)');

	}

	public function down() {
		$this->db->query('drop index idx_total_date on total_cashback_player_game_daily');
		$this->db->query('drop index idx_player_id on total_cashback_player_game_daily');
		$this->db->query('drop index idx_game_description_id on total_cashback_player_game_daily');

		$this->dbforge->drop_column($this->tableName, 'history_id');
	}
}

///END OF FILE/////