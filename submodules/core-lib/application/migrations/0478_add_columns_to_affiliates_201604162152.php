<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_affiliates_201604162152 extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'totalPlayerCashback' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		));

		$this->dbforge->add_column($this->tableName, array(
			'totalPlayerBonus' => array(
				'type' => 'DOUBLE',
				'null' => false,
				'default' => 0,
			),
		));

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'totalPlayerCashback');
		$this->dbforge->drop_column($this->tableName, 'totalPlayerBonus');
	}
}

///END OF FILE////////////////