<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_urls_to_external_system extends CI_Migration {

	private $tableName = 'external_system';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
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
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'live_url');
		$this->dbforge->drop_column($this->tableName, 'sandbox_url');
		$this->dbforge->drop_column($this->tableName, 'live_key');
		$this->dbforge->drop_column($this->tableName, 'live_secret');
		$this->dbforge->drop_column($this->tableName, 'sandbox_key');
		$this->dbforge->drop_column($this->tableName, 'sandbox_secret');
		$this->dbforge->drop_column($this->tableName, 'live_mode');
	}
}
