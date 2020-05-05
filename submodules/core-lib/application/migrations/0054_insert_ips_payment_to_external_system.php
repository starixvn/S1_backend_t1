<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_ips_payment_to_external_system extends CI_Migration {

	private $tableName = 'external_system';

	public function up() {

		//update default asset_url
		$this->db->insert($this->tableName, array("id" => IPS_PAYMENT_API, "system_name" => "IPS_PAYMENT_API"));
	}

	public function down() {
		$this->db->delete($this->tableName, array('id' => IPS_PAYMENT_API));
	}
}
