<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_lotus_game_logs_20181026 extends CI_Migration {

	private $tableName = 'lotus_game_logs';

	public function up() {

		$fields = array(
            'response_result_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
		);

		if (!$this->db->field_exists('response_result_id', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
		}
	
	}

    public function down(){
    }
}