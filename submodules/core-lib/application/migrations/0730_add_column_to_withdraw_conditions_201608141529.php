<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_withdraw_conditions_201608141529 extends CI_Migration {
	public function up() {
		$fields = array(
			'note' => array(
				'type' => 'TEXT',
				'null' => true
			),
		);
		$this->dbforge->add_column('withdraw_conditions', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('withdraw_conditions', 'note');
	}
}
