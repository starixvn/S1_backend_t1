<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_logs_id_on_agin_game_logs_201709301445 extends CI_Migration {

	public function up() {
		$fields = array(
			'logs_ID' => array(
				'name' => 'logs_ID',
				'type' => 'VARCHAR',
				'constraint'=>"30",
				'null' => false,
			),
		);
		$this->dbforge->modify_column('agin_game_logs', $fields);
	}

	public function down() {
		//ignore
	}
}
