<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_for_after_balance_on_ebet_th_game_logs_20190620 extends CI_Migration {

	private $tableName = 'ebet_th_game_logs';

	public function up() {

        $fields = array(
            'balance' => array(
                'type' => 'DOUBLE',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);

	}

	public function down() {
        $this->dbforge->drop_column($this->tableName, 'balance');
    }
}