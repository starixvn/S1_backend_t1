<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_tier_comm_patterns_201711120720 extends CI_Migration {

	public function up() {

        // Add new table 'agency_tier_comm_patterns'
		$fields = array(
			'pattern_id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'pattern_name' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
			'tier_count' => array(
				'type' => 'INT',
				'null' => false,
                'default' => 1
			),
			'cal_method' => array(
				'type' => 'INT',
				'null' => false,
                'default' => 0
			),
			'min_bets' => array(
				'type' => 'DOUBLE',
				'null' => false,
                'default' => 0
			),
			'min_trans' => array(
				'type' => 'DOUBLE',
				'null' => false,
                'default' => 0
			),
			'min_active_player_count' => array(
				'type' => 'INT',
				'null' => false,
                'default' => 0
			),
			'rev_share' => array(
				'type' => 'DOUBLE',
				'null' => false,
                'default' => 0
			),
			'rolling_comm_basis' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
			'rolling_comm' => array(
				'type' => 'DOUBLE',
				'null' => false,
                'default' => 0
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('pattern_id', TRUE);
		$this->dbforge->create_table('agency_tier_comm_patterns');

        // Add new table 'agency_tier_comm_pattern_tiers'
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'pattern_id' => array(
				'type' => 'INT',
				'null' => false,
                'default' => 0
			),
			'tier_index' => array(
				'type' => 'INT',
				'null' => false,
                'default' => 0
			),
			'upper_bound' => array(
				'type' => 'DOUBLE',
				'null' => false,
                'default' => 0
			),
			'rev_share' => array(
				'type' => 'DOUBLE',
				'null' => false,
                'default' => 0
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('agency_tier_comm_pattern_tiers');
		$this->db->query('ALTER TABLE `agency_tier_comm_pattern_tiers` ADD UNIQUE `agency_tier_comm_pattern_tiers` (`pattern_id`, `tier_index`)');

		// add column 'pattern_id' to  table 'agency_agent_game_types' and 'agency_structure_game_types'
		$fields = array(
			'pattern_id' => array(
				'type' => 'INT',
				'null' => false,
                'default' => 0
            ),
		);

        $this->dbforge->add_column('agency_agent_game_types', $fields);
        $this->dbforge->add_column('agency_structure_game_types', $fields);
    }

	public function down() {
		$this->dbforge->drop_table('agency_tier_comm_patterns');
		$this->dbforge->drop_table('agency_tier_comm_pattern_tiers');
		$this->dbforge->drop_column('agency_agent_game_types', 'pattern_id');
		$this->dbforge->drop_column('agency_structure_game_types', 'pattern_id');
	}

}
