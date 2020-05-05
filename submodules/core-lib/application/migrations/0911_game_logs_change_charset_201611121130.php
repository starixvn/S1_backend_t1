<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_game_logs_change_charset_201611121130 extends CI_Migration {

	public function up() {

		$this->db->query("ALTER TABLE entwine_game_logs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
		$this->db->query("ALTER TABLE fg_game_logs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
		$this->db->query("ALTER TABLE oneworks_game_logs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
		$this->db->query("ALTER TABLE isb_game_logs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
		$this->db->query("ALTER TABLE isb_raw_game_logs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");

	}

	public function down() {

	}
}
