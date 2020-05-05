<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_http_request extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'playerId' => array(
				'type' => 'INT',
				'null' => false,
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'cookie' => array(
				'type' => 'TEXT',
				'null' => true,
				'comment' => "value only",
			),
			'referrer' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'user_agent' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'os' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
				'comment' => "get from user_agent",
			),
			'device' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
				'comment' => "get from user_agent",
			),
			'is_mobile' => array(
				'type' => 'int',
				'null' => true,
				'comment' => "get from user_agent: 1- true, 0 - false",
			),
			'type' => array(
				'type' => 'INT',
				'null' => false,
				'comment' => "1 - Registration, 2 - Last Login, 3 - Deposit, 4 - Withdraw, 5 - Transfer From Main Wallet to Sub Wallet, 6 - Transfer From Sub Wallet to Main Wallet",
			),
			'createdat' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('http_request');
	}

	public function down() {
		$this->dbforge->drop_table('http_request');
	}
}