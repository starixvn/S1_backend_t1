<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_agent_and_structure_20171026 extends CI_Migration {

	public function up() {
		$fields = array(
			'admin_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'transaction_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'bonus_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
			'cashback_fee' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('agency_agents', $fields);
		$this->dbforge->add_column('agency_structures', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_agents', 'admin_fee');
		$this->dbforge->drop_column('agency_agents', 'transaction_fee');
		$this->dbforge->drop_column('agency_agents', 'bonus_fee');
		$this->dbforge->drop_column('agency_agents', 'cashback_fee');
		$this->dbforge->drop_column('agency_structures', 'admin_fee');
		$this->dbforge->drop_column('agency_structures', 'transaction_fee');
		$this->dbforge->drop_column('agency_structures', 'bonus_fee');
		$this->dbforge->drop_column('agency_structures', 'cashback_fee');
	}
}
