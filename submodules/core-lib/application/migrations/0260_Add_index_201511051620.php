<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_index_201511051620 extends CI_Migration {

	public function up() {
		//player_id, updated_at, payment_flag, payment_kind, status
		$this->db->query('create index idx_player_id on sale_orders(player_id)');
		$this->db->query('create index idx_updated_at on sale_orders(updated_at)');
		$this->db->query('create index idx_payment_flag on sale_orders(payment_flag)');
		$this->db->query('create index idx_payment_kind on sale_orders(payment_kind)');
		$this->db->query('create index idx_status on sale_orders(status)');
		//nt_game_logs, log_info_id
		$this->db->query('create index idx_log_info_id on nt_game_logs(log_info_id)');
	}

	public function down() {
		$this->db->query('drop index idx_player_id on sale_orders');
		$this->db->query('drop index idx_updated_at on sale_orders');
		$this->db->query('drop index idx_payment_flag on sale_orders');
		$this->db->query('drop index idx_payment_kind on sale_orders');
		$this->db->query('drop index idx_status on sale_orders');

		$this->db->query('drop index idx_log_info_id on nt_game_logs');
	}
}

///END OF FILE//////////