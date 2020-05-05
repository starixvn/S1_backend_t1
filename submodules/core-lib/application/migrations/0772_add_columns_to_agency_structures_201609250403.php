<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_agency_structures_201609250403 extends CI_Migration {

	private $tableName = 'agency_structures';

	public function up() {
		$fields = array(
			'show_bet_limit_template' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 1,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'show_bet_limit_template');
	}
}
