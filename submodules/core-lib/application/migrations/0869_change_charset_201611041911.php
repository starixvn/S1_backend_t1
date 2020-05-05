<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_charset_201611041911 extends CI_Migration {

	public function up() {

		$this->db->query("ALTER TABLE game_provider_auth CHANGE login_name login_name varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci");

		$this->db->query("ALTER TABLE game_description CHANGE game_code game_code varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci");

		$this->db->query("ALTER TABLE game_description CHANGE external_game_id external_game_id varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci");

		$this->db->query("ALTER TABLE game_description CHANGE english_name english_name varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci");

	}

	public function down() {

	}
}
