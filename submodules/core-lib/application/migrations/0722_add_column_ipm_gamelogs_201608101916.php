<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_ipm_gamelogs_201608101916 extends CI_Migration {
	public function up() {
		$fields = array(
			'ParlayBetDetails' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('ipm_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('ipm_game_logs', 'ParlayBetDetails');
	}
}
