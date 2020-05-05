<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OG-698
 *
 *
 */
class Migration_Add_created_by_to_payment_account extends CI_Migration {

	private $tableName = 'payment_account';

	public function up() {
		$fields = array(
			'created_by_userid' => array(
				'type' => 'INT',
				'null' => true,
			),
			'updated_by_userid' => array(
				'type' => 'INT',
				'null' => true,
			),
			'notes' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'created_by_userid');
		$this->dbforge->drop_column($this->tableName, 'updated_by_userid');
		$this->dbforge->drop_column($this->tableName, 'notes');
	}
}

///END OF FILE/////