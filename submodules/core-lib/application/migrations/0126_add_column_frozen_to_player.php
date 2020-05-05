<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_frozen_to_player extends CI_Migration {

	public function up() {
		$fields = array(
			'frozen' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
		);
		$this->dbforge->add_column('player', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('player', 'frozen');
	}
}