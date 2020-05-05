<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_table_ip_and_country_whitelist_20160716 extends CI_Migration {

	public function up() {
		$this->db->query("ALTER TABLE `country_whitelist` 
			ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT FIRST,
			DROP PRIMARY KEY,
			ADD PRIMARY KEY (`id`),
			ADD UNIQUE INDEX `index2` (`game_platform_id` ASC, `country` ASC)");

		$this->db->query("ALTER TABLE `ip_whitelist` 
			ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT FIRST,
			DROP PRIMARY KEY,
			ADD PRIMARY KEY (`id`),
			ADD UNIQUE INDEX `index2` (`game_platform_id` ASC, `ip_address` ASC)");
	}

	public function down() {
	}
}