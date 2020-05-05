<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_playerupdatehistory_column_changes_length extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE playerupdatehistory CHANGE changes changes VARCHAR(2000)");
	}

	public function down() {
		$this->db->query("ALTER TABLE playerupdatehistory CHANGE changes changes VARCHAR(200)");
	}
}