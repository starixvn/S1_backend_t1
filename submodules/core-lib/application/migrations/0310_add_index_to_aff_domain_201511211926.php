<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_aff_domain_201511211926 extends CI_Migration {

	public function up() {
		$this->db->query('create unique index idx_aff_domain on affiliates(affdomain)');
	}

	public function down() {
		$this->db->query('drop index idx_aff_domain on affiliates');
	}
}

///END OF FILE//////////