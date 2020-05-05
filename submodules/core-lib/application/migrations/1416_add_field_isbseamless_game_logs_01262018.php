<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_isbseamless_game_logs_01262018 extends CI_Migration {

	private $tableName = 'isbseamless_game_logs';

	public function up() {

		$fields = array(
			'transaction_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
			)
		);

		if (!$this->db->field_exists('status', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
		}
	
	}

    public function down(){
		$this->dbforge->drop_column($this->tableName, 'transaction_status');
    }
}