<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_response_results_201805141446 extends CI_Migration {

	private $tableName = 'response_results';

	public function up() {

		$fields = array(
			'decoded_result_text' => array(
				'type' => 'TEXT',
				'null' => true,
			),

		);

		$this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'decoded_result_text');
	}
}
