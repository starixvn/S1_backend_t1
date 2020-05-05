<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_extreme_live_gaming_game_logs_table_20171103 extends CI_Migration {

	public function up() {
		$fields = array(
			'bet_referenceId' => array(
				'type' => 'int',
                'unsigned' => TRUE,
                'null' => true,
			),
		);
		$this->dbforge->add_column('extreme_live_gaming_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('extreme_live_gaming_game_logs', 'bet_referenceId');
	}
	
}
