<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_group_level_cashback_percentage_history_201612281747 extends CI_Migration {

	public function up() {

		$db_true=1;

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'vipsetting_cashbackrule_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'percentage_history' => array(
				'type' => 'TEXT',
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

		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('vipsetting_cashbackrule_id');

		$this->dbforge->create_table('group_level_cashback_percentage_history');

	}

	public function down() {

		$this->dbforge->drop_table('group_level_cashback_percentage_history');

	}
}
