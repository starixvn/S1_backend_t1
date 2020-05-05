<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_charset_201612071408 extends CI_Migration {

	public function up() {

		$this->db->query("ALTER TABLE kuma_game_logs CHANGE Username Username varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci");

		$this->db->query("ALTER TABLE kuma_game_logs CHANGE GameID GameID varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci");
	}

	public function down() {

	}
}
