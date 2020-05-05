<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promo_cms_settings_201704270435 extends CI_Migration {

	public function up() {
		$fields = array(
			'is_default_banner_flag' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column('promocmssetting', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('promocmssetting','is_default_banner_flag');
	}
}