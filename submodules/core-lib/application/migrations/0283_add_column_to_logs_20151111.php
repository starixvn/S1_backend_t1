<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_logs_20151111 extends CI_Migration {

	private $tableName = 'logs';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'referrer' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'uri' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true,
			),
			'data' => array(
				'type' => 'VARCHAR',
				'constraint' => '2000',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'referrer');
		$this->dbforge->drop_column($this->tableName, 'uri');
		$this->dbforge->drop_column($this->tableName, 'data');
	}
}

///END OF FILE//////////