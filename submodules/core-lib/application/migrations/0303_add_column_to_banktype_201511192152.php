<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_banktype_201511192152 extends CI_Migration {

	private $tableName = 'banktype';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'banktype_order' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 100,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'banktype_order');
	}
}

///END OF FILE//////////