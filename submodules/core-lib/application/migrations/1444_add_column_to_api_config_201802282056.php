<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_api_config_201802282056 extends CI_Migration {

	public function up() {
		$fields = array(
			'sync_enabled' => array(
				'type' => 'INT',
				'null' => TRUE,
				'default' => 0,
			),
		);

		$this->dbforge->add_column('api_config', $fields);

		$this->load->model(['player_model']);
		$this->player_model->addIndex('game_logs', 'idx_sync_index', 'sync_index');

	}

	public function down() {

		$this->dbforge->drop_column('api_config', 'sync_enabled');

	}
}
