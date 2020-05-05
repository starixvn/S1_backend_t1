<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_ebetkuma_gamelogs_201708241514 extends CI_Migration {
	public function up() {
		$fields = array(
			'playerId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true
			),
		);
		$this->dbforge->add_column('ebetkuma_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('ebetkuma_game_logs', 'playerId');
	}
}