<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_to_bigint_for_totals_201604291522 extends CI_Migration {

	// private $tableName = 'external_system';

	public function up() {

		$this->db->query('alter table total_player_game_minute modify column id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->db->query('alter table total_player_game_hour modify column id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->db->query('alter table total_player_game_day modify column id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->db->query('alter table total_player_game_month modify column id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->db->query('alter table total_player_game_year modify column id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');

		$this->db->query('alter table total_operator_game_minute modify column id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->db->query('alter table total_operator_game_hour modify column id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->db->query('alter table total_operator_game_day modify column id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->db->query('alter table total_operator_game_month modify column id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->db->query('alter table total_operator_game_year modify column id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');

		// $this->dbforge->modify_column('total_player_game_minute', array(
		// 	'id' => array(
		// 		'type' => 'BIGINT',
		// 		'null' => false,
		// 	),
		// ));
	}

	public function down() {
		// $this->dbforge->drop_column($this->tableName, 'extra_info');
	}
}

///END OF FILE//////////