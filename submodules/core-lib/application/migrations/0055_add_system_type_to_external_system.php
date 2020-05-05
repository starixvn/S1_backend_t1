<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_system_type_to_external_system extends CI_Migration {

	private $tableName = 'external_system';

	public function up() {

		//1 = game api, 2= payment
		$this->dbforge->add_column($this->tableName, array(
			'system_type' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

		$this->db->where_in('id', array(PT_API, AG_API, AG_FTP));
		$this->db->update($this->tableName, array("system_type" => SYSTEM_GAME_API));

		$this->db->where('id', IPS_PAYMENT_API);
		$this->db->update($this->tableName, array("system_type" => SYSTEM_PAYMENT));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'system_type');
	}
}
