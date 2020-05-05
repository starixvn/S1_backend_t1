<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_external_system_id_column_in_banktype_201606220616 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE banktype CHANGE COLUMN `external_system_id` `external_system_id` VARCHAR(64) NULL DEFAULT NULL COMMENT ''");
	}

	public function down() {

	}
}