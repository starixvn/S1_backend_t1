<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_column_id_of_rwb_game_logs_20180322 extends CI_Migration {

	private $tableName = 'rwb_game_logs';

	public function up() {
		$fields = array(
            'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
        );

        $this->dbforge->modify_column($this->tableName, $fields);
    }

	public function down() {
		$fields = array(
            'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
        );

        $this->dbforge->modify_column($this->tableName, $fields);
	}	

}

///END OF FILE//////////