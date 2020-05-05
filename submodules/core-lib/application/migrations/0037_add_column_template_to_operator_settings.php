<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_template_to_operator_settings extends CI_Migration {

	public function up() {
		$fields = array(
			'template' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('operator_settings', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('operator_settings', 'note');
	}
}