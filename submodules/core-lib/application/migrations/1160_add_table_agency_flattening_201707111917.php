<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_flattening_201707111917 extends CI_Migration {

	private $tableName = 'agency_flattening';

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
			'master_agent_id' => array(
				'type' => 'INT',
			),
			'agent_wl' => array(
				 'type' => 'DECIMAL',
				 'constraint' => '9,2',
			) ,
			'agent_rolling' => array(
				 'type' => 'DECIMAL',
				 'constraint' => '9,2',
			) ,
			'agent_comm' => array(
				 'type' => 'DECIMAL',
				 'constraint' => '9,2',
			) ,
			'base_credit' => array(
				 'type' => 'DECIMAL',
				 'constraint' => '9,2',
			) ,
			'flattening' => array(
				 'type' => 'DECIMAL',
				 'constraint' => '9,2',
			) ,
			'period' => array(
				'type' => 'ENUM("daily","weekly","monthly","custom_daterange")' ,
				'default' => 'weekly' ,
			) ,
			'date_range_from' => array(
				'type' => 'DATETIME',
				'null' => true ,
			) ,
			'date_range_to' => array(
				'type' => 'DATETIME',
				'null' => true ,
			) ,
			'created_at' => array(
				'type' => 'DATETIME',
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
