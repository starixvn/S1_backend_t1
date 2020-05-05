<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// class Migration_Add_table_agency_game_rolling_comm_201607201415 extends CI_Migration {
class Migration_Add_table_agency_player_details_201707251413 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'playerId' => array(
				'type' => 'INT',
				'null' => true,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'base_credit' => array(
			    'type' => 'DECIMAL',
				'constraint' => '9,2',
			) ,
			'notes' => array(
				'type' => 'TEXT',
				'null' => true,
			)
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('agency_player_details');
	}

	public function down() {
		$this->dbforge->drop_table('agency_player_details');
	}
}
