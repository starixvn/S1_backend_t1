<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_affiliates_201712062106 extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {
		$fields = array(
			'deleted_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'deleted_at');
	}
}

////END OF FILE////