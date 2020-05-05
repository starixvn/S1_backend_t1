<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_flag_to_rwb_game_transactions_20181206 extends CI_Migration {

	public function up() {
		$fields = array(
			'is_settled' => array(
                'type' => 'SMALLINT',
                'null' => true,
			)
		);
		$this->dbforge->add_column('rwb_game_transactions', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('rwb_game_transactions', 'is_settled');
	}
}
