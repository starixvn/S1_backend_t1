<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201608151335 extends CI_Migration {
	public function up() {

		$fields = array(
			'show_on_active_available' => array(
				'type' => 'INT',
				'null' => true,
				'default'=>0,
			),

		);
		$this->dbforge->add_column('promorules', $fields);

	}

	public function down() {

		$this->dbforge->drop_column('promorules', 'show_on_active_available');

	}
}
