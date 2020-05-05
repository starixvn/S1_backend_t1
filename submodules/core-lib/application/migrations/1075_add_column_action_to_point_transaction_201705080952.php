<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_action_to_point_transaction_201705080952 extends CI_Migration {

	public function up() {
		$fields = array(
			'action' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column('point_transactions', $fields);
	}

	public function down() {
		if ( $this->db->table_exists('point_transactions')){
		$this->dbforge->drop_column('point_transactions','action');
		}
	}
}