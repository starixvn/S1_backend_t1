<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_playerdetails_column_proof_filename_datatype_20170420 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE playerdetails CHANGE proof_filename proof_filename TEXT");
		$this->db->trans_complete();
	}

	public function down() {
		$this->db->query("ALTER TABLE playerdetails CHANGE proof_filename proof_filename VARCHAR(20)");
		$this->db->trans_complete();
	}
}