<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promocmssetting_20170518 extends CI_Migration {

	public function up() {
		$fields = array(
			'promo_category' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),

		);

		$this->dbforge->add_column('promocmssetting', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('promocmssetting', 'promo_category');
	}
}
