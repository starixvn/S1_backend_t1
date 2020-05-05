<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_order_id_to_game_type extends CI_Migration {

	public function up() {
		$fields = array(
			'order_id' => array(
				'type' => 'INT',
				'unsigned' => false,
				'null' => true,
			),
		);
		$this->dbforge->add_column('game_type', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('game_type', 'order_id');
	}
}