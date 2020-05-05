<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 *
 */
class Migration_Add_history_date_to_player_level_history_and_sites_201510241230 extends CI_Migration {

	private $tableName = 'player_level_history';

	public function up() {
		$fields = array(
			'history_date' => array(
				'type' => 'DATE',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'cashback_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'cashback_maxbonus' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);

		$this->db->query('create unique index idx_unique_player_id_date on player_level_history(player_id, history_date)');
	}

	public function down() {

		$this->db->query('drop index idx_unique_player_id_date on player_level_history');

		$this->dbforge->drop_column($this->tableName, 'history_date');
		$this->dbforge->drop_column($this->tableName, 'updated_at');
		$this->dbforge->drop_column($this->tableName, 'cashback_percentage');
		$this->dbforge->drop_column($this->tableName, 'cashback_maxbonus');
	}
}

///END OF FILE/////