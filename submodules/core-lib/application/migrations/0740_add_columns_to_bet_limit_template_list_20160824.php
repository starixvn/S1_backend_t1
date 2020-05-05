<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_bet_limit_template_list_20160824 extends CI_Migration {

	private $tableName = 'bet_limit_template_list';

	public function up() {
		$fields = array(
			'default_template' => array(
				'type' => 'INT',
				'default' => 0,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'default_template');
	}

}
