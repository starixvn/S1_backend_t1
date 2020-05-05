<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_external_common_tokens_01302018 extends CI_Migration {

	private $tableName = 'external_common_tokens';

	public function up() {

		$fields = array(
			'status' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => true
			)
		);

		if (!$this->db->field_exists('status', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
		}
	
	}

    public function down(){
		$this->dbforge->drop_column($this->tableName, 'status');
    }
}