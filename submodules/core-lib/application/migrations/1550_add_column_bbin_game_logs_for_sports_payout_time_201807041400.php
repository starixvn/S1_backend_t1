<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_bbin_game_logs_for_sports_payout_time_201807041400 extends CI_Migration {
	private $tableName = 'bbin_game_logs';

	public function up() {
		if (!$this->db->field_exists('payout_time', $this->tableName)) {
			$fields = array(
				'payout_time' => array(
					'type' => 'DATETIME',
					'null' => true,
				),
			);
			$this->dbforge->add_column($this->tableName, $fields);
		}
	}

	public function down() {
	}
}