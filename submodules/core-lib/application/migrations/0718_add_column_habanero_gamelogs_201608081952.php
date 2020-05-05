<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_habanero_gamelogs_201608081952 extends CI_Migration {
	public function up() {
		$fields = array(
			'BrandId' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true
			),
		);
		$this->dbforge->add_column('haba88_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('haba88_game_logs', 'BrandId');
	}
}
