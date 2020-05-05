<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_game_platforms_20170927 extends CI_Migration {

	public function up() {

		$this->dbforge->drop_table('agency_agent_game_platforms');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'rev_share' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'rolling_comm' => array(
				'type' => 'DOUBLE',
				'null' => false,
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('agency_agent_game_platforms');

		$this->db->query('ALTER TABLE `agency_agent_game_platforms` ADD UNIQUE `agency_agent_game_platforms` (`game_platform_id`, `agent_id`)');

		$this->dbforge->drop_table('agency_structure_game_platforms');
		
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'structure_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'rev_share' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'rolling_comm' => array(
				'type' => 'DOUBLE',
				'null' => false,
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('agency_structure_game_platforms');

		$this->db->query('ALTER TABLE `agency_structure_game_platforms` ADD UNIQUE `agency_structure_game_platforms` (`game_platform_id`, `structure_id`)');

	}

	public function down() {

		$this->dbforge->drop_table('agency_agent_game_platforms');
		$this->dbforge->drop_table('agency_structure_game_platforms');

	}

}
