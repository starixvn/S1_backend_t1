<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_group_level_cashback_percentage_history_20181020 extends CI_Migration {

	private $tableName = 'group_level_cashback_percentage_history';

	public function up() {

		if (!$this->db->field_exists('admin_user_id', $this->tableName)) {
			$this->dbforge->add_column($this->tableName, array(
				'admin_user_id' => array(
					'type' => 'INT',
					'null' => true,
				),
			));
		}

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'admin_user_id');
	}
}
