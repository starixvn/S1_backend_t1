<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_lotus_game_logs_20181025 extends CI_Migration {

	private $tableName = 'lotus_game_logs';

	public function up() {

		$fields = array(
			'betting_date' => array(
                'type' => 'DATETIME',
                'null' => true,
			),
		);

		if (!$this->db->field_exists('betting_date', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
		}
	
	}

    public function down(){
    }
}