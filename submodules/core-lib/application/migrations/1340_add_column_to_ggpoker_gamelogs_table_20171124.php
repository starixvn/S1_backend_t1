<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ggpoker_gamelogs_table_20171124 extends CI_Migration {

	public function up() {
		$fields = array(
			'converted_profit_and_loss' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
		);
		$this->dbforge->add_column('ggpoker_game_logs', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('ggpoker_game_logs', 'converted_profit_and_loss');
	}
	
}
