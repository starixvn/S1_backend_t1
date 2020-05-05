<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_extra_info_column_to_static_sites_201603201045 extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {
		$this->db->trans_start();

		$this->dbforge->add_column($this->tableName, array(
			'extra_info' => array(
				'type' => 'TEXT',
				'null' => true
			),
		));

		$this->db->where('site_name', 'default');
		$this->db->or_where('site_name', 'staging');
		$this->db->update($this->tableName, array(
			'extra_info' => '{"aff_analytic_code" : "", "player_analytic_code" : "", "admin_analytic_code" : "", "customer_service_code" : ""}',
		));

		$this->db->trans_complete();
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'extra_info');
	}
}
