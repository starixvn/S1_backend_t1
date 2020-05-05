<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_duplicate_account_info_201705240946 extends CI_Migration {

	private $tableName = 'duplicate_account_info';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'userName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'dup_userName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'dup_regIp' => array(
				'type' => 'VARCHAR',
				'constraint' => '15',
				'null' => true,
			),
			'dup_loginIp' => array(
				'type' => 'VARCHAR',
				'constraint' => '15',
				'null' => true,
			),
			'dup_depositIp' => array(
				'type' => 'VARCHAR',
				'constraint' => '15',
				'null' => true,
			),
			'dup_withDrawIp' => array(
				'type' => 'VARCHAR',
				'constraint' => '15',
				'null' => true,
			),
			'dup_TranMain2SubIp' => array(
				'type' => 'VARCHAR',
				'constraint' => '15',
				'null' => true,
			),
			'dup_TranSub2MainIp' => array(
				'type' => 'VARCHAR',
				'constraint' => '15',
				'null' => true,
			),
			'dup_realName' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'dup_passwd' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'dup_email' => array(
				'type' => 'VARCHAR',
				'constraint' => '250',
				'null' => true,
			),
			'dup_mobile' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true,
			),
			'dup_address' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'dup_city' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'dup_country' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'dup_cookie' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'dup_referrer' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'dup_device' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'total_rate' => array(
				'type' => 'INT',
				'constraint' => 3,
				'null' => false,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}