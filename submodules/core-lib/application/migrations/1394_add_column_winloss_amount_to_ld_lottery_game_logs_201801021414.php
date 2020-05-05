<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_winloss_amount_to_ld_lottery_game_logs_201801021414 extends CI_Migration {

	private $tableName = 'ld_lottery_game_logs';

	public function up() {
		$fields = array(
			'winloss_amount' => array(
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
                'after' => 'bet_amount'
            ),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'winloss_amount');
	}
}

////END OF FILE////