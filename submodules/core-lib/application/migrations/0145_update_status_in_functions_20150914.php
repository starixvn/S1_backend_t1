<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_status_in_functions_20150914 extends CI_Migration {

	// private $tableName = 'functions';

	public function up() {
		// $this->db->set('status', 2);
		// $this->db->where_in('funcId', [48,49,50,53,54,55,56,60,61,62,17,18,19]);
		// $this->db->update($this->tableName);
	}

	public function down() {
		// $this->db->set('status', 1);
		// $this->db->where_in('funcId', [48,49,50,53,54,55,56,60,61,62,17,18,19]);
		// $this->db->update($this->tableName);
	}
}