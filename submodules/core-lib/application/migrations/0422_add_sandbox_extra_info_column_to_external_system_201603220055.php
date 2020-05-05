<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_sandbox_extra_info_column_to_external_system_201603220055 extends CI_Migration {

	public function up() {
		$this->db->trans_start();

		$this->dbforge->add_column('external_system', array(
			'sandbox_extra_info' => array(
				'type' => 'TEXT',
				'null' => true
			),
		));

		$this->dbforge->add_column('external_system_list', array(
			'sandbox_extra_info' => array(
				'type' => 'TEXT',
				'null' => true
			),
		));

		$this->db->trans_complete();
	}

	public function down() {
		$this->dbforge->drop_column('external_system', 'sandbox_extra_info');
		$this->dbforge->drop_column('external_system_list', 'sandbox_extra_info');
	}
}
