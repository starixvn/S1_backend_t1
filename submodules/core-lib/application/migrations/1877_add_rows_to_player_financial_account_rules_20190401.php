<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_player_financial_account_rules_20190401 extends CI_Migration {

	public function up() {

		$query = $this->db->query("SELECT * FROM player_financial_account_rules");
		$result = $query->row_array();

		if ($result) {
			return false;
		}

 		$this->db->query("
 		INSERT INTO `player_financial_account_rules` (`id`, `payment_type_flag`, `account_number_min_length`, `account_number_max_length`, `account_number_only_allow_numeric`, `account_name_allow_modify_by_players`, `field_show`, `field_required`)
 		VALUES
			(1, 1, 16,  19, 1, 0, '1,3', '1'),
			(2, 2,  1, 100, 1, 0, '1',   '1'),
			(3, 3,  1, 100, 0, 0, '1',   '');
		");
	}

	public function down() {
	}
}