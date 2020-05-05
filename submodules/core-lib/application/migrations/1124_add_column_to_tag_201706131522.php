<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_tag_201706131522 extends CI_Migration {

	public function up() {
		$fields = array(
			'tagColor' => array(
				'type' => 'VARCHAR',
                'constraint' => '12',
				'null' => TRUE,
			),
		);

		$this->dbforge->add_column('tag', $fields, 'tagDescription');

	}

	public function down() {
		$this->dbforge->drop_column('tag', 'tagColor');
	}
}
