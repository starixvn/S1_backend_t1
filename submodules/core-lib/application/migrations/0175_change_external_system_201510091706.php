<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_external_system_201510091706 extends CI_Migration {

	public function up() {
		$this->load->model('external_system');
		$fields = array(
			'system_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'default' => External_system::STATUS_NORMAL,
				'null' => true,
			),
			'class_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'local_path' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'manager' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		);
		$this->dbforge->add_column('external_system', $fields);

		//fix data, add NT and MG
		// $this->db->insert_batch('external_system', array(
		// 	array(
		// 		'id' => NT_API,
		// 		'system_name' => 'NT_API',
		// 		'system_type' => SYSTEM_GAME_API,
		// 	),
		// 	array(
		// 		'id' => MG_API,
		// 		'system_name' => 'MG_API',
		// 		'system_type' => SYSTEM_GAME_API,
		// 	),
		// ));

		$data = array(
			array(
				'id' => PT_API,
				'system_code' => 'PT',
				"class_name" => "game_api_pt",
				"local_path" => "game_platform",
				"manager" => "game_platform_manager",
			),
			array(
				'id' => AG_API,
				'system_code' => 'AG',
				"class_name" => "game_api_ag",
				"local_path" => "game_platform",
				"manager" => "game_platform_manager",
			),
			// array(
			// 	'id' => MG_API,
			// 	'system_code' => 'MG',
			// 	"class_name" => "game_api_mg",
			// 	"local_path" => "game_platform",
			// 	"manager" => "game_platform_manager",
			// ),
			// array(
			// 	'id' => NT_API,
			// 	'system_code' => 'NT',
			// 	"class_name" => "game_api_nt",
			// 	"local_path" => "game_platform",
			// 	"manager" => "game_platform_manager",
			// ),
			array(
				'id' => IPS_PAYMENT_API,
				'system_code' => 'IPS',
				"class_name" => "payment_api_ips",
				"local_path" => "payment",
				"manager" => "payment_manager",
			),
			array(
				'id' => GOPAY_PAYMENT_API,
				'system_code' => 'GOPAY',
				"class_name" => "payment_api_gopay",
				"local_path" => "payment",
				"manager" => "payment_manager",
			),
		);
		$this->db->update_batch('external_system', $data, 'id');
		//remove AG_FTP
		$this->db->delete('external_system', array('id' => AG_FTP));
	}

	public function down() {
		$this->dbforge->drop_column('external_system', 'system_code');
		$this->dbforge->drop_column('external_system', 'status');
		$this->dbforge->drop_column('external_system', 'class_name');
		$this->dbforge->drop_column('external_system', 'local_path');
		$this->dbforge->drop_column('external_system', 'manager');
	}
}