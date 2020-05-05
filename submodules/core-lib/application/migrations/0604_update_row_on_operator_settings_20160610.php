<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_row_on_operator_settings_20160610 extends CI_Migration {
	private $tableName = 'operator_settings';
	public function up() {
		// $this->db->update($this->tableName, array('description_json' => '{"type":"checkbox","default_value":false,"label_lang":"Single Player Session"}'), array('name' => 'single_player_session'));
	}

	public function down() {
	}
}