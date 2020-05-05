<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_kyc_status_column_kyc_player_datatype extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE kyc_player CHANGE kyc_status kyc_status TEXT");
		$this->db->query("ALTER TABLE kyc_player CHANGE generated_by generated_by TEXT");
	}

	public function down() {
		$this->db->query("ALTER TABLE kyc_player CHANGE kyc_status kyc_status VARCHAR(4000)");
		$this->db->query("ALTER TABLE kyc_player CHANGE generated_by generated_by INT");
	}
}