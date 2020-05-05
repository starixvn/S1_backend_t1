<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Set_auto_increment_to_tables extends CI_Migration {

	public function up() {
		$this->db->query('alter table sale_orders AUTO_INCREMENT=130853;');
		$this->db->query('alter table player AUTO_INCREMENT=130853;');
	}

	public function down() {
	}
}

///END OF FILE//////////