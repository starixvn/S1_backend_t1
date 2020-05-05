<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_shopping_center_201705100357 extends CI_Migration {

	public function up() {
		$fields = array(
			'hide_it_on_player_center' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column('shopping_center', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('shopping_center', 'hide_it_on_player_center');
	}
}