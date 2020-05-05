<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_data_and_lang_to_static_sites extends CI_Migration {

	public function up() {

		$this->dbforge->add_column('static_sites', array(
			'lang' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => false,
				'default' => 'english',
			),
		));

		//insert rows
		$this->load->model('static_site');
		$this->static_site->createSite('default', 'http://www.og.local', 'chinese', 'black_and_red', 'sites/black_and_red');
	}

	public function down() {
		$this->load->model('static_site');
		$this->static_site->db->delete('static_sites', array('site_name' => 'default'));
		$this->dbforge->drop_column('static_sites', 'lang');
	}
}
