<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promocmssetting_201606270324 extends CI_Migration {

	public function up() {
		$fields = array(
			'promo_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
		);
		$this->dbforge->add_column('promocmssetting', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('promocmssetting', 'promo_code');
	}
}