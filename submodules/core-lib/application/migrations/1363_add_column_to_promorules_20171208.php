<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_20171208 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('promorules', array(
			'enable_edit' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 1,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('promorules', 'enable_edit');
	}
}