<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_tables_regarding_bets_statistics extends CI_Migration {

	public function up() {
		$fields = array(
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => false,
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
	}

	public function down() {
		$this->dbforge->drop_column('total_player_game_hour', $this->up->fields);
		$this->dbforge->drop_column('total_player_game_day', $this->up->fields);
		$this->dbforge->drop_column('total_player_game_month', $this->up->fields);
		$this->dbforge->drop_column('total_player_game_year', $this->up->fields);
		$this->dbforge->drop_column('total_operator_game_hour', $this->up->fields);
		$this->dbforge->drop_column('total_operator_game_day', $this->up->fields);
		$this->dbforge->drop_column('total_operator_game_month', $this->up->fields);
		$this->dbforge->drop_column('total_operator_game_year', $this->up->fields);
	}
}
