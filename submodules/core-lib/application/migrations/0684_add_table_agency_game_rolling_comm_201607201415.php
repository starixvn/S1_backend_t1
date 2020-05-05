<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_agency_game_rolling_comm_201607201415 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_platform_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'game_type_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'game_desc_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('agency_game_rolling_comm');
	}

	public function down() {
		$this->dbforge->drop_table('agency_game_rolling_comm');
	}
}
