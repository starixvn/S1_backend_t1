<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_player_column_verify_201709071333 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		
		$fields = array(
                        'verify' => array(
                                        'type' => 'VARCHAR',
										'constraint' => '100',
										'null' => false
                                         )
					);

          $this->dbforge->modify_column($this->tableName, $fields);

// gives ALTER TABLE table_name CHANGE old_name new_name TEXT
	}

	public function down() {

		$fields = array(
                        'verify' => array(
                                        'type' => 'VARCHAR',
										'constraint' => '32',
										'null' => false
                                         )
					);

          $this->dbforge->modify_column($this->tableName, $fields);
	}
}
