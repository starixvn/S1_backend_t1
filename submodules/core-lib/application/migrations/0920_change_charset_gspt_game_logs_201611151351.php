<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_charset_gspt_game_logs_201611151351 extends CI_Migration {

	public function up() {

		$this->db->query("ALTER TABLE gspt_game_logs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");

	}

	public function down() {

	}
}
