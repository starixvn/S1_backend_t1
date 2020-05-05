<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_sagaming_game_logs_052220171435 extends CI_Migration {

	public function up() {
		$fields = array(
			'extGameCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
		);
		$this->dbforge->add_column('sagaming_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('sagaming_game_logs', 'extGameCode');
	}
}