<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_static_sites_201603082350 extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'company_title' => array(
				'type' => 'VARCHAR(400)',
				'null' => true
			),
			'contact_skype' => array(
				'type' => 'VARCHAR(400)',
				'null' => true
			),
			'contact_email' => array(
				'type' => 'VARCHAR(400)',
				'null' => true
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'auto_add_new_game');
	}
}
