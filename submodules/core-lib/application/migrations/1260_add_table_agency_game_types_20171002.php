<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_game_types_20171002 extends CI_Migration {

	public function up() {

		$this->dbforge->drop_table('agency_agent_game_types');

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
			'game_type_id' => array(
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
			),
			'rolling_comm_out' => array(
				'type' => 'DOUBLE',
				'null' => false,
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('agency_agent_game_types');

		$this->db->query('ALTER TABLE `agency_agent_game_types` ADD UNIQUE `agency_agent_game_types` (`agent_id`, `game_platform_id`, `game_type_id`)');

		$this->dbforge->drop_table('agency_structure_game_types');
		
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
			'game_type_id' => array(
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
			),
			'rolling_comm_out' => array(
				'type' => 'DOUBLE',
				'null' => false,
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('agency_structure_game_types');

		$this->db->query('ALTER TABLE `agency_structure_game_types` ADD UNIQUE `agency_structure_game_types` (`structure_id`, `game_platform_id`, `game_type_id`)');

		$this->dbforge->drop_column('agency_agent_game_platforms', 'rev_share');
		$this->dbforge->drop_column('agency_agent_game_platforms', 'rolling_comm');

		$this->dbforge->drop_column('agency_structure_game_platforms', 'rev_share');
		$this->dbforge->drop_column('agency_structure_game_platforms', 'rolling_comm');

	}

	public function down() {

		$this->dbforge->drop_table('agency_agent_game_types');
		$this->dbforge->drop_table('agency_structure_game_types');

	}

}
