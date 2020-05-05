<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_fields_to_promorules_201805271211 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {

		$fields = array(
			'max_bonus_by_limit_date_type' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0,
			),

		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'max_bonus_by_limit_date_type');
	}
}
