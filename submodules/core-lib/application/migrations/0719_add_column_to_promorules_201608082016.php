<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201608082016 extends CI_Migration {
	public function up() {

		$fields = array(
			'disabled_pre_application' => array(
				'type' => 'INT',
				'null' => true,
				'default'=>0,
			),

		);
		$this->dbforge->add_column('promorules', $fields);

	}

	public function down() {

		$this->dbforge->drop_column('promorules', 'disabled_pre_application');

	}
}
