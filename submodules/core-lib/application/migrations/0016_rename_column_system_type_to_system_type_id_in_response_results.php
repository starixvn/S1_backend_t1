<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Rename_column_system_type_to_system_type_id_in_response_results extends CI_Migration {

	public function up() {
		$fields = array(
		        'system_type' => array(
		                'name' => 'system_type_id',
						'type' => 'INT',
						'null' => false,
		        ),
		);
		$this->dbforge->modify_column('response_results', $fields);
	}

	public function down() {
		$fields = array(
		        'system_type_id' => array(
		                'name' => 'system_type',
						'type' => 'INT',
						'null' => false,
		        ),
		);
		$this->dbforge->modify_column('response_results', $fields);
	}
}