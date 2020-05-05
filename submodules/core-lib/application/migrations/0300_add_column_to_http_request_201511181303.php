<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_http_request_201511181303 extends CI_Migration {

	private $tableName = 'http_request';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'source_site' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'source_site');
	}
}

///END OF FILE//////////