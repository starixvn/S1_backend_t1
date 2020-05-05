<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_game_description_on_super_game_report_201804151856 extends CI_Migration {
	public function up() {
		//modify column
		$fields = array(
			'game_description_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => false,
			),
		);
		$this->dbforge->modify_column('super_game_report', $fields);
		$this->dbforge->modify_column('super_cashback_report', $fields);
	}

	public function down() {
		$fields = array(
			'game_description_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
		);

		$this->dbforge->modify_column('super_game_report', $fields);
		$this->dbforge->modify_column('super_cashback_report', $fields);
	}
}