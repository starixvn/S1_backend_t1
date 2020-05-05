<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_game_types_20171003 extends CI_Migration {

	public function up() {

		# agency_agent_game_platforms ###############################################################################################################
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
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('agency_agent_game_platforms');

		$this->db->query('ALTER TABLE `agency_agent_game_platforms` ADD UNIQUE `agency_agent_game_platforms` (`game_platform_id`, `agent_id`)');
		# agency_agent_game_platforms ###############################################################################################################

		# agency_structure_game_platforms ###############################################################################################################
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
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('agency_structure_game_platforms');

		$this->db->query('ALTER TABLE `agency_structure_game_platforms` ADD UNIQUE `agency_structure_game_platforms` (`game_platform_id`, `structure_id`)');
		# agency_structure_game_platforms ###############################################################################################################

		# agency_agent_game_types ###############################################################################################################
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
			'rolling_comm_basis' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
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
		# agency_agent_game_types ###############################################################################################################

		# agency_structure_game_types ###############################################################################################################
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
			'rolling_comm_basis' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
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
		# agency_structure_game_typ5es ###############################################################################################################

	}

	public function down() {

		$this->dbforge->drop_table('agency_agent_game_platforms');
		$this->dbforge->drop_table('agency_agent_game_types');
		$this->dbforge->drop_table('agency_structure_game_platforms');
		$this->dbforge->drop_table('agency_structure_game_types');

	}

}
