<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_static_sites extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'site_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'site_url' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => false,
			),
			'template_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'template_path' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'notes' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => true,
			),

		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('static_sites');
	}

	public function down() {
		$this->dbforge->drop_table('static_sites');
	}
}
