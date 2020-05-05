<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_charset_ezugi_gamelogs_201701091500 extends CI_Migration {

	public function up() {

		$this->db->query("ALTER TABLE ezugi_game_logs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");

	}

	public function down() {

	}
}