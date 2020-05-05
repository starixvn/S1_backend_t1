<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_promorules_201607271200 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {
		$fields = array(
			'trigger_on_transfer_to_subwallet' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);

		//add triggered_subwallet_id
		$fields = array(
			'triggered_subwallet_id' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
        );

		$this->dbforge->add_column('playerpromo', $fields);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'trigger_on_transfer_to_subwallet');
		$this->dbforge->drop_column('playerpromo', 'triggered_subwallet_id');
	}
}
