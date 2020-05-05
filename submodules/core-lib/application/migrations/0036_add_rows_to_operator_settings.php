<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_operator_settings extends CI_Migration {

	public function up() {
// 		$this->db->query("INSERT INTO `operator_settings` (`id`, `name`, `value`, `note`)
// VALUES
// 	(4, 'previous_balance_set_amount', 100, 'checks previous balances of player and disables those balances equal or less the set amount and not include to the computation of withdraw conditions'),
// 	(5, 'non_promo_withdraw_setting', 2, 'withdraw condition for non promo with bet or no bet')
// ");
	}

	public function down() {
		// $this->db->query('DELETE * FROM `operator_settings');
	}
}