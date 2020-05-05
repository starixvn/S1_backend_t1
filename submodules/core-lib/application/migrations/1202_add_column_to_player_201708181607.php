<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_201708181607 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$fields = array(
			'notes' => array(
				'type' => 'VARCHAR',
				'constraint' => 1000,
				'null' => true,
			),
		);

		if (!$this->db->field_exists('notes', $this->tableName))
		{
			$this->dbforge->add_column($this->tableName, $fields);
		}
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'notes');
	}
}

////END OF FILE////