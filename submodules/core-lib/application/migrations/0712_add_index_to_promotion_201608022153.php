<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_promotion_201608022153 extends CI_Migration {

	public function up() {

		$this->load->model(['player_model']);
		$this->player_model->addIndex('withdraw_conditions','idx_promotion_id' , 'promotion_id');
		$this->player_model->addIndex('withdraw_conditions','idx_source_id' , 'source_id');
		$this->player_model->addIndex('withdraw_conditions','idx_player_id' , 'player_id');
		$this->player_model->addIndex('transactions','idx_player_promo_id' , 'player_promo_id');
		$this->player_model->addIndex('transactions','idx_promo_category' , 'promo_category');
		$this->player_model->addIndex('sale_orders','idx_timeout_at' , 'timeout_at');
		$this->player_model->addIndex('sale_orders','idx_created_at' , 'created_at');

	}

	public function down() {
		$this->db->query('drop index idx_promotion_id on withdraw_conditions');
		$this->db->query('drop index idx_source_id on withdraw_conditions');
		$this->db->query('drop index idx_player_id on withdraw_conditions');
		$this->db->query('drop index idx_player_promo_id on transactions');
		$this->db->query('drop index idx_promo_category on transactions');
		$this->db->query('drop index idx_timeout_at on sale_orders');
		$this->db->query('drop index idx_created_at on sale_orders');
	}
}

///END OF FILE//////////