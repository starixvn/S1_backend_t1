<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Edit_cashback_settings_default_to_operator_settings_201511162120 extends CI_Migration {

	public function up() {
// 		$this->db->trans_start();
// 		$query = <<<EOD
// UPDATE `operator_settings` SET value='{"daysAgo":"1","fromHour":"12:00","toHour":"11:00","payTimeHour":"14:00", "calcLastUpdate":"2015-11-14 12:00", "payLastUpdate":"2015-11-15 12:00"}' WHERE name ='cashback_settings'
// EOD;
// 		$this->db->query($query);
// 		$this->db->trans_complete();
// 		if ($this->db->trans_status() === FALSE) {
// 			echo "Error Occured";
// 		}
	}

	public function down() {
// 		$this->db->trans_start();
// 		$query = <<<EOD
// UPDATE `operator_settings` SET value='{"daysAgo":"1","fromHour":"12:00","toHour":"11:00","payTimeHour":"8:00", "lastUpdate":"2015-11-00 12:00"}' WHERE name ='cashback_settings'
// EOD;
// 		$this->db->query($query);
// 		$this->db->trans_complete();
// 		if ($this->db->trans_status() === FALSE) {
// 			echo "Error Occured";
// 		}
	}
}
