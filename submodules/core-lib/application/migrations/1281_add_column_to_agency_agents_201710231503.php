<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_agents_201710231503 extends CI_Migration {

	public function up() {
		$fields = array(
			'firstname' => array(
				'type' => 'VARCHAR',
                'constraint' => 50,
				'null' => true,
			),
			'lastname' => array(
				'type' => 'VARCHAR',
                'constraint' => 50,
				'null' => true,
			),
			'email' => array(
				'type' => 'VARCHAR',
                'constraint' => 100,
				'null' => true,
			),
			'gender' => array(
				'type' => 'VARCHAR',
                'constraint' => 10,
				'null' => true,
			),
			'mobile' => array(
				'type' => 'VARCHAR',
                'constraint' => 30,
				'null' => true,
			),
			'im1' => array(
				'type' => 'VARCHAR',
                'constraint' => 50,
				'null' => true,
			),
			'im2' => array(
				'type' => 'VARCHAR',
                'constraint' => 50,
				'null' => true,
			),
		);
		$this->dbforge->add_column('agency_agents', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_agents', 'firstname');
		$this->dbforge->drop_column('agency_agents', 'lastname');
		$this->dbforge->drop_column('agency_agents', 'email');
		$this->dbforge->drop_column('agency_agents', 'gender');
		$this->dbforge->drop_column('agency_agents', 'mobile');
		$this->dbforge->drop_column('agency_agents', 'im1');
		$this->dbforge->drop_column('agency_agents', 'im2');
	}

}
