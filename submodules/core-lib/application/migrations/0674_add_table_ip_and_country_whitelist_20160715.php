<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ip_and_country_whitelist_20160715 extends CI_Migration {

	public function up() {

		$fields = array(
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'ip_address' => array(
				'type' => 'VARCHAR',
				'null' => false,
				'constraint'=> 45,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key(array('game_platform_id', 'ip_address'), TRUE);
		$this->dbforge->create_table('ip_whitelist');


		$fields = array(
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'country' => array(
				'type' => 'VARCHAR',
				'null' => false,
				'constraint'=> 45,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key(array('game_platform_id', 'country'), TRUE);
		$this->dbforge->create_table('country_whitelist');

	}

	public function down() {
		$this->dbforge->drop_table('ip_whitelist');
		$this->dbforge->drop_table('country_whitelist');
	}
}