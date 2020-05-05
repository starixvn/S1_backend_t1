<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_agents_table_20171122 extends CI_Migration {

	public function up() {
		$fields = array(
			'withdraw_password_md5' => array(
				'type' => 'VARCHAR',
                'constraint' => 32,
				'null' => true,
			),
		);
		$this->dbforge->add_column('agency_agents', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('agency_agents', 'withdraw_password_md5');
	}
	
}
