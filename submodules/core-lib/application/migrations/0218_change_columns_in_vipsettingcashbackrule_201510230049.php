<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_columns_in_vipsettingcashbackrule_201510230049 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE vipsettingcashbackrule CHANGE COLUMN firsttime_dep_withdraw_condition firsttime_dep_withdraw_condition DOUBLE NULL");
		$this->db->query("ALTER TABLE vipsettingcashbackrule CHANGE COLUMN succeeding_dep_withdraw_condition succeeding_dep_withdraw_condition DOUBLE NULL");
		$this->db->query("ALTER TABLE vipsettingcashbackrule CHANGE COLUMN firsttime_dep_percentage_upto firsttime_dep_percentage_upto DOUBLE NULL");
		$this->db->query("ALTER TABLE vipsettingcashbackrule CHANGE COLUMN succeeding_dep_percentage_upto succeeding_dep_percentage_upto DOUBLE NULL");
	}

	public function down() {
		//don't callback
		// $this->db->query("ALTER TABLE vipsettingcashbackrule CHANGE COLUMN firsttime_dep_withdraw_condition firsttime_dep_withdraw_condition INT NULL");
	}
}