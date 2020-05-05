<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_description_json_to_operator_settings_201606080039 extends CI_Migration {

	private $tableName = 'operator_settings';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'description_json' => array(
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
		]);

		$json = json_encode(array(
			"type" => "text", "default_value" => '',
		));
		$this->db->update($this->tableName, array('description_json' => $json));

		// $this->db->insert_batch($this->tableName, array(
		// 	array(
		// 		'name' => 'approve_transfer_to_main',
		// 		'value' => '',
		// 		'note' => 'Allow admin user to approve transfer to main',
		// 		'description_json' => json_encode(array(
		// 			"type" => "checkbox",
		// 			"default_value" => false, "label_lang" => "Approve Transfer To Main",
		// 		)),
		// 	),
		// 	array(
		// 		'name' => 'approve_transfer_from_main',
		// 		'value' => '',
		// 		'note' => 'Allow admin user to approve transfer from main',
		// 		'description_json' => json_encode(array(
		// 			"type" => "checkbox",
		// 			"default_value" => false, "label_lang" => "Approve Transfer From Main",
		// 		)),
		// 	),
		// ));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'description_json');
		// $this->db->where_in('name', array('approve_transfer_to_main', 'approve_transfer_from_main'))->delete($this->tableName);
	}
}