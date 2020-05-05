<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_index_from_playeraccount_201510210137 extends CI_Migration {

	public function up() {
		$this->db->query('alter table playeraccount drop foreign key FK_playeraccount');
	}

	public function down() {
	}
}

///END OF FILE//////////