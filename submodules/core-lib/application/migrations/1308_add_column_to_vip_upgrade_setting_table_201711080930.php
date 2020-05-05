<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vip_upgrade_setting_table_201711080930 extends CI_Migration {

	public function up() {
		if (!$this->db->field_exists('before_cost', 'vip_upgrade_setting')){
			$fields = array(
				'accumulation' => array(
					'type' => 'INT',
					'null' => false,
					'default' => 0
				),
			);

			$this->dbforge->add_column('vip_upgrade_setting', $fields);
		}
	}

	public function down() {
		$this->dbforge->drop_column('vip_upgrade_setting', 'accumulation');
	}
	
}
