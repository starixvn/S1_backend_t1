<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_value_column_changes_length_20160701172400 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE operator_settings CHANGE value value VARCHAR(300)");
	}

	public function down() {
		// $this->db->query("ALTER TABLE operator_settings CHANGE value value VARCHAR(300)");
	}
}