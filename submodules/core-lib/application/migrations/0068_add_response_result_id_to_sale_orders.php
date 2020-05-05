<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_response_result_id_to_sale_orders extends CI_Migration {

	private $tableName = 'sale_orders';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'response_result_id');
	}
}
