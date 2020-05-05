<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_deleted_flag_to_promocmssetting_201706161435 extends CI_Migration {

	public function up() {
		$fields = array(
			'deleted_flag' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_column('promocmssetting', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('promocmssetting', 'deleted_flag');
	}
}
