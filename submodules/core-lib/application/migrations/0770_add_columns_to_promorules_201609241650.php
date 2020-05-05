<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_promorules_201609241650 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {
		$fields = array(
			'disable_cashback_if_not_finish_withdraw_condition' => array(
				'type' => 'INT',
				'null' => true,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'disable_cashback_if_not_finish_withdraw_condition');
	}
}
