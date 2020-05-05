<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_201708191654 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$fields = array(
			'total_wrong_login_attempt' => array(
				'type' => 'INT',
				'constraint' => 1,
				'default' => 0,
			),
		);

		if (!$this->db->field_exists('total_wrong_login_attempt', $this->tableName))
		{
			$this->dbforge->add_column($this->tableName, $fields);
		}
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'total_wrong_login_attempt');
	}
}

////END OF FILE////