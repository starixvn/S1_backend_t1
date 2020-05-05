<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_agin_gamelogs_201611011414 extends CI_Migration {

	private $tableName = 'agin_game_logs';

	public function up() {

		$this->db->query("ALTER TABLE agin_game_logs MODIFY bettime DATETIME ");

	}

	public function down() {
	}
}