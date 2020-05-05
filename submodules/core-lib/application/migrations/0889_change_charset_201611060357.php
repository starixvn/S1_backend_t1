<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_charset_201611060357 extends CI_Migration {

	public function up() {

		$this->db->query("ALTER TABLE impt_game_logs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");

	}

	public function down() {

	}
}
