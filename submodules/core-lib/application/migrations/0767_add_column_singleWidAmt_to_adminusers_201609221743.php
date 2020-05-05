<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_singleWidAmt_to_adminusers_201609221743 extends CI_Migration {

	private $tableName = 'adminusers';

	function up() {
		$this->dbforge->add_column($this->tableName, [
			'singleWidAmt' => array(
				'type' => 'INT',
				'constraint' => 40,
				'null' => true,
			),
		]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'singleWidAmt');
	}
}