<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_wl_settlement_invoice_201706301155 extends CI_Migration {

	private $tableName = 'agency_wl_settlement_invoice';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
            'agent_id' => array(
                'type' => 'INT'
            ),
			'settlement_date_from' => array(
				'type' => 'DATETIME',
			),
			'settlement_date_to' => array(
				'type' => 'DATETIME',
			),
			'created_on' => array(
				'type' => 'DATETIME',
			),
			'updated_on' => array(
				'type' => 'DATETIME',
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
