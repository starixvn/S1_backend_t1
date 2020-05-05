<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_metadata_for_special_payment_list_201606121000 extends CI_Migration {
	private $tableName = 'operator_settings';
	public function up() {
		// $data = array(
		// 	'note' => 'Defines which 3rd party payment to appear in the sidebar during withdrawal. Comma separated collection account IDs.',
		// 	'description_json' => '{"type":"text","default_value":""}'
		// );
		// $this->db->where(array('name' => 'special_payment_list'));
		// $this->db->update($this->tableName, $data);
	}

	public function down() {
		# no rollback action required
	}
}