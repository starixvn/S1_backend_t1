<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_charset_ag_gamelogs_201611082125 extends CI_Migration {

	public function up() {

		$this->db->query("ALTER TABLE ag_game_logs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");

	}

	public function down() {

	}
}
