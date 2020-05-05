<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201608030103 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {
		$fields = array(
			'release_when_finish_withdraw_cond' => array(
				'type' => 'INT',
				'null' => true,
				'default' => 0,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'release_when_finish_withdraw_cond');
	}

}
