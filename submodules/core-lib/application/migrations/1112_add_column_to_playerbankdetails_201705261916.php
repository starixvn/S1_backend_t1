<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_playerbankdetails_201705261916 extends CI_Migration {

	public function up() {
		$fields = array(
			'external_id' => array(
				'type' => 'VARCHAR',
                'constraint' => '100',
				'null' => TRUE,
			),
		);

		$this->dbforge->add_column('playerbankdetails', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('playerbankdetails', 'external_id');
	}
}
