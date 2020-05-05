<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_session_id_to_adminusers_20151006 extends CI_Migration {

	private $tableName = 'adminusers';

	function up() {
		$this->db->query("ALTER TABLE ci_admin_sessions CONVERT TO CHARACTER SET utf8 COLLATE 'utf8_unicode_ci'");
		$this->dbforge->add_column($this->tableName, [
			'session_id' => array(
				'type' => 'VARCHAR',
				'constraint' => 40,
				'null' => true,
			),
		]);
	}

	public function down() {
		// $this->db->query("ALTER TABLE ci_admin_sessions CONVERT TO CHARACTER SET utf8 COLLATE 'utf8_general_ci'");
		$this->dbforge->drop_column($this->tableName, 'session_id');
	}
}