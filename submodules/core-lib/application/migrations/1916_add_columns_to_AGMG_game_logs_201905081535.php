<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_AGMG_game_logs_201905081535 extends CI_Migration {

	private $tableName = 'agmg_game_logs';

	public function up() {

		$fields = array(
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
		
		$this->load->model('player_model');
		$this->player_model->addIndex('agmg_game_logs', 'idx_uniqueid', 'uniqueid', true);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'response_result_id');
		$this->dbforge->drop_column($this->tableName, 'uniqueid');

		
	}
}