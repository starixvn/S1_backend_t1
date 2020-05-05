<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_gsmg_game_logs_201612191758 extends CI_Migration {

	public function up() {
		$this->load->model(['player_model']);

		$fields = array(
			'game_account_without_prefix' => array(
				'type' => 'VARCHAR',
				'constraint'=>'100',
				'null' => true
			),
		);

		$this->dbforge->add_column('gsmg_game_logs', $fields);

		$this->player_model->addIndex('gsmg_game_logs', 'idx_game_end_time', 'game_end_time');
		$this->player_model->addIndex('gsmg_game_logs', 'idx_account_number', 'account_number');
		$this->player_model->addIndex('gsmg_game_logs', 'idx_game_account_without_prefix', 'game_account_without_prefix');

	}

	public function down() {
		$this->load->model(['player_model']);

		$this->player_model->dropIndex('gsmg_game_logs', 'idx_game_end_time');
		$this->player_model->dropIndex('gsmg_game_logs', 'idx_account_number');
		$this->player_model->dropIndex('gsmg_game_logs', 'idx_game_account_without_prefix');

		$this->dbforge->drop_column('gsmg_game_logs', 'game_account_without_prefix');

	}
}
