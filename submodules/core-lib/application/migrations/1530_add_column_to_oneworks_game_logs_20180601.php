<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_oneworks_game_logs_20180601 extends CI_Migration {

	private $tableName = 'oneworks_game_logs';

	public function up() {
		$fields = array(
			'parlay_type' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'combo_type' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			)
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'parlay_type');
		$this->dbforge->drop_column($this->tableName, 'combo_type');
	}
}
