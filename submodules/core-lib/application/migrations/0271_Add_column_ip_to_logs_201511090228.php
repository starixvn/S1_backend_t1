<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_ip_to_logs_201511090228 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('logs', array(
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('logs', 'ip');
	}
}