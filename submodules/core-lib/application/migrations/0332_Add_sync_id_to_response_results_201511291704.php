<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_sync_id_to_response_results_201511291704 extends CI_Migration {

	private $tableName = 'response_results';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'sync_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName);
	}
}
