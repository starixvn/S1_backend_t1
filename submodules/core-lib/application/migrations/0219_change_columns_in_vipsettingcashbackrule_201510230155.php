<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_columns_in_vipsettingcashbackrule_201510230155 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE vipsettingcashbackrule CHANGE COLUMN cashback_percentage cashback_percentage DOUBLE NULL");
	}

	public function down() {
		//don't callback
		// $this->db->query("ALTER TABLE vipsettingcashbackrule CHANGE COLUMN firsttime_dep_withdraw_condition firsttime_dep_withdraw_condition INT NULL");
	}
}