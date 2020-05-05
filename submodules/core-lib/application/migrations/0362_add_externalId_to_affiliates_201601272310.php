<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_externalId_to_affiliates_201601272310 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('affiliates', array(
			'externalId' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('affiliates', 'externalId');
	}
}