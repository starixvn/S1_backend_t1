<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_operator_settings_201511121327 extends CI_Migration {

	public function up() {
		// $this->db->query("INSERT INTO `operator_settings` (`name`, `value`)
		// 			VALUES
		// 				('min_withdraw', '200')
		// 			");
  //       $this->dbforge->drop_column('operator_settings', 'withdraw_min_amount');//shoud not use field
	}

	public function down() {
		// $this->db->query('DELETE FROM operator_settings WHERE name=min_withdraw');
	}
}