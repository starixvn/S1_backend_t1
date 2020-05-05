<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_is_vip_show_done_201707271542 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$fields = array(
			'is_vip_show_done' => array(
				'type' => 'INT(2)',
				'null' => false,
				'default' => 0,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'is_vip_show_done');
	}
}

////END OF FILE////