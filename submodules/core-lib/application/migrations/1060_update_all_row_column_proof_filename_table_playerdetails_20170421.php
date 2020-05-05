<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_all_row_column_proof_filename_table_playerdetails_20170421 extends CI_Migration {

	public function up() {

		$this->db->trans_start();

		$this->db->update('playerdetails', array(
							'proof_filename' => null
						));

		$this->db->trans_complete();
	}

	public function down() {

	}
}