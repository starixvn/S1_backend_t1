<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_player_game_types_201711141614 extends CI_Migration {

	public function up() {
		# agency_player_game_platforms ###############################################################################################################
		$this->dbforge->drop_table('agency_player_game_platforms');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
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
		$this->dbforge->create_table('agency_player_game_platforms');

		$this->db->query('ALTER TABLE `agency_player_game_platforms` ADD UNIQUE `agency_player_game_platforms` (`game_platform_id`, `player_id`)');
		# agency_player_game_platforms ###############################################################################################################

		# agency_player_game_types ###############################################################################################################
		$this->dbforge->drop_table('agency_player_game_types');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
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
			'pattern_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('agency_player_game_types');

		$this->db->query('ALTER TABLE `agency_player_game_types` ADD UNIQUE `agency_player_game_types` (`player_id`, `game_platform_id`, `game_type_id`)');
		# agency_player_game_types ###############################################################################################################
	}

	public function down() {
		$this->dbforge->drop_table('agency_player_game_platforms');
		$this->dbforge->drop_table('agency_player_game_types');
	}
}
