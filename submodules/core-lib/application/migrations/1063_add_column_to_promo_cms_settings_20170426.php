<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promo_cms_settings_20170426 extends CI_Migration {

	public function up() {
		$fields = array(
			'tag_as_new_flag' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column('promocmssetting', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('promocmssetting', 'tag_as_new_flag');
	}
}