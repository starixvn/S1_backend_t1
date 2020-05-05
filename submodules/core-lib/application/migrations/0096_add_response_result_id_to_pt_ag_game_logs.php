<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_response_result_id_to_pt_ag_game_logs extends CI_Migration {

	public function up() {
		$fields = array(
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('ag_game_logs', $fields);
		$this->dbforge->add_column('pt_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('ag_game_logs', 'response_result_id');
		$this->dbforge->drop_column('pt_game_logs', 'response_result_id');
	}
}
///END OF FILE/////////////