<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_transactions_20160516 extends CI_Migration {

	public function up() {
		// $fields = array(
		// 	'before_balance_info' => array(
		// 		'type' => 'TEXT',
		// 		'null' => true,
		// 	),
		// 	'after_balance_info' => array(
		// 		'type' => 'TEXT',
		// 		'null' => true,
		// 	),
		// );
		// $this->dbforge->add_column('transactions', $fields);
	}

	public function down() {
		// $this->dbforge->drop_column('transactions', 'before_balance_info');
		// $this->dbforge->drop_column('transactions', 'after_balance_info');
	}

}
