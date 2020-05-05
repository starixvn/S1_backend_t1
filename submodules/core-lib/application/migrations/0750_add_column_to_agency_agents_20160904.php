<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_agents_20160904 extends CI_Migration {
	private $tableName = 'agency_agents';
	

	public function up() {
		$fields = array(
			'bet_limit_id' => array(
				'type' => 'INT',
				'null' => true,
				'default' => -1,
			),
		);
		$this->dbforge->add_column('agency_agents', $fields);

		$fields = array(
			'public_to_downline' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->add_column('bet_limit_template_list', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_agents', 'bet_limit_id');
		$this->dbforge->drop_column('bet_limit_template_list', 'public_to_downline');
	}

}
