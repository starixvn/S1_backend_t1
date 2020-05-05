<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_cashback_settings_default_to_operator_settings_2015111321 extends CI_Migration {

	public function up() {
// 		$query = <<<EOD
// INSERT
// 	INTO `operator_settings`
// 	(`name`, `value`, `note`)
// 	VALUES
// 	('cashback_settings', '{"daysAgo":"1","fromHour":"12:00","toHour":"11:00","payTimeHour":"8:00", "lastUpdate":"2015-11-00 12:00"}', 'Cashback Settings Default in json format')
// EOD;
// 		$this->db->query($query);
	}

	public function down() {
		// $this->db->query('DELETE FROM operator_settings WHERE name = "cashback_settings"');
	}
}