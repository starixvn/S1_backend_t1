<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_delete_foreign_for_game_table_201604051536 extends CI_Migration {

	public function up() {
		$this->db->query('alter table vipsettingcashbackbonuspergame drop FOREIGN key FK_vipsettingcashbackbonuspergame_gi');
		$this->db->query('alter table playergame drop FOREIGN key FK_playergame_gi');
	}

	public function down() {
	}
}
