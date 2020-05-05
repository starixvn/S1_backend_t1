<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_import_payment_account_to__201606131121 extends CI_Migration {

	public function up() {
		// $this->load->model('operatorglobalsettings');
		// $this->operatorglobalsettings->importPaymentAccountSetting();
	}

	public function down() {
		// $this->dbforge->drop_column($this->tableName, 'ip_used');
	}
}
