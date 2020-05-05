<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ggpoker_gamelogs_table_20171123 extends CI_Migration {

	public function up() {
		$fields = array(
			'game_date' => array(
				'type' => 'DATETIME',
                'null' => true,
			),
		);
		$this->dbforge->add_column('ggpoker_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('ggpoker_game_logs', 'game_date');
	}
	
}
