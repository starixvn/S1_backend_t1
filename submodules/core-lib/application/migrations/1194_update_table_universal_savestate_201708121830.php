<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_table_universal_savestate_201708121830 extends CI_Migration {

	private $tableName = 'universal_savestate';

	public function up() {
		if ($this->db->field_exists('columnshownumber', $this->tableName))
		{
			$this->dbforge->drop_column($this->tableName, 'columnshownumber');
			$this->db->query('ALTER TABLE universal_savestate CHANGE COLUMN columnhidenumber columnhidenumber VARCHAR(100) NULL');
		}
	}

	public function down() {
		$fields = array(
			'columnshownumber' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
		);

		if (!$this->db->field_exists('columnshownumber', $this->tableName))
		{
			$this->dbforge->add_column($this->tableName, $fields);
			$this->db->query('ALTER TABLE universal_savestate CHANGE COLUMN columnhidenumber columnhidenumber VARCHAR(100) NOT NULL');
		}
	}
}

////END OF FILE////