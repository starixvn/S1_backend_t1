<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_ci_sessions extends CI_Migration {

	public function up() {

		$fields = array(
			'session_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '40',
			),
			'ip_address' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
			),
			'user_agent' => array(
				'type' => 'VARCHAR',
				'constraint' => '250',
			),
			'last_activity' => array(
				'type' => 'INT',
				'constraint' => '10',
			),
			'user_data' => array(
				'type' => 'TEXT',
			),
		);

		// $this->dbforge->add_field($fields);
		// $this->dbforge->add_key('session_id', TRUE);
		// $this->dbforge->create_table('ci_admin_sessions');

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('session_id', TRUE);
		$this->dbforge->create_table('ci_player_sessions');

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('session_id', TRUE);
		$this->dbforge->create_table('ci_aff_sessions');
	}

	public function down() {
		// $this->dbforge->drop_table('ci_admin_sessions');
		$this->dbforge->drop_table('ci_player_sessions');
		$this->dbforge->drop_table('ci_aff_sessions');
	}
}

///END OF FILE