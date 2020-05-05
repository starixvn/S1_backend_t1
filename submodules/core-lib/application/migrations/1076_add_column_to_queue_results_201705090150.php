<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_queue_results_201705090150 extends CI_Migration {

	public function up() {
		$fields = array(
			'full_params' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column('queue_results', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('queue_results', 'full_params');
	}
}