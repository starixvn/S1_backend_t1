<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_affdomain_to_aff_201511211748 extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'affdomain' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'affdomain');
	}
}

///END OF FILE//////////