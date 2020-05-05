<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_kenogame_gamelogs_player_id_column_datatype_201604072059 extends CI_Migration {

	private $tableName = 'kenogame_game_logs';

	public function up() {
		
		$fields = array(
                        'PlayerId' => array(
                                        'type' => 'VARCHAR',
										'constraint' => '100',
										'null' => true,
                                         )
					);

          $this->dbforge->modify_column($this->tableName, $fields);

// gives ALTER TABLE table_name CHANGE old_name new_name TEXT
	}

	public function down() {

		$fields = array(
                        'PlayerId' => array(
                                        'type' => 'INT',
										'constraint' => '10',
										'null' => true,
                                         )
					);

          $this->dbforge->modify_column($this->tableName, $fields);
	}
}
