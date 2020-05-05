<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_collection_and_promo_agent_20171102 extends CI_Migration {

	private $tableName1 = 'payment_account_agent';
	private $tableName2 = 'promorulesallowedagent';

	public function up() {
		
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'payment_account_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName1);

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'promoruleId' => array(
				'type' => 'INT',
				'null' => false,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName2);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName1);
		$this->dbforge->drop_table($this->tableName2);
	}
}