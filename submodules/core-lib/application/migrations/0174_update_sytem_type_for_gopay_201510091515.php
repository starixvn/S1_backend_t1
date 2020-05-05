<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_sytem_type_for_gopay_201510091515 extends CI_Migration {

	public function up() {
		$this->db->set('system_type', SYSTEM_PAYMENT)->where('id', GOPAY_PAYMENT_API)->update('external_system');
	}

	public function down() {
	}
}