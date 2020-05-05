<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_201608211915 extends CI_Migration {

	public function up() {
		$this->load->model(['player_model']);

		$this->player_model->addIndex('transactions', 'idx_player_promo_id', 'player_promo_id');
		$this->player_model->addIndex('transactions', 'idx_transaction_type', 'transaction_type');
		$this->player_model->addIndex('withdraw_conditions', 'idx_player_id', 'player_id');
		$this->player_model->addIndex('withdraw_conditions', 'idx_status', 'status');
		$this->player_model->addIndex('withdraw_conditions', 'idx_started_at', 'started_at');
		$this->player_model->addIndex('sale_orders', 'idx_timeout_at', 'timeout_at');

		$fields = array(
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true
			),
			'count_player_session'=>array(
				'type' => 'INT',
				'null' => true
			),
		);
		$this->dbforge->add_column('admin_dashboard', $fields);

	}

	public function down() {

		$this->dbforge->drop_column('admin_dashboard', 'updated_at');
		$this->dbforge->drop_column('admin_dashboard', 'count_player_session');

	}
}
