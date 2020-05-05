<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ld_lottery_game_logs_201712051324 extends CI_Migration {

	private $tableName = 'ld_lottery_game_logs';

	public function up() {
		$fields = array(
			'round_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
			'extra' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {		
		$this->dbforge->drop_column($this->tableName, 'round_id');
		$this->dbforge->drop_column($this->tableName, 'extra');
	}
}

////END OF FILE////