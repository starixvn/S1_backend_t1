<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_tables_change_charset_201611121454 extends CI_Migration {

	private $tables = array('game_provider_auth','game_description','game_type','external_system','external_system_list','bank_list','payment_account','total_operator_game_day', 'total_operator_game_hour','total_operator_game_month', 'total_operator_game_year', 'total_player_game_day', 'total_player_game_hour', 'total_player_game_month', 'total_player_game_year');

	public function up() {

		foreach ($this->tables as $tableName) {
			$this->db->query("ALTER TABLE " . $tableName . " CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
		}

	}

	public function down() {

	}
}
