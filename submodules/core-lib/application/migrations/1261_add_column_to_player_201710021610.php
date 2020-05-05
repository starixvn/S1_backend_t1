<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_201710021610 extends CI_Migration {

	public function up() {
		if (!$this->db->field_exists('agent_tracking_code', 'player')) {
			$field = array(
				'agent_tracking_code' => array(
					'type' => 'VARCHAR',
					'constraint' => '100',
					'null' => false,
				),
			);
			$this->dbforge->add_column('player', $field);
		}
		if (!$this->db->field_exists('agent_tracking_source_code', 'player')) {
			$field = array(
				'agent_tracking_source_code' => array(
					'type' => 'VARCHAR',
					'constraint' => '100',
					'null' => false,
				),
			);
			$this->dbforge->add_column('player', $field);
		}
	}

	public function down() {
		$this->dbforge->drop_column('player', 'agent_tracking_code');
		$this->dbforge->drop_column('player', 'agent_tracking_source_code');
	}
}
