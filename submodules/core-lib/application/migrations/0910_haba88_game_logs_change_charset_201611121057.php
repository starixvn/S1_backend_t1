<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_haba88_game_logs_change_charset_201611121057 extends CI_Migration {

	public function up() {

		$this->db->query("ALTER TABLE haba88_game_logs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");

	}

	public function down() {

	}
}
