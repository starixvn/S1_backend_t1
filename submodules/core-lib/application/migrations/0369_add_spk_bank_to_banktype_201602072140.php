<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_spk_bank_to_banktype_201602072140 extends CI_Migration {

	private $tableName = 'banktype';

	public function up() {
		//spb
		$this->db->insert($this->tableName,
			array(
				"bankName" => "bank_type_spdb",
				"createdOn" => $this->utils->getNowForMysql(),
				"updatedOn" => $this->utils->getNowForMysql(),
				"createdBy" => 1,
				"updatedBy" => 1,
				"status" => 'active',
				"show_on_player" => 1,
				"default_payment_flag" => 1,
				"banktype_order" => 100,
				"enabled_withdrawal" => 1,
				"enabled_deposit" => 1,
			)
		);

	}

	public function down() {
	}
}

///END OF FILE///////////////