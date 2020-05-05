<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_key_from_agency_wl_settlement_201707111120 extends CI_Migration {

	private $tableName = 'agency_wl_settlement';

	public function up() {

        $this->db->query('drop index agency_wl_settlement_idx on agency_wl_settlement');
        $this->db->query('ALTER TABLE `agency_wl_settlement` ADD UNIQUE INDEX `agency_wl_settlement_idx` (`type`, `user_id`, `settlement_date_from`)');
	}

	public function down() {
        $this->db->query('drop index agency_wl_settlement_idx on agency_wl_settlement');
        $this->db->query('ALTER TABLE `agency_wl_settlement` ADD UNIQUE INDEX `agency_wl_settlement_idx` (`type`, `user_id`, `settlement_date_from`, `settlement_date_to`)');
	}
}
