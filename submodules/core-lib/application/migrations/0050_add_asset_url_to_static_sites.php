<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_asset_url_to_static_sites extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {

		//asset_url is for css/js/image ...
		$this->dbforge->add_column($this->tableName, array(
			'asset_url' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		));
		//update default asset_url
		$this->db->where('site_name', 'default');
		$this->db->update($this->tableName, array("asset_url" => "//og.local"));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'asset_url');
	}
}
