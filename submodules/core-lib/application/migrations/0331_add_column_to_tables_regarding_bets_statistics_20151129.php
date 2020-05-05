<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_tables_regarding_bets_statistics_20151129 extends CI_Migration {

	public function up() {
		$fields = array(
			'result_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
		);
		$this->db->trans_start();
		$this->dbforge->add_column('total_player_game_hour', $fields);
		$this->dbforge->add_column('total_player_game_month', $fields);
		$this->dbforge->add_column('total_player_game_year', $fields);
		$this->dbforge->add_column('total_operator_game_hour', $fields);
		$this->dbforge->add_column('total_operator_game_day', $fields);
		$this->dbforge->add_column('total_operator_game_month', $fields);
		$this->dbforge->add_column('total_operator_game_year', $fields);
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->trans_start();
		$this->dbforge->drop_column('total_player_game_hour', $this->up->fields);
		$this->dbforge->drop_column('total_player_game_month', $this->up->fields);
		$this->dbforge->drop_column('total_player_game_year', $this->up->fields);
		$this->dbforge->drop_column('total_operator_game_hour', $this->up->fields);
		$this->dbforge->drop_column('total_operator_game_day', $this->up->fields);
		$this->dbforge->drop_column('total_operator_game_month', $this->up->fields);
		$this->dbforge->drop_column('total_operator_game_year', $this->up->fields);
		$this->db->trans_complete();
	}
	
}
