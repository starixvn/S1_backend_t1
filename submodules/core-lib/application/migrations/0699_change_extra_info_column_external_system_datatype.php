<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_extra_info_column_external_system_datatype extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE external_system CHANGE extra_info extra_info TEXT");
	}

	public function down() {
		$this->db->query("ALTER TABLE external_system CHANGE extra_info extra_info VARCHAR(4000)");
	}
}