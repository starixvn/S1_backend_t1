<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_createdon_201607080352 extends CI_Migration {
	public function up() {
		$fields = array(
			'created_on' => array(
				'type' => 'TIMESTAMP',
				'null' => false,
			),
		);
		$this->dbforge->add_column('game_description', $fields);
		$this->dbforge->add_column('game_type', $fields);
		$this->dbforge->add_column('external_system', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('game_description', 'created_on');
		$this->dbforge->drop_column('game_type', 'created_on');
		$this->dbforge->drop_column('external_system', 'created_on');
	}
}
