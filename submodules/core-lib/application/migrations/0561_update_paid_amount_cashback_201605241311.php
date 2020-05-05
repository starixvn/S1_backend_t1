<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_paid_amount_cashback_201605241311 extends CI_Migration {

	public function up() {
		$this->db->query('update total_cashback_player_game_daily set paid_amount=amount where paid_flag=1');
	}

	public function down() {

	}
}