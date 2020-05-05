<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 *
 */
class Migration_add_columns_to_balanceadjustmenthistory_20151024 extends CI_Migration {

	private $tableName = 'balanceadjustmenthistory';

	public function up() {
		$fields = array(
			'oldBalance' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'adjustmentType' => array(
				'type' => 'INT',
				'null' => false,
			),
		);
		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'oldBalance');
		$this->dbforge->drop_column($this->tableName, 'adjustmentType');
	}
}

///END OF FILE/////