<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_dispatch_account_level_20181004 extends CI_Migration {
	private $tableName = 'dispatch_account_level';

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => true,
				'auto_increment' => true,
			),
			'group_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'level_order' => array(
				'type' => 'INT',
				'null' => false,
			),
			'level_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'level_member_limit' => array(
				'type' => 'INT',
				'null' => false,
			),
			'level_single_max_deposit' => array(
				'type' => 'INT',
				'null' => false,
			),
			'level_total_deposit' => array(
				'type' => 'INT',
				'null' => false,
			),
			'level_deposit_count' => array(
				'type' => 'INT',
				'null' => false,
			),
			'level_total_withdraw' => array(
				'type' => 'INT',
				'null' => false,
			),
			'level_withdraw_count' => array(
				'type' => 'INT',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			)
		);

        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
        }

		// $this->load->model('dispatch_account');
		// $data = array(
		// 	"group_name" => "Default Dispatch Account Group",
		// 	"group_level_count" => 1,
		// 	"group_description" => "Default Dispatch Account Group",
		// 	"level_member_limit" => 100,
		// 	"level_single_max_deposit" => 100,
		// 	"level_total_deposit" => 1000,
		// 	"level_deposit_count" => 1,
		// 	"level_total_withdraw" => 1,
		// 	"level_withdraw_count" => 1,
		// 	"created_at" => $this->utils->getNowForMysql(),
		// 	"updated_at" => $this->utils->getNowForMysql()
		// );
		// $this->dispatch_account->addDispatchAccountGroup($data);
	}

	public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
	}
}
