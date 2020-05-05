<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_deleted_to_promotype_201606061609 extends CI_Migration {

	public function up() {
		$fields = array(
			'deleted' => array(
				'type' => 'INT',
				'constraint' => 1,
				'default' => '0',
			),
		);
		$this->dbforge->add_column('promotype', $fields);
		//echo $this->db->last_query();
	}

	public function down() {
		$this->dbforge->drop_column('promotype', 'deleted');
	}
}