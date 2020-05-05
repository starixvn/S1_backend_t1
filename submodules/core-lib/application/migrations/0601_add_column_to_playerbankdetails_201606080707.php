<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_playerbankdetails_201606080707 extends CI_Migration {

	public function up() {
		/*$fields = array(
			'bankCode' => array(
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => true,
			),
		);
		$this->dbforge->add_column('playerbankdetails', $fields);*/
	}

	public function down() {
		/*$this->dbforge->drop_column('playerbankdetails', 'bankCode');*/
	}
}

