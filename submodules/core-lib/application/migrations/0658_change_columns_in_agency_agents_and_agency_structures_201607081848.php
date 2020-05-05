<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_columns_in_agency_agents_and_agency_structures_201607081848 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE agency_agents MODIFY rev_share decimal(5,2) NOT NULL DEFAULT 0");

		$this->db->query("ALTER TABLE agency_structures MODIFY rev_share decimal(5,2) NOT NULL DEFAULT 0");
	}

	public function down() {
		//don't callback
		// $this->db->query("ALTER TABLE vipsettingcashbackrule CHANGE COLUMN firsttime_dep_withdraw_condition firsttime_dep_withdraw_condition INT NULL");
	}
}
