<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_domain_list_201709250123 extends CI_Migration {

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'domain_name' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => true,
			),
			'notes' => array(
				'type' => 'VARCHAR',
				'constraint'=> 200,
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'created_by' => array(
				'type' => 'INT',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_by' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('domain_name');
		$this->dbforge->create_table('agency_domain');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'agency_domain_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('agent_id');
		$this->dbforge->add_key('agency_domain_id');
		$this->dbforge->create_table('agency_domain_permissions');

	}

	public function down() {

		$this->dbforge->drop_table('agency_domain');
		$this->dbforge->drop_table('agency_domain_permissions');

	}

}
