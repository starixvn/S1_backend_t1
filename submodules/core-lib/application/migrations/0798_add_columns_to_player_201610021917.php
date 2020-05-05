<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_player_201610021917 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$fields = array(
			'disabled_cashback' => array(
				'type' => 'INT',
				'null' => true,
				'default'=> 0,
			),
			'disabled_promotion' => array(
				'type' => 'INT',
				'null' => true,
				'default'=> 0,
			),
		);

		$this->dbforge->add_column($this->tableName, $fields);

		// $this->load->model(['roles']);
		// $this->roles->initFunction('disable_cashback', 'Disable player cashback', 167, 15, true);
		// $this->roles->initFunction('disable_promotion', 'Disable player promotion', 168, 15, true);

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'disabled_cashback');
		$this->dbforge->drop_column($this->tableName, 'disabled_promotion');

		// $this->load->model(['roles']);
		// $this->roles->deleteFunction(167);
		// $this->roles->deleteFunction(168);

	}
}
