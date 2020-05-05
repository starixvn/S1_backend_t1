<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_ttg_game_logs_columns_20160525 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE ttg_game_logs
CHANGE COLUMN `handId` `handId` VARCHAR(64) NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `transactionId` `transactionId` VARCHAR(64) NULL DEFAULT NULL COMMENT ''");
	}

	public function down() {

	}
}