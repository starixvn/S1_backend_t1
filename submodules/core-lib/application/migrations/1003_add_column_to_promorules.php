<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules extends CI_Migration {

	public function up() {
		$fields = array(
			'always_join_promotion' => array(
				'type' => 'INT',
				'null' => true,
				'default' => '0',
			),
		);
		$this->dbforge->add_column('promorules', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('promorules', 'always_join_promotion');
	}
}
