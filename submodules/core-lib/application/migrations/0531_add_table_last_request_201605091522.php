<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_last_request_201605091522 extends CI_Migration {

	// private $tableName = 'device_player_last_request';

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'device' => array(
				'type' => 'VARCHAR',
				'constraint' => '220',
				'null' => true,
				'comment' => "get from user_agent",
			),
			'last_datetime' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'http_request_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('player_device_last_request');

		$this->db->query('create unique index idx_player_device on player_device_last_request(player_id,device)');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => false,
			),
			'last_datetime' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'http_request_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('player_ip_last_request');

		$this->db->query('create unique index idx_player_device on player_ip_last_request(player_id,ip)');

	}

	public function down() {
		$this->dbforge->drop_table('player_device_last_request');
		$this->dbforge->drop_table('player_ip_last_request');
	}
}