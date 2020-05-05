<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_display_name_to_table_transactions_20151208 extends CI_Migration {

	public function up() {
		$fields = array(
			'display_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
		);
		$this->dbforge->add_column('transactions', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('transactions', $this->up->fields);
	}
	
}
