<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_createdAt_and_updatedAt_to_pt_krw_201711151706 extends CI_Migration {

	public function up() {
		$this->dbforge->add_column('pt_krw_game_logs', array(
			'createdAt' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updatedAt' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('pt_krw_game_logs', 'createdAt');
		$this->dbforge->drop_column('pt_krw_game_logs', 'updatedAt');
	}
}
