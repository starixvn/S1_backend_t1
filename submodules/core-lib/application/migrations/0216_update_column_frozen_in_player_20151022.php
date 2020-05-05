<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_frozen_in_player_20151022 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE player CHANGE COLUMN frozen frozen DOUBLE NOT NULL DEFAULT 0 COMMENT ''");
	}

	public function down() {
		$this->db->query("ALTER TABLE player CHANGE COLUMN frozen frozen DOUBLE NULL COMMENT ''");
	}
}