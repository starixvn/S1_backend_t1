<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_for_playeraccount_201607141729 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_type on playeraccount(type)');
		$this->db->query('create index idx_typeid on playeraccount(typeId)');
	}

	public function down() {
		$this->db->query('drop index idx_type on playeraccount');
		$this->db->query('drop index idx_typeid on playeraccount');
	}
}
