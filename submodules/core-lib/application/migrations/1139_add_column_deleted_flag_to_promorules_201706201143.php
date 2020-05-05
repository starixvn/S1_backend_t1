<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_deleted_flag_to_promorules_201706201143 extends CI_Migration {

	public function up() {
		$fields = array(
			'deleted_flag' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_column('promorules', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('promorules', 'deleted_flag');
	}
}
