<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_list_for_external_system_201510091751 extends CI_Migration {

	public function up() {
		$this->load->model('external_system');

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'system_name' => array(
				'type' => 'varchar',
				'constraint' => '200',
				'null' => false,
			),
			'note' => array(
				'type' => 'varchar',
				'constraint' => '1000',
				'null' => true,
			),
			'last_sync_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'last_sync_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'last_sync_details' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'system_type' => array(
				'type' => 'INT',
				'null' => true,
			),
			'live_url' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,
				'null' => true,
			),
			'sandbox_url' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,
				'null' => true,
			),
			'live_key' => array(
				'type' => 'VARCHAR',
				'constraint' => 500,
				'null' => true,
			),
			'live_secret' => array(
				'type' => 'VARCHAR',
				'constraint' => 500,
				'null' => true,
			),
			'sandbox_key' => array(
				'type' => 'VARCHAR',
				'constraint' => 500,
				'null' => true,
			),
			'sandbox_secret' => array(
				'type' => 'VARCHAR',
				'constraint' => 500,
				'null' => true,
			),
			'live_mode' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'second_url' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'sandbox_account' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'live_account' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
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
		));

		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('external_system_list');
		//copy
// 		$sql = <<<EOD
// insert into external_system_list(
// 	id, system_name,note,last_sync_datetime,last_sync_id,
// 	last_sync_details,system_type,live_url,sandbox_url,live_key,
// 	live_secret,sandbox_key,sandbox_secret,live_mode,second_url,
// 	sandbox_account,live_account,system_code,status,class_name,
// 	local_path, manager	)
// select id, system_name,note,last_sync_datetime,last_sync_id,
// 	last_sync_details,system_type,live_url,sandbox_url,live_key,
// 	live_secret,sandbox_key,sandbox_secret,live_mode,second_url,
// 	sandbox_account,live_account,system_code,status,class_name,
// 	local_path, manager
// 	from external_system
// EOD;
// 		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_table('external_system_list');
	}
}