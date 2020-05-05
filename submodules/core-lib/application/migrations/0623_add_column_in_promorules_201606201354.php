<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_in_promorules_201606201354 extends CI_Migration {

	// private $tableName = 'promorules';

	public function up() {
		// $fields = array(
		// 	'withdrawShouldMinusDepositCondition' => array(
		// 		'type' => 'INT',
		// 		'null' => TRUE,
		// 		'default'=> 0,
		// 	),
		// );
		// $this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		// $this->dbforge->drop_column($this->tableName, 'withdrawShouldMinusDepositCondition');
	}
}