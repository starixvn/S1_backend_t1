<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_banktype_201708121301 extends CI_Migration {

	private $tableName = 'banktype';

	public function up() {
		$fields = array(
			'bankIcon' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
		);

		if (!$this->db->field_exists('bankIcon', $this->tableName))
		{
			$this->dbforge->add_column($this->tableName, $fields);
		}
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'bankIcon');
	}
}

////END OF FILE////