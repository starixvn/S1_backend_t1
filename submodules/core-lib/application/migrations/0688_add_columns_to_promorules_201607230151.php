<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_promorules_201607230151 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {
		$fields = array(
			'bonusApplicationLimitDateType' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'bonusApplicationLimitDateType');
	}
}
