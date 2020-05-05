<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_common_cashback_rules_percentage_history_20181121 extends CI_Migration {

	private $tableName = 'common_cashback_rules_percentage_history';

	public function up() {

		$db_true=1;

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'common_cashback_rules_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'percentage_history' => array(
                'type' => 'MEDIUMTEXT',
                'null' => true,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => $db_true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'new_percentage' => array(
				'type' => 'MEDIUMTEXT',
				'null' => true,
			),
			'admin_user_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		if (!$this->db->table_exists($this->tableName)) {
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->add_key('common_cashback_rules_id');

			$this->dbforge->create_table($this->tableName);
		}


	}

	public function down() {

		$this->dbforge->drop_table('group_level_cashback_percentage_history');

	}
}
