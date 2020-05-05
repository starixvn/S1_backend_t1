<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_20190624 extends CI_Migration {

	public function up() {
		$fields = array(
			'c6_status' => array(
				'type' => 'VARCHAR',
                'constraint' => '100',
				'null' => TRUE,
			),
		);

		$this->dbforge->add_column('player', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('player', 'c6_status');
	}
}
