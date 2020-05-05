<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_id_to_ci_sessions_201607110130 extends CI_Migration {

	public function up() {

		$fields = array(
			'player_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('ci_player_sessions', $fields);

		// $fields = array(
		// 	'admin_id' => array(
		// 		'type' => 'INT',
		// 		'null' => true,
		// 	),
		// );
		// $this->dbforge->add_column('ci_admin_sessions', $fields);

		$fields = array(
			'agent_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('ci_agency_sessions', $fields);

		$fields = array(
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('ci_aff_sessions', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('ci_player_sessions', 'player_id');
		// $this->dbforge->drop_column('ci_admin_sessions', 'admin_id');
		$this->dbforge->drop_column('ci_agency_sessions', 'agent_id');
		$this->dbforge->drop_column('ci_aff_sessions', 'affiliate_id');
	}
}