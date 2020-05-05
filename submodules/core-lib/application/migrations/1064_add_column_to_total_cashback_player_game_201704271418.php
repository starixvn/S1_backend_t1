<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_total_cashback_player_game_201704271418 extends CI_Migration {

	public function up() {

		$fields = array(
			'cashback_request_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_column('total_cashback_player_game', $fields);

		$this->load->model(['player_model']);
		$this->player_model->addIndex('total_cashback_player_game', 'idx_cashback_request_id', 'cashback_request_id');
	}

	public function down() {
		$this->load->model(['player_model']);
		$this->player_model->dropIndex('total_cashback_player_game', 'idx_cashback_request_id');

		$this->dbforge->drop_column('total_cashback_player_game', 'cashback_request_id');
	}


}