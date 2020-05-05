<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_flattening_options_201707111915 extends CI_Migration {

	private $tableName = 'agency_flattening_options';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'agent_id' => array(
				'type' => 'INT',
			),
			'base_credit' => array(
				 'type' => 'DECIMAL',
				 'constraint' => '9,2',
			) ,
			'period' => array(
				'type' => 'ENUM("daily","weekly","monthly","custom_daterange")' ,
				'default' => 'weekly' ,
			) ,
			'custom_daterange_from' => array(
				'type' => 'DATETIME',
				'null' => true ,
			) ,
			'custom_daterange_to' => array(
				'type' => 'DATETIME',
				'null' => true ,
			) ,
			'updated_at' => array(
				'type' => 'TIMESTAMP',
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

        $this->db->query('ALTER TABLE `agency_flattening_options` ADD UNIQUE INDEX `agency_flattening_options_agent_id` (`agent_id`)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
