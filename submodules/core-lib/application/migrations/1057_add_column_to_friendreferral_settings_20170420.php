<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_friendreferral_settings_20170420 extends CI_Migration {

	public function up() {
		$fields = array(
			'promo_id' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column('friendreferralsettings', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('friendreferralsettings', 'promo_id');
	}
}