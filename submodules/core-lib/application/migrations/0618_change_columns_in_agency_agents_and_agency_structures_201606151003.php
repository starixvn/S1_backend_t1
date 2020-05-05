<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_columns_in_agency_agents_and_agency_structures_201606151003 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE agency_agents MODIFY credit_limit decimal(11,2) NOT NULL DEFAULT 0");
		$this->db->query("ALTER TABLE agency_agents MODIFY available_credit decimal(11,2) NOT NULL DEFAULT 0");
		$this->db->query("ALTER TABLE agency_agents MODIFY rev_share decimal(4,2) NOT NULL DEFAULT 0");
		$this->db->query("ALTER TABLE agency_agents MODIFY rolling_comm decimal(4,2) NOT NULL DEFAULT 0");
		$this->db->query("ALTER TABLE agency_agents MODIFY vip_level varchar(200) DEFAULT NULL");

		$this->db->query("ALTER TABLE agency_structures MODIFY credit_limit decimal(11,2) NOT NULL DEFAULT 0");
		$this->db->query("ALTER TABLE agency_structures MODIFY available_credit decimal(11,2) NOT NULL DEFAULT 0");
		$this->db->query("ALTER TABLE agency_structures MODIFY rev_share decimal(4,2) NOT NULL DEFAULT 0");
		$this->db->query("ALTER TABLE agency_structures MODIFY rolling_comm decimal(4,2) NOT NULL DEFAULT 0");
		$this->db->query("ALTER TABLE agency_structures MODIFY vip_level varchar(200) DEFAULT NULL");
	}

	public function down() {
		//don't callback
		// $this->db->query("ALTER TABLE vipsettingcashbackrule CHANGE COLUMN firsttime_dep_withdraw_condition firsttime_dep_withdraw_condition INT NULL");
	}
}
