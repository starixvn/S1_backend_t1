<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_notes_column_datatype_201609200440 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE playernotes CHANGE notes notes TEXT");
	}

	public function down() {
		$this->db->query("ALTER TABLE playernotes CHANGE notes notes VARCHAR(250)");
	}
}